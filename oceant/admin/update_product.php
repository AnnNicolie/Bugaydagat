<?php
// ABSOLUTELY NOTHING BEFORE THIS LINE - NO WHITESPACE, NO NOTHING
// Enable maximum error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!-- Debug: Script started -->";

// Include connection file
include("../connection/connect.php");
echo "<!-- Debug: Connection included -->";

// Start session
session_start();
echo "<!-- Debug: Session started -->";

// Check if we have a database connection
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "<!-- Debug: Database connected -->";

// Check if menu ID is provided
if (!isset($_GET['menu_upd']) || empty($_GET['menu_upd'])) {
    die("Error: No menu ID provided in URL");
}

$menu_id = intval($_GET['menu_upd']);
echo "<!-- Debug: Menu ID = $menu_id -->";

// Initialize variables
$error = '';
$success = '';
$roww = null;

// Get current product data
$sql = "SELECT * FROM seafoods WHERE d_id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $menu_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$roww = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$roww) {
    die("Error: Product with ID $menu_id not found in database");
}
echo "<!-- Debug: Product data loaded -->";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    echo "<!-- Debug: Form submitted -->";
    
    // Get form data
    $d_name = mysqli_real_escape_string($db, $_POST['d_name'] ?? '');
    $about = mysqli_real_escape_string($db, $_POST['about'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $res_name = intval($_POST['res_name'] ?? 0);
    $stock = floatval($_POST['stock'] ?? 0);
    
    // Basic validation
    if (empty($d_name) || empty($about) || $price <= 0 || $res_name <= 0 || $stock < 0) {
        $error = '<div class="alert alert-danger">All fields are required and must be valid!</div>';
    } else {
        // Handle file upload if provided
        $image_name = $roww['img']; // Keep current image by default
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['file'];
            $fname = $file['name'];
            $temp = $file['tmp_name'];
            $fsize = $file['size'];
            $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
            
            // Validate file type
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($extension, $allowed_extensions)) {
                // Validate file size (1MB max)
                if ($fsize <= 1000000) {
                    $image_name = uniqid() . '.' . $extension;
                    $store = "Res_img/dishes/" . $image_name;
                    
                    if (move_uploaded_file($temp, $store)) {
                        echo "<!-- Debug: File uploaded successfully -->";
                    } else {
                        $error = '<div class="alert alert-danger">Failed to upload image file.</div>';
                        $image_name = $roww['img']; // Revert to current image
                    }
                } else {
                    $error = '<div class="alert alert-danger">Image size must be less than 1MB.</div>';
                }
            } else {
                $error = '<div class="alert alert-danger">Only JPG, PNG, and GIF images are allowed.</div>';
            }
        }
        
        // If no errors, update the database
        if (empty($error)) {
            $update_sql = "UPDATE seafoods SET rs_id = ?, title = ?, slogan = ?, price = ?, stock = ?, img = ? WHERE d_id = ?";
            $update_stmt = mysqli_prepare($db, $update_sql);
            
            if ($update_stmt) {
                mysqli_stmt_bind_param($update_stmt, "issddsi", $res_name, $d_name, $about, $price, $stock, $image_name, $menu_id);
                
                if (mysqli_stmt_execute($update_stmt)) {
                    $success = '<div class="alert alert-success">Product updated successfully!</div>';
                    
                    // Refresh the product data
                    $refresh_sql = "SELECT * FROM seafoods WHERE d_id = ?";
                    $refresh_stmt = mysqli_prepare($db, $refresh_sql);
                    mysqli_stmt_bind_param($refresh_stmt, "i", $menu_id);
                    mysqli_stmt_execute($refresh_stmt);
                    $refresh_result = mysqli_stmt_get_result($refresh_stmt);
                    $roww = mysqli_fetch_assoc($refresh_result);
                    mysqli_stmt_close($refresh_stmt);
                } else {
                    $error = '<div class="alert alert-danger">Database update failed: ' . mysqli_error($db) . '</div>';
                }
                mysqli_stmt_close($update_stmt);
            } else {
                $error = '<div class="alert alert-danger">Failed to prepare update statement.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Update Product - Ocean Table</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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
                    <a class="navbar-brand" href="dashboard.php">
                        <span><img src="images/icn.png" alt="homepage" class="dark-logo" /></span>
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
                                <li><a href="all_available.php">All Available Seafoods</a></li>
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
                        <h2>Update Product</h2>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Product Name</label>
                                                <input type="text" name="d_name" class="form-control" 
                                                       value="<?php echo htmlspecialchars($roww['title'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <input type="text" name="about" class="form-control" 
                                                       value="<?php echo htmlspecialchars($roww['slogan'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Price (₱)</label>
                                                <input type="number" name="price" class="form-control" step="0.01" min="0"
                                                       value="<?php echo htmlspecialchars($roww['price'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Stock (kg)</label>
                                                <input type="number" name="stock" class="form-control" step="0.01" min="0"
                                                       value="<?php echo htmlspecialchars($roww['stock'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Current Image</label><br>
                                                <?php if (!empty($roww['img'])): ?>
                                                    <img src="Res_img/dishes/<?php echo $roww['img']; ?>" 
                                                         alt="Current Image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                                                    <p class="text-muted mt-2"><?php echo $roww['img']; ?></p>
                                                <?php else: ?>
                                                    <p class="text-muted">No image available</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Change Image (Optional)</label>
                                                <input type="file" name="file" class="form-control" accept="image/*">
                                                <small class="form-text text-muted">Max size: 1MB. Formats: JPG, PNG, GIF</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select name="res_name" class="form-control" required>
                                                    <option value="">-- Select Category --</option>
                                                    <?php
                                                    $cat_sql = "SELECT * FROM categories";
                                                    $cat_result = mysqli_query($db, $cat_sql);
                                                    if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                                                        while ($cat = mysqli_fetch_assoc($cat_result)) {
                                                            $selected = ($cat['rs_id'] == $roww['rs_id']) ? 'selected' : '';
                                                            echo '<option value="' . $cat['rs_id'] . '" ' . $selected . '>' . 
                                                                 htmlspecialchars($cat['title']) . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
                                        <a href="all_available.php" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            © <?php echo date('Y'); ?> - Ocean Table Seafood System
                        </div>
                    </div>
                </div>
            </footer>
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