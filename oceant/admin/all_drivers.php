<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

// Function to get profile image with fallback - FIXED SYNTAX
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

// Delete driver
if(isset($_GET['delete_driver'])) {
    $driver_id = $_GET['delete_driver'];
    
    // Get driver info to delete profile image
    $get_driver = mysqli_query($db, "SELECT profile_pic FROM delivery_drivers WHERE driver_id = $driver_id");
    $driver = mysqli_fetch_assoc($get_driver);
    
    // Delete profile image if it's not the default
    if (!empty($driver['profile_pic']) && $driver['profile_pic'] != 'images/default-avatar.png') {
        $image_path = '../' . $driver['profile_pic'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $sql = "DELETE FROM delivery_drivers WHERE driver_id = $driver_id";
    if(mysqli_query($db, $sql)) {
        $_SESSION['success'] = "Driver deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting driver: " . mysqli_error($db);
    }
    header('location:all_drivers.php');
    exit();
}

// Get all drivers
$sql = "SELECT * FROM delivery_drivers ORDER BY driver_id DESC";
$result = mysqli_query($db, $sql);

// Check if is_available column exists
$check_column_sql = "SHOW COLUMNS FROM delivery_drivers LIKE 'is_available'";
$column_result = mysqli_query($db, $check_column_sql);
$is_available_column_exists = (mysqli_num_rows($column_result) > 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delivery Drivers</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-img-small {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .profile-img-card {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            margin: 0 auto 15px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .status-active {
            color: #28a745;
            font-weight: bold;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
        .availability-available {
            color: #28a745;
            font-weight: bold;
        }
        .availability-busy {
            color: #dc3545;
            font-weight: bold;
        }
        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .driver-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .driver-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .driver-info {
            margin-bottom: 10px;
        }
        .driver-info strong {
            color: #007bff;
            min-width: 120px;
            display: inline-block;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .btn-action {
            flex: 1;
            min-width: 80px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #dee2e6;
                border-radius: 10px;
                padding: 15px;
                background: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border: none;
                border-bottom: 1px solid #f8f9fa;
            }
            .table tbody td:last-child {
                border-bottom: none;
            }
            .table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #007bff;
                text-align: left;
                min-width: 120px;
            }
            .profile-img-small {
                width: 60px;
                height: 60px;
            }
            .action-buttons {
                justify-content: center;
            }
            .btn-action {
                flex: none;
            }
        }
        
        /* Desktop table enhancements */
        @media (min-width: 769px) {
            .table-hover tbody tr:hover {
                background-color: rgba(0,123,255,0.05);
            }
        }
        
        /* Card view for mobile */
        .driver-card-view {
            display: none;
        }
        
        @media (max-width: 768px) {
            .table-view {
                display: none;
            }
            .driver-card-view {
                display: block;
            }
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .form-check.form-switch {
            display: inline-flex;
            align-items: center;
        }
        .form-check-input {
            margin-right: 8px;
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
                        <h4 class="m-b-0 text-white">All Delivery Drivers</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        if(isset($_SESSION['success'])) { 
                            echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>'; 
                            unset($_SESSION['success']);
                        }
                        if(isset($_SESSION['error'])) { 
                            echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>'; 
                            unset($_SESSION['error']);
                        }
                        ?>
                        
                        <!-- Desktop Table View -->
                        <div class="table-responsive m-t-40 table-view">
                            <table id="myTable" class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Profile</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Plate Number</th>
                                        <th>Vehicle Type</th>
                                        <th>License Number</th>
                                        <th>Status</th>
                                        <th>Availability</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_array($result)) {
                                            $profile_pic = getDriverProfileImage($row['profile_pic']);
                                            $is_available = $is_available_column_exists ? $row['is_available'] : 1;
                                            
                                            echo '<tr>
                                                <td class="text-center" data-label="Profile">
                                                    <img src="' . $profile_pic . '" alt="Profile" class="profile-img-small" 
                                                         onerror="this.src=\'../images/default-avatar.png\'">
                                                </td>
                                                <td data-label="Full Name">'.$row['full_name'].'</td>
                                                <td data-label="Username">'.$row['username'].'</td>
                                                <td data-label="Email">'.$row['email'].'</td>
                                                <td data-label="Phone">'.$row['phone'].'</td>
                                                <td data-label="Address">'.substr($row['address'], 0, 30).'...</td>
                                                <td data-label="Plate Number">'.$row['plate_number'].'</td>
                                                <td data-label="Vehicle Type">'.$row['vehicle_type'].'</td>
                                                <td data-label="License Number">'.$row['license_number'].'</td>
                                                <td data-label="Status">
                                                    <span class="'.($row['status'] == 1 ? 'status-active' : 'status-inactive').'">
                                                        '.($row['status'] == 1 ? 'Active' : 'Inactive').'
                                                    </span>
                                                </td>
                                                <td data-label="Availability">';
                                                
                                            if ($is_available_column_exists) {
                                                echo '<div class="form-check form-switch">
                                                    <input class="form-check-input availability-toggle" 
                                                           type="checkbox" 
                                                           data-driver-id="'.$row['driver_id'].'"
                                                          '.($is_available == 1 ? 'checked' : '').'>
                                                    <label class="form-check-label">
                                                        '.($is_available == 1 ? 'Available' : 'Busy').'
                                                    </label>
                                                </div>';
                                            } else {
                                                echo '<span class="availability-available">Available</span>';
                                            }
                                                
                                            echo '</td>
                                                <td data-label="Action">
                                                    <div class="action-buttons">
                                                        <a href="edit_driver.php?driver_id='.$row['driver_id'].'" class="btn btn-primary btn-sm btn-action">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>
                                                        <a href="all_drivers.php?delete_driver='.$row['driver_id'].'" class="btn btn-danger btn-sm btn-action" 
                                                           onclick="return confirm(\'Are you sure you want to delete this driver?\')">
                                                            <i class="fa fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="12" class="text-center">No drivers found</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile Card View -->
                        <div class="driver-card-view">
                            <?php
                            if(mysqli_num_rows($result) > 0) {
                                // Reset pointer and loop again for card view
                                mysqli_data_seek($result, 0);
                                while($row = mysqli_fetch_array($result)) {
                                    $profile_pic = getDriverProfileImage($row['profile_pic']);
                                    $is_available = $is_available_column_exists ? $row['is_available'] : 1;
                                    
                                    echo '
                                    <div class="driver-card">
                                        <div class="text-center">
                                            <img src="' . $profile_pic . '" alt="Profile" class="profile-img-card" 
                                                 onerror="this.src=\'../images/default-avatar.png\'">
                                            <h5>'.$row['full_name'].'</h5>
                                            <span class="'.($row['status'] == 1 ? 'status-active' : 'status-inactive').'">
                                                '.($row['status'] == 1 ? 'Active' : 'Inactive').'
                                            </span>
                                        </div>
                                        <div class="driver-info">
                                            <strong>ID:</strong> '.$row['driver_id'].'<br>
                                            <strong>Username:</strong> '.$row['username'].'<br>
                                            <strong>Email:</strong> '.$row['email'].'<br>
                                            <strong>Phone:</strong> '.$row['phone'].'<br>
                                            <strong>Address:</strong> '.substr($row['address'], 0, 50).'...<br>
                                            <strong>Plate No:</strong> '.$row['plate_number'].'<br>
                                            <strong>Vehicle:</strong> '.$row['vehicle_type'].'<br>
                                            <strong>License:</strong> '.$row['license_number'].'<br>
                                            <strong>Availability:</strong> 
                                            <span class="'.($is_available == 1 ? 'availability-available' : 'availability-busy').'">
                                                '.($is_available == 1 ? 'Available' : 'Busy').'
                                            </span>
                                        </div>
                                        <div class="action-buttons">
                                            <a href="edit_driver.php?driver_id='.$row['driver_id'].'" class="btn btn-primary btn-sm btn-action">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href="all_drivers.php?delete_driver='.$row['driver_id'].'" class="btn btn-danger btn-sm btn-action" 
                                               onclick="return confirm(\'Are you sure you want to delete this driver?\')">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>';
                                }
                            } else {
                                echo '
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h4>No Drivers Found</h4>
                                    <p>There are no delivery drivers in the system yet.</p>
                                    <a href="add_driver.php" class="btn btn-primary mt-3">
                                        <i class="fa fa-plus"></i> Add First Driver
                                    </a>
                                </div>';
                            }
                            ?>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="add_driver.php" class="btn btn-success btn-lg">
                                <i class="fa fa-plus"></i> Add New Driver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('includes/footer.php'); ?>
    
    <script>
    // Add data-labels for mobile table view
    document.addEventListener('DOMContentLoaded', function() {
        const tableCells = document.querySelectorAll('.table-view td');
        const headers = ['Profile', 'Full Name', 'Username', 'Email', 'Phone', 'Address', 'Plate Number', 'Vehicle Type', 'License Number', 'Status', 'Availability', 'Action'];
        
        tableCells.forEach((cell, index) => {
            const headerIndex = index % headers.length;
            cell.setAttribute('data-label', headers[headerIndex]);
        });

        // Availability toggle functionality
        const availabilityToggles = document.querySelectorAll('.availability-toggle');
        
        availabilityToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const driverId = this.getAttribute('data-driver-id');
                const isAvailable = this.checked ? 1 : 0;
                const label = this.nextElementSibling;
                
                fetch('update_driver_availability.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `driver_id=${driverId}&is_available=${isAvailable}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = isAvailable ? 'Available' : 'Busy';
                        
                        // Show success message
                        showToast('Driver availability updated successfully', 'success');
                    } else {
                        this.checked = !this.checked; // Revert toggle
                        showToast('Error updating availability', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !this.checked; // Revert toggle
                    showToast('Network error', 'error');
                });
            });
        });
        
        function showToast(message, type) {
            // Simple toast implementation
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    });
    </script>
</body>
</html>