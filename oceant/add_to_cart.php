<?php
include("connection/connect.php");
error_reporting(0);
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = intval($_POST['product_id']);
    $quantity = floatval($_POST['quantity']);
    $price = floatval($_POST['price']);
    
    // Check if product exists and is in stock
    $check_sql = "SELECT * FROM seafoods WHERE d_id = '$product_id'";
    $check_result = mysqli_query($db, $check_sql);
    $product = mysqli_fetch_assoc($check_result);
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    
    if ($product['stock'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart_item'])) {
        $_SESSION['cart_item'] = array();
    }
    
    // Check if product already in cart
    if (isset($_SESSION['cart_item'][$product_id])) {
        // Update quantity
        $_SESSION['cart_item'][$product_id]['quantity'] += $quantity;
    } else {
        // Add new item to cart
        $_SESSION['cart_item'][$product_id] = array(
            'd_id' => $product_id,
            'title' => $product['title'],
            'price' => $price,
            'quantity' => $quantity
        );
    }
    
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>