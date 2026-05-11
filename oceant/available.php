<?php
include("connection/connect.php"); 
error_reporting(0);
session_start();

// Handle cart actions
if(isset($_GET['action']) && $_GET['action'] == "add") {
    $product_id = $_GET['id'];
    $quantity = $_POST['quantity'];
    
    // Get product details
    $product_query = mysqli_query($db, "SELECT * FROM seafoods WHERE d_id='$product_id'");
    $product = mysqli_fetch_array($product_query);
    
    if($product) {
        $itemArray = array(
            $product['d_id'] => array(
                'title' => $product['title'],
                'd_id' => $product['d_id'],
                'quantity' => $quantity,
                'price' => $product['price'],
                'image' => $product['img']
            )
        );
        
        if(!empty($_SESSION["cart_item"])) {
            if(array_key_exists($product['d_id'], $_SESSION["cart_item"])) {
                // Update quantity if item already exists
                $_SESSION["cart_item"][$product['d_id']]["quantity"] += $quantity;
            } else {
                // Add new item
                $_SESSION["cart_item"] = $_SESSION["cart_item"] + $itemArray;
            }
        } else {
            // First item
            $_SESSION["cart_item"] = $itemArray;
        }
        
        // Redirect to avoid form resubmission
        header("Location: available.php?res_id=".$_GET['res_id']."&added=1");
        exit();
    }
}

// Handle remove item from cart
if(isset($_GET['action']) && $_GET['action'] == "remove") {
    $remove_id = $_GET['id'];
    if(!empty($_SESSION["cart_item"])) {
        foreach($_SESSION["cart_item"] as $k => $v) {
            if($v["d_id"] == $remove_id) {
                unset($_SESSION["cart_item"][$k]);
                break;
            }
        }
        if(empty($_SESSION["cart_item"])) {
            unset($_SESSION["cart_item"]);
        }
    }
    header("Location: available.php?res_id=".$_GET['res_id']);
    exit();
}

