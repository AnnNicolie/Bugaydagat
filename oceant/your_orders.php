<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(empty($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

// Initialize variables
$message = '';
$result = null;

// Success message handling
if(isset($_GET['del_success'])) {
    $message = $_GET['del_success'] == 1 ? 
        '<div class="alert alert-success">Order cancelled successfully!</div>' : 
        '<div class="alert alert-danger">Failed to cancel order</div>';
}

// Initialize read messages session array
if(!isset($_SESSION['read_messages'])) {
    $_SESSION['read_messages'] = [];
}
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>My Orders</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <style>
    /* Add this new CSS for logo separation */
    .navbar-brand {
        display: flex;
        align-items: center;
        height: 60px; /* Fixed height for the navbar brand area */
        padding: 3px 0px;
    }
    
     
    .logo-container {
        display: flex;
        align-items: center;
        height: 145%; /* Adjusted for mobile */
    }
    
    .logo-img {
        object-fit: contain;
        max-height: 111%;
        width: auto;
        transition: all 0.3s ease;
        border-radius: 8px;
        max-width: 150px; /* Limit logo width */
    }
    

    /* Logo container styling */
    .logo-container {
        flex-shrink: 0;
        margin-top: 5px;
    }

    /* Guimbal logo styling */
    .guimbal-logo {
        width: 70px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Adjust address section for logo */
    .address .d-flex {
        align-items: flex-start;
    }

    /* Responsive adjustments for logo */
    @media (max-width: 768px) {
        .guimbal-logo {
            width: 60px;
        }
        
        .address .d-flex {
            flex-direction: column;
            text-align: center;
        }
        
        .logo-container {
            margin: 0 auto 15px;
        }
    }

    /* REST OF YOUR EXISTING STYLES REMAIN EXACTLY THE SAME - ONLY HEADER/FOOTER UPDATED */
    /* General Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        padding-top: 80px; /* Reduced from 140px */
    }

    /* Fixed Header Styling */
    .header-scroll {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1030;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    /* Navbar Styling */
    .navbar {
        background: linear-gradient(135deg, #ffd700, #ffa500) !important;
        padding: 10px 0;
    }

    .navbar-nav .nav-link {
        color: #333 !important;
        font-weight: 500;
        font-size: 16px;
        padding: 10px 15px;
        transition: all 0.3s ease-in-out;
        border-radius: 5px;
        margin: 0 5px;
    }

    .navbar-nav .nav-link:hover {
        color: #fff !important;
        background-color: rgba(255,255,255,0.2);
    }

    .navbar-nav .nav-item .active {
        background-color: rgba(255,255,255,0.3);
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

    /* Profile Dropdown Styling */
    .nav-item .dropdown-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .profile-pic {
        width: 50px;
        height: 30px;
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

    /* Page Title Styling */
    .page-title {
        color: #333;
        font-weight: 700;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 3px solid #ffd700;
        display: inline-block;
        font-size: 2rem;
    }

    /* Hero Section */
    .inner-page-hero {
        background: linear-gradient(rgba(255, 215, 0, 0.7), rgba(255, 165, 0, 0.7)), url('images/img/background2.jpg') no-repeat center center/cover;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #333;
        position: relative;
    }

    /* Footer Styling */
    .footer {
        background: linear-gradient(135deg, #ffd700, #ffa500) !important;
        color: #5a3700;
        padding: 25px 0 12px;
        position: relative;
        width: 100%;
        margin-top: 60px;
        box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px 10px 0 0;
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

    /* Table Styling - Keeping your original table styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 20px 0;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(255, 165, 0, 0.1);
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
    }

    .table thead {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        color: #333;
        position: sticky;
        top: 0;
    }

    .table th {
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #ffd700;
    }

    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr {
        transition: background-color 0.3s;
    }

    .table tbody tr:hover {
        background-color: #fff9e6;
    }

    /* Status Labels */
    .label {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        color: white;
    }

    .label-info { 
        background: linear-gradient(to right, #3498db, #2980b9); 
    }
    .label-warning { 
        background: linear-gradient(to right, #f39c12, #e67e22); 
    }
    .label-success { 
        background: linear-gradient(to right, #27ae60, #2ecc71); 
    }
    .label-danger { 
        background: linear-gradient(to right, #e74c3c, #c0392b); 
    }

    /* Buttons */
    .btn {
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-danger {
        background: linear-gradient(to right, #e74c3c, #c0392b);
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(to right, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
    }

    .btn-default {
        background: #f0f0f0;
        color: #666;
        border: 1px solid #ddd;
    }

    .btn-default:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Alerts */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 12px 15px;
        margin: 15px 0;
    }

    .alert-success {
        background: linear-gradient(to right, #d4edda, #c3e6cb);
        color: #155724;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background: linear-gradient(to right, #f8d7da, #f5c6cb);
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .alert-info {
        background: linear-gradient(to right, #d1ecf1, #bee5eb);
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    /* Notification Badge */
    .notification-badge {
        background: linear-gradient(to right, #e74c3c, #c0392b);
        color: white;
        border-radius: 50%;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: bold;
        margin-left: 5px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* NOTIFICATION CELL - FIXED WIDTH AND OVERLAPPING ISSUE */
    .notification-cell {
        min-width: 150px; /* Increased minimum width */
        max-width: 180px; /* Increased maximum width */
        text-align: center;
        padding: 8px 12px !important; /* Added padding to prevent text touching borders */
    }

    .notification-toggle {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        border: none;
        border-radius: 15px;
        padding: 6px 12px; /* Increased padding */
        color: #333;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px; /* Increased minimum width */
        font-size: 0.75rem; /* Slightly increased font size */
        white-space: nowrap;
        height: 32px; /* Increased height */
        box-sizing: border-box;
        position: relative;
    }

    .notification-toggle:hover {
        background: linear-gradient(135deg, #ffa500, #ff8c00);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(255, 165, 0, 0.2);
    }

    .notification-toggle i {
        margin-right: 5px; /* Increased spacing between icon and text */
        font-size: 0.8rem; /* Slightly larger icon */
    }

    /* FEEDBACK BUBBLE STYLES */
    .feedback-bubble {
        position: fixed;
        right: 20px;
        bottom: 20px;
        z-index: 1000;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .feedback-bubble:hover {
        transform: scale(1.05);
    }

    .cloud-bubble {
        background: white;
        border-radius: 50px;
        padding: 12px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        border: 2px solid #ffd700;
    }

    .cloud-bubble:after {
        content: '';
        position: absolute;
        bottom: -10px;
        right: 20px;
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 15px solid white;
        filter: drop-shadow(0 4px 4px rgba(0,0,0,0.1));
    }

    .cloud-bubble:before {
        content: '';
        position: absolute;
        bottom: -12px;
        right: 18px;
        width: 0;
        height: 0;
        border-left: 12px solid transparent;
        border-right: 12px solid transparent;
        border-top: 17px solid #ffd700;
        z-index: -1;
    }

    .feedback-text {
        font-weight: 600;
        color: #333;
        font-size: 14px;
        margin: 0;
    }

    .feedback-icon {
        margin-right: 8px;
        color: #ffa500;
        font-size: 16px;
    }

    /* FEEDBACK MODAL STYLES */
    .feedback-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }

    .feedback-modal.active {
        display: flex;
    }

    .feedback-content {
        background: white;
        border-radius: 10px;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
    }

    .feedback-header {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        padding: 15px 20px;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        border-bottom: 1px solid #ff8c00;
    }

    .feedback-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .close-feedback {
        background: none;
        border: none;
        font-size: 20px;
        color: #333;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border-radius: 50%;
    }

    .close-feedback:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .feedback-body {
        padding: 20px;
        max-height: calc(90vh - 70px);
        overflow-y: auto;
        flex-grow: 1;
        background: #fafafa;
    }

    .feedback-form .form-group {
        margin-bottom: 15px;
    }

    .feedback-form label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }

    .feedback-form input,
    .feedback-form textarea,
    .feedback-form select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: 'Poppins', sans-serif;
    }

    .feedback-form input:focus,
    .feedback-form textarea:focus,
    .feedback-form select:focus {
        border-color: #ffa500;
        box-shadow: 0 0 0 2px rgba(255, 165, 0, 0.2);
        outline: none;
    }

    .feedback-form textarea {
        min-height: 120px;
        resize: vertical;
    }

    .rating-stars {
        display: flex;
        gap: 5px;
        margin-bottom: 10px;
    }

    .star {
        font-size: 24px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star.active {
        color: #ffa500;
    }

    .submit-feedback {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        color: #333;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
        margin-top: 10px;
    }

    .submit-feedback:hover {
        background: linear-gradient(135deg, #ffa500, #ff8c00);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 165, 0, 0.3);
    }

    /* Messages Modal - Ultra Compact Version */
    .messages-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 10px;
    }

    .messages-modal.active {
        display: flex;
    }

    .messages-content {
        background: white;
        border-radius: 10px;
        width: 96%;
        max-width: 600px;
        max-height: 80vh;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        display: flex;
        flex-direction: column;
    }

    .messages-header {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        padding: 12px 15px;
        color: #333;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
        border-bottom: 1px solid #ff8c00;
    }

    .messages-header h4 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .close-messages {
        background: none;
        border: none;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        border-radius: 50%;
    }

    .close-messages:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .messages-body {
        padding: 10px;
        max-height: calc(80vh - 50px);
        overflow-y: auto;
        flex-grow: 1;
        background: #fafafa;
    }

    .message-item {
        background: #ffffff;
        border-radius: 6px;
        padding: 8px;
        margin-bottom: 8px;
        border-left: 2px solid #ffa500;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid #e9ecef;
    }

    .message-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 1px 5px rgba(0,0,0,0.1);
    }

    .message-item:last-child {
        margin-bottom: 0;
    }

    .message-item.unread {
        background: #fff9e6;
        border-left-color: #ff8c00;
        border: 1px solid #ffd700;
        box-shadow: 0 1px 4px rgba(255, 140, 0, 0.1);
    }

    .message-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
        padding-bottom: 3px;
        border-bottom: 1px solid #e9ecef;
    }

    .message-sender {
        font-weight: 600;
        color: #ff8c00;
        font-size: 0.75rem;
    }

    .message-time {
        color: #6c757d;
        font-size: 0.65rem;
        font-weight: 500;
    }

    .message-content {
        color: #333;
        line-height: 1.2;
        font-size: 0.65rem;
        word-wrap: break-word;
        white-space: pre-wrap;
        max-height: 80px;
        overflow-y: auto;
        padding: 1px 0;
    }

    /* Scrollbar for individual message content */
    .message-content::-webkit-scrollbar {
        width: 2px;
    }

    .message-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 1px;
    }

    .message-content::-webkit-scrollbar-thumb {
        background: #ffa500;
        border-radius: 1px;
    }

    .message-content::-webkit-scrollbar-thumb:hover {
        background: #ff8c00;
    }

    .no-messages {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 15px 10px;
        font-size: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        margin: 8px 0;
    }

    /* Scrollbar styling for messages body */
    .messages-body::-webkit-scrollbar {
        width: 4px;
    }

    .messages-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }

    .messages-body::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        border-radius: 2px;
    }

    .messages-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #ffa500, #ff8c00);
    }

    /* Responsive adjustments for the modal and notification button */
    @media (max-width: 768px) {
        .messages-modal {
            padding: 5px;
        }
        
        .messages-content {
            width: 98%;
            max-width: none;
            max-height: 75vh;
            border-radius: 8px;
        }
        
        .messages-header {
            padding: 10px 12px;
        }
        
        .messages-header h4 {
            font-size: 0.9rem;
        }
        
        .messages-body {
            padding: 8px;
            max-height: calc(75vh - 45px);
        }
        
        .message-item {
            padding: 6px;
            margin-bottom: 6px;
        }
        
        .message-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }
        
        .message-sender {
            font-size: 0.7rem;
        }
        
        .message-time {
            font-size: 0.6rem;
        }
        
        .message-content {
            font-size: 0.6rem;
            line-height: 1.15;
            max-height: 60px;
        }
        
        .notification-toggle {
            min-width: 110px;
            padding: 5px 10px;
            font-size: 0.7rem;
            height: 28px;
        }
        
        .notification-cell {
            min-width: 130px;
            max-width: 150px;
        }
        
        .notification-badge {
            padding: 0px 4px;
            font-size: 0.55rem;
            margin-left: 2px;
            min-width: 14px;
            height: 14px;
        }
        
        /* Feedback bubble responsive */
        .feedback-bubble {
            right: 15px;
            bottom: 15px;
        }
        
        .cloud-bubble {
            padding: 10px 16px;
            min-width: 110px;
        }
        
        .feedback-text {
            font-size: 13px;
        }
        
        .feedback-icon {
            font-size: 14px;
            margin-right: 6px;
        }
        
        .feedback-content {
            max-width: 95%;
        }
        
        .feedback-body {
            padding: 15px;
        }
    }

    @media (max-width: 576px) {
        .messages-content {
            border-radius: 6px;
        }
        
        .messages-header {
            padding: 8px 10px;
        }
        
        .messages-header h4 {
            font-size: 0.85rem;
        }
        
        .close-messages {
            font-size: 14px;
            width: 18px;
            height: 18px;
        }
        
        .messages-body {
            padding: 6px;
        }
        
        .message-item {
            padding: 5px;
            margin-bottom: 5px;
        }
        
        .message-content {
            font-size: 0.58rem;
            line-height: 1.1;
            max-height: 50px;
        }
        
        .notification-toggle {
            min-width: 100px;
            padding: 4px 8px;
            font-size: 0.65rem;
            height: 26px;
        }
        
        .notification-cell {
            min-width: 120px;
            max-width: 140px;
        }
        
        .notification-toggle i {
            font-size: 0.65rem;
            margin-right: 3px;
        }
        
        .notification-badge {
            padding: 0px 3px;
            font-size: 0.5rem;
            min-width: 12px;
            height: 12px;
        }
        
        .no-messages {
            padding: 10px 8px;
            font-size: 0.7rem;
        }
        
        /* Feedback bubble mobile */
        .feedback-bubble {
            right: 10px;
            bottom: 10px;
        }
        
        .cloud-bubble {
            padding: 8px 14px;
            min-width: 100px;
        }
        
        .feedback-text {
            font-size: 12px;
        }
        
        .feedback-icon {
            font-size: 12px;
            margin-right: 5px;
        }
        
        .feedback-header {
            padding: 12px 15px;
        }
        
        .feedback-header h4 {
            font-size: 1.1rem;
        }
        
        .feedback-body {
            padding: 12px;
        }
        
        .star {
            font-size: 20px;
        }
    }

    /* For very small screens */
    @media (max-width: 380px) {
        .messages-modal {
            padding: 3px;
        }
        
        .messages-content {
            max-height: 70vh;
            border-radius: 5px;
        }
        
        .messages-body {
            max-height: calc(70vh - 40px);
            padding: 5px;
        }
        
        .message-content {
            font-size: 0.55rem;
            max-height: 40px;
            line-height: 1.05;
        }
        
        .notification-toggle {
            min-width: 90px;
            padding: 3px 6px;
            font-size: 0.6rem;
            height: 24px;
        }
        
        .notification-cell {
            min-width: 110px;
            max-width: 120px;
        }
        
        .messages-header {
            padding: 6px 8px;
        }
        
        .messages-header h4 {
            font-size: 0.8rem;
        }
        
        /* Feedback bubble extra small */
        .feedback-bubble {
            right: 8px;
            bottom: 8px;
        }
        
        .cloud-bubble {
            padding: 6px 12px;
            min-width: 90px;
        }
        
        .feedback-text {
            font-size: 11px;
        }
        
        .feedback-icon {
            font-size: 11px;
            margin-right: 4px;
        }
    }

    /* For extremely small screens */
    @media (max-width: 320px) {
        .messages-content {
            max-height: 65vh;
        }
        
        .messages-body {
            max-height: calc(65vh - 35px);
            padding: 4px;
        }
        
        .message-item {
            padding: 4px;
            margin-bottom: 4px;
        }
        
        .message-content {
            font-size: 0.5rem;
            max-height: 35px;
        }
        
        .notification-toggle {
            min-width: 85px;
            padding: 2px 5px;
            font-size: 0.55rem;
            height: 22px;
        }
        
        .notification-cell {
            min-width: 100px;
            max-width: 110px;
        }
        
        .notification-badge {
            padding: 0px 2px;
            font-size: 0.45rem;
            min-width: 10px;
            height: 10px;
        }
        
        /* Feedback bubble extremely small */
        .cloud-bubble {
            padding: 5px 10px;
            min-width: 80px;
        }
        
        .feedback-text {
            font-size: 10px;
        }
        
        .feedback-icon {
            display: none; /* Hide icon on very small screens */
        }
    }

    /* Make notification column responsive */
    @media (max-width: 768px) {
        body {
            padding-top: 160px; /* Adjust for mobile header height */
        }
        
        .table-responsive {
            margin: 10px 0;
        }
        
        .table th, .table td {
            padding: 8px;
            font-size: 14px;
        }
        
        .notification-cell:before {
            content: 'Notifications';
            position: absolute;
            left: 12px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #ff8c00;
        }
        
        .notification-toggle {
            min-width: 110px;
            padding: 5px 10px;
            font-size: 0.7rem;
        }
        
        .messages-content {
            width: 95%;
            margin: 10px;
        }
        
        .address, .additional-info {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 15px;
        }
        
        .footer .col-sm-6 {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .footer h5::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .address, .additional-info {
            margin: 3px;
            padding: 10px;
        }
        
        .footer h5 {
            font-size: 1rem;
        }
        
        .footer p {
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .footer {
            padding: 20px 0 10px;
        }
        
        .inner-page-hero {
            height: 150px;
        }
    }

    @media (max-width: 576px) {
        .table thead {
            display: none;
        }
        
        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }
        
        .table tr {
            margin-bottom: 15px;
            border: 1px solid #ffd700;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table td {
            text-align: right;
            padding-left: 50%;
            position: relative;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .table td:before {
            content: attr(data-label);
            position: absolute;
            left: 12px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #ff8c00;
        }
        
        .notification-cell:before {
            white-space: normal;
        }
        
        .notification-toggle {
            min-width: 100px;
            padding: 4px 8px;
            font-size: 0.65rem;
        }
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
    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                <a class="navbar-brand" href="index.php">
                    <div class="logo-container">
                        <img class="logo-img" src="images/logo6.png" alt="Guimbal Seafoods Logo">
                    </div>
                </a>
                <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="seafoods.php">Seafoods</a>
                        </li>
                        <?php
                        if (empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link">Login</a> </li>
                                  <li class="nav-item"><a href="registration.php" class="nav-link">Register</a> </li>';
                        } else {
                            echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                        }
                        ?>
                      <!-- Profile Dropdown -->
<li class="nav-item dropdown">
   <?php
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
                </div>
            </div>
        </nav>
    </header>


        <section class="restaurants-page">
            <div class="container">
                <!-- Added page title in upper left -->
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">My Orders</h2>
                    </div>
                </div>
                
                <?php if(!empty($message)) echo $message; ?>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity (kg)</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                                <th>Shipping Fee</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th class="notification-cell">Notifications</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // First, let's check what columns exist in the remark table
                            // For now, let's use a simpler query that doesn't rely on created_at
                            $query = "SELECT 
                                o.order_id,
                                o.order_date,
                                o.status,
                                o.shipping_fee,
                                o.grand_total,
                                (SELECT GROUP_CONCAT(oi.product_name SEPARATOR '|') 
                                 FROM order_items oi 
                                 WHERE oi.order_id = o.order_id) AS product_names,
                                (SELECT GROUP_CONCAT(oi.quantity SEPARATOR '|') 
                                 FROM order_items oi 
                                 WHERE oi.order_id = o.order_id) AS quantities,
                                (SELECT GROUP_CONCAT(oi.price SEPARATOR '|') 
                                 FROM order_items oi 
                                 WHERE oi.order_id = o.order_id) AS prices,
                                (SELECT SUM(oi.subtotal) 
                                 FROM order_items oi 
                                 WHERE oi.order_id = o.order_id) AS items_subtotal,
                                (SELECT GROUP_CONCAT(r.remark SEPARATOR '||||') 
                                 FROM remark r 
                                 WHERE r.frm_id = o.order_id 
                                 ORDER BY r.id DESC) AS remarks
                                FROM orders o
                                WHERE o.u_id = ?
                                ORDER BY o.order_date DESC";

                            $stmt = $db->prepare($query);
                            $stmt->bind_param("i", $_SESSION['user_id']);
                            
                            if ($stmt->execute()) {
                                $result = $stmt->get_result();
                                
                                if ($result->num_rows === 0) {
                                    echo '<tr><td colspan="10" class="text-center"><div class="alert alert-info">You have no orders yet.</div></td></tr>';
                                } else {
                                    while ($row = $result->fetch_assoc()) {
                                        $products = !empty($row['product_names']) ? explode('|', $row['product_names']) : [];
                                        $quantities = !empty($row['quantities']) ? explode('|', $row['quantities']) : [];
                                        $prices = !empty($row['prices']) ? explode('|', $row['prices']) : [];
                                        $remarksData = !empty($row['remarks']) ? explode('||||', $row['remarks']) : [];
                                        
                                        // Process remarks to count unread messages
                                        $unreadCount = 0;
                                        $allMessages = [];
                                        
                                        foreach ($remarksData as $index => $remarkItem) {
                                            $message = trim($remarkItem);
                                            
                                            if (!empty($message) && !preg_match('/Order received|Processing your request/i', $message)) {
                                                // Create a more unique and persistent message ID
                                                $messageId = 'order_' . $row['order_id'] . '_msg_' . $index . '_' . md5($message . $row['order_date']);
                                                $isRead = isset($_SESSION['read_messages'][$row['order_id']][$messageId]);
                                                
                                                $allMessages[] = [
                                                    'content' => $message,
                                                    'time' => $row['order_date'],
                                                    'read' => $isRead,
                                                    'id' => $messageId
                                                ];
                                                
                                                if (!$isRead) {
                                                    $unreadCount++;
                                                }
                                            }
                                        }
                                        
                                        echo '<tr>';
                                        echo '<td data-label="Item">';
                                        foreach ($products as $product) {
                                            echo htmlspecialchars($product) . '<br>';
                                        }
                                        echo '</td>';
                                        
                                        echo '<td data-label="Quantity">';
                                        foreach ($quantities as $qty) {
                                            echo htmlspecialchars($qty) . '<br>';
                                        }
                                        echo '</td>';
                                        
                                        echo '<td data-label="Price">';
                                        foreach ($prices as $price) {
                                            echo '₱' . htmlspecialchars($price) . '<br>';
                                        }
                                        echo '</td>';
                                        
                                        echo '<td data-label="Subtotal">₱' . number_format($row['items_subtotal'] ?? 0, 2) . '</td>';
                                        echo '<td data-label="Shipping Fee">₱' . number_format($row['shipping_fee'] ?? 0, 2) . '</td>';
                                        echo '<td data-label="Total Amount">₱' . number_format($row['grand_total'] ?? 0, 2) . '</td>';
                                        
                                        // Status display
                                        echo '<td data-label="Status">';
                                        switch(strtolower($row['status'])) {
                                            case "in process":
                                            case "on the way":
                                                echo '<span class="label label-warning">On The Way</span>';
                                                break;
                                            case "closed":
                                                echo '<span class="label label-success">Completed</span>';
                                                break;
                                            case "rejected":
                                            case "cancelled":
                                                echo '<span class="label label-danger">Cancelled</span>';
                                                break;
                                            default:
                                                echo '<span class="label label-info">' . ucfirst($row['status']) . '</span>';
                                        }
                                        echo '</td>';
                                        
                                        // Notification button with count
                                        echo '<td class="notification-cell" data-label="Notifications">';
                                        echo '<button class="notification-toggle view-messages" 
                                                data-order-id="' . $row['order_id'] . '" 
                                                data-messages=\'' . htmlspecialchars(json_encode($allMessages), ENT_QUOTES, 'UTF-8') . '\'>';
                                        echo '<i class="fa fa-bell"></i> Notifications';
                                        if ($unreadCount > 0) {
                                            echo '<span class="notification-badge">' . $unreadCount . '</span>';
                                        }
                                        echo '</button>';
                                        echo '</td>';
                                        
                                        echo '<td data-label="Date">' . date('M d, Y h:i A', strtotime($row['order_date'])) . '</td>';
                                        
                                        // Cancel button section
                                        echo '<td data-label="Action">';
                                        if (strtolower($row['status']) === 'pending') {
                                            echo '<a href="cancel_order.php?order_id='.$row['order_id'].'" 
                                                 onclick="return confirm(\'Are you sure you want to cancel this order?\')" 
                                                 class="btn btn-danger btn-flat btn-sm">
                                                 <i class="fa fa-times"></i> Cancel</a>';
                                        } else {
                                            echo '<button class="btn btn-default btn-sm" disabled>
                                                  <i class="fa fa-ban"></i> Cannot Cancel</button>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                            } else {
                                echo '<tr><td colspan="10" class="text-center"><div class="alert alert-danger">Error fetching orders: ' . $db->error . '</div></td></tr>';
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Feedback Bubble -->
        <div class="feedback-bubble">
            <div class="cloud-bubble">
                <i class="fa fa-comment feedback-icon"></i>
                <p class="feedback-text">Feedback</p>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div class="feedback-modal" id="feedbackModal">
            <div class="feedback-content">
                <div class="feedback-header">
                    <h4>Send Feedback</h4>
                    <button class="close-feedback" id="closeFeedback">&times;</button>
                </div>
                <div class="feedback-body">
                    <form class="feedback-form" id="feedbackForm">
                        <div class="form-group">
                            <label for="feedbackType">Feedback Type</label>
                            <select id="feedbackType" name="feedbackType" required>
                                <option value="">Select type</option>
                                <option value="suggestion">Suggestion</option>
                                <option value="bug">Bug Report</option>
                                <option value="complaint">Complaint</option>
                                <option value="praise">Praise</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="feedbackRating">Rating</label>
                            <div class="rating-stars" id="ratingStars">
                                <span class="star" data-rating="1">★</span>
                                <span class="star" data-rating="2">★</span>
                                <span class="star" data-rating="3">★</span>
                                <span class="star" data-rating="4">★</span>
                                <span class="star" data-rating="5">★</span>
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label for="feedbackMessage">Your Feedback</label>
                            <textarea id="feedbackMessage" name="message" placeholder="Please share your thoughts about our system..." required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-feedback">
                            <i class="fa fa-paper-plane"></i> Send Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Messages Modal -->
        <div class="messages-modal" id="messagesModal">
            <div class="messages-content">
                <div class="messages-header">
                    <h4>Order Messages</h4>
                    <button class="close-messages" id="closeMessages">&times;</button>
                </div>
                <div class="messages-body" id="messagesBody">
                    <!-- Messages will be loaded here -->
                </div>
            </div>
        </div>

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
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Open messages modal
            $('.view-messages').click(function() {
                const orderId = $(this).data('order-id');
                const messages = $(this).data('messages');
                const $button = $(this);
                
                // Display messages in modal
                const messagesBody = $('#messagesBody');
                messagesBody.empty();
                
                if (messages && messages.length > 0) {
                    let unreadMessageIds = [];
                    let hasUnread = false;
                    
                    messages.forEach((msg, index) => {
                        const messageHtml = `
                            <div class="message-item ${msg.read ? '' : 'unread'}">
                                <div class="message-meta">
                                    <span class="message-sender">Admin</span>
                                    <span class="message-time">${formatDate(msg.time)}</span>
                                </div>
                                <div class="message-content">${msg.content}</div>
                            </div>
                        `;
                        messagesBody.append(messageHtml);
                        
                        if (!msg.read) {
                            hasUnread = true;
                            unreadMessageIds.push(msg.id);
                        }
                    });
                    
                    // Mark messages as read via AJAX if there are unread ones
                    if (hasUnread && unreadMessageIds.length > 0) {
                        $.post('mark_notifications_read.php', { 
                            order_id: orderId,
                            message_ids: unreadMessageIds  // Send specific message IDs
                        }).done(function(response) {
                            if (response === 'success') {
                                // Remove badge from button
                                $button.find('.notification-badge').remove();
                                
                                // Update the button data to reflect read status
                                const updatedMessages = messages.map(msg => {
                                    if (unreadMessageIds.includes(msg.id)) {
                                        return {...msg, read: true};
                                    }
                                    return msg;
                                });
                                $button.data('messages', updatedMessages);
                                
                                // Also update the session by refreshing the page after a short delay
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }).fail(function() {
                            console.error('Failed to mark messages as read');
                        });
                    }
                } else {
                    messagesBody.html('<div class="no-messages">No messages available for this order.</div>');
                }
                
                // Show modal
                $('#messagesModal').addClass('active');
            });
            
            // Close modal
            $('#closeMessages').click(function() {
                $('#messagesModal').removeClass('active');
            });
            
            // Close modal when clicking outside
            $('#messagesModal').click(function(e) {
                if (e.target === this) {
                    $(this).removeClass('active');
                }
            });
            
            // Format date function
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
            
            // FEEDBACK BUBBLE FUNCTIONALITY
            $('.feedback-bubble').click(function() {
                $('#feedbackModal').addClass('active');
            });
            
            $('#closeFeedback').click(function() {
                $('#feedbackModal').removeClass('active');
            });
            
            $('#feedbackModal').click(function(e) {
                if (e.target === this) {
                    $(this).removeClass('active');
                resetFeedbackForm();
                }
            });
            
            // Star rating functionality
            $('.star').click(function() {
                const rating = $(this).data('rating');
                $('#ratingValue').val(rating);
                
                // Update stars appearance
                $('.star').each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            });
            
            // Hover effect for stars
            $('.star').hover(
                function() {
                    const rating = $(this).data('rating');
                    $('.star').each(function() {
                        if ($(this).data('rating') <= rating) {
                            $(this).addClass('active');
                        }
                    });
                },
                function() {
                    const currentRating = $('#ratingValue').val();
                    $('.star').each(function() {
                        if ($(this).data('rating') > currentRating) {
                            $(this).removeClass('active');
                        }
                    });
                }
            );
            
        
           // Feedback form submission
$('#feedbackForm').submit(function(e) {
    e.preventDefault();
    
    const formData = {
        type: $('#feedbackType').val(),
        rating: $('#ratingValue').val(),
        message: $('#feedbackMessage').val()
    };
    
    // Submit feedback via AJAX
    $.post('submit_feedback.php', formData)
        .done(function(response) {
            if (response === 'success') {
                alert('Thank you for your feedback! We appreciate your input.');
                resetFeedbackForm();
                $('#feedbackModal').removeClass('active');
            } else {
                alert('There was an error submitting your feedback. Please try again.');
            }
        })
        .fail(function() {
            alert('There was an error submitting your feedback. Please try again.');
        });
});
            
            function resetFeedbackForm() {
                $('#feedbackForm')[0].reset();
                $('#ratingValue').val('0');
                $('.star').removeClass('active');
            }
        });
    </script>
</body>
</html>