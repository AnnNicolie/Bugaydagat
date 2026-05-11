<?php
session_start();
include("connection/connect.php");

$user_id = $_SESSION['user_id'];
$query = mysqli_query($db, "SELECT * FROM users WHERE u_id='$user_id'");
$user = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        /* Centered Profile Container */
        .profile-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Profile Picture Styling */
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Profile Table */
        .profile-table {
            width: 100%;
            margin-top: 20px;
        }

        .profile-table th {
            text-align: right;
            padding-right: 15px;
            color: #555;
        }

        .profile-table td {
            font-weight: bold;
            color: #333;
        }

        /* Button Styling */
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease-in-out;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">

<div class="profile-container">
    <h2 class="mb-3">Profile Information</h2>
    <!-- Updated to show actual profile image from database -->
    <img src="<?php echo $user['profile_image']; ?>" alt="User Profile" class="profile-pic">
    
    <table class="profile-table table">
        <tr><th>Username:</th><td><?php echo $user['username']; ?></td></tr>
        <tr><th>Full Name:</th><td><?php echo $user['fullname']; ?></td></tr>
        <tr><th>Email:</th><td><?php echo $user['email']; ?></td></tr>
        <tr><th>Phone:</th><td><?php echo $user['phone']; ?></td></tr>
        <tr><th>Address:</th><td><?php echo $user['address']; ?></td></tr>
        <tr><th>Municipality:</th><td><?php echo $user['municipality']; ?></td></tr>
        <tr><th>Barangay:</th><td><?php echo $user['barangay']; ?></td></tr>
        <tr><th>Postal Code:</th><td><?php echo $user['postal_code']; ?></td></tr>
    </table>
    <a href="edit_profile.php" class="btn btn-primary mt-3">Edit Profile</a>
</div>

</body>
</html>