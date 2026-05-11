<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../connection/connect.php");

// Get order ID from URL
if (!isset($_GET['order_id'])) {
    echo "Invalid request!";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details
$sql = "SELECT 
            o.order_id,
            o.order_date,
            o.status,
            o.payment_method,
            o.payment_reference,
            o.shipping_fee,
            o.grand_total,
            o.delivery_location,
            o.barangay,
            u.fullname,
            u.phone,
            u.address,
            GROUP_CONCAT(CONCAT(oi.product_name, ' (', oi.quantity, 'kg × ₱', oi.price, ')') SEPARATOR '<br>') AS items,
            SUM(oi.subtotal) AS items_subtotal
        FROM orders o
        JOIN users u ON o.u_id = u.u_id
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.order_id = ?
        GROUP BY o.order_id";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Order not found!";
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: "Courier New", monospace;
            text-align: center;
            background-color: #f8f8f8;
            padding: 20px;
        }
        .receipt {
            width: 350px;
            background: white;
            padding: 20px;
            border: 2px dashed #333;
            display: inline-block;
            text-align: left;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        }
        .receipt h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .receipt p {
            margin: 5px 0;
            font-size: 14px;
        }
        .receipt .separator {
            border-top: 2px dashed #333;
            margin: 10px 0;
        }
        .buttons {
            margin-top: 15px;
            text-align: center;
        }
        .buttons button {
            font-size: 14px;
            padding: 8px 12px;
            margin: 5px;
            border: none;
            cursor: pointer;
            background: #333;
            color: white;
            font-weight: bold;
            border-radius: 5px;
        }
        .buttons button:hover {
            background: #555;
        }
        .items-list {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>Order Receipt</h2>
        <p>BUGAY DAGAT</p>
        <p>Tel No: 09103857426</p>
        
        <div class="separator"></div>

        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($order['phone']) ?></p>
        
        <div class="separator"></div>
        
        <h4>Items Ordered:</h4>
        <div class="items-list"><?= $order['items'] ?></div>
        
        <div class="separator"></div>
        
        <p><strong>Subtotal:</strong> ₱<?= number_format($order['items_subtotal'], 2) ?></p>
        <p><strong>Shipping Fee:</strong> ₱<?= number_format($order['shipping_fee'], 2) ?></p>
        <p><strong>Total Amount:</strong> ₱<?= number_format($order['grand_total'], 2) ?></p>
        
        <p><strong>Payment Method:</strong> <?= strtoupper($order['payment_method']) ?></p>
        <?php if(!empty($order['payment_reference'])): ?>
            <p><strong>Payment Reference:</strong> <?= htmlspecialchars($order['payment_reference']) ?></p>
        <?php endif; ?>
        
        <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['address'] . ', ' . $order['barangay'] . ', ' . $order['delivery_location']) ?></p>
        <p><strong>Order Date:</strong> <?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></p>
        
        <div class="separator"></div>
        
        <div class="buttons">
            <button onclick="window.print()">Print Receipt</button>
            <button onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        </div>
    </div>
</body>
</html>