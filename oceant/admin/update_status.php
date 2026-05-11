<?php
include("../connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Authentication check
if(strlen($_SESSION['adm_id']) == 0) { 
    header('location: ../login.php');
    exit();
}

// Validate inputs
if(!isset($_POST['order_id']) || !isset($_POST['status'])) {
    $_SESSION['error'] = "Invalid request parameters";
    header("Location: all_orders.php");
    exit();
}

$order_id = (int)$_POST['order_id'];
$status = $_POST['status'];
$driver_id = isset($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
$admin_message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate status
$allowed_statuses = ['pending', 'processing', 'on the way', 'delivered', 'cancelled'];
if(!in_array($status, $allowed_statuses)) {
    $_SESSION['error'] = "Invalid status selected";
    header("Location: view_order.php?order_id=$order_id");
    exit();
}

// Get order details for notifications
$order_query = "SELECT o.*, u.email, u.phone, u.fullname 
                FROM orders o 
                JOIN users u ON o.u_id = u.u_id 
                WHERE o.order_id = ?";
$stmt = $db->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if($order_result->num_rows == 0) {
    $_SESSION['error'] = "Order not found";
    header("Location: all_orders.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Get driver information if driver_id is provided
$driver_info = '';
if($driver_id) {
    $driver_query = "SELECT full_name, phone, vehicle_type, plate_number 
                     FROM delivery_drivers 
                     WHERE driver_id = ?";
    $stmt = $db->prepare($driver_query);
    $stmt->bind_param("i", $driver_id);
    $stmt->execute();
    $driver_result = $stmt->get_result();
    
    if($driver_result->num_rows > 0) {
        $driver = $driver_result->fetch_assoc();
        $driver_info = $driver;
    }
}

// Begin transaction
$db->begin_transaction();

try {
    // 1. Update order status and driver_id
    $update_sql = "UPDATE orders SET status = ?";
    $params = [$status];
    $types = "s";
    
    if($driver_id) {
        $update_sql .= ", driver_id = ?";
        $params[] = $driver_id;
        $types .= "i";
    }
    
    $update_sql .= " WHERE order_id = ?";
    $params[] = $order_id;
    $types .= "i";
    
    $stmt = $db->prepare($update_sql);
    $stmt->bind_param($types, ...$params);
    
    if(!$stmt->execute()) {
        throw new Exception("Failed to update order status");
    }

    // 2. Create status message
    $status_messages = [
        'pending' => 'Your order is being processed',
        'processing' => 'We are preparing your order',
        'on the way' => 'Your order is on the way!',
        'delivered' => 'Your order has been delivered',
        'cancelled' => 'Your order has been cancelled'
    ];

    $message_to_customer = $status_messages[$status] . "\n\n";

    // Add payment details
    if(strtolower($order['payment_method']) == 'gcash') {
        $message_to_customer .= "Payment Method: GCash\n";
        $message_to_customer .= "Amount: ₱" . number_format($order['grand_total'], 2) . "\n";
        $message_to_customer .= "Reference: " . $order['payment_reference'] . "\n";
    } elseif(strtolower($order['payment_method']) == 'cod') {
        $message_to_customer .= "Payment Method: COD\n";
        $message_to_customer .= "Amount Due: ₱" . number_format($order['grand_total'], 2) . "\n";
    }

    // Add driver info if provided and status is on the way or delivered
    if(!empty($driver_info) && ($status == 'on the way' || $status == 'delivered')) {
        $message_to_customer .= "\n--- Delivery Driver Information ---\n";
        $message_to_customer .= "Driver Name: " . $driver_info['full_name'] . "\n";
        $message_to_customer .= "Contact Number: " . $driver_info['phone'] . "\n";
        $message_to_customer .= "Vehicle: " . $driver_info['vehicle_type'] . " (" . $driver_info['plate_number'] . ")\n";
        $message_to_customer .= "---\n\n";
    }

    // Add admin message if provided
    if(!empty($admin_message)) {
        $message_to_customer .= "Note: " . $admin_message;
    }

    // 3. Save remark to database
    $remark_sql = "INSERT INTO remark (frm_id, status, remark) VALUES (?, ?, ?)";
    $stmt = $db->prepare($remark_sql);
    $stmt->bind_param("iss", $order_id, $status, $message_to_customer);
    
    if(!$stmt->execute()) {
        throw new Exception("Failed to save order remark");
    }

    // Commit transaction
    $db->commit();

    // 4. Send notifications
    sendNotifications($order, $status, $message_to_customer, $driver_info);

    $_SESSION['success'] = "Order #$order_id updated to " . ucwords($status);
    header("Location: view_order.php?order_id=$order_id");
    exit();

} catch (Exception $e) {
    $db->rollback();
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header("Location: view_order.php?order_id=$order_id");
    exit();
}

/**
 * Send email and SMS notifications
 */
function sendNotifications($order, $status, $message, $driver_info = []) {
    // Email notification
    $email_subject = "Order #{$order['order_id']} Status Update";
    
    $driver_section = '';
    if(!empty($driver_info)) {
        $driver_section = "
        <div style='background: #e8f4fd; padding: 15px; margin: 15px 0; border-left: 4px solid #3498db;'>
            <h3 style='margin-top: 0; color: #2c3e50;'>Delivery Driver Information</h3>
            <p><strong>Driver Name:</strong> {$driver_info['full_name']}</p>
            <p><strong>Contact Number:</strong> {$driver_info['phone']}</p>
            <p><strong>Vehicle:</strong> {$driver_info['vehicle_type']} ({$driver_info['plate_number']})</p>
        </div>
        ";
    }
    
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; border: 1px solid #ddd; }
            .status { 
                display: inline-block; 
                padding: 8px 15px; 
                border-radius: 4px; 
                font-weight: bold; 
                color: white;
                margin: 10px 0;
            }
            .pending { background: #3498db; }
            .processing { background: #3498db; }
            .on-the-way { background: #f39c12; }
            .delivered { background: #2ecc71; }
            .cancelled { background: #e74c3c; }
            .message { background: #f9f9f9; padding: 15px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Order #{$order['order_id']} Update</h2>
            </div>
            <div class='content'>
                <p>Hello {$order['fullname']},</p>
                
                <div class='status " . str_replace(' ', '-', $status) . "'>" . ucwords($status) . "</div>
                
                <div class='message'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                
                $driver_section
                
                <p>Thank you for your order!</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Seafood Store <noreply@seafoodstore.com>\r\n";
    
    mail($order['email'], $email_subject, $email_body, $headers);
    
    // SMS notification (basic implementation)
    $sms_message = "Order #{$order['order_id']}: " . ucwords($status);
    if(!empty($driver_info)) {
        $sms_message .= ". Driver: {$driver_info['full_name']} - {$driver_info['phone']}";
    }
    $sms_message .= ". " . substr(str_replace("\n", " ", $message), 0, 100);
    
    // In a real system, you would call your SMS gateway API here
    // sendSMS($order['phone'], $sms_message);
}
?>