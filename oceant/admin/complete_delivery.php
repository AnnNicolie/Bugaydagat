<?php
session_start();
include("../connection/connect.php");

// Check if driver is logged in
if(!isset($_SESSION['driver_id']) || empty($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $driver_id = $_SESSION['driver_id'];
    
    // Verify the order belongs to this driver and is in correct status
    $check_sql = "SELECT order_id FROM orders WHERE order_id = ? AND driver_id = ? AND status IN ('on the way', 'processing')";
    $check_stmt = mysqli_prepare($db, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $order_id, $driver_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if(mysqli_num_rows($check_result) > 0) {
        // Check if image is uploaded
        if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Please upload a delivery proof image.";
            header("Location: driver_dashboard.php");
            exit();
        }

        // Handle file upload
        $upload_dir = "../delivery_proofs/";
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $filename = "delivery_" . $order_id . "_" . time() . "." . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['proof_image']['type'];
        
        if(!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            header("Location: driver_dashboard.php");
            exit();
        }

        if(move_uploaded_file($_FILES['proof_image']['tmp_name'], $upload_path)) {
            // Insert into delivery_proofs table
            $insert_proof_sql = "INSERT INTO delivery_proofs (order_id, driver_id, image_path) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($db, $insert_proof_sql);
            mysqli_stmt_bind_param($insert_stmt, "iis", $order_id, $driver_id, $filename);
            $insert_success = mysqli_stmt_execute($insert_stmt);

            if (!$insert_success) {
                $_SESSION['error'] = "Failed to save delivery proof: " . mysqli_error($db);
                header("Location: driver_dashboard.php");
                exit();
            }

            // Update order status to delivered
            $update_sql = "UPDATE orders SET status = 'delivered' WHERE order_id = ?";
            $update_stmt = mysqli_prepare($db, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "i", $order_id);
            
            if(mysqli_stmt_execute($update_stmt)) {
                // Success message
                $_SESSION['success'] = "Order #" . $order_id . " has been marked as delivered successfully!";
                
                // Redirect to view_order.php with the order_id
                header("Location: ../admin/view_order.php?order_id=" . $order_id);
                exit();
            } else {
                $_SESSION['error'] = "Failed to update order status: " . mysqli_error($db);
                header("Location: driver_dashboard.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Failed to upload image.";
            header("Location: driver_dashboard.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Order not found or you don't have permission to complete this delivery.";
        header("Location: driver_dashboard.php");
        exit();
    }
} else {
    header("Location: driver_dashboard.php");
    exit();
}
?>