<?php
include("../connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if profile_image column exists, if not create it
$check_column = mysqli_query($db, "SHOW COLUMNS FROM users LIKE 'profile_image'");
if(mysqli_num_rows($check_column) == 0) {
    $alter_sql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'images/user-icn.webp'";
    if(mysqli_query($db, $alter_sql)) {
        echo '<div class="alert alert-info">profile_image column was added to users table</div>';
    }
}

// Force fresh load when coming from an update
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if(isset($_SESSION['order_updated'])) {
    $updated_order = (int)$_SESSION['order_updated'];
    unset($_SESSION['order_updated']);
    echo '<div class="alert alert-success alert-dismissible fade show">
            Order #'.$updated_order.' status updated successfully
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
}

function getProfileImage($image_path) {
    $default_image = '../images/user-icn.webp';
    
    // If no image path is provided or it's empty, return default
    if (empty($image_path)) {
        return $default_image;
    }
    
    // If the image path is already the default, return it
    if ($image_path === 'images/user-icn.webp') {
        return $default_image;
    }
    
    // Check different possible paths
    $possible_paths = [
        $image_path, // Original path from database
        '../' . $image_path, // With ../ prefix for admin
        'uploads/profile_images/' . basename($image_path), // Just filename in uploads
        '../uploads/profile_images/' . basename($image_path) // With ../ prefix
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // If no image found, return default
    return $default_image;
}

// Function to format status badge
function getStatusBadge($status) {
    switch(strtolower($status)) {
        case "pending": 
            return '<span class="badge badge-info"><i class="fa fa-clock"></i> Pending</span>';
        case "processing": 
            return '<span class="badge badge-primary"><i class="fa fa-cog"></i> Processing</span>';
        case "on the way": 
        case "in process":
            return '<span class="badge badge-warning"><i class="fa fa-truck"></i> On the Way</span>';
        case "delivered": 
        case "closed":
            return '<span class="badge badge-success"><i class="fa fa-check-circle"></i> Delivered</span>';
        case "cancelled": 
        case "rejected":
            return '<span class="badge badge-danger"><i class="fa fa-times-circle"></i> Cancelled</span>';
        default: 
            return '<span class="badge badge-secondary">'.$status.'</span>';
    }
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>All Orders</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .order-panel {
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .order-panel .panel-heading {
            padding: 15px;
            background: #404040;
            color: white;
        }
        .order-items {
            margin-bottom: 15px;
        }
        .order-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .order-message {
            margin-top: 15px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .badge {
            font-size: 12px;
            padding: 5px 10px;
        }
        .badge-info { background-color: #17a2b8; }
        .badge-warning { background-color: #ffc107; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }
        .customer-profile {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .profile-img-medium {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            margin-right: 10px;
        }
        .customer-info {
            flex: 1;
        }
    </style>
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
                        <h3>BUGAY DAGAT</h3>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
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
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Types of Seafoods</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_categories.php">All Types of Seafoods</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_product.php">Add Types of Seafoods</a></li>
                            </ul>
                        </li>
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Products</span></a>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header" style="background-color: blue;">
                                <h4 class="m-b-0 text-white">All Orders</h4>
                            </div>
                            <div class="card-body">
                                <?php
                                // Modified SQL query to include profile_image
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
                                    u.profile_image,
                                    GROUP_CONCAT(CONCAT(oi.product_name, ' (', oi.quantity, 'kg × ₱', oi.price, ')') SEPARATOR '<br>') AS items,
                                    SUM(oi.quantity) AS total_quantity,
                                    SUM(oi.subtotal) AS items_subtotal
                                FROM orders o
                                JOIN users u ON o.u_id = u.u_id
                                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                                GROUP BY o.order_id
                                ORDER BY o.order_date DESC";
                                
                                $query = mysqli_query($db, $sql);
                                
                                if(!$query) {
                                    echo '<div class="alert alert-danger">Error: '.mysqli_error($db).'</div>';
                                } elseif(mysqli_num_rows($query) === 0) {
                                    echo '<div class="alert alert-info">No orders found</div>';
                                } else {
                                    while($row = mysqli_fetch_assoc($query)) {
                                        $profile_img = getProfileImage($row['profile_image']);
                                        echo '<div class="panel panel-default order-panel">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Order #'.$row['order_id'].'</strong>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        '.date('M d, Y h:i A', strtotime($row['order_date'])).'
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="customer-profile">
                                                            <img src="' . $profile_img . '" alt="Customer Profile" class="profile-img-medium" 
                                                                 onerror="this.src=\'../images/user-icn.webp\'">
                                                            <div class="customer-info">
                                                                <h5><strong>' . htmlspecialchars($row['fullname']) . '</strong></h5>
                                                                <p class="mb-1"><strong>Contact:</strong> ' . htmlspecialchars($row['phone']) . '</p>
                                                            </div>
                                                        </div>
                                                        <p><strong>Address:</strong> ' . htmlspecialchars($row['address'] . ', ' . $row['barangay'] . ', ' . $row['delivery_location']) . '</p>
                                                        
                                                        <h5><strong>Items Ordered:</strong></h5>
                                                        <div class="order-items">' . $row['items'] . '</div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="order-summary">
                                                            <div class="row">
                                                                <div class="col-xs-6">
                                                                    <strong>Subtotal:</strong>
                                                                </div>
                                                                <div class="col-xs-6 text-right">
                                                                    ₱' . number_format($row['items_subtotal'], 2) . '
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-xs-6">
                                                                    <strong>Shipping Fee:</strong>
                                                                </div>
                                                                <div class="col-xs-6 text-right">
                                                                    ₱' . number_format($row['shipping_fee'], 2) . '
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-xs-6">
                                                                    <strong>Total Amount:</strong>
                                                                </div>
                                                                <div class="col-xs-6 text-right">
                                                                    <strong>₱' . number_format($row['grand_total'], 2) . '</strong>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row">
                                                                <div class="col-xs-6">
                                                                    <strong>Payment Method:</strong>
                                                                </div>
                                                                <div class="col-xs-6 text-right">
                                                                    ' . strtoupper($row['payment_method']) . '
                                                                </div>
                                                            </div>';
                                                            
                                                            if(!empty($row['payment_reference'])) {
                                                                echo '<div class="row">
                                                                    <div class="col-xs-6">
                                                                        <strong>Payment Reference:</strong>
                                                                    </div>
                                                                    <div class="col-xs-6 text-right">
                                                                        ' . htmlspecialchars($row['payment_reference']) . '
                                                                    </div>
                                                                </div>';
                                                            }
                                                            
                                                            echo '<div class="row">
                                                                <div class="col-xs-12">
                                                                    <strong>Status:</strong> ' . getStatusBadge($row['status']) . '
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                          <div class="panel-footer">
    <div class="text-right">
        <a href="view_order.php?order_id=' . $row['order_id'] . '" class="btn btn-info btn-sm">
            <i class="fa fa-eye"></i> View Details
        </a>
        <a href="mailto:' . htmlspecialchars($row['email'] ?? '') . '" class="btn btn-warning btn-sm">
            <i class="fa fa-envelope"></i> Email
        </a>
        <a href="receipt.php?order_id=' . $row['order_id'] . '" class="btn btn-secondary btn-sm">
            <i class="fa fa-print"></i> Print Receipt
        </a>
        <a href="delete_orders.php?order_del=' . $row['order_id'] . '" 
           class="btn btn-danger btn-sm" 
           onclick="return confirm(\'Are you sure you want to delete this order?\');">
            <i class="fa fa-trash"></i> Delete
        </a>
    </div>
</div>
                                        </div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="footer"></footer>
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
</body>
</html>