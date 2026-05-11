<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");  
error_reporting(0);  
session_start(); 
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">   
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Home</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
   <style>
    /* ========== MOBILE-FIRST STYLES ========== */
    
    /* Logo Styling */
    .navbar-brand {
        display: flex;
        align-items: center;
        height: 50px; /* Reduced height for mobile */
        padding: 3px 0px;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
        height: 145%; /* Adjusted for mobile */
    }
    
    .logo-img {
        object-fit: contain;
        max-height:111%;
        width: auto;
        transition: all 0.3s ease;
        border-radius: 8px;
        max-width: 150px; /* Limit logo width */
    }
    
    /* General Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff;
        color: #333;
        overflow-x: hidden; /* Prevent horizontal scroll */
    }

    /* Navbar Styling - Mobile First */
    .navbar {
        background: linear-gradient(135deg, #ffd700, #ffa500) !important;
        padding: 10px 0; /* Reduced padding for mobile */
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* Navbar Links */
    .navbar-nav .nav-link {
        color: #333 !important;
        font-weight: bold;
        font-size: 14px; /* Smaller font for mobile */
        padding: 8px 12px; /* Reduced padding for mobile */
        transition: all 0.3s ease-in-out;
        text-align: center; /* Center align for mobile menu */
    }

    .navbar-nav .nav-link:hover {
        color: #fff !important;
        background-color: rgba(255,255,255,0.2);
        border-radius: 5px;
    }

    /* Active Link Styling */
    .navbar-nav .nav-item .active {
        background-color: rgba(255,255,255,0.3);
        border-radius: 5px;
    }

    /* Navbar Toggler (Mobile) */
    .navbar-toggler {
        border: none;
        background-color: #ffa500;
        color: #333;
        padding: 5px 8px; /* Smaller padding */
        font-size: 0.9rem; /* Smaller icon */
    }

    .navbar-toggler:hover {
        background-color: #ff8c00;
    }

    /* Hero Section - Mobile First */
    .hero {
        background: linear-gradient(rgba(255, 215, 0, 0.7), rgba(255, 165, 0, 0.7)), url('https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
        height: 350px; /* Reduced height for mobile */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #333;
        position: relative;
        padding: 0 15px; /* Add padding for mobile */
    }

    .hero-text h1 {
        font-size: 1.8rem; /* Smaller for mobile */
        font-weight: bold;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(255,255,255,0.7);
        line-height: 1.3;
    }

    .steps {
        display: flex;
        flex-direction: column; /* Stack vertically on mobile */
        align-items: center;
        margin-top: 20px;
    }

    .step-item {
        text-align: center;
        background: rgba(255,255,255,0.8);
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        width: 100%; /* Full width on mobile */
        margin-bottom: 15px;
    }

    .step-item svg {
        width: 40px; /* Smaller icons for mobile */
        height: 40px;
        fill: #ffa500;
        margin-bottom: 8px;
    }

    .step-item h4 {
        font-size: 1rem; /* Smaller font for mobile */
        font-weight: bold;
        color: #333;
    }

    /* Best Seller Section */
    .popular {
        padding: 40px 0; /* Reduced padding for mobile */
        background: #fff9e6;
    }

    .food-item-wrap {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 4px 15px rgba(255, 165, 0, 0.2);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 20px;
    }

    .food-item-wrap:hover {
        transform: translateY(-5px); /* Smaller lift on mobile */
        box-shadow: 0px 8px 15px rgba(255, 165, 0, 0.3);
    }

    .figure-wrap {
        height: 180px; /* Slightly smaller for mobile */
        background-size: cover;
        background-position: center;
    }

    .content {
        padding: 15px;
        text-align: center;
    }

    .content h5 a {
        color: #ff8c00;
        font-size: 1rem; /* Smaller for mobile */
        text-decoration: none;
        font-weight: bold;
    }

    .price-btn-block {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        flex-wrap: wrap; /* Allow wrapping on small screens */
    }

    .price {
        font-size: 1rem; /* Smaller for mobile */
        font-weight: bold;
        color: #ff8c00;
        margin-bottom: 5px; /* Add spacing when wrapped */
    }

    .theme-btn-dash {
        background: linear-gradient(to right, #ffd700, #ffa500);
        color: #333;
        padding: 6px 12px; /* Smaller button for mobile */
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: bold;
        border: none;
        font-size: 0.9rem; /* Smaller text */
    }

    .theme-btn-dash:hover {
        background: linear-gradient(to right, #ffa500, #ff8c00);
        color: #fff;
        transform: scale(1.05);
    }

    /* How It Works Section */
    .how-it-works {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        padding: 40px 0; /* Reduced padding for mobile */
        text-align: center;
        color: #333;
    }

    .how-it-works h2 {
        font-size: 1.8rem; /* Smaller for mobile */
        font-weight: bold;
        margin-bottom: 30px;
        color: #333;
    }

    /* Footer - Updated to match header gradient */
    .footer {
        background: linear-gradient(135deg, #ffd700, #ffa500) !important;
        color: #333;
        padding: 25px 0 12px;
        position: relative;
        width: 100%;
        margin-top: 40px; /* Reduced margin for mobile */
        box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
    }

    .bottom-footer {
        padding-top: 12px;
    }

    .footer h5 {
        color: #5a3700;
        font-weight: 600;
        margin-bottom: 10px;
        position: relative;
        padding-bottom: 5px;
        font-size: 1rem; /* Smaller for mobile */
        text-align: center; /* Center on mobile */
    }

    .footer h5::after {
        content: '';
        position: absolute;
        left: 50%; /* Center on mobile */
        transform: translateX(-50%); /* Center on mobile */
        bottom: 0;
        width: 35px;
        height: 2px;
        background: #ff6b00;
        border-radius: 1px;
    }

    .footer p {
        color: #5a3700;
        line-height: 1.5;
        margin-bottom: 8px;
        font-weight: 400;
        font-size: 0.85rem; /* Smaller for mobile */
        text-align: center; /* Center on mobile */
    }

    .address, .additional-info {
        text-align: center;
        padding: 0 10px;
        margin-bottom: 20px; /* Add space between sections on mobile */
    }

    .footer a {
        color: #d35400;
        text-decoration: none;
        transition: color 0.3s;
        font-weight: 500;
        font-size: 0.85rem; /* Smaller for mobile */
    }

    .footer a:hover {
        color: #a84300;
        text-decoration: underline;
    }

    .copyright {
        text-align: center;
        padding-top: 12px;
        margin-top: 12px;
        border-top: 1px solid rgba(90, 55, 0, 0.2);
        color: #5a3700;
        font-size: 0.8rem;
        font-weight: 400;
    }

    /* Make About Us section wider */
    .address {
        flex: 0 0 100%; /* Full width on mobile */
        max-width: 100%;
    }

    .additional-info {
        flex: 0 0 100%; /* Full width on mobile */
        max-width: 100%;
    }

    /* Card-like styling for content areas */
    .address, .additional-info {
        background: rgba(255, 255, 255, 0.4);
        border-radius: 6px;
        padding: 12px;
        margin: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(3px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Profile Dropdown Styling */
    .nav-item .dropdown-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
        justify-content: center; /* Center on mobile */
    }

    .profile-pic {
        width: 40px; /* Smaller for mobile */
        height: 40px;
        border-radius: 50%;
        transition: transform 0.3s;
    }

    .profile-pic:hover {
        transform: scale(1.1);
    }

    .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        padding: 10px;
        background-color: #fff;
        border: 1px solid #ffd700;
        width: 200px; /* Fixed width for consistency */
        left: auto !important; /* Override Bootstrap positioning */
        right: 0; /* Align to right */
    }

    .dropdown-user {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .dropdown-user li {
        padding: 8px 15px;
        transition: background-color 0.3s;
        border-radius: 5px;
    }

    .dropdown-user li:hover {
        background-color: #fff9e6;
    }

    .dropdown-user a {
        color: #ff8c00;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
    }

    .dropdown-user a i {
        font-size: 16px;
    }

    /* Remove arrows from step items */
    .steps .step-item::before,
    .steps .step-item::after {
        display: none !important;
        content: none !important;
    }

    /* If arrows are coming from the parent container */
    .steps::before,
    .steps::after {
        display: none !important;
        content: none !important;
    }

    /* In your CSS file */
    .popular .title h2 {
        background: linear-gradient(45deg, #ffd700, #d9af7cff);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        text-transform: uppercase;
        letter-spacing: 1px; /* Reduced for mobile */
        font-weight: 800;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        display: inline-block;
        padding: 0 15px; /* Reduced for mobile */
        position: relative;
        font-size: 1.5rem; /* Smaller for mobile */
    }

    /* Featured Restaurants */
    .featured-restaurants {
        padding: 40px 0; /* Reduced for mobile */
        background: #fff;
    }

    .restaurant-wrap {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(235, 194, 116, 0.2);
        overflow: hidden;
        transition: transform 0.3s;
        margin-bottom: 20px;
    }

    .restaurant-wrap:hover {
        transform: translateY(-5px);
    }

    .restaurant-logo img {
        width: 100%;
        height: 120px; /* Smaller for mobile */
        object-fit: cover;
    }

    /* Guimbal Culture Section */
    .guimbal-culture {
        background: linear-gradient(135deg, #375983ff, #375983ff);
        padding: 40px 0; /* Reduced for mobile */
        text-align: center;
        color: #fff;
    }

    .guimbal-culture h2 {
        font-size: 1.8rem; /* Smaller for mobile */
        font-weight: bold;
        margin-bottom: 30px;
        color: #fff;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }

    .guimbal-culture-item {
        margin-bottom: 25px;
        padding: 0 10px; /* Reduced padding for mobile */
        transition: transform 0.3s ease-in-out;
        height: 100%;
    }

    .guimbal-culture-item:hover {
        transform: translateY(-5px); /* Smaller lift on mobile */
    }

    .culture-item {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 15px;
        height: 100%;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.3s ease;
    }

    .culture-item:hover {
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .culture-image {
        width: 100%;
        height: 150px; /* Smaller for mobile */
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .culture-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .culture-item:hover .culture-image img {
        transform: scale(1.05);
    }

    .guimbal-culture h3 {
        font-size: 1.3rem; /* Smaller for mobile */
        margin: 10px 0;
        font-weight: bold;
        color: #ffd700;
    }

    .guimbal-culture p {
        font-size: 0.9rem; /* Smaller for mobile */
        color: #f0f0f0;
        line-height: 1.5;
    }

    /* Logo container styling */
    .logo-container {
        flex-shrink: 0;
    }

    /* Guimbal logo styling */
    .guimbal-logo {
        width: 50px; /* Smaller for mobile */
        height: auto;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Adjust address section for logo */
    .address .d-flex {
        align-items: center;
        flex-direction: column; /* Stack on mobile */
        text-align: center;
    }

    /* Remove dropdown arrow from profile */
    .nav-item.dropdown .dropdown-toggle::after {
        display: none !important;
    }

    /* Alternative method - if the above doesn't work */
    .navbar .nav-item.dropdown .nav-link.dropdown-toggle::after {
        content: none !important;
    }

    .navbar .dropdown-toggle::after {
        display: none !important;
    }

    /* Featured Restaurants Filter */
    .restaurants-filter {
        margin-top: 15px;
    }

    .restaurants-filter nav.primary ul {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding: 0;
        margin: 0;
    }

    .restaurants-filter nav.primary ul li {
        margin: 5px;
    }

    /* ========== TABLET STYLES ========== */
    @media (min-width: 768px) {
        .navbar-brand {
            height: 60px; /* Restore original height */
        }
        
        .logo-img {
            max-width: 180px; /* Restore original width */
        }
        
        .navbar-nav .nav-link {
            font-size: 16px; /* Restore original size */
            padding: 10px 15px;
            text-align: left; /* Left align for tablet/desktop */
        }
        
        .hero {
            height: 450px; /* Increase height for tablet */
            padding: 0;
        }
        
        .hero-text h1 {
            font-size: 2.5rem; /* Increase for tablet */
        }
        
        .steps {
            flex-direction: row; /* Horizontal layout for tablet */
            justify-content: space-around;
        }
        
        .step-item {
            width: 30%; /* Restore original width */
            margin-bottom: 0;
        }
        
        .step-item svg {
            width: 50px; /* Restore original size */
            height: 50px;
        }
        
        .step-item h4 {
            font-size: 1.2rem; /* Restore original size */
        }
        
        .popular {
            padding: 50px 0; /* Increase padding for tablet */
        }
        
        .figure-wrap {
            height: 200px; /* Restore original height */
        }
        
        .content h5 a {
            font-size: 1.1rem; /* Increase for tablet */
        }
        
        .price {
            font-size: 1.1rem; /* Increase for tablet */
        }
        
        .theme-btn-dash {
            padding: 8px 15px; /* Restore original padding */
            font-size: 1rem; /* Restore original size */
        }
        
        .how-it-works {
            padding: 50px 0; /* Increase padding for tablet */
        }
        
        .how-it-works h2 {
            font-size: 2.2rem; /* Increase for tablet */
        }
        
        .footer h5 {
            font-size: 1.1rem; /* Increase for tablet */
            text-align: left; /* Left align for tablet/desktop */
        }
        
        .footer h5::after {
            left: 0; /* Left align for tablet/desktop */
            transform: none;
        }
        
        .footer p {
            font-size: 0.9rem; /* Increase for tablet */
            text-align: left; /* Left align for tablet/desktop */
        }
        
        .address {
            flex: 0 0 30%; /* Restore original width */
            max-width: 30%;
            margin-bottom: 0;
        }
        
        .additional-info {
            flex: 0 0 65%; /* Restore original width */
            max-width: 65%;
            margin-bottom: 0;
        }
        
        .address .d-flex {
            flex-direction: row; /* Horizontal layout for tablet */
            text-align: left;
        }
        
        .popular .title h2 {
            font-size: 2rem; /* Increase for tablet */
            letter-spacing: 2px; /* Restore original spacing */
        }
        
        .featured-restaurants {
            padding: 50px 0; /* Increase padding for tablet */
        }
        
        .restaurant-logo img {
            height: 150px; /* Increase for tablet */
        }
        
        .guimbal-culture {
            padding: 50px 0; /* Increase padding for tablet */
        }
        
        .guimbal-culture h2 {
            font-size: 2.2rem; /* Increase for tablet */
        }
        
        .guimbal-culture-item {
            padding: 0 15px; /* Restore original padding */
        }
        
        .culture-item {
            padding: 20px; /* Restore original padding */
        }
        
        .culture-image {
            height: 180px; /* Increase for tablet */
        }
        
        .guimbal-culture h3 {
            font-size: 1.5rem; /* Increase for tablet */
        }
        
        .guimbal-culture p {
            font-size: 1rem; /* Increase for tablet */
        }
        
        .guimbal-logo {
            width: 60px; /* Increase for tablet */
        }
        
        .nav-item .dropdown-toggle {
            justify-content: flex-start; /* Left align for tablet/desktop */
        }
        
        .profile-pic {
            width: 50px; /* Restore original size */
            height: 50px;
        }
    }

    /* ========== DESKTOP STYLES ========== */
    @media (min-width: 992px) {
        .hero {
            height: 500px; /* Restore original height */
        }
        
        .hero-text h1 {
            font-size: 3.5rem; /* Restore original size */
        }
        
        .popular {
            padding: 60px 0; /* Restore original padding */
        }
        
        .how-it-works {
            padding: 60px 0; /* Restore original padding */
        }
        
        .how-it-works h2 {
            font-size: 2.5rem; /* Restore original size */
        }
        
        .popular .title h2 {
            font-size: 2.5rem; /* Restore original size */
        }
        
        .featured-restaurants {
            padding: 60px 0; /* Restore original padding */
        }
        
        .guimbal-culture {
            padding: 60px 0; /* Restore original padding */
        }
        
        .guimbal-culture h2 {
            font-size: 2.5rem; /* Restore original size */
        }
        
        .culture-image {
            height: 200px; /* Restore original height */
        }
        
        .guimbal-logo {
            width: 70px; /* Restore original size */
        }
    }

    /* ========== EXTRA SMALL DEVICES ========== */
    @media (max-width: 576px) {
        .hero {
            height: 300px; /* Even smaller for very small devices */
        }
        
        .hero-text h1 {
            font-size: 1.5rem; /* Even smaller for very small devices */
        }
        
        .step-item {
            padding: 12px; /* Even smaller padding */
        }
        
        .step-item svg {
            width: 35px; /* Even smaller icons */
            height: 35px;
        }
        
        .step-item h4 {
            font-size: 0.9rem; /* Even smaller font */
        }
        
        .figure-wrap {
            height: 150px; /* Even smaller for very small devices */
        }
        
        .content {
            padding: 12px; /* Even smaller padding */
        }
        
        .price-btn-block {
            flex-direction: column; /* Stack price and button */
            align-items: center;
        }
        
        .price {
            margin-bottom: 8px;
        }
        
        .guimbal-culture h3 {
            font-size: 1.1rem; /* Even smaller for very small devices */
        }
        
        .guimbal-culture p {
            font-size: 0.85rem; /* Even smaller for very small devices */
        }
        
        .navbar-toggler {
            padding: 4px 6px; /* Even smaller for very small devices */
            font-size: 0.8rem;
        }
    }
    
</style>
</head>
<body>
<body class="home">
    
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                    <a class="navbar-brand" href="index.php">
                        <div class="logo-container">
                            <img class="logo-img" src="images/logo6.png" alt="Guimbal Seafoods Logo">
                        </div>
                    </a>

                    <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                            <li class="nav-item"> <a class="nav-link active" href="seafoods.php">Seafoods <span class="sr-only"></span></a> </li>
                            
                           
							<?php
						if(empty($_SESSION["user_id"])) // if user is not login
							{
								echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
							  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
							}
						else
							{	
									echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
									
							}
                            
						?>
                         <!-- Profile Dropdown -->
                         <li class="nav-item dropdown">
   <?php
session_start();
include("connection/connect.php");

// Get user profile image
$profile_image = 'images/user-icn.webp'; // Default image
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_query = mysqli_query($db, "SELECT profile_image FROM users WHERE u_id='$user_id'");
    if ($user_data = mysqli_fetch_assoc($user_query)) {
        $profile_image = $user_data['profile_image'];
    }
}
?>

<!-- In your navbar where the profile image is -->
<a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <img src="<?php echo $profile_image; ?>" alt="user" class="profile-pic" style="width:50px; height:50px; border-radius:50%; object-fit: cover;" />
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

<script>
    function confirmLogout() {
        return confirm("Are you sure you want to log out?");
    }
</script>						 
                        </ul>
						 
                    </div>
                </div>
            </nav>                    
        </header>

        <section class="hero bg-image" data-image-src="images/img/feature1.jpg">
            <div class="hero-inner">
                <div class="container text-center hero-text font-white">
                    <div class="banner-form">
                        <form class="form-inline">
                          
                        </form>
                    </div>
                 
        </section>
        <section class="popular">
            <div class="container">
                <div class="title text-xs-center m-b-30">
                      <h1>Order & Delivery Services </h1>
                    <h2>BEST SELLERS</h2>
                    <p class="lead"></p>
                </div>
                <div class="row">
						<?php 					
						$query_res= mysqli_query($db,"select * from seafoods LIMIT 6"); 
                                while($r=mysqli_fetch_array($query_res))
                                {
                                        
                                    echo '  <div class="col-xs-12 col-sm-6 col-md-4 food-item">
                                            <div class="food-item-wrap">
                                                <div class="figure-wrap bg-image" data-image-src="admin/Res_img/dishes/'.$r['img'].'"></div>
                                                <div class="content">
                                                    <h5><a href="available.php?res_id='.$r['rs_id'].'">'.$r['title'].'</a></h5>
                                                    <div class="product-name">'.$r['slogan'].'</div>
                                                    <div class="price-btn-block"> <span class="price">₱'.$r['price'].'</span> <a href="available.php?res_id='.$r['rs_id'].'" class="btn theme-btn-dash pull-right">Order Now</a> </div>
                                                </div>
                                                
                                            </div>
                                    </div>';                                      
                                }	
						?>
                </div>
            </div>
        </section>

        
 
     <section class="guimbal-culture">
    <div class="container">
        <div class="text-xs-center">
            <h2>Discover Guimbal's Rich Culture</h2>
            <div class="row guimbal-culture-features">
                <div class="col-xs-12 col-sm-12 col-md-4 guimbal-culture-item white-txt">
                    <div class="culture-wrap">
                        <div class="culture-item">
                            <div class="culture-image">
                                <img src="images/img/culture.png" alt="Guimbal Heritage" class="img-fluid">
                            </div>
                            <h3>FESTIVALS AND TRADITIONS OF GUIMBAL  </h3>
                            <p>The Bantayan Festival and Disyembre sa Guimbal in Guimbal, Iloilo, celebrate the town's heritage and resilience through cultural performances, festive events, and tributes to its ancestors and former Mayor Oscar Garin Sr..</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 guimbal-culture-item white-txt">
                    <div class="culture-item">
                        <div class="culture-image">
                            <img src="images/img/culture1.png" alt="Guimbal Festival" class="img-fluid">
                        </div>
                        <h3>HERITAGE AND HISTORY</h3>
                        <p>Guimbal, established in 1703 and named after a warning instrument used during Moro raids, is rich in heritage with sites like the Virginia Bridge also known as Guimbal Bridge and old watchtowers..</p>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-4 guimbal-culture-item white-txt">
                    <div class="culture-item">
                        <div class="culture-image">
                            <img src="images/img/culture2.png" alt="Guimbal Cuisine" class="img-fluid">
                        </div>
                        <h3>GUIMBAL LOCAL CUISINE</h3>
                        <p>Guimbal's cuisine blends traditional Filipino flavors with fresh coastal seafood, featuring dishes like La Paz Batchoy and local specialties such as Bas-o, with popular spots like Southpark Grill known for their grilled seafood..</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        <section class="featured-restaurants">
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="title-block pull-left">
                            <h4>Featured </h4> </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="restaurants-filter pull-right">
                            <nav class="primary pull-left">
                                <ul>
                                    <li><a href="#" class="selected" data-filter="*">all</a> </li>
									<?php 
									$res= mysqli_query($db,"select * from res_category");
									      while($row=mysqli_fetch_array($res))
										  {
											echo '<li><a href="#" data-filter=".'.$row['c_name'].'"> '.$row['c_name'].'</a> </li>';
										  }
									?>
                                   
                                </ul>
                            </nav>
                        </div>
          
                    </div>
                </div>
    
                <div class="row">
                    <div class="restaurant-listing">
                        
						
						<?php  
						$ress= mysqli_query($db,"select * from categories");  
									      while($rows=mysqli_fetch_array($ress))
										  {
													
													$query= mysqli_query($db,"select * from res_category where c_id='".$rows['c_id']."' ");
													 $rowss=mysqli_fetch_array($query);
						
													 echo ' <div class="col-xs-12 col-sm-12 col-md-6 single-restaurant all '.$rowss['c_name'].'">
														<div class="restaurant-wrap">
															<div class="row">
																<div class="col-xs-12 col-sm-3 col-md-12 col-lg-3 text-xs-center">
																	<a class="restaurant-logo" href="available.php?res_id='.$rows['rs_id'].'" > <img src="admin/Res_img/'.$rows['image'].'" alt="Restaurant logo"> </a>
																</div>
													
																<div class="col-xs-12 col-sm-9 col-md-12 col-lg-9">
																	<h5><a href="available.php?res_id='.$rows['rs_id'].'" >'.$rows['title'].'</a></h5> <span>'.$rows['address'].'</span>
																</div>
													
															</div>
												</div>	
										</div>';
								 }
						?>						
                    </div>
                </div>
            </div>
        </section>
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
</body>

</html>