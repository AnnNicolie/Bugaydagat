<!DOCTYPE html>
<html lang="en">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("connection/connect.php");

if (isset($_POST['submit'])) {
    // Check for empty fields
    $required_fields = ['fullname', 'username', 'email', 'phone', 'password', 'cpassword', 'address', 'municipality'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $message = "<div class='message error'>All fields must be filled!</div>";
            exit;
        }
    }

    // Validate password match
    if ($_POST['password'] != $_POST['cpassword']) {
        $message = "<div class='message error'>Password does not match!</div>";
        exit;
    }

    // Validate password length
    if (strlen($_POST['password']) < 6) {
        $message = "<div class='message error'>Password must be at least 6 characters!</div>";
        exit;
    }

    // Validate phone number
    if (strlen($_POST['phone']) < 10 || !is_numeric($_POST['phone'])) {
        $message = "<div class='message error'>Invalid phone number!</div>";
        exit;
    }

    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='message error'>Invalid email address!</div>";
        exit;
    }

    // Check duplicate username
    $check_username = mysqli_query($db, "SELECT username FROM users WHERE username = '".mysqli_real_escape_string($db, $_POST['username'])."'");
    if (mysqli_num_rows($check_username) > 0) {
        $message = "<div class='message error'>Username already exists!</div>";
        exit;
    }

    // Check duplicate email
    $check_email = mysqli_query($db, "SELECT email FROM users WHERE email = '".mysqli_real_escape_string($db, $_POST['email'])."'");
    if (mysqli_num_rows($check_email) > 0) {
        $message = "<div class='message error'>Email already exists!</div>";
        exit;
    }

    // Handle profile image upload
    $profile_image = 'images/user-icn.webp'; // Default image
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_image']['type'];
        $file_size = $_FILES['profile_image']['size'];
        
        // Check file type
        if (in_array($file_type, $allowed_types)) {
            // Check file size (max 5MB)
            if ($file_size <= 5 * 1024 * 1024) {
                $upload_dir = 'uploads/profile_images/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $new_filename = 'profile_' . time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image = $upload_path;
                } else {
                    $message = "<div class='message error'>Failed to upload image. Please try again.</div>";
                }
            } else {
                $message = "<div class='message error'>Image size too large. Maximum size is 5MB.</div>";
            }
        } else {
            $message = "<div class='message error'>Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, WEBP.</div>";
        }
    }

    // Prepare the INSERT statement
    $stmt = $db->prepare("INSERT INTO users (username, fullname, f_name, l_name, email, phone, password, address, municipality, barangay, postal_code, profile_image, status, date) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())");

    if ($stmt) {
        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        
        // Split fullname for separate fields
        $nameParts = explode(' ', $_POST['fullname'], 2);
        $f_name = $nameParts[0];
        $l_name = isset($nameParts[1]) ? $nameParts[1] : '';
        
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = md5($_POST['password']);
        $address = $_POST['address'];
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'] ?? '';
        $postal_code = $_POST['postal_code'] ?? '';

        $stmt->bind_param("ssssssssssss", 
            $username,
            $fullname,
            $f_name,
            $l_name,
            $email,
            $phone,
            $password,
            $address,
            $municipality,
            $barangay,
            $postal_code,
            $profile_image
        );

        if ($stmt->execute()) {
            // Calculate and store shipping fee based on municipality
            $user_id = $stmt->insert_id;
            $municipality = $_POST['municipality'];
            
            // Check if municipality is Guimbal
            if ($municipality == 'Guimbal') {
                $shipping_fee = 30.00;
            } else {
                // Get base fee from iloilo_shipping_rates table
                $fee_query = mysqli_query($db, "SELECT base_fee FROM iloilo_shipping_rates WHERE municipality = '".mysqli_real_escape_string($db, $municipality)."'");
                if (mysqli_num_rows($fee_query) > 0) {
                    $fee_data = mysqli_fetch_assoc($fee_query);
                    $shipping_fee = $fee_data['base_fee'];
                } else {
                    $shipping_fee = 50.00; // Default fee if municipality not found
                }
            }
            
            // Store the shipping fee in user record
            mysqli_query($db, "UPDATE users SET shipping_fee = '$shipping_fee' WHERE u_id = '$user_id'");
            
            $message = "<div class='message success'>Registration successful! Redirecting to login...</div>";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } else {
            $message = "<div class='message error'>Error: ".$stmt->error."</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='message error'>Database error: ".$db->error."</div>";
    }
}
?>
<head>
    <meta charset="UTF-8">
    <title>Register - Guimbal Seafoods</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
  
    <style>
        .message {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 500;
}

.error {
    background-color: #ffe6e6;
    color: #d63031;
    border: 1px solid #ff9999;
}

