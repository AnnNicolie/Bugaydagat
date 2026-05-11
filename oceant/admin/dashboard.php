<?php
// Start session and error reporting at the VERY TOP
error_reporting(0);
session_start();

include("../connection/connect.php");

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Count new feedback for notification badge - MOVED OUTSIDE THE IF BLOCK
$new_feedback_count = mysqli_query($db, "SELECT COUNT(*) as count FROM feedback WHERE status = 'new'");
$new_count = mysqli_fetch_assoc($new_feedback_count)['count'];

// Get monthly sales data for each product
function getMonthlySales($db, $productName) {
    $monthlyData = array_fill(0, 12, 0); // Initialize all months to 0
    
    $sql = "SELECT 
                MONTH(o.order_date) as month, 
                SUM(oi.quantity) as total 
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.order_id
            WHERE oi.product_name = ? AND o.status = 'delivered'
            GROUP BY MONTH(o.order_date)";
    
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $productName);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $monthIndex = $row['month'] - 1; // Convert to 0-based index (Jan = 0)
        $monthlyData[$monthIndex] = $row['total'];
    }
    
    return $monthlyData;
}

// Get monthly order totals
function getMonthlyOrderTotals($db) {
    $monthlyData = array_fill(0, 12, 0);
    $sql = "SELECT MONTH(order_date) as month, COUNT(*) as total 
            FROM orders 
            WHERE status = 'delivered'
            GROUP BY MONTH(order_date)";
    $result = mysqli_query($db, $sql);
    
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $monthIndex = $row['month'] - 1;
            $monthlyData[$monthIndex] = $row['total'];
        }
    }
    return $monthlyData;
}

// Get monthly revenue
function getMonthlyRevenue($db) {
    $monthlyData = array_fill(0, 12, 0);
    $sql = "SELECT MONTH(order_date) as month, SUM(grand_total) as total 
            FROM orders 
            WHERE status = 'delivered'
            GROUP BY MONTH(order_date)";
    $result = mysqli_query($db, $sql);
    
    if($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $monthIndex = $row['month'] - 1;
            $monthlyData[$monthIndex] = $row['total'];
        }
    }
    return $monthlyData;
}

// Get all seafood products for the chart
$products = array();
$sql = "SELECT title FROM seafoods";  
$result = mysqli_query($db, $sql);
if($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row['title'];
    }
}

// Prepare product datasets for the chart
$productDatasets = [];
$colorPalette = [
    '#4dc9f6', '#f67019', '#f53794', '#537bc4', '#acc236',
    '#166a8f', '#00a950', '#58595b', '#8549ba'
];

foreach ($products as $index => $product) {
    $monthlyData = getMonthlySales($db, $product);
    
    $productDatasets[] = [
        'label' => $product,
        'data' => $monthlyData,
        'backgroundColor' => $colorPalette[$index % count($colorPalette)],
        'borderColor' => $colorPalette[$index % count($colorPalette)],
        'borderWidth' => 1
    ];
}

// Get top selling products
$topProducts = array();
$topQuantities = array();
$sql = "SELECT oi.product_name as title, SUM(oi.quantity) as total 
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.status = 'delivered'
        GROUP BY oi.product_name 
        ORDER BY total DESC 
        LIMIT 10";
$result = mysqli_query($db, $sql);
if($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $topProducts[] = $row['title'];
        $topQuantities[] = $row['total'];
    }
}

