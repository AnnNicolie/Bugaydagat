<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Add username to required fields
$required_fields = ['full_name', 'username', 'email', 'password', 'phone', 'address', 'plate_number', 'vehicle_type', 'license_number'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $error = "All fields must be filled!";
        break;
    }
}

// Add username validation
if (!isset($error) && (strlen($_POST['username']) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $_POST['username']))) {
    $error = "Username must be at least 3 characters and contain only letters, numbers, and underscores!";
}

// Check duplicate username (add this after email duplicate check)
if (!isset($error)) {
    $check_username = mysqli_query($db, "SELECT username FROM delivery_drivers WHERE username = '".mysqli_real_escape_string($db, $_POST['username'])."'");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username Already exists!";
    }
}

    // Validate password length
    if (!isset($error) && strlen($_POST['password']) < 6) {
        $error = "Password must be at least 6 characters!";
    }

    // Validate phone number
    if (!isset($error) && (strlen($_POST['phone']) < 10 || !is_numeric($_POST['phone']))) {
        $error = "Invalid phone number!";
    }

    // Validate email
    if (!isset($error) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address!";
    }

    // Check duplicate email
    if (!isset($error)) {
        $check_email = mysqli_query($db, "SELECT email FROM delivery_drivers WHERE email = '".mysqli_real_escape_string($db, $_POST['email'])."'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email Already exists!";
        }
    }

    // Handle profile image upload
    $profile_pic = 'images/default-avatar.png'; // Default image
    if (!isset($error) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/driver_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_filename = 'driver_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                $profile_pic = 'uploads/driver_images/' . $new_filename;
            } else {
                $error = "Failed to upload profile image!";
            }
        } else {
            $error = "Invalid image format! Allowed: JPG, JPEG, PNG, GIF, WEBP";
        }
    }

  if(!isset($error)) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username']; // Add this line
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $plate_number = $_POST['plate_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $license_number = $_POST['license_number'];
    
    $sql = "INSERT INTO delivery_drivers (full_name, username, email, password, phone, address, plate_number, vehicle_type, license_number, profile_pic, status, created_at) 
            VALUES ('$full_name', '$username', '$email', '$password', '$phone', '$address', '$plate_number', '$vehicle_type', '$license_number', '$profile_pic', 1, NOW())";
    
    if(mysqli_query($db, $sql)) {
        $success = "Driver added successfully!";
        // Clear form or redirect
        $_POST = array(); // Clear form
    } else {
        $error = "Error: " . mysqli_error($db);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Delivery Driver</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-image-upload {
            text-align: center;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }

        .profile-preview-container {
            position: relative;
            display: inline-block;
            margin-bottom: 15px;
        }

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
        }

        .default-profile-icon {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #6c757d;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .default-profile-icon i {
            font-size: 50px;
            color: #6c757d;
        }

        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 123, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .profile-preview-container:hover .upload-overlay {
            opacity: 1;
        }

        .upload-overlay i {
            color: white;
            font-size: 24px;
        }

        .btn-upload {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-upload:hover {
            background: #0056b3;
        }

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

        .form-group {
            margin-bottom: 1rem;
        }

        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
    </style>
</head>
<body class="fix-header">
    <?php include('includes/header.php'); ?>
    
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="col-lg-12">
                <div class="card card-outline-primary">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Add New Delivery Driver</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(isset($error)) { 
                            echo '<div class="alert alert-danger">'.$error.'</div>'; 
                        }
                        if(isset($success)) { 
                            echo '<div class="alert alert-success">'.$success.'</div>'; 
                        }
                        ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <!-- Profile Image Upload Section - ONLY THIS SECTION CHANGED -->
                            <div class="profile-image-upload">
                                <label class="form-label" style="display: block; margin-bottom: 10px; font-weight: 600; color: #007bff;">Driver Profile Image</label>
                                <div class="profile-preview-container">
                                    <!-- Default icon -->
                                    <div id="default_icon" class="default-profile-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <!-- Image preview (hidden initially) -->
                                    <img id="profile_preview" src="" alt="Profile Preview" class="profile-preview" style="display: none;">
                                    <div class="upload-overlay" onclick="document.getElementById('profile_pic').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <br>
                                <div style="position: relative; display: inline-block;">
                                    <input type="file" class="form-control-file" id="profile_pic" name="profile_pic" accept="image/*" 
                                           style="position: absolute; left: -9999px; opacity: 0; width: 0; height: 0;">
                                    <button type="button" class="btn-upload" onclick="document.getElementById('profile_pic').click()">
                                        <i class="fas fa-camera"></i> Choose Image
                                    </button>
                                </div>
                                <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB</small>
                            </div>

                         <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" class="form-control" name="full_name" value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>" required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Username *</label>
            <input type="text" class="form-control" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required>
            <small class="form-text text-muted">Unique username for login</small>
        </div>
    </div>
</div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password *</label>
                                        <input type="password" class="form-control" name="password" required>
                                        <small class="form-text text-muted">Password must be at least 6 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number *</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address *</label>
                                <textarea class="form-control" name="address" rows="3" required><?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Plate Number *</label>
                                        <input type="text" class="form-control" name="plate_number" value="<?php echo isset($_POST['plate_number']) ? $_POST['plate_number'] : ''; ?>" required>
                                    </div>
                                </div>
                              <div class="col-md-4">
    <div class="form-group">
        <label>Vehicle Type *</label>
        <select class="form-control" name="vehicle_type" required>
            <option value="">Select Vehicle Type</option>
            <option value="Motorcycle" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
            <option value="Car" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Car') ? 'selected' : ''; ?>>Car</option>
            <option value="Truck" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Truck') ? 'selected' : ''; ?>>Truck</option>
            <option value="Bicycle" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Bicycle') ? 'selected' : ''; ?>>Bicycle</option>
            <option value="Scooter" <?php echo (isset($_POST['vehicle_type']) && $_POST['vehicle_type'] == 'Scooter') ? 'selected' : ''; ?>>Scooter</option>
        </select>
    </div>
</div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>License Number *</label>
                                        <input type="text" class="form-control" name="license_number" value="<?php echo isset($_POST['license_number']) ? $_POST['license_number'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">Add Driver</button>
                                <a href="all_drivers.php" class="btn btn-secondary btn-lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('includes/footer.php'); ?>

    <script>
    // Profile image preview - ONLY THIS SCRIPT CHANGED
    document.getElementById('profile_pic').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const defaultIcon = document.getElementById('default_icon');
        const profilePreview = document.getElementById('profile_preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
                profilePreview.style.display = 'block';
                defaultIcon.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            profilePreview.style.display = 'none';
            defaultIcon.style.display = 'flex';
        }
    });
    </script>
</body>
</html>0