<?php
session_start();
include("connection/connect.php");

if(empty($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if(isset($_POST['order_id']) && isset($_POST['message_ids'])) {
    $order_id = (int)$_POST['order_id'];
    $message_ids = $_POST['message_ids'];
    
    // Initialize session array if not exists
    if(!isset($_SESSION['read_messages'])) {
        $_SESSION['read_messages'] = [];
    }
    
    if(!isset($_SESSION['read_messages'][$order_id])) {
        $_SESSION['read_messages'][$order_id] = [];
    }
    
    // Mark each specific message as read
    foreach($message_ids as $message_id) {
        $_SESSION['read_messages'][$order_id][$message_id] = true;
    }
    
    echo 'success';
} else {
    echo 'error';
}
?>