// Shipping fee
$shipping_fee = 50.00; // Fixed shipping fee of 50 pesos
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Seafoods</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            padding-top: 80px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, #ffd700, #ffa500) !important;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            width: 120px;
            height: auto;
            border-radius: 10px;
        }

        .navbar-nav .nav-link {
            color: #333 !important;
            font-weight: bold;
            font-size: 16px;
            padding: 10px 15px;
            transition: all 0.3s ease-in-out;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        /* Hero Section */
        .inner-page-hero {
            background: url('images/img/restrrr.png') no-repeat center center/cover;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #ffffff;
            position: relative;
            padding: 40px 0;
        }

        .inner-page-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .profile {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        /* Category Image */
        .profile-img .image-wrap {
            background: white;
            padding: 8px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: inline-block;
            max-width: 100%;
        }

        .profile-img img {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        /* Category Description */
        .profile-desc h6 {
            font-size: 28px;
            font-weight: 800;
            color: #ffd700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .profile-desc p {
            font-size: 18px;
            color: #ffffff;
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        /* Product Card - IMPROVED */
        .food-item {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.15);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .food-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.2);
        }

        .food-item .rest-logo {
            background: white;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
        }

        .food-item .restaurant-logo img {
            width: 180px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .rest-descr h6 {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .rest-descr p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 22px;
            font-weight: 800;
            color: #ff8c00;
            margin-bottom: 15px;
        }

        /* CART DESIGN - COMPLETELY REDESIGNED */
        .widget-cart {
            background: #ffffff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.15);
            position: sticky;
            top: 100px;
            z-index: 100;
            border: 2px solid #ffd700;
        }

        .widget-heading {
            border-bottom: 2px solid #ffd700;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .widget-heading h3 {
            color: #333;
            font-weight: 700;
            font-size: 24px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .widget-heading h3 i {
            color: #ffa500;
        }

        .cart-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #ffa500;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background: #e9ecef;
        }

        .cart-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .cart-item-name {
            font-weight: 600;
            color: #333;
            flex: 1;
            font-size: 15px;
        }

        .cart-item-delete {
            color: #dc3545;
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 50%;
            transition: all 0.3s;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .cart-item-delete:hover {
            background: #dc3545;
            color: white;
            text-decoration: none;
        }

        .cart-item-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item-price {
            font-weight: 700;
            color: #ff8c00;
            font-size: 16px;
        }

        .cart-item-quantity {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px 10px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        /* Shipping Fee Section */
        .shipping-fee {
            background: #e7f3ff;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }

        .shipping-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .shipping-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .shipping-amount {
            font-weight: 700;
            color: #007bff;
            font-size: 16px;
        }

        .free-shipping-note {
            font-size: 12px;
            color: #28a745;
            font-weight: 600;
            margin-top: 5px;
            text-align: center;
        }

        .cart-total {
            background: linear-gradient(135deg, #ffd700, #ffa500);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.2);
        }

        .total-breakdown {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .total-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
            font-weight: 800;
            font-size: 18px;
        }

        .total-label {
            color: #333;
            font-weight: 600;
        }

        .total-amount {
            color: #333;
            font-weight: 700;
        }

        .grand-total {
            font-size: 28px;
            font-weight: 800;
            color: #333;
            margin: 15px 0;
        }

        .checkout-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }

        .checkout-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .checkout-btn.disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .checkout-btn.disabled:hover {
            background: #6c757d;
            transform: none;
            box-shadow: none;
        }

        .empty-cart-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-cart-message i {
            font-size: 64px;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .empty-cart-message p {
            font-size: 16px;
            margin: 0;
        }

        /* Buttons */
        .btn.theme-btn {
            background: linear-gradient(to right, #ffd700, #ffa500);
            color: #333;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            padding: 12px 25px;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.2);
        }

        .btn.theme-btn:hover {
            background: linear-gradient(to right, #ffa500, #ff8c00);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 165, 0, 0.3);
        }

        .quantity-input {
            width: 120px !important;
            text-align: center;
            border: 2px solid #ffd700;
            border-radius: 6px;
            padding: 10px;
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .quantity-input:focus {
            border-color: #ffa500;
            box-shadow: 0 0 0 0.2rem rgba(255, 165, 0, 0.25);
        }

        /* Success Alert */
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            color: #155724;
            padding: 12px 15px;
            margin-bottom: 20px;
        }

        /* Stock Status */
        .stock-status {
            font-size: 14px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .in-stock {
            background: #d4edda;
            color: #155724;
        }

        .out-of-stock {
            background: #f8d7da;
            color: #721c24;
        }

        /* FOOTER - FIXED ALIGNMENT */
        .footer {
            background: linear-gradient(135deg, #ffd700, #ffa500) !important;
            color: #333;
            padding: 30px 0 15px;
            margin-top: 50px;
            width: 100%;
            flex-shrink: 0;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .footer-section {
            flex: 1;
            min-width: 300px;
            margin-bottom: 20px;
        }

        .footer h5 {
            color: #5a3700;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 1.2rem;
            border-bottom: 2px solid rgba(90, 55, 0, 0.2);
            padding-bottom: 8px;
        }

        .footer p {
            color: #5a3700;
            line-height: 1.6;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .address-container {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .guimbal-logo {
            width: 70px;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .address-text {
            flex: 1;
        }

        .additional-info {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 2px solid rgba(90, 55, 0, 0.3);
            color: #5a3700;
            font-size: 0.9rem;
            font-weight: 500;
            width: 100%;
        }

        /* Remove dropdown arrow */
        .nav-item.dropdown .dropdown-toggle::after {
            display: none !important;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            transition: transform 0.3s;
            object-fit: cover;
            border: 2px solid #ffd700;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .food-item {
                padding: 20px;
                text-align: center;
            }

            .rest-logo {
                margin-bottom: 15px;
            }

            .widget-cart {
                margin-top: 20px;
                margin-bottom: 30px;
                position: relative;
                top: 0;
            }

            .food-item .restaurant-logo img {
                width: 150px;
                height: 120px;
            }

            .footer-section {
                min-width: 100%;
                text-align: center;
            }

            .address-container {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .inner-page-hero {
                min-height: 160px;
                padding: 20px 0;
            }

            .profile-img img {
                max-width: 150px;
            }

            .profile-desc h6 {
                font-size: 20px;
            }

            .profile-desc p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    
<header id="header" class="header-scroll top-header headrom" style="position: fixed; top: 0; width: 100%; z-index: 1000;">
    <nav class="navbar navbar-dark">
        <div class="container">
            <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
            <a class="navbar-brand" href="index.php">
                <img class="img-rounded" src="images/logo6.png" alt="" style="width: 100px; height: 70px;">
            </a>

            <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item"> <a class="nav-link active" href="index.php">Home</a> </li>
                    <li class="nav-item"> <a class="nav-link active" href="seafoods.php">Seafoods</a> </li>
                            
                    <?php
                    if(empty($_SESSION["user_id"])) {
                        echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                              <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                    } else {	
                        echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                    }
                    ?>
                    
                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <?php
                        $profile_image = 'images/user-icn.webp';
                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $user_query = mysqli_query($db, "SELECT profile_image FROM users WHERE u_id='$user_id'");
                            if ($user_data = mysqli_fetch_assoc($user_query)) {
                                $profile_image = $user_data['profile_image'];
                            }
                        }
                        ?>
                        <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?php echo $profile_image; ?>" alt="user" class="profile-pic">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                            <ul class="dropdown-user">
                                <li><a href="view_profile.php"><i class="fa fa-user"></i> View Profile</a></li>
                                <li><a href="edit_profile.php"><i class="fa fa-edit"></i> Edit Profile</a></li>
                                <li>
                                    <a href="logout.php" onclick="return confirmLogout()"><i class="fa fa-power-off"></i> Logout</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>                    
</header>

<script>
// Adjust content margin based on header height
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('header');
    if (header) {
        const headerHeight = header.offsetHeight;
        document.body.style.paddingTop = headerHeight + 'px';
    }
});

function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}
</script>

<div class="main-content">
<?php 
// Get category details
$ress = mysqli_query($db,"SELECT * FROM categories WHERE rs_id='$_GET[res_id]'");
$rows = mysqli_fetch_array($ress);
?>
    
<section class="inner-page-hero bg-image">
    <div class="profile">
        <div class="container">
            <div class="row align-items-center">
                <!-- Category Image -->
                <div class="col-12 col-md-4 col-lg-4 profile-img text-center text-md-start mb-3 mb-md-0">
                    <div class="image-wrap mx-auto mx-md-0">
                        <figure>
                            <?php echo '<img src="admin/Res_img/'.$rows['image'].'" alt="Category logo" class="img-fluid">'; ?>
                        </figure>
                    </div>
                </div>
                
                <!-- Category Description -->
                <div class="col-12 col-md-8 col-lg-8 profile-desc text-center text-md-start">
                    <div class="white-txt">
                        <h6><?php echo $rows['title']; ?></h6>
                        <p><?php echo $rows['address']; ?></p>   
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container m-t-30">
    <?php if(isset($_GET['added']) && $_GET['added'] == 1): ?>
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> Item successfully added to cart!
    </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Cart Section - REDESIGNED -->
        <div class="col-12 col-md-4 col-lg-3 order-2 order-md-1">
            <div class="widget widget-cart">
                <div class="widget-heading">
                    <h3 class="widget-title text-dark">
                        <i class="fa fa-shopping-cart"></i> Your Cart
                    </h3>
                    <div class="clearfix"></div>
                </div>
                <div class="widget-body">
                    <?php
                    $item_total = 0;
                    if(isset($_SESSION["cart_item"]) && !empty($_SESSION["cart_item"])) {
                        foreach ($_SESSION["cart_item"] as $item) {
                            $item_subtotal = $item["price"] * $item["quantity"];
                            $item_total += $item_subtotal;
                    ?>									
                            <div class="cart-item">
                                <div class="cart-item-header">
                                    <div class="cart-item-name"><?php echo $item["title"]; ?></div>
                                    <a href="available.php?res_id=<?php echo $_GET['res_id']; ?>&action=remove&id=<?php echo $item["d_id"]; ?>" class="cart-item-delete" onclick="return confirm('Remove this item from cart?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                                <div class="cart-item-details">
                                    <div class="cart-item-price"><?php echo "₱".number_format($item_subtotal, 2); ?></div>
                                    <div class="cart-item-quantity"><?php echo $item["quantity"]; ?> kg</div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="empty-cart-message">
                                <i class="fa fa-shopping-cart"></i>
                                <p>Your cart is empty</p>
                                <small class="text-muted">Add some delicious seafood to get started!</small>
                              </div>';
                    }
                    ?>	
                </div>
                
                <?php if($item_total > 0): ?>
                <!-- Shipping Fee Section -->
                <div class="shipping-fee">
                    <div class="shipping-details">
                        <div class="shipping-label">
                            <i class="fa fa-truck"></i> Shipping Fee
                        </div>
                        <div class="shipping-amount">
                            ₱<?php echo number_format($shipping_fee, 2); ?>
                        </div>
                    </div>
                    <?php if($item_total >= 500): ?>
                    <div class="free-shipping-note">
                        <i class="fa fa-gift"></i> Free shipping on orders over ₱500!
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Total Breakdown -->
                <div class="cart-total">
                    <div class="total-breakdown">
                        <div class="total-row">
                            <div class="total-label">Subtotal:</div>
                            <div class="total-amount">₱<?php echo number_format($item_total, 2); ?></div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Shipping:</div>
                            <div class="total-amount">
                                <?php 
                                $final_shipping_fee = ($item_total >= 500) ? 0 : $shipping_fee;
                                echo "₱" . number_format($final_shipping_fee, 2);
                                if($item_total >= 500) echo ' <small style="color:#28a745;">(FREE)</small>';
                                ?>
                            </div>
                        </div>
                        <div class="total-row">
                            <div class="total-label">Total Amount:</div>
                            <div class="total-amount">₱<?php echo number_format($item_total + $final_shipping_fee, 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="grand-total">
                        ₱<?php echo number_format($item_total + $final_shipping_fee, 2); ?>
                    </div>
                    
                    <a href="checkout.php?res_id=<?php echo $_GET['res_id'];?>" class="checkout-btn">
                        <i class="fa fa-credit-card"></i> PROCEED TO CHECKOUT
                    </a>
                </div>
                <?php else: ?>
                <div class="cart-total">
                    <a href="#" class="checkout-btn disabled" onclick="return false;">
                        <i class="fa fa-shopping-cart"></i> CART IS EMPTY
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Products Section - FIXED FORM STRUCTURE -->
        <div class="col-12 col-md-8 col-lg-9 order-1 order-md-2">
            <div class="menu-widget" id="2">
                <div class="widget-heading">
                    <h3 class="widget-title text-dark">
                        <i class="fa fa-fish"></i> AVAILABLE SEAFOODS
                    </h3>
                    <div class="clearfix"></div>
                </div>
                <div class="collapse in" id="popular2">
                    <?php  
                    $stmt = $db->prepare("SELECT * FROM seafoods WHERE rs_id=?");
                    $stmt->bind_param("s", $_GET['res_id']);
                    $stmt->execute();
                    $products = $stmt->get_result();
                    
                    if ($products->num_rows > 0) {
                        while($product = $products->fetch_assoc()) {				
                    ?>
                            <div class="food-item">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-3 text-center mb-3 mb-lg-0">
                                        <div class="rest-logo">
                                            <div class="restaurant-logo">
                                                <?php echo '<img src="admin/Res_img/dishes/'.$product['img'].'" alt="'.$product['title'].'">'; ?>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                                        <div class="rest-descr">
                                            <h6><?php echo $product['title']; ?></h6>
                                            <p><?php echo $product['slogan']; ?></p>
                                            <div class="product-price">
                                                ₱<?php echo number_format($product['price'], 2); ?> 
                                                <small class="text-muted">per kilo</small>
                                            </div>
                                            <?php if($product['stock'] > 0): ?>
                                                <span class="stock-status in-stock">
                                                    <i class="fa fa-check"></i> In Stock (<?php echo $product['stock']; ?> kg available)
                                                </span>
                                            <?php else: ?>
                                                <span class="stock-status out-of-stock">
                                                    <i class="fa fa-times"></i> Out of Stock
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                           
                                    <div class="col-12 col-lg-4 text-center"> 
                                        <form method="post" action='available.php?res_id=<?php echo $_GET['res_id'];?>&action=add&id=<?php echo $product['d_id']; ?>' class="add-to-cart-form">
                                            <?php if($product['stock'] > 0): ?>
                                                <div class="form-group">
                                                    <label class="text-muted"><small>Quantity (kilos):</small></label>
                                                    <input type="number" name="quantity" min="0.1" max="<?php echo $product['stock']; ?>" 
                                                        class="form-control quantity-input mx-auto" value="0" step="0.1" required>
                                                </div>
                                                <button type="submit" class="btn theme-btn">
                                                    <i class="fa fa-cart-plus"></i> Add To Cart
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fa fa-times"></i> Out of Stock
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="alert alert-info text-center py-4">
                                <i class="fa fa-info-circle fa-2x mb-3"></i>
                                <h5>No products available</h5>
                                <p class="mb-0">No seafood items are currently available in this category.</p>
                              </div>';
                    }
                    ?>	
                </div>
            </div>
        </div>
    </div>
</div>
</div> <!-- End main-content -->

  <footer class="footer">
    <div class="container">
        <div class="bottom-footer">
            <div class="row align-items-center justify-content-between">
                <div class="col-12 col-sm-4 address">
                    <div class="d-flex align-items-start">
                        <!-- Guimbal Logo -->
                        <div class="logo-container me-3">
                            <img src="images/logo.jpg" alt="Guimbal Logo" class="guimbal-logo">
                        </div>
                        <div>
                            <h5>Information</h5>
                            <p>Guimbal, Iloilo, Philippines</p>
                            <p>Phone: 0987 654 3210</p>
                            <p>Email: info@guimbalseafoods.com</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-8 additional-info">
                    <h5>About Us</h5>
                    <p>Guimbal, Iloilo, is famous for its fresh seafood, sourced daily by local fishermen. Our thriving market offers high-quality fish, crabs, shrimp, and more at affordable prices. To make these products more accessible, we launched this online platform—bringing fresh seafood straight to your door, whether you're nearby or across town. No need to visit the market; enjoy Guimbal's best catch with just a few clicks!</p>
                </div>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2023 Guimbal Seafoods. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="js/jquery.min.js"></script>
<script src="js/tether.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/animsition.min.js"></script>
<script src="js/bootstrap-slider.min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/headroom.js"></script>
<script src="js/foodpicky.min.js"></script>

<script>
// Add some interactivity
$(document).ready(function() {
    // Form submission feedback
    $('.add-to-cart-form').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
        
        setTimeout(function() {
            btn.html(originalText).prop('disabled', false);
        }, 1500);
    });
    
    // Quantity input validation
    $('.quantity-input').on('change', function() {
        const max = parseFloat($(this).attr('max'));
        const min = parseFloat($(this).attr('min'));
        const value = parseFloat($(this).val());
        
        if (value < min) {
            $(this).val(min);
        } else if (value > max) {
            $(this).val(max);
            alert('Maximum available stock is ' + max + ' kilos');
        }
    });

    // Auto-hide success message after 3 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 3000);
});
</script>
</body>
</html>