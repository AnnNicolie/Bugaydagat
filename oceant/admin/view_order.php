<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(strlen($_SESSION['adm_id']) == 0) { 
    header('location: .');
    exit();
}

function getStatusBadge($status) {
    switch($status) {
        case "pending": 
            return '<span class="badge badge-info"><i class="fa fa-clock"></i> Pending</span>';
        case "processing": 
            return '<span class="badge badge-primary"><i class="fa fa-cog"></i> Processing</span>';
        case "on the way": 
            return '<span class="badge badge-warning"><i class="fa fa-truck"></i> On the Way</span>';
        case "delivered": 
            return '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Delivered</span>';
        case "cancelled": 
            return '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Cancelled</span>';
        default: 
            return '<span class="badge badge-secondary">'.$status.'</span>';
    }
}

// Secure order retrieval
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get order details with driver information
$sql = "SELECT 
    o.order_id,
    o.order_date,
    o.status,
    o.cancelled_by,
    o.payment_method,
    o.payment_reference,
    o.shipping_fee,
    o.grand_total,
    o.delivery_location,
    o.barangay,
    o.driver_id,
    u.u_id,
    u.username,
    u.fullname,
    u.phone,
    u.address,
    d.full_name as driver_name,
    d.vehicle_type as driver_vehicle,
    d.plate_number as driver_plate,
    d.phone as driver_phone,
    GROUP_CONCAT(CONCAT(oi.product_name, ' (', oi.quantity, 'kg × ₱', oi.price, ')') SEPARATOR '<br>') AS items,
    SUM(oi.quantity) AS total_quantity,
    SUM(oi.subtotal) AS items_subtotal
FROM orders o
JOIN users u ON o.u_id = u.u_id
LEFT JOIN delivery_drivers d ON o.driver_id = d.driver_id
LEFT JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_id = ?
GROUP BY o.order_id";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if(!$order) {
    header('Location: all_orders.php');
    exit();
}

// Then separately get delivery proof if exists
$proof_sql = "SELECT image_path, uploaded_at 
              FROM delivery_proofs 
              WHERE order_id = ? 
              ORDER BY uploaded_at DESC 
              LIMIT 1";
$proof_stmt = $db->prepare($proof_sql);
$proof_stmt->bind_param("i", $order_id);
$proof_stmt->execute();
$proof_result = $proof_stmt->get_result();
$delivery_proof = $proof_result->fetch_assoc();

// Get GCash payment proof if exists
$payment_proof_sql = "SELECT image_path, uploaded_at FROM payment_proofs WHERE order_id = ?";
$payment_proof_stmt = $db->prepare($payment_proof_sql);
$payment_proof_stmt->bind_param("i", $order_id);
$payment_proof_stmt->execute();
$payment_proof_result = $payment_proof_stmt->get_result();
$payment_proof = $payment_proof_result->fetch_assoc();

// Check if is_available column exists, if not use only status check
$check_column_sql = "SHOW COLUMNS FROM delivery_drivers LIKE 'is_available'";
$column_result = mysqli_query($db, $check_column_sql);
$is_available_column_exists = (mysqli_num_rows($column_result) > 0);