// Get monthly order totals for chart
$monthlyOrderTotals = getMonthlyOrderTotals($db);
$monthlyRevenue = getMonthlyRevenue($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin Panel</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .chart-container {
            position: relative;
            height: 40vh;
            min-height: 300px;
            width: 100%;
        }
        .weather-widget {
            background: linear-gradient(135deg, #72EDF2 10%, #5151E5 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card {
            background-color: #f1f3f5;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .compact-row {
            margin-left: -8px;
            margin-right: -8px;
        }
        .compact-row > [class^="col-"] {
            padding-left: 8px;
            padding-right: 8px;
        }
        .dashboard-card h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .feedback-badge {
    background: #ff4444;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
    margin-left: 5px;
}
    </style>
</head>

<body class="fix-header">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    
    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">BUGAY DAGAT</a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>              
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
                </div>
            </nav>
        </div>
      
        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li> <a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li> <a href="all_users.php"><span><i class="fa fa-user f-s-20"></i></span><span>Users</span></a></li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Types of Seafoods</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_categories.php">All Types of Seafoods</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_product.php">Add Types of Seafoods</a></li>
                            </ul>
                        </li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Products</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_available.php">All Available Seafoods</a></li>
                                <li><a href="add_seafoods.php">Add Seafoods</a></li>
                            </ul>
                        </li>
                        <li> <a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                        <!-- Add Delivery Drivers Menu Item -->
                      <!-- Delivery Drivers Section -->
 <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-motorcycle" aria-hidden="true"></i><span class="hide-menu">Delivery Drivers</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_drivers.php">All Drivers</a></li>
                            </ul>
                        </li>
                       <li><a href="all_feedback.php">Customer Feedback 
    <?php if($new_count > 0): ?>
        <span class="feedback-badge"><?php echo $new_count; ?></span>
    <?php endif; ?>
</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    
        <div class="page-wrapper">
            <div class="container-fluid">
                <div class="col-lg-12">
                    <div class="card card-outline-primary">
                        <div class="card-header">
                            <h4 class="m-b-0 text-white">Admin Dashboard</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle">
                                            <span><i class="fa fa-home f-s-40"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql="SELECT * FROM categories";
                                                $result=mysqli_query($db,$sql); 
                                                $rws=mysqli_num_rows($result);
                                                echo $rws;
                                            ?></h2>
                                            <p class="m-b-0">Types of Seafoods</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle">
                                            <span><i class="fa fa-cutlery f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql="SELECT * FROM seafoods";
                                                $result=mysqli_query($db,$sql); 
                                                $rws=mysqli_num_rows($result);
                                                echo $rws;
                                            ?></h2>
                                            <p class="m-b-0">Products</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle">
                                            <span><i class="fa fa-users f-s-40"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql="SELECT * FROM users";
                                                $result=mysqli_query($db,$sql); 
                                                $rws=mysqli_num_rows($result);
                                                echo $rws;
                                            ?></h2>
                                            <p class="m-b-0">Users</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle"> 
                                            <span><i class="fa fa-shopping-cart f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql = "SELECT COUNT(*) as total FROM orders";
                                                $result = mysqli_query($db, $sql);
                                                if($result) {
                                                    $row = mysqli_fetch_assoc($result);
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                            ?></h2>
                                            <p class="m-b-0">Total Orders</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Removed duplicate categories card -->
                            
                            <div class="col-md-4">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle"> 
                                            <span><i class="fa fa-spinner f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql = "SELECT COUNT(*) as total FROM orders 
                                                        WHERE status IN ('processing', 'on the way')";
                                                $result = mysqli_query($db, $sql);
                                                if($result) {
                                                    $row = mysqli_fetch_assoc($result);
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                            ?></h2>
                                            <p class="m-b-0">Processing Orders</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle"> 
                                            <span><i class="fa fa-check f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql = "SELECT COUNT(*) as total FROM orders 
                                                        WHERE status = 'delivered'";
                                                $result = mysqli_query($db, $sql);
                                                if($result) {
                                                    $row = mysqli_fetch_assoc($result);
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                            ?></h2>
                                            <p class="m-b-0">Delivered Orders</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card p-30">
                                    <div class="media">
                                        <div class="media-left meida media-middle"> 
                                            <span><i class="fa fa-times f-s-40" aria-hidden="true"></i></span>
                                        </div>
                                        <div class="media-body media-text-right">
                                            <h2><?php 
                                                $sql = "SELECT COUNT(*) as total FROM orders 
                                                        WHERE status = 'cancelled'";
                                                $result = mysqli_query($db, $sql);
                                                if($result) {
                                                    $row = mysqli_fetch_assoc($result);
                                                    echo $row['total'];
                                                } else {
                                                    echo "0";
                                                }
                                            ?></h2>
                                            <p class="m-b-0">Cancelled Orders</p>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <!-- Charts Section -->
                        <div class="container py-4">
                            <h1 class="text-center mb-4">Analysis Dashboard</h1>
                            
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="dashboard-card">
                                        <h3>Product Performance by Month</h3>
                                        <div class="chart-container">
                                            <canvas id="productsChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="dashboard-card">
                                        <h3>Top Selling Products</h3>
                                        <div class="chart-container">
                                            <canvas id="topProductsChart"></canvas>
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

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
       

        // 3. Product Performance Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: <?php echo json_encode($productDatasets); ?>
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity Sold'
                        }
                    }
                }
            }
        });

        // 4. Top Products Chart
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($topProducts); ?>,
                datasets: [{
                    label: 'Total Sold',
                    data: <?php echo json_encode($topQuantities); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
    </script>
</body>
</html>