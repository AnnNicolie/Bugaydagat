<?php
// Use absolute path to ensure the file is found
require_once(__DIR__.'/../connection/connect.php');
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verify database connection
if(!isset($db) || !$db) {
    die("Database connection failed. Please check your connection file.");
}

// Check if user is logged in
if(empty($_SESSION['user_id'])) {
    header('location:../login.php');
    exit();
}

if(isset($_GET['order_del'])) {
    $order_id = (int)$_GET['order_del'];
    
    // Verify the order belongs to the user
    $check_query = "SELECT status FROM orders WHERE order_id = ? AND u_id = ?";
    $stmt = $db->prepare($check_query);
    
    if(!$stmt) {
        header("location:../your_orders.php?del_success=0&error=prepare_failed&db_error=".urlencode($db->error));
        exit();
    }
    
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    
    if(!$stmt->execute()) {
        header("location:../your_orders.php?del_success=0&error=query_failed&db_error=".urlencode($db->error));
        exit();
    }
    
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        header("location:../your_orders.php?del_success=0&error=no_order");
        exit();
    }
    
    $order = $result->fetch_assoc();
    $status = strtolower($order['status']);
    
    // Only allow cancellation for pending or processing orders
    if($status === 'pending' || $status === 'processing') {
        $update_order = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
        
        if(!$update_order) {
            header("location:../your_orders.php?del_success=0&error=prepare_failed&db_error=".urlencode($db->error));
            exit();
        }
        
        $update_order->bind_param("i", $order_id);
        
        if($update_order->execute()) {
            // Add cancellation remark
            $remark = $db->prepare("INSERT INTO remark (frm_id, status, remark, remarkDate) VALUES (?, 'cancelled', 'Order cancelled by customer', NOW())");
            $remark->bind_param("i", $order_id);
            $remark->execute();
            
            header("location:../your_orders.php?del_success=1");
        } else {
            header("location:../your_orders.php?del_success=0&error=update_failed&db_error=".urlencode($db->error));
        }
    } else {
        header("location:../your_orders.php?del_success=0&error=invalid_status&current_status=$status");
    }
} else {
    header("location:../your_orders.php?error=no_order_specified");
}
exit();
?>