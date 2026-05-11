<?php
// Start session and error reporting at the VERY TOP
error_reporting(0);
session_start();
include("../connection/connect.php");

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Handle feedback status updates
if(isset($_POST['update_status'])) {
    $feedback_id = $_POST['feedback_id'];
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE feedback SET status = '$new_status' WHERE id = '$feedback_id'";
    mysqli_query($db, $update_sql);
}

// Get all feedback
$feedback_query = "SELECT f.*, u.username as user_name 
                   FROM feedback f 
                   JOIN users u ON f.user_id = u.u_id 
                   ORDER BY f.created_at DESC";
$feedback_result = mysqli_query($db, $feedback_query);

// Count new feedback
$new_feedback_count = mysqli_query($db, "SELECT COUNT(*) as count FROM feedback WHERE status = 'new'");
$new_count = mysqli_fetch_assoc($new_feedback_count)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Admin Panel - Customer Feedback</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .feedback-badge {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }
        .status-new { background-color: #e3f2fd; border-left: 4px solid #2196f3; }
        .status-read { background-color: #f3e5f5; border-left: 4px solid #9c27b0; }
        .status-addressed { background-color: #e8f5e8; border-left: 4px solid #4caf50; }
        .rating-stars { color: #ffa500; }
        .feedback-card { 
            margin-bottom: 15px; 
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                            <h4 class="m-b-0 text-white">Customer Feedback
                                <?php if($new_count > 0): ?>
                                    <span class="badge badge-danger"><?php echo $new_count; ?> New</span>
                                <?php endif; ?>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if(mysqli_num_rows($feedback_result) > 0): ?>
                                <?php while($feedback = mysqli_fetch_assoc($feedback_result)): ?>
                                    <div class="card feedback-card <?php echo 'status-' . $feedback['status']; ?>">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-8">
    <h5 class="card-title">
        <strong><?php echo htmlspecialchars($feedback['username']); ?></strong>
        <small class="text-muted">(#<?php echo $feedback['user_id']; ?>)</small>
    </h5>
    <h6 class="card-subtitle mb-2 text-muted">
        <span class="badge badge-info"><?php echo ucfirst($feedback['feedback_type']); ?></span>
        <span class="rating-stars">
            <?php 
            for($i = 1; $i <= 5; $i++): 
                if($i <= $feedback['rating']): 
                    echo '★';
                else:
                    echo '☆';
                endif;
            endfor; 
            ?>
            (<?php echo $feedback['rating']; ?>/5)
        </span>
    </h6>
    <p class="card-text"><?php echo nl2br(htmlspecialchars($feedback['message'])); ?></p>
    <small class="text-muted">
        Submitted: <?php echo date('M d, Y h:i A', strtotime($feedback['created_at'])); ?>
    </small>
</div>
                                                <div class="col-md-4 text-right">
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                                                        <div class="form-group">
                                                            <label><strong>Status:</strong></label>
                                                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                                                <option value="new" <?php echo $feedback['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                                                <option value="read" <?php echo $feedback['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                                                                <option value="addressed" <?php echo $feedback['status'] == 'addressed' ? 'selected' : ''; ?>>Addressed</option>
                                                            </select>
                                                        </div>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <h4 class="alert-heading">No Feedback Yet</h4>
                                    <p>No customer feedback has been submitted yet.</p>
                                </div>
                            <?php endif; ?>
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
</body>
</html>