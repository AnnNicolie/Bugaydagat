<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

if(empty($_SESSION["adm_id"])) {
    header('location:index.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $driver_id = (int)$_POST['driver_id'];
    $is_available = (int)$_POST['is_available'];
    
    $sql = "UPDATE delivery_drivers SET is_available = ? WHERE driver_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $is_available, $driver_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($db)]);
    }
    exit();
}
?>