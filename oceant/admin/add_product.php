<!DOCTYPE html>
<html lang="en">
    <?php
    include("../connection/connect.php");
    error_reporting(E_ALL); // Enable error reporting
    ini_set('display_errors', 1); // Show errors
    session_start();

    // Initialize variables
    $error = '';
    $success = '';

    if (isset($_POST['submit'])) {
        // Check if all required fields are filled
        if (empty($_POST['c_name']) || empty($_POST['res_name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['o_hr']) || empty($_POST['c_hr']) || empty($_POST['o_days']) || empty($_POST['address'])) {
            $error = '<div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>All fields must be filled!</strong>
                    </div>';
        } else {
            // Handle file upload
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $fname = $_FILES['file']['name'];
                $temp = $_FILES['file']['tmp_name'];
                $fsize = $_FILES['file']['size'];
                $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                $fnew = uniqid() . '.' . $extension;
                $store = "Res_img/" . basename($fnew);

                // Check file extension
                if (in_array($extension, ['jpg', 'png', 'gif', 'jpeg'])) {
                    // Check file size (1MB = 1048576 bytes)
                    if ($fsize <= 1048576) {
                        $res_name = mysqli_real_escape_string($db, $_POST['res_name']);
                        $email = mysqli_real_escape_string($db, $_POST['email']);
                        $phone = mysqli_real_escape_string($db, $_POST['phone']);
                        $url = mysqli_real_escape_string($db, $_POST['url']);
                        $o_hr = mysqli_real_escape_string($db, $_POST['o_hr']);
                        $c_hr = mysqli_real_escape_string($db, $_POST['c_hr']);
                        $o_days = mysqli_real_escape_string($db, $_POST['o_days']);
                        $address = mysqli_real_escape_string($db, $_POST['address']);
                        $c_name = mysqli_real_escape_string($db, $_POST['c_name']);

                      $sql = "INSERT INTO categories(c_id, title, email, phone, url, o_hr, c_hr, o_days, address, image) 
        VALUES('$c_name', '$res_name', '$email', '$phone', '$url', '$o_hr', '$c_hr', '$o_days', '$address', '$fnew')";
                        
                        if (mysqli_query($db, $sql)) {
                            move_uploaded_file($temp, $store);
                            $success = '<div class="alert alert-success alert-dismissible fade show">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            New categories added successfully.
                                        </div>';
                        } else {
                            $error = '<div class="alert alert-danger alert-dismissible fade show">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <strong>Database error: ' . mysqli_error($db) . '</strong>
                                    </div>';
                        }
                    } else {
                        $error = '<div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>Max image size is 1MB!</strong> Try a different image.
                                </div>';
                    }
                } else {
                    $error = '<div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>Invalid extension!</strong> Only JPG, PNG, and GIF are accepted.
                            </div>';
                }
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Please select an image file</strong>
                        </div>';
            }
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
    <title>Add Branch</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="fix-header">
  
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
			<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /></svg>
    </div>
   
    <div id="main-wrapper">
   
       <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                        <h3>OCEAN TABLE</h3>
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="images/bookingSystem/user-icn.png" alt="user" class="profile-pic" /></a>
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
                        <li> <a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li> <a href="all_users.php">  <span><i class="fa fa-user f-s-20 "></i></span><span>Users</span></a></li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Types of Seafoods</span></a>
                            <ul aria-expanded="false" class="collapse">
								<li><a href="all_categories.php">All Types of Seafoods</a></li>
								<li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_product.php">Add Types of Seafoods</a></li>
                            </ul>
                        </li>
                       <li> <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Products</span></a>
                            <ul aria-expanded="false" class="collapse">
								<li><a href="all_available.php">All Availble Seafoods</a></li>
								<li><a href="add_seafoods.php">Add Seafoods</a></li>
                            </ul>
                        </li>
						 <li> <a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                          <li> <a href="all_drivers.php"><i class="fa fa-motorcycle" aria-hidden="true"></i><span>Delivery Drivers</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
      
        <div class="page-wrapper">
            <div class="container-fluid">
                <?php  
                    echo $error;
                    echo $success; 
                ?>
                
                <div class="col-lg-12">
                    <div class="card card-outline-primary">
                        <div class="card-header">
                            <h4 class="m-b-0 text-white">Add Types of Seafoods</h4>
                        </div>
                        <div class="card-body">
                            <form action='' method='post' enctype="multipart/form-data">
                                <div class="form-body">
                                    <hr>
                                    <div class="row p-t-20">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Product Name</label>
                                                <input type="text" name="res_name" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Business E-mail</label>
                                                <input type="email" name="email" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-t-20">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Phone</label>
                                                <input type="text" name="phone" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Website URL (Optional)</label>
                                                <input type="text" name="url" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-t-20">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Open Hours</label>
                                                <select name="o_hr" class="form-control custom-select" required>
                                                    <option value="">--Select your Hours--</option>
                                                    <option value="6am">6am</option>
                                                    <option value="7am">7am</option> 
													<option value="8am">8am</option>
													<option value="9am">9am</option>
													<option value="10am">10am</option>
													<option value="11am">11am</option>
                                                    <option value="12pm">12pm</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Close Hours</label>
                                                <select name="c_hr" class="form-control custom-select" required>
                                                    <option value="">--Select your Hours--</option>
													<option value="5pm">5pm</option>
													<option value="6pm">6pm</option>
													<option value="7pm">7pm</option>
													<option value="8pm">8pm</option>
                                                    <option value="9pm">9pm</option>
                                                    <option value="10pm">10pm</option>
                                                    <option value="11pm">11pm</option>
                                                    <option value="12am">12am</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Open Days</label>
                                                <select name="o_days" class="form-control custom-select" required>
                                                    <option value="">--Select your Days--</option>
                                                    <option value="Mon-Tue">Mon-Tue</option>
                                                    <option value="Mon-Wed">Mon-Wed</option> 
													<option value="Mon-Thu">Mon-Thu</option>
													<option value="Mon-Fri">Mon-Fri</option>
													<option value="Mon-Sat">Mon-Sat</option>
													<option value="24hr-x7">24hr-x7</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Image</label>
                                                <input type="file" name="file" class="form-control" accept="image/jpeg,image/png,image/gif" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Select Category</label>
												<select name="c_name" class="form-control custom-select" required>
                                                    <option value="">--Select Category--</option>
                                                 <?php 
                                                 $ssql = "SELECT * FROM res_category";
                                                 $res = mysqli_query($db, $ssql); 
                                                 if(mysqli_num_rows($res) > 0) {
                                                     while($row = mysqli_fetch_array($res)) {
                                                         echo '<option value="'.$row['c_id'].'">'.$row['c_name'].'</option>';
                                                     }
                                                 } else {
                                                     echo '<option value="">No categories found</option>';
                                                 }
                                                 ?> 
												</select>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="box-title m-t-40">Address</h3>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <textarea name="address" class="form-control" style="height:100px;" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <input type="submit" name="submit" class="btn btn-primary" value="Save"> 
                                    <a href="add_product.php" class="btn btn-inverse">Cancel</a>
                                </div>
                            </form>
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
</body>
</html>