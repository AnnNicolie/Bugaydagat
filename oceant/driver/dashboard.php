<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(!isset($_SESSION['driver_id'])) {
    header("location: index.php");
    exit;
}

// Get driver info
$driver_id = $_SESSION['driver_id'];
$driver_query = "SELECT * FROM drivers WHERE driver_id = '$driver_id'";
$driver_result = mysqli_query($db, $driver_query);
$driver = mysqli_fetch_assoc($driver_result);

// Sample orders data (in real application, this would come from database)
$orders = array(
    array(
        'id' => 'ORD-001',
        'customer' => 'Alice Johnson',
        'address' => '123 Main St, Apt 4B, New York, NY 10001',
        'items' => '2 packages',
        'status' => 'pending',
        'notes' => 'Fragile items'
    ),
    array(
        'id' => 'ORD-002',
        'customer' => 'Bob Smith', 
        'address' => '456 Oak Ave, Los Angeles, CA 90210',
        'items' => '1 package',
        'status' => 'out-for-delivery',
        'notes' => 'Leave at front door if no answer'
    ),
    array(
        'id' => 'ORD-003',
        'customer' => 'Carol Davis',
        'address' => '789 Pine Rd, Chicago, IL 60616',
        'items' => '3 packages',
        'status' => 'pending',
        'notes' => 'Customer will be home after 5 PM'
    )
);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .dashboard-title {
            font-size: 28px;
            color: #2c3e50;
        }
        
        .driver-info {
            display: flex;
            gap: 30px;
            color: #555;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .orders-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .order-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s;
        }
        
        .order-card:hover {
            transform: translateY(-5px);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-id {
            font-weight: bold;
            color: #2c3e50;
            font-size: 18px;
        }
        
        .order-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-out-for-delivery {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-delivered {
            background: #d4edda;
            color: #155724;
        }
        
        .order-details p {
            margin-bottom: 10px;
            color: #555;
        }
        
        .order-actions {
            margin-top: 15px;
            text-align: right;
        }
        
        .update-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .update-btn:hover {
            background: #2980b9;
        }
        
        .update-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
        }
        
        .back-btn {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-btn:hover {
            text-decoration: underline;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .modal h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .cancel-btn {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .confirm-btn {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-top">
                <h1 class="dashboard-title">Driver Dashboard</h1>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
            <div class="driver-info">
                <div><strong>Driver:</strong> <?php echo $driver['full_name']; ?></div>
                <div><strong>Vehicle:</strong> <?php echo $driver['vehicle_number']; ?></div>
                <div><strong>Username:</strong> <?php echo $driver['username']; ?></div>
            </div>
        </header>

        <div class="info-card">
            <h3>Your Delivery Assignments</h3>
            <p>Update the status of your deliveries as you complete them.</p>
        </div>
        
        <div class="orders-container" id="orders-container">
            <?php foreach($orders as $order): ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-id"><?php echo $order['id']; ?></div>
                    <div class="order-status status-<?php echo $order['status']; ?>">
                        <?php 
                        if($order['status'] == 'pending') echo 'Pending';
                        if($order['status'] == 'out-for-delivery') echo 'Out for Delivery';
                        if($order['status'] == 'delivered') echo 'Delivered';
                        ?>
                    </div>
                </div>
                <div class="order-details">
                    <p><strong>Customer:</strong> <?php echo $order['customer']; ?></p>
                    <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                    <p><strong>Items:</strong> <?php echo $order['items']; ?></p>
                    <p><strong>Notes:</strong> <?php echo $order['notes']; ?></p>
                </div>
                <div class="order-actions">
                    <button class="update-btn" onclick="updateOrderStatus('<?php echo $order['id']; ?>')" 
                        <?php echo $order['status'] == 'delivered' ? 'disabled' : ''; ?>>
                        <?php 
                        if($order['status'] == 'pending') echo 'Start Delivery';
                        if($order['status'] == 'out-for-delivery') echo 'Mark as Delivered';
                        if($order['status'] == 'delivered') echo 'Delivered';
                        ?>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <h3>Update Order Status</h3>
            <p>Are you sure you want to mark this order as delivered?</p>
            <div class="modal-buttons">
                <button class="cancel-btn" onclick="closeModal()">Cancel</button>
                <button class="confirm-btn" onclick="confirmDelivery()">Confirm Delivery</button>
            </div>
        </div>
    </div>

    <script>
        let currentOrderId = null;

        function updateOrderStatus(orderId) {
            const orderElement = document.querySelector(`[onclick="updateOrderStatus('${orderId}')"]`);
            const statusElement = orderElement.closest('.order-card').querySelector('.order-status');
            
            if(orderElement.textContent === 'Start Delivery') {
                // Update to "Out for Delivery"
                orderElement.textContent = 'Mark as Delivered';
                statusElement.textContent = 'Out for Delivery';
                statusElement.className = 'order-status status-out-for-delivery';
                
                // In real application, send AJAX request to update database
                alert('Order status updated to: Out for Delivery');
                
            } else if(orderElement.textContent === 'Mark as Delivered') {
                currentOrderId = orderId;
                document.getElementById('statusModal').style.display = 'flex';
            }
        }

        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
            currentOrderId = null;
        }

        function confirmDelivery() {
            if (currentOrderId) {
                const orderElement = document.querySelector(`[onclick="updateOrderStatus('${currentOrderId}')"]`);
                const statusElement = orderElement.closest('.order-card').querySelector('.order-status');
                
                orderElement.textContent = 'Delivered';
                orderElement.disabled = true;
                statusElement.textContent = 'Delivered';
                statusElement.className = 'order-status status-delivered';
                
                // In real application, send AJAX request to update database
                alert('Order marked as delivered successfully!');
            }
            closeModal();
        }
    </script>
</body>
</html>