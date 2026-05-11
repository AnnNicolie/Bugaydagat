<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Initialize variables
$driver_id = isset($_GET['driver_id']) ? intval($_GET['driver_id']) : 0;
$driver_data = null;
$error = '';
$success = '';

// Fetch driver data
if($driver_id > 0) {
    $sql = "SELECT * FROM delivery_drivers WHERE driver_id = $driver_id";
    $result = mysqli_query($db, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $driver_data = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "Driver not found!";
        header('location:all_drivers.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid driver ID!";
    header('location:all_drivers.php');
    exit();
}

// Function to get profile image with fallback
function getDriverProfileImage($image_path) {
    $default_image = '../images/default-avatar.png';
    
    if (empty($image_path)) {
        return $default_image;
    }
    
    // Check if image exists with current path
    if (file_exists($image_path)) {
        return $image_path;
    }
    
    // Check if image exists with ../ prefix (for admin access)
    if (file_exists('../' . $image_path)) {
        return '../' . $image_path;
    }
    
    // Check if image exists without path (just filename)
    if (file_exists('../uploads/driver_images/' . $image_path)) {
        return '../uploads/driver_images/' . $image_path;
    }
    
    return $default_image;
}

// Handle form submission
if(isset($_POST['submit'])) {
    // Check for empty fields
    $required_fields = ['full_name', 'email', 'phone', 'address', 'plate_number', 'vehicle_type', 'license_number'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field]))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error = "Please fill all required fields: " . implode(', ', $missing_fields);
    }

    // Validate phone number
    if (empty($error) && (strlen($_POST['phone']) < 10 || !is_numeric($_POST['phone']))) {
        $error = "Invalid phone number! Must be at least 10 digits and numeric only.";
    }

    // Validate email
    if (empty($error) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address!";
    }

    // Check duplicate email (excluding current driver)
    if (empty($error)) {
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $check_email = mysqli_query($db, "SELECT driver_id FROM delivery_drivers WHERE email = '$email' AND driver_id != $driver_id");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email already exists!";
        }
    }

    // Handle profile image upload
    $profile_pic = $driver_data['profile_pic']; // Keep current image by default
    
    if (empty($error) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_pic']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/driver_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old profile image if it's not the default
            if (!empty($driver_data['profile_pic']) && $driver_data['profile_pic'] != 'images/default-avatar.png') {
                $old_image_path = '../' . $driver_data['profile_pic'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
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

    // Handle password update (only if provided)
    $password_update = '';
    if (empty($error) && !empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $error = "Password must be at least 6 characters!";
        } else {
            $new_password = md5($_POST['password']);
            $password_update = ", password = '$new_password'";
        }
    }

    // Update status
    $status = isset($_POST['status']) ? 1 : 0;

    // Only proceed with database update if no errors
    if(empty($error)) {
        // Escape all inputs to prevent SQL injection
        $full_name = mysqli_real_escape_string($db, $_POST['full_name']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $phone = mysqli_real_escape_string($db, $_POST['phone']);
        $address = mysqli_real_escape_string($db, $_POST['address']);
        $plate_number = mysqli_real_escape_string($db, $_POST['plate_number']);
        $vehicle_type = mysqli_real_escape_string($db, $_POST['vehicle_type']);
        $license_number = mysqli_real_escape_string($db, $_POST['license_number']);
        $profile_pic = mysqli_real_escape_string($db, $profile_pic);
        
        // Build the update query
        $sql = "UPDATE delivery_drivers SET 
                full_name = '$full_name',
                email = '$email',
                phone = '$phone',
                address = '$address',
                plate_number = '$plate_number',
                vehicle_type = '$vehicle_type',
                license_number = '$license_number',
                profile_pic = '$profile_pic',
                status = $status
                $password_update
                WHERE driver_id = $driver_id";
        
        // Debug: Uncomment the line below to see the actual SQL query
        // echo "SQL Query: " . $sql . "<br>";
        
        if(mysqli_query($db, $sql)) {
            if(mysqli_affected_rows($db) > 0) {
                $success = "Driver updated successfully!";
                // Refresh driver data
                $result = mysqli_query($db, "SELECT * FROM delivery_drivers WHERE driver_id = $driver_id");
                $driver_data = mysqli_fetch_assoc($result);
            } else {
                $error = "No changes were made or driver not found.";
            }
        } else {
            $error = "Error updating driver: " . mysqli_error($db);
            // For debugging - you can remove this in production
            error_log("MySQL Error: " . mysqli_error($db));
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
    <title>Edit Delivery Driver</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin-bottom: 15px;
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
        
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #28a745;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .alert {
            margin-bottom: 20px;
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
                        <h4 class="m-b-0 text-white">Edit Delivery Driver - <?php echo htmlspecialchars($driver_data['full_name']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(!empty($error)) { 
                            echo '<div class="alert alert-danger"><strong>Error!</strong> '.$error.'</div>'; 
                        }
                        if(!empty($success)) { 
                            echo '<div class="alert alert-success"><strong>Success!</strong> '.$success.'</div>'; 
                        }
                        ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <!-- Profile Image Upload Section -->
                            <div class="profile-image-upload">
                                <label class="form-label" style="display: block; margin-bottom: 10px; font-weight: 600; color: #007bff;">Driver Profile Image</label>
                                <div>
                                    <img id="profile_preview" src="<?php echo getDriverProfileImage($driver_data['profile_pic']); ?>" alt="Profile Preview" class="profile-preview">
                                    <br>
                                    <div style="position: relative; display: inline-block;">
                                        <input type="file" class="form-control-file" id="profile_pic" name="profile_pic" accept="image/*" 
                                               style="position: absolute; left: -9999px; opacity: 0; width: 0; height: 0;">
                                        <button type="button" class="btn-upload" onclick="document.getElementById('profile_pic').click()">
                                            <i class="fas fa-camera"></i> Change Image
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name *</label>
                                        <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($driver_data['full_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email *</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($driver_data['email']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                                        <small class="form-text text-muted">Enter new password (min 6 characters) or leave blank to keep current</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number *</label>
                                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($driver_data['phone']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address *</label>
                                <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($driver_data['address']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Plate Number *</label>
                                        <input type="text" class="form-control" name="plate_number" value="<?php echo htmlspecialchars($driver_data['plate_number']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vehicle Type *</label>
                                        <select class="form-control" name="vehicle_type" required>
                                            <option value="">Select Vehicle Type</option>
                                            <option value="Motorcycle" <?php echo ($driver_data['vehicle_type'] == 'Motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                                            <option value="Car" <?php echo ($driver_data['vehicle_type'] == 'Car') ? 'selected' : ''; ?>>Car</option>
                                            <option value="Truck" <?php echo ($driver_data['vehicle_type'] == 'Truck') ? 'selected' : ''; ?>>Truck</option>
                                            <option value="Bicycle" <?php echo ($driver_data['vehicle_type'] == 'Bicycle') ? 'selected' : ''; ?>>Bicycle</option>
                                            <option value="Scooter" <?php echo ($driver_data['vehicle_type'] == 'Scooter') ? 'selected' : ''; ?>>Scooter</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>License Number *</label>
                                        <input type="text" class="form-control" name="license_number" value="<?php echo htmlspecialchars($driver_data['license_number']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="status-toggle">
                                    <label class="form-check-label" for="status">Driver Status</label>
                                    <label class="switch">
                                        <input type="checkbox" name="status" id="status" <?php echo ($driver_data['status'] == 1) ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span id="status-text"><?php echo ($driver_data['status'] == 1) ? 'Active' : 'Inactive'; ?></span>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Driver
                                </button>
                                <a href="all_drivers.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('includes/footer.php'); ?>

    <script>
    // Profile image preview
    document.getElementById('profile_pic').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile_preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Status toggle text update
    document.getElementById('status').addEventListener('change', function() {
        document.getElementById('status-text').textContent = this.checked ? 'Active' : 'Inactive';
    });
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let valid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
                field.style.borderColor = 'red';
            } else {
                field.style.borderColor = '';
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please fill all required fields!');
        }
    });
    </script>
</body>
</html>