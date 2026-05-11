<?php
session_start();
include("connection/connect.php");

$user_id = $_SESSION['user_id'];

// Fetch existing user data
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$user_id'");
$user = mysqli_fetch_assoc($query);

// Check if the form is submitted
if (isset($_POST['update'])) {
    // Get updated values from form inputs
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $postal_code = $_POST['postal_code'];

    // Handle profile image upload
    $profile_image = $user['profile_image']; // Keep existing image by default
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['profile_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'uploads/profile_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . time() . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old profile image if it's not the default one
                if ($user['profile_image'] != 'images/user-icn.webp' && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
                $profile_image = $upload_path;
            }
        }
    }

    // Update the user's information in the database
    $update_query = "UPDATE users SET 
                     f_name='".mysqli_real_escape_string($db, $fullname)."', 
                     email='".mysqli_real_escape_string($db, $email)."', 
                     phone='".mysqli_real_escape_string($db, $phone)."', 
                     address='".mysqli_real_escape_string($db, $address)."',
                     municipality='".mysqli_real_escape_string($db, $municipality)."',
                     barangay='".mysqli_real_escape_string($db, $barangay)."',
                     postal_code='".mysqli_real_escape_string($db, $postal_code)."',
                     profile_image='".mysqli_real_escape_string($db, $profile_image)."'
                     WHERE u_id='$user_id'";

    if (mysqli_query($db, $update_query)) {
        // Update shipping fee if municipality changed
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
        
        // Update shipping fee
        mysqli_query($db, "UPDATE users SET shipping_fee = '$shipping_fee' WHERE u_id = '$user_id'");
        
        echo "<script>alert('Profile updated successfully!'); window.location='view_profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: ".mysqli_error($db)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .profile-edit-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        .btn-primary {
            background: #3498db;
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .address-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .section-title {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        .profile-pic-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3498db;
            margin-bottom: 15px;
        }
        
        /* Hide file input completely */
        .hidden-file-input {
            position: absolute;
            left: -9999px;
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        /* Custom upload button styling */
        .btn-upload-custom {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .btn-upload-custom:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">

<div class="profile-edit-container">
    <h2 class="mb-4 text-center">Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Profile Image Upload - Fixed Section -->
        <div class="form-group text-center mb-4">
            <label class="form-label">Profile Image</label>
            <div>
                <img id="profile_preview" src="<?php echo $user['profile_image']; ?>" 
                     alt="Profile Preview" class="profile-pic-preview">
                <br>
                <!-- Completely hidden file input -->
                <input type="file" class="hidden-file-input" id="profile_image" 
                       name="profile_image" accept="image/*">
                <!-- Custom styled button that triggers the file input -->
                <button type="button" class="btn-upload-custom mt-2" 
                        onclick="document.getElementById('profile_image').click()">
                    <i class="fa fa-camera"></i> Change Image
                </button>
                <!-- Optional: Show selected file name -->
                <div id="selected_file_name" style="margin-top: 5px; font-size: 12px; color: #666; display: none;"></div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="fullname" 
                 value="<?php echo htmlspecialchars($user['f_name'].' '.$user['l_name']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" 
                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" 
                   value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        
        <div class="address-section">
            <h4 class="section-title">Shipping Address</h4>
            
            <div class="mb-3">
                <label class="form-label">Complete Address</label>
                <textarea class="form-control" name="address" rows="3" required><?php 
                    echo htmlspecialchars($user['address'] ?? ''); 
                ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Municipality</label>
                    <select class="form-control" name="municipality" required>
                        <option value="">Select Municipality</option>
                        <?php
                        // Only show municipalities from DISTRICT 1
                        $municipalities = mysqli_query($db, "SELECT * FROM iloilo_shipping_rates WHERE district = 1 ORDER BY municipality");
                        while($mun = mysqli_fetch_array($municipalities)) {
                            $selected = ($mun['municipality'] == ($user['municipality'] ?? '')) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($mun['municipality'])."' $selected>
                                ".htmlspecialchars($mun['municipality'])."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Barangay</label>
                    <input type="text" class="form-control" name="barangay" 
                           value="<?php echo htmlspecialchars($user['barangay'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Postal Code</label>
                <input type="text" class="form-control" name="postal_code" 
                       value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
            </div>
        </div>
        
        <button type="submit" name="update" class="btn btn-primary mt-3">
            <i class="fa fa-save"></i> Save Changes
        </button>
    </form>
</div>

<script>
// Profile image preview
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const fileNameDisplay = document.getElementById('selected_file_name');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile_preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Optional: Show selected file name
        fileNameDisplay.textContent = 'Selected: ' + file.name;
        fileNameDisplay.style.display = 'block';
    } else {
        fileNameDisplay.style.display = 'none';
    }
});
</script>

</body>
</html>