// Check for success message from driver delivery
$success_message = '';
if(isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>View Order</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <style>
.card-header h5 {
    color: white !important;
    margin: 0;
}

.card-header {
    background: #4171e3ff !important;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.order-details .card-header {
    background: linear-gradient(135deg, #4171e3ff,  #4171e3ff) !important;
}

.delivery-proof-section {
    border-left: 4px solid #28a745;
    background-color: #f8fff9;
    padding: 15px;
    margin-top: 20px;
    border-radius: 5px;
}

.delivery-proof-image {
    max-width: 300px;
    max-height: 300px;
    border: 2px solid #dee2e6;
    border-radius: 5px;
    margin-top: 10px;
}

.proof-info {
    background: #e9f7ef;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.driver-info-section {
    border-left: 4px solid #007bff;
    background-color: #f0f8ff;
    padding: 15px;
    margin-top: 15px;
    border-radius: 5px;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.driver-field {
    transition: all 0.3s ease;
}

.payment-proof-section {
    border-left: 4px solid #17a2b8;
    background-color: #f0f9ff;
    padding: 15px;
    margin-top: 20px;
    border-radius: 5px;
}

.payment-proof-image {
    max-width: 300px;
    max-height: 300px;
    border: 2px solid #dee2e6;
    border-radius: 5px;
    margin-top: 10px;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.payment-proof-image:hover {
    transform: scale(1.02);
    border-color: #007bff;
}
</style>
    
    <script language="javascript" type="text/javascript">
        var popUpWin = 0;
        function popUpWindow(URLStr, left, top, width, height) {
            if(popUpWin) {
                if(!popUpWin.closed) popUpWin.close();
            }
            popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+1000+',height='+1000+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
        }
    </script>
</head>

<body class="fix-header fix-sidebar">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
   
    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="images/icn.png" alt="homepage" class="dark-logo" /></span>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <div class="dropdown-menu dropdown-menu-right mailbox animated zoomIn">
                                <ul>
                                    <li>
                                        <div class="drop-title">Notifications</div>
                                    </li>
                                    <li>
                                        <a class="nav-link text-center" href="javascript:void(0);">
                                            <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="images/bookingSystem/user-icn.png" alt="user" class="profile-pic" />
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                <ul class="dropdown-user">
                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li><a href="all_users.php"><span><i class="fa fa-user f-s-20"></i></span><span>Users</span></a></li>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false">
                                <i class="fa fa-archive f-s-20 color-warning"></i>
                                <span class="hide-menu">Types of Seafoods</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_categories.php">All Types of Seafoods</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_product.php">Add Types of Seafoods</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false">
                                <i class="fa fa-cutlery" aria-hidden="true"></i>
                                <span class="hide-menu">Products</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                            <li><a href="all_available.php">All Availble Seafoods</a></li>
                                <li><a href="add_seafoods.php">Add Seafoods</a></li>
                            </ul>
                        </li>
                        <li><a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                         <li> <a href="all_drivers.php"><i class="fa fa-motorcycle" aria-hidden="true"></i><span>Delivery Drivers</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Success Message Alert -->
                <?php if(!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-12">
                        <div class="col-lg-12">
                            <div class="card card-outline-primary">
                                <div class="card-header">
                                    <h4 class="m-b-0 text-white">Order Details #<?php echo $order['order_id']; ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card order-details">
                                                <div class="card-header">
                                                    <h5>Customer Information</h5>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['fullname']); ?></p>
                                                    <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                                                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'].', '.$order['barangay'].', '.$order['delivery_location']); ?></p>
                                                </div>
                                            </div>
                                            
                                            <div class="card order-details mt-3">
                                                <div class="card-header">
                                                    <h5>Order Items</h5>
                                                </div>
                                                <div class="card-body">
                                                    <?php echo $order['items']; ?>
                                                    <p class="mt-2"><strong>Total Quantity:</strong> <?php echo $order['total_quantity']; ?> kg</p>
                                                    
                                                    <!-- GCASH PAYMENT PROOF SECTION -->
                                                    <?php if($order['payment_method'] == 'gcash' && !empty($payment_proof)): ?>
                                                    <div class="payment-proof-section">
                                                        <h6><i class="fa fa-camera text-info"></i> <strong>GCash Payment Proof</strong></h6>
                                                        <div class="proof-info">
                                                            <p class="mb-1"><strong>Reference No:</strong> <?php echo htmlspecialchars($order['payment_reference']); ?></p>
                                                            <?php if(!empty($payment_proof['uploaded_at'])): ?>
                                                            <p class="mb-1"><strong>Uploaded:</strong> <?php echo date('M d, Y h:i A', strtotime($payment_proof['uploaded_at'])); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <p><strong>Payment Screenshot:</strong></p>
                                                        <img src="../<?php echo htmlspecialchars($payment_proof['image_path']); ?>" 
                                                             alt="GCash Payment Proof" 
                                                             class="payment-proof-image img-thumbnail"
                                                             onclick="window.open('../<?php echo htmlspecialchars($payment_proof['image_path']); ?>', '_blank')">
                                                        <p class="text-muted mt-2"><small>Click image to view full size</small></p>
                                                    </div>
                                                    <?php elseif($order['payment_method'] == 'gcash'): ?>
                                                    <div class="alert alert-warning mt-3">
                                                        <i class="fa fa-exclamation-triangle"></i> No payment proof uploaded for this GCash order.
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- CURRENT DRIVER INFORMATION -->
                                                    <?php if(!empty($order['driver_id'])): ?>
                                                    <div class="driver-info-section">
                                                        <h6><i class="fa fa-truck text-primary"></i> <strong>Assigned Delivery Driver</strong></h6>
                                                        <div class="proof-info">
                                                            <p class="mb-1"><strong>Driver:</strong> <?php echo htmlspecialchars($order['driver_name']); ?></p>
                                                            <p class="mb-1"><strong>Vehicle:</strong> <?php echo htmlspecialchars($order['driver_vehicle'] . ' (' . $order['driver_plate'] . ')'); ?></p>
                                                            <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($order['driver_phone']); ?></p>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- DELIVERY PROOF SECTION -->
                                                    <?php if($order['status'] == 'delivered' && !empty($delivery_proof['image_path'])): ?>
                                                    <div class="delivery-proof-section mt-4">
                                                        <h6><i class="fa fa-check-circle text-success"></i> <strong>Delivery Completed</strong></h6>
                                                        <div class="proof-info">
                                                            <p class="mb-1"><strong>Delivered By:</strong> <?php echo htmlspecialchars($order['driver_name'] ?? 'Driver'); ?></p>
                                                            <?php if(!empty($delivery_proof['uploaded_at'])): ?>
                                                            <p class="mb-1"><strong>Delivered On:</strong> <?php echo date('M d, Y h:i A', strtotime($delivery_proof['uploaded_at'])); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <p><strong>Delivery Proof:</strong></p>
                                                        <img src="../delivery_proofs/<?php echo htmlspecialchars($delivery_proof['image_path']); ?>" 
                                                             alt="Delivery Proof" 
                                                             class="delivery-proof-image img-thumbnail"
                                                             onclick="window.open('../delivery_proofs/<?php echo htmlspecialchars($delivery_proof['image_path']); ?>', '_blank')"
                                                             style="cursor: pointer;">
                                                        <p class="text-muted mt-2"><small>Click image to view full size</small></p>
                                                    </div>
                                                    <?php elseif($order['status'] == 'delivered'): ?>
                                                    <div class="alert alert-info mt-3">
                                                        <i class="fa fa-info-circle"></i> Order marked as delivered (no proof image uploaded).
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="card order-details">
                                                <div class="card-header">
                                                    <h5>Order Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <strong>Order Date:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <strong>Subtotal:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            ₱<?php echo number_format($order['items_subtotal'], 2); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <strong>Shipping Fee:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            ₱<?php echo number_format($order['shipping_fee'], 2); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row table-active mt-2">
                                                        <div class="col-6">
                                                            <strong>Total Amount:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <strong>₱<?php echo number_format($order['grand_total'], 2); ?></strong>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="row mt-2">
                                                        <div class="col-6">
                                                            <strong>Payment Method:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <?php echo strtoupper($order['payment_method']); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if(!empty($order['payment_reference'])): ?>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <strong>Payment Reference:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <?php echo htmlspecialchars($order['payment_reference']); ?>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="row mt-3">
                                                        <div class="col-6">
                                                            <strong>Order Status:</strong>
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <?php echo getStatusBadge($order['status']); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- STATUS UPDATE FORM -->
                                                    <div class="mt-4">
                                                        <form action="update_status.php" method="post" id="statusForm">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label for="status"><strong>Update Status:</strong></label>
                                                                <select class="form-control" name="status" id="status" required>
                                                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                                    <option value="on the way" <?= $order['status'] == 'on the way' ? 'selected' : '' ?>>On the Way</option>
                                                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled by Admin</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- DELIVERY DRIVER SELECTION - THIS WAS MISSING -->
                                                            <div class="form-group driver-field" id="driverField">
                                                                <label for="driver_id"><strong>Assign Delivery Driver:</strong></label>
                                                                <select class="form-control" name="driver_id" id="driver_id">
                                                                    <option value="">-- Select Driver --</option>
                                                                    <?php
                                                                    // Build driver query based on column existence
                                                                    if ($is_available_column_exists) {
                                                                        $driver_sql = "SELECT driver_id, full_name, vehicle_type, plate_number, phone 
                                                                                      FROM delivery_drivers 
                                                                                      WHERE status = 1 AND is_available = 1
                                                                                      ORDER BY full_name";
                                                                    } else {
                                                                        $driver_sql = "SELECT driver_id, full_name, vehicle_type, plate_number, phone 
                                                                                      FROM delivery_drivers 
                                                                                      WHERE status = 1
                                                                                      ORDER BY full_name";
                                                                    }
                                                                    $driver_result = mysqli_query($db, $driver_sql);
                                                                    
                                                                    if(mysqli_num_rows($driver_result) > 0) {
                                                                        while($driver = mysqli_fetch_assoc($driver_result)) {
                                                                            $selected = ($driver['driver_id'] == $order['driver_id']) ? 'selected' : '';
                                                                            echo '<option value="'.$driver['driver_id'].'" '.$selected.'>
                                                                                    '.htmlspecialchars($driver['full_name']).' - '.$driver['vehicle_type'].' ('.$driver['plate_number'].') - '.$driver['phone'].'
                                                                                  </option>';
                                                                        }
                                                                    } else {
                                                                        echo '<option value="" disabled>No available drivers found</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <small class="form-text text-muted">Driver assignment is recommended for 'Processing' and required for 'On the Way' status</small>
                                                                <?php if(mysqli_num_rows($driver_result) == 0): ?>
                                                                <small class="form-text text-danger"><i class="fa fa-exclamation-triangle"></i> No available drivers. Please add drivers first.</small>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label for="message"><strong>Message to Customer:</strong></label>
                                                                <textarea class="form-control" name="message" rows="3" placeholder="Optional message to customer..."></textarea>
                                                            </div>
                                                            
                                                            <button type="submit" class="btn btn-primary">Update Status</button>
                                                            <a href="all_orders.php" class="btn btn-secondary">Back to Orders</a>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="footer">© 2025 - Online Seafood Ordering System</footer>
        </div>
    </div>
    
    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/lib/datatables/datatables.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="js/lib/datatables/cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="js/lib/datatables/cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="js/lib/datatables/cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="js/lib/datatables/datatables-init.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const driverSelect = document.getElementById('driver_id');
        const driverField = document.getElementById('driverField');
        const form = document.getElementById('statusForm');
        
        function toggleDriverRequirement() {
            const status = statusSelect.value;
            
            if (status === 'on the way') {
                driverSelect.setAttribute('required', 'required');
                driverField.style.display = 'block';
                // Highlight required field
                driverSelect.classList.add('border', 'border-warning');
            } 
            else if (status === 'processing') {
                driverSelect.removeAttribute('required');
                driverField.style.display = 'block';
                driverSelect.classList.remove('border', 'border-warning', 'is-invalid');
            }
            else {
                driverSelect.removeAttribute('required');
                driverField.style.display = 'none';
                driverSelect.classList.remove('border', 'border-warning', 'is-invalid');
            }
        }
        
        // Show current driver info if assigned
        function showCurrentDriverInfo() {
            const currentDriverId = '<?php echo $order['driver_id'] ?? 0; ?>';
            if (currentDriverId && currentDriverId != '0') {
                const currentOption = driverSelect.querySelector(`option[value="${currentDriverId}"]`);
                if (currentOption) {
                    // Add a note about current assignment
                    const driverInfo = document.createElement('div');
                    driverInfo.className = 'alert alert-info mt-2';
                    driverInfo.innerHTML = `<i class="fa fa-info-circle"></i> Currently assigned to: <strong>${currentOption.textContent}</strong>`;
                    if (!driverField.querySelector('.alert')) {
                        driverField.appendChild(driverInfo);
                    }
                }
            }
        }
        
        // Initialize on page load
        toggleDriverRequirement();
        showCurrentDriverInfo();
        
        statusSelect.addEventListener('change', toggleDriverRequirement);
        
        form.addEventListener('submit', function(e) {
            const status = statusSelect.value;
            const driverId = driverSelect.value;
            
            // Reset validation styles
            driverSelect.classList.remove('is-invalid');
            
            if (status === 'on the way' && !driverId) {
                e.preventDefault();
                alert('Please assign a delivery driver for "On the Way" status.');
                driverSelect.focus();
                driverSelect.classList.add('is-invalid');
                return false;
            }
            
            const orderId = <?php echo $order['order_id']; ?>;
            let confirmMessage = `Are you sure you want to update Order #${orderId} to "${status}"?`;
            
            if (driverId && status === 'on the way') {
                const driverName = driverSelect.options[driverSelect.selectedIndex].text;
                confirmMessage += `\n\nDriver: ${driverName}`;
            }
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
            
            return true;
        });
    });
    </script>
</body>
</html>