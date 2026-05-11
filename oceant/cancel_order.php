<?php
include("connection/connect.php");
session_start();

if(empty($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

if(isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    
    // Verify the order belongs to the logged-in user
    $check_stmt = $db->prepare("SELECT u_id, status FROM orders WHERE order_id = ?");
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows === 1) {
        $order = $check_result->fetch_assoc();
        
        if($order['u_id'] == $_SESSION['user_id']) {
            // Only allow cancellation if status is pending or processing
            if(strtolower($order['status']) === 'pending' || strtolower($order['status']) === 'processing') {
                // Update order status to cancelled
                $update_stmt = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = ?");
                $update_stmt->bind_param("i", $order_id);
                
                if($update_stmt->execute()) {
                    // Add a remark about the cancellation
                    $remark = "Order cancelled by customer on " . date('Y-m-d H:i:s');
                    $remark_stmt = $db->prepare("INSERT INTO remark (frm_id, status, remark) VALUES (?, 'cancelled', ?)");
                    $remark_stmt->bind_param("is", $order_id, $remark);
                    $remark_stmt->execute();
                    
                    header('location: your_orders.php?del_success=1');
                    exit();
                } else {
                    header('location: your_orders.php?del_success=0');
                    exit();
                }
            } else {
                // Order cannot be cancelled
                header('location: your_orders.php?del_success=0');
                exit();

                // After verifying the order belongs to the user:
if(strtolower($order['status']) === 'pending') {
    // Mark as cancelled AND track who cancelled it
    $update_stmt = $db->prepare("UPDATE orders 
                               SET status = 'cancelled', 
                                   cancelled_by = 'customer' 
                               WHERE order_id = ?");
    $update_stmt->bind_param("i", $order_id);
    $update_stmt->execute();
    
    // Add remark
    $remark = "Customer cancelled this order on " . date('M d, Y h:i A');
    $remark_stmt = $db->prepare("INSERT INTO remark (frm_id, status, remark) 
                               VALUES (?, 'cancelled', ?)");
    $remark_stmt->bind_param("is", $order_id, $remark);
    $remark_stmt->execute();
}
            }
        }
    }
}

// If anything fails
header('location: your_orders.php?del_success=0');
exit();
?>