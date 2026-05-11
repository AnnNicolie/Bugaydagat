<?php
session_start();
include("../connection/connect.php");

// Check if driver is logged in
if(!isset($_SESSION['driver_id']) || empty($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit();
}

$driver_id = $_SESSION['driver_id'];
$driver_name = isset($_SESSION['driver_name']) ? $_SESSION['driver_name'] : 'Driver';

// Get driver profile information
$profile_pic = 'images/default-avatar.png'; // Default image

// Try multiple ways to get driver data
$driver_query = false;
$possible_id_columns = ['driver_id', 'id', 'd_id']; // Common primary key names

foreach ($possible_id_columns as $column) {
    $driver_query = mysqli_query($db, "SELECT profile_pic FROM delivery_drivers WHERE $column = '$driver_id'");
    if($driver_query && mysqli_num_rows($driver_query) > 0) {
        break;
    }
}

// If still no result, try using session username/email
if(!$driver_query || mysqli_num_rows($driver_query) == 0) {
    $driver_username = isset($_SESSION['driver_username']) ? $_SESSION['driver_username'] : '';
    $driver_email = isset($_SESSION['driver_email']) ? $_SESSION['driver_email'] : '';
    
    if(!empty($driver_username)) {
        $driver_query = mysqli_query($db, "SELECT profile_pic FROM delivery_drivers WHERE username = '$driver_username'");
    } 
    if((!$driver_query || mysqli_num_rows($driver_query) == 0) && !empty($driver_email)) {
        $driver_query = mysqli_query($db, "SELECT profile_pic FROM delivery_drivers WHERE email = '$driver_email'");
    }
}

// Process driver data
if($driver_query && mysqli_num_rows($driver_query) > 0) {
    $driver_data = mysqli_fetch_assoc($driver_query);
    $profile_pic = isset($driver_data['profile_pic']) ? $driver_data['profile_pic'] : 'images/default-avatar.png';
}

// SIMPLIFIED QUERY - Get assigned deliveries
$deliveries = [];

// First, get basic order information
$orders_query = mysqli_query($db, "SELECT 
    o.order_id,
    o.order_date,
    o.status,
    o.grand_total,
    o.delivery_location,
    o.barangay,
    o.payment_method,
    o.payment_reference,
    u.fullname,
    u.phone,
    u.address
FROM orders o
JOIN users u ON o.u_id = u.u_id
WHERE o.driver_id = '$driver_id' AND o.status IN ('on the way', 'processing')
ORDER BY o.order_date DESC");

if($orders_query && mysqli_num_rows($orders_query) > 0) {
    while($order = mysqli_fetch_assoc($orders_query)) {
        $order_id = $order['order_id'];
        
        // Get items for this order
        $items_query = mysqli_query($db, "SELECT product_name, quantity FROM order_items WHERE order_id = '$order_id'");
        $items = [];
        if($items_query && mysqli_num_rows($items_query) > 0) {
            while($item = mysqli_fetch_assoc($items_query)) {
                $items[] = $item['product_name'] . ' (' . $item['quantity'] . 'kg)';
            }
        }
        $order['items'] = implode(', ', $items);
        
        // Check for payment proof
        $payment_proof_query = mysqli_query($db, "SELECT COUNT(*) as proof_count FROM payment_proofs WHERE order_id = '$order_id'");
        $payment_proof = mysqli_fetch_assoc($payment_proof_query);
        $order['has_payment_proof'] = $payment_proof['proof_count'];
        
        $deliveries[] = $order;
    }
}

// Get delivery stats for the dashboard
$stats_query = mysqli_query($db, "SELECT 
    COUNT(*) as total_deliveries,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_deliveries,
    SUM(CASE WHEN status IN ('on the way', 'processing') THEN 1 ELSE 0 END) as active_deliveries
FROM orders 
WHERE driver_id = '$driver_id'");

$stats = $stats_query ? mysqli_fetch_assoc($stats_query) : [
    'total_deliveries' => 0,
    'completed_deliveries' => 0,
    'active_deliveries' => 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Bugay Dagat</title>
    <link href="../css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../css/helper.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <!-- ADD Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --primary-dark: #1b5e20;
            --secondary: #ff9800;
            --dark: #333;
            --light: #f8f9fa;
            --gray: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
        }
        
        /* IMPROVED HEADER STYLES */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 25px 0 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.08)" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,170.7C960,160,1056,160,1152,165.3C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            opacity: 0.6;
        }
        
        .dashboard-header .container {
            position: relative;
            z-index: 1;
        }
        
        .header-main-row {
            align-items: flex-start;
        }
        
        .brand-section {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .brand-logo {
            background: rgba(255,255,255,0.2);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .brand-logo i {
            font-size: 32px;
            color: white;
        }
        
        .brand-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            letter-spacing: 0.5px;
        }
        
        .welcome-message {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-top: 0;
            font-weight: 500;
        }
        
        .profile-section-left {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .profile-welcome {
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 500;
            text-align: left;
        }
        
        /* Simple Profile Button Styles - BACKGROUND REMOVED */
        .profile-btn {
            background: transparent; /* Changed from rgba(255,255,255,0.2) */
            border: none; /* Removed border */
            padding: 10px 20px;
            border-radius: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-weight: 600;
            box-shadow: none; /* Removed box shadow */
            text-decoration: none;
        }
        
        .profile-btn:hover {
            background: rgba(255,255,255,0.1); /* Lighter background on hover */
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: white;
            text-decoration: none;
        }
        
        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.8);
        }
        
        .profile-default {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255,255,255,0.8);
        }
        
        .profile-default i {
            font-size: 20px;
            color: white;
        }
        
        .profile-name {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 20px 0 15px 0;
            }
            
            .brand-logo {
                width: 60px;
                height: 60px;
                margin-right: 15px;
            }
            
            .brand-text h1 {
                font-size: 2rem;
            }
            
            .welcome-message {
                font-size: 1.1rem;
            }
            
            .profile-section-left {
                align-items: center;
                margin-top: 15px;
            }
            
            .profile-welcome {
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .brand-text h1 {
                font-size: 1.8rem;
            }
            
            .welcome-message {
                font-size: 1rem;
            }
            
            .profile-btn {
                padding: 8px 15px;
            }
            
            .profile-name {
                font-size: 1rem;
            }
        }

        /* Rest of your existing styles remain the same */
        .delivery-card {
            border: none;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            overflow: hidden;
            background: white;
        }
        
        .delivery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .delivery-card-header {
            background: linear-gradient(to right, var(--light), #e9ecef);
            padding: 15px 25px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .delivery-card-body {
            padding: 25px;
        }
        
        .status-badge {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .page-title {
            color: var(--dark);
            margin-bottom: 25px;
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }
        
        .page-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        .delivery-info {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }
        
        .delivery-info strong {
            color: var(--primary);
            min-width: 160px;
            display: inline-block;
        }
        
        .delivery-info i {
            width: 20px;
            margin-right: 10px;
            color: var(--primary);
        }
        
        .payment-status {
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .payment-paid {
            background-color: rgba(40, 167, 69, 0.15);
            color: var(--success);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .payment-unpaid {
            background-color: rgba(220, 53, 69, 0.15);
            color: var(--danger);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .payment-cod {
            background-color: rgba(23, 162, 184, 0.15);
            color: var(--info);
            border: 1px solid rgba(23, 162, 184, 0.3);
        }
        
        .payment-info-section {
            background: rgba(0, 123, 255, 0.05);
            border-left: 4px solid var(--info);
            padding: 18px;
            margin: 20px 0;
            border-radius: 8px;
        }
        
        /* Buttons */
        .btn-success {
            background: linear-gradient(to right, var(--success), #1e7e34);
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-success:hover {
            background: linear-gradient(to right, #1e7e34, #155724);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .btn-outline-primary {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-outline-primary:hover {
            transform: translateY(-2px);
        }
        
        /* Image preview */
        .image-preview-container {
            display: none;
            margin-top: 15px;
            position: relative;
            width: 150px;
        }
        
        .preview-img {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .remove-image-btn {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .delivery-info {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .delivery-info strong {
                min-width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container">
            <div class="row header-main-row">
                <!-- Left Side - Brand -->
                <div class="col-md-6">
                    <div class="brand-section">
                        <div class="brand-logo">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div class="brand-text">
                            <h1>Bugay Dagat</h1>
                            <p class="welcome-message">Welcome, <?php echo htmlspecialchars($driver_name); ?>!</p>
                        </div>
                    </div>
                </div>
                  <!-- Profile Dropdown -->
                        <div class="profile-dropdown">
                            <button class="profile-btn" onclick="toggleDropdown()">
                                <?php if ($profile_pic && file_exists('../' . $profile_pic) && $profile_pic != 'images/default-avatar.png'): ?>
                                    <img src="../<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-img">
                                <?php else: ?>
                                    <div class="profile-default">
                                        <i class="fas fa-user"></i>
                                    </div>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu" id="profileDropdown">
                                <a href="driver_logout.php" class="dropdown-item logout-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay to close dropdown when clicking outside -->
                    <div class="dropdown-overlay" id="dropdownOverlay" onclick="closeDropdown()"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h4 class="page-title"><i class="fas fa-list-alt mr-2"></i>Your Assigned Deliveries</h4>
        
        <?php if(!empty($deliveries)): ?>
            <?php foreach($deliveries as $row): 
                // Determine payment status and styling
                $payment_method = strtoupper($row['payment_method']);
                $payment_status = '';
                $payment_class = '';
                $payment_icon = '';
                
                if ($row['payment_method'] == 'cash_on_delivery') {
                    $payment_status = 'Cash on Delivery';
                    $payment_class = 'payment-cod';
                    $payment_icon = 'fa-money-bill-wave';
                } elseif ($row['payment_method'] == 'gcash') {
                    if ($row['has_payment_proof'] > 0) {
                        $payment_status = 'Paid (GCash)';
                        $payment_class = 'payment-paid';
                        $payment_icon = 'fa-check-circle';
                    } else {
                        $payment_status = 'Unpaid (GCash)';
                        $payment_class = 'payment-unpaid';
                        $payment_icon = 'fa-exclamation-triangle';
                    }
                } else {
                    $payment_status = $payment_method;
                    $payment_class = 'payment-cod';
                    $payment_icon = 'fa-credit-card';
                }
            ?>
                <div class="delivery-card">
                    <div class="delivery-card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <strong><i class="fas fa-receipt"></i> Order #<?php echo $row['order_id']; ?></strong>
                            </div>
                            <div class="col-md-6 text-right">
                                <small class="text-muted">
                                    <i class="far fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="delivery-card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- PAYMENT INFORMATION SECTION -->
                                <div class="payment-info-section">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Payment Method:</strong> 
                                            <span class="payment-status <?php echo $payment_class; ?> mt-2 d-inline-block">
                                                <i class="fas <?php echo $payment_icon; ?>"></i> 
                                                <?php echo $payment_status; ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <strong>Total Amount:</strong> 
                                            <span class="font-weight-bold h5 text-dark">₱<?php echo number_format($row['grand_total'], 2); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if($row['payment_method'] == 'gcash' && !empty($row['payment_reference'])): ?>
                                    <div class="mt-2">
                                        <strong>Reference No:</strong> 
                                        <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($row['payment_reference']); ?></code>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($row['payment_method'] == 'cash_on_delivery'): ?>
                                    <div class="mt-3 alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        <strong>Note:</strong> Please collect ₱<?php echo number_format($row['grand_total'], 2); ?> upon delivery.
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="delivery-info">
                                    <i class="fas fa-user"></i>
                                    <div>
                                        <strong>Customer:</strong> 
                                        <?php echo htmlspecialchars($row['fullname']); ?>
                                    </div>
                                </div>
                                <div class="delivery-info">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Contact:</strong> 
                                        <?php echo htmlspecialchars($row['phone']); ?>
                                    </div>
                                </div>
                                <div class="delivery-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <strong>Delivery Address:</strong> 
                                        <?php echo htmlspecialchars($row['address'] . ', ' . $row['barangay'] . ', ' . $row['delivery_location']); ?>
                                    </div>
                                </div>
                                <div class="delivery-info">
                                    <i class="fas fa-box"></i>
                                    <div>
                                        <strong>Items:</strong> 
                                        <?php echo $row['items']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex flex-column h-100">
                                    <div class="mb-3 text-center">
                                        <?php 
                                        $status_badge = '';
                                        $status = strtolower($row['status']);
                                        switch($status) {
                                            case "on the way": 
                                                $status_badge = '<span class="badge badge-warning status-badge"><i class="fas fa-truck"></i> On the Way</span>';
                                                break;
                                            case "processing":
                                                $status_badge = '<span class="badge badge-info status-badge"><i class="fas fa-cog"></i> Processing</span>';
                                                break;
                                            default: 
                                                $status_badge = '<span class="badge badge-secondary status-badge">'.$row['status'].'</span>';
                                        }
                                        echo $status_badge;
                                        ?>
                                    </div>
                                    
                                    <?php if ($status === 'on the way' || $status === 'processing'): ?>
                                    <div class="mt-auto">
                                        <form action="complete_delivery.php" method="post" enctype="multipart/form-data" class="delivery-form">
                                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                                            
                                            <div class="form-group text-center">
                                                <label for="proof_image_<?php echo $row['order_id']; ?>" class="btn btn-outline-primary btn-block">
                                                    <i class="fas fa-camera"></i> Upload Delivery Proof
                                                </label>
                                                <input type="file" 
                                                       id="proof_image_<?php echo $row['order_id']; ?>" 
                                                       name="proof_image" 
                                                       accept="image/*" 
                                                       class="d-none" 
                                                       required
                                                       onchange="previewImage(this, <?php echo $row['order_id']; ?>)">
                                                
                                                <div id="image_preview_<?php echo $row['order_id']; ?>" class="image-preview-container">
                                                    <img id="preview_<?php echo $row['order_id']; ?>" src="#" alt="Preview" class="preview-img">
                                                    <button type="button" class="remove-image-btn" onclick="removeImage(<?php echo $row['order_id']; ?>)">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <button type="submit" 
                                                    class="btn btn-success btn-lg btn-block mt-3"
                                                    onclick="return validateForm(<?php echo $row['order_id']; ?>)">
                                                <i class="fas fa-check-circle"></i> Mark as Delivered
                                            </button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="text-muted">No Deliveries Assigned</h3>
                <p class="text-muted mb-4">You don't have any deliveries assigned at the moment. Please check back later.</p>
                <button class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh Page
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS for better functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Delivery proof image functions
    function previewImage(input, orderId) {
        const preview = document.getElementById('preview_' + orderId);
        const previewContainer = document.getElementById('image_preview_' + orderId);
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage(orderId) {
        const input = document.getElementById('proof_image_' + orderId);
        const previewContainer = document.getElementById('image_preview_' + orderId);
        
        input.value = '';
        previewContainer.style.display = 'none';
    }

    function validateForm(orderId) {
        const input = document.getElementById('proof_image_' + orderId);
        
        if (!input.files[0]) {
            alert('Please upload a delivery proof image before marking as delivered.');
            return false;
        }
        
        // Check file size (max 5MB)
        if (input.files[0].size > 5 * 1024 * 1024) {
            alert('Image size should be less than 5MB.');
            return false;
        }
        
        return confirm('Are you sure you want to mark order #' + orderId + ' as delivered?');
    }
    </script>
</body>
</html>