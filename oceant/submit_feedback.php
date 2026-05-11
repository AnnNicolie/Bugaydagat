<?php
include("connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(empty($_SESSION['user_id'])) {
    echo 'error: User not logged in';
    exit();
}

if($_POST) {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $rating = $_POST['rating'];
    $message = $_POST['message'];
    
    // Get user's name
    $user_query = mysqli_query($db, "SELECT username FROM users WHERE u_id='$user_id'");
    $user_data = mysqli_fetch_assoc($user_query);
    $user_name = $user_data['username'];
    
    // Insert feedback into database
    $sql = "INSERT INTO feedback (user_id, user_name, feedback_type, rating, message) 
            VALUES ('$user_id', '$user_name', '$type', '$rating', '$message')";
    
    if(mysqli_query($db, $sql)) {
        echo 'success';
    } else {
        echo 'error: ' . mysqli_error($db);
    }
} else {
    echo 'error: Invalid request';
}
?>