.success {
    background-color: #e6ffe6;
    color: #00b894;
    border: 1px solid #99cc99;
}
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #fff9e6;
            color: #333;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        .navbar-nav .nav-item .active {
            background-color: rgba(255,255,255,0.3);
            border-radius: 5px;
        }

        .navbar-toggler {
            border: none;
            background-color: #ffa500;
            color: #333;
            padding: 5px 10px;
        }

        .navbar-toggler:hover {
            background-color: #ff8c00;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.1));
        }

        /* Registration Container */
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(255, 165, 0, 0.2);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #ffd700, #ffa500);
            padding: 80px 20px 40px; 
            text-align: center;
            color: white;
        }

        .register-header h2 {
            margin: 100;
            font-size: 28px;
            font-weight: 700;
        }

        .register-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }

        .register-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #ffa500;
            box-shadow: 0 0 0 0.2rem rgba(255, 165, 0, 0.25);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffa500;
        }

        .input-icon input, .input-icon select {
            padding-left: 45px;
        }

        .btn-register {
            background: linear-gradient(to right, #ffd700, #ffa500);
            border: none;
            color: #333;
            padding: 12px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn-register:hover {
            background: linear-gradient(to right, #ffa500, #ff8c00);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .register-footer a {
            color: #ff8c00;
            text-decoration: none;
            font-weight: 500;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .error {
            background-color: #ffe6e6;
            color: #d63031;
            border: 1px solid #ff9999;
        }

        .success {
            background-color: #e6ffe6;
            color: #00b894;
            border: 1px solid #99cc99;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .form-col {
            flex: 1;
            min-width: 250px;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .address-section {
            background-color: #fff9e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ffd700;
        }

        .section-title {
            color: #ff8c00;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ffd700;
        }

        /* Profile Image Styles */
        .profile-image-upload {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffa500;
            margin-bottom: 10px;
        }

        .btn-upload {
            background: #ffa500;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-upload:hover {
            background: #ff8c00;
        }

        /* Footer Styles */
        .footer {
            background: linear-gradient(135deg, #ffd700, #ffb347);
            color: #5a3700;
            padding: 25px 0 12px;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
        }

        .footer h5 {
            color: #5a3700;
            font-weight: 600;
            margin-bottom: 10px;
            position: relative;
            padding-bottom: 5px;
            font-size: 1.1rem;
        }

        .footer h5::after {
            content: '';
            position: absolute;
            left: 0;
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
            font-size: 0.9rem;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                padding: 20px 15px;
            }
            
            .register-container {
                max-width: 100%;
            }
            
            .register-body {
                padding: 20px;
            }
            
            .form-col {
                flex: 100%;
                min-width: 100%;
            }
        }
        /* Hide file input text and browser default styling */
input[type="file"] {
    color: transparent;
}

input[type="file"]::file-selector-button {
    display: none;
}

input[type="file"]::-webkit-file-upload-button {
    display: none;
}

input[type="file"]::-ms-browse {
    display: none;
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
    </style>
</head>

<body>

<div class="main-content">
    <div class="register-container">
        <div class="register-header">
            <h2>Create Your Account</h2>
            <p>Join Bugay-Dagat to start ordering fresh seafood</p>
        </div>
        
        <div class="register-body">
            <!-- Add enctype="multipart/form-data" to form -->
            <form action="" method="post" enctype="multipart/form-data">
                
                <!-- Profile Image Upload Section -->
                <div class="profile-image-upload">
    <label class="form-label" style="display: block; margin-bottom: 10px; font-weight: 600; color: #ff8c00;">Profile Image</label>
    <div>
        <img id="profile_preview" src="images/user-icn.webp" alt="Profile Preview" class="profile-preview">
        <br>
        <div style="position: relative; display: inline-block;">
            <input type="file" class="form-control-file" id="profile_image" name="profile_image" accept="image/*" 
                   style="position: absolute; left: -9999px; opacity: 0; width: 0; height: 0;">
            <button type="button" class="btn-upload" onclick="document.getElementById('profile_image').click()">
                <i class="fas fa-camera"></i> Choose Image
            </button>
        </div>
    </div>
</div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="fullname" placeholder="Full Name" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-user-circle"></i>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" class="form-control" name="phone" placeholder="Phone Number" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="cpassword" placeholder="Confirm Password" required>
                        </div>
                    </div>
                </div>
                
                <div class="address-section">
                    <h5 class="section-title">Delivery Address</h5>
                    <div class="form-group">
                        <textarea class="form-control" name="address" rows="3" placeholder="Complete Address" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group input-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <select class="form-control" name="municipality" required>
                                    <option value="">Select Municipality</option>
                                    <?php
                                    // Only show municipalities from DISTRICT 1
                                    $municipalities = mysqli_query($db, "SELECT DISTINCT municipality FROM iloilo_shipping_rates WHERE district = 1 ORDER BY municipality");
                                    while($mun = mysqli_fetch_array($municipalities)) {
                                        echo "<option value='".htmlspecialchars($mun['municipality'])."'>".htmlspecialchars($mun['municipality'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <input type="text" class="form-control" name="barangay" placeholder="Barangay">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <input type="text" class="form-control" name="postal_code" placeholder="Postal Code">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn-register">Create Account</button>
            </form>
            
            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>
</div>

<script>
// Profile image preview
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile_preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/animsition.min.js"></script>
<script src="js/foodpicky.min.js"></script>
</body>
</html>