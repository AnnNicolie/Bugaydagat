<!DOCTYPE html>
<html lang="en">
<?php
include("connection/connect.php");
error_reporting(0);
session_start();
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Seafoods</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 <style>
    /* Add this new CSS to hide the hero section when searching */
    .inner-page-hero.hidden {
        display: none;
    }
    
    .search-active .inner-page-hero {
        display: none;
    }

    /* Remove arrows from number input */
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .quantity-input {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    /* Smaller, cleaner seafood cards */
.seafood-card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #fff;
}

.seafood-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 15px rgba(255, 165, 0, 0.25);
}

.seafood-card img {
    height: 130px;
    object-fit: cover;
    border-bottom: 2px solid #ffa50033;
}

.seafood-card .card-body {
    padding: 10px;
}

.seafood-card .card-title {
    color: #ff8c00;
    font-weight: 600;
    font-size: 14px;
}

.seafood-card .card-text {
    font-size: 12px;
    color: #666;
}

.btn-purple.btn-sm {
    font-size: 13px;
    padding: 6px 10px;
    border-radius: 20px;
    font-weight: 600;
}

    /* Add this new CSS for logo separation */
    .navbar-brand {
        display: flex;
        align-items: center;
        height: 60px; /* Fixed height for the navbar brand area */
        padding: 3px -19px;
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
    
    /* Rest of your existing CSS remains the same */
    /* General Styles */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        padding-top: 80px; /* Reduced for fixed header */
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

    .search-input:focus {
        border-color: #ff8c00;
        box-shadow: 0 0 10px rgba(255, 140, 0, 0.3);
    }

    .search-input::placeholder {
        color: #888;
    }

    /* Top Links Styling */
    .top-links {
        background: linear-gradient(135deg, #ffd700, #ffa500);
        padding: 20px 0;
        margin-top: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .top-links .links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: space-around;
    }

    .top-links .link-item {
        text-align: center;
        padding: 15px;
        color: #333;
        font-weight: bold;
        position: relative;
        transition: all 0.3s;
    }

    .top-links .link-item:hover {
        transform: translateY(-5px);
    }

    .top-links .link-item.active span {
        background: #ff8c00;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-block;
        line-height: 35px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .top-links .link-item a {
        color: #333;
        text-decoration: none;
        font-weight: 600;
    }

    /* Hero Section */
    .inner-page-hero {
        background: linear-gradient(rgba(255, 215, 0, 0.7), rgba(255, 165, 0, 0.7)), url('images/img/2ndbackground.jpg') no-repeat center center/cover;
        height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #333;
        position: relative;
        border-radius: 10px;
        margin-top: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: opacity 0.3s ease, height 0.3s ease;
    }

    /* Restaurant Entries */
    .restaurant-entry {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0px 4px 15px rgba(255, 165, 0, 0.15);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-bottom: 30px;
        padding: 20px;
        border: 1px solid #f0f0f0;
    }

    .restaurant-entry:hover {
        transform: translateY(-5px);
        box-shadow: 0px 10px 20px rgba(255, 165, 0, 0.25);
    }

    .entry-logo img {
        width: 100px;
        height: auto;
        border-radius: 5px;
    }

    .entry-dscr h5 a {
        color: #ff8c00;
        font-size: 1.2rem;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-purple {
        background: linear-gradient(to right, #ffd700, #ffa500);
        color: #333;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: bold;
        border: none;
        display: block;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-purple:hover {
        background: linear-gradient(to right, #ffa500, #ff8c00);
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* Restaurant Cards */
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        height: 100%;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .card-body {
        padding: 20px;
    }

    .card-title {
        color: #ff8c00;
        font-weight: 600;
    }

    .card-footer {
        background: transparent;
        border-top: 1px solid #eee;
        padding: 15px 20px;
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

    .address, .additional-info {
        text-align: center;
        padding: 0 10px;
    }

    .footer a {
        color: #d35400;
        text-decoration: none;
        transition: color 0.3s;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .footer a:hover {
        color: #a84300;
        text-decoration: underline;
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

    /* Make About Us section wider */
    .address {
        flex: 0 0 30%;
        max-width: 30%;
    }

    .additional-info {
        flex: 0 0 65%;
        max-width: 65%;
    }

    /* Card-like styling for content areas */
    .address, .additional-info {
        background: rgba(255, 255, 255, 0.4);
        border-radius: 6px;
        padding: 12px;
        margin: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(3px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Navbar search styles */
    .navbar-search {
        display: flex;
        align-items: center;
        margin-left: 15px;
    }

    .nav-search-input {
        width: 180px;
        padding: 6px 12px;
        font-size: 14px;
        border: 1px solid #ffa500;
        border-radius: 20px;
        outline: none;
        transition: all 0.3s ease;
        background: white;
    }

    .nav-search-input:focus {
        width: 220px;
        border-color: #ff8c00;
        box-shadow: 0 0 8px rgba(255, 140, 0, 0.3);
    }

    .nav-search-input::placeholder {
        color: #888;
        font-size: 13px;
    }

    /* Search Results Styles - UPDATED TO REMOVE SLIDE AND FIX LAYOUT */
    .search-results-container {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        width: 450px;
        min-width: 100%;
        border: 1px solid #ffa500;
        border-top: none;
    }

    .search-result-item {
        display: grid;
        grid-template-columns: 60px 1fr auto auto;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s;
        gap: 10px;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        grid-column: 1;
    }

    .search-result-details {
        grid-column: 2;
        min-width: 150px;
    }

    .search-result-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 4px;
        font-size: 14px;
        line-height: 1.2;
    }

    .search-result-price {
        color: #ff8c00;
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .search-result-stock {
        font-size: 12px;
        color: #666;
    }

    .quantity-input {
        width: 80px;
        padding: 6px;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-align: center;
        font-size: 13px;
        grid-column: 3;
        /* Remove number input arrows */
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .add-to-cart-btn {
        background: linear-gradient(to right, #ffd700, #ffa500);
        color: #333;
        border: none;
        padding: 8px 12px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 13px;
        white-space: nowrap;
        grid-column: 4;
    }

    .add-to-cart-btn:hover:not(:disabled) {
        background: linear-gradient(to right, #ffa500, #ff8c00);
        color: #fff;
        transform: scale(1.05);
    }

    .add-to-cart-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .out-of-stock {
        color: #dc3545;
        font-weight: 600;
    }

    .no-results {
        padding: 20px;
        text-align: center;
        color: #666;
        font-style: italic;
    }

    /* Cart Styles */
    .cart-container {
        position: relative;
        margin-left: 15px;
    }

    .cart-icon {
        position: relative;
        display: flex;
        align-items: center;
        padding: 10px 15px !important;
        transition: all 0.3s ease;
    }

    .cart-icon:hover {
        background-color: rgba(255,255,255,0.2);
        border-radius: 5px;
    }

    .cart-count-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ff4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .cart-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        z-index: 1001;
        display: none;
        border: 1px solid #eee;
    }

    .cart-dropdown.show {
        display: block;
    }

    .cart-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background: linear-gradient(135deg, #ffd700, #ffa500);
        border-radius: 10px 10px 0 0;
    }

    .cart-header h5 {
        margin: 0;
        color: #333;
        font-weight: 600;
    }

    .cart-items {
        max-height: 300px;
        overflow-y: auto;
        padding: 10px 0;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border-bottom: 1px solid #f5f5f5;
        transition: background-color 0.3s;
    }

    .cart-item:hover {
        background-color: #f8f9fa;
    }

    .cart-item-details h6 {
        margin: 0 0 5px 0;
        color: #333;
        font-size: 14px;
    }

    .cart-item-details p {
        margin: 0;
        color: #666;
        font-size: 12px;
    }

    .cart-item-total {
        font-weight: 600;
        color: #ff8c00;
    }

    .empty-cart {
        padding: 30px 20px;
        text-align: center;
        color: #666;
        font-style: italic;
    }

    .cart-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        background: #f8f9fa;
        border-radius: 0 0 10px 10px;
    }

    .cart-total {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
        text-align: center;
    }

    .btn-view-cart, .btn-checkout {
        display: block;
        width: 100%;
        padding: 10px;
        text-align: center;
        border: none;
        border-radius: 5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        margin-bottom: 8px;
    }

    .btn-view-cart {
        background: #6c757d;
        color: white;
    }

    .btn-view-cart:hover {
        background: #5a6268;
        color: white;
        transform: translateY(-2px);
    }

    .btn-checkout {
        background: linear-gradient(to right, #ffd700, #ffa500);
        color: #333;
    }

    .btn-checkout:hover {
        background: linear-gradient(to right, #ffa500, #ff8c00);
        color: #fff;
        transform: translateY(-2px);
    }

    /* Header Alignment Styles */
    .navbar-nav.align-items-center {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
    }

    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
        margin: 0 5px;
    }

    /* Search Bar Styling */
    .search-nav-item {
        margin: 0 15px;
    }

    .navbar-search {
        position: relative;
    }

    /* Enhanced Cart Dropdown */
    .cart-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        width: 350px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        z-index: 1001;
        display: none;
        border: 1px solid #eee;
        max-height: 80vh;
        overflow: hidden;
    }

    .cart-dropdown.show {
        display: block;
    }

    .cart-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background: linear-gradient(135deg, #ffd700, #ffa500);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cart-header h5 {
        margin: 0;
        color: #333;
        font-weight: 600;
    }

    .close-cart {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #333;
    }

    .cart-items {
        max-height: 300px;
        overflow-y: auto;
        padding: 10px 0;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid #f5f5f5;
        transition: background-color 0.3s;
    }

    .cart-item:hover {
        background-color: #f8f9fa;
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-details h6 {
        margin: 0 0 5px 0;
        color: #333;
        font-size: 14px;
        font-weight: 600;
    }

    .cart-item-details p {
        margin: 0;
        color: #666;
        font-size: 12px;
    }

    .cart-item-price {
        font-weight: 600;
        color: #ff8c00;
        margin-left: 15px;
    }

    .cart-item-remove {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        margin-left: 10px;
        padding: 5px;
    }

    .cart-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
        background: #f8f9fa;
    }

    .cart-subtotal, .cart-shipping, .cart-total {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .cart-total {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        border-top: 1px solid #ddd;
        padding-top: 8px;
        margin-top: 8px;
    }

    .cart-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 15px;
    }

    .empty-cart {
        padding: 40px 20px;
        text-align: center;
        color: #666;
    }

    .empty-cart i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ccc;
    }

    /* Profile Dropdown Alignment */
    .profile-dropdown .dropdown-toggle {
        padding: 5px 10px !important;
    }

    .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: transform 0.3s;
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

    /* MOBILE SEARCH BAR STYLING */
    .mobile-search-container {
        display: none;
        padding: 10px 15px;
        background: rgba(255,255,255,0.2);
        border-top: 1px solid rgba(255,255,255,0.3);
    }

    .mobile-search {
        width: 100%;
    }

    .mobile-search .nav-search-input {
        width: 100% !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        body {
            padding-top: 160px;
        }
        
        .mobile-search-container {
            display: block;
        }
        
        /* Hide desktop search in mobile */
        .navbar-nav .search-nav-item {
            display: none !important;
        }
        
        .top-links .links {
            flex-direction: column;
        }
        
        .top-links .link-item {
            margin-bottom: 10px;
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
            height: 200px;
        }
        
        .navbar-nav {
            flex-direction: column;
            align-items: flex-start !important;
            width: 100%;
        }
        
        .navbar-nav .nav-item {
            margin: 5px 0;
            width: 100%;
        }
        
        .nav-search-input {
            width: 100%;
        }
        
        .cart-dropdown {
            position: fixed;
            top: 70px;
            right: 10px;
            left: 10px;
            width: auto;
        }

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
        
        /* Mobile search results adjustments */
        .search-results-container {
            width: 100%;
            left: 0;
            right: 0;
            position: fixed;
            top: 160px;
            max-height: calc(100vh - 160px);
        }
        
        .search-result-item {
            grid-template-columns: 50px 1fr;
            gap: 8px;
        }
        
        .quantity-input, .add-to-cart-btn {
            grid-column: 1 / span 2;
            width: 100%;
            margin-top: 5px;
        }
        
        .add-to-cart-btn {
            grid-column: 1 / span 2;
        }
        
        .nav-search-input {
            width: 100% !important;
            padding-right: 40px !important;
        }
        
        .nav-search-input:focus {
            width: 100% !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-search-container {
            display: none !important;
        }
        
        .navbar-nav .search-nav-item {
            display: flex !important;
        }
    }

    @media (max-width: 576px) {
        body {
            padding-top: 150px;
        }
        
        .cart-dropdown {
            width: 280px;
            right: 5px;
        }
    }

    /* Make search input wider */
    .nav-search-input {
        width: 250px;
        padding: 8px 40px 8px 15px;
        font-size: 14px;
        border: 1px solid #ffa500;
        border-radius: 20px;
        outline: none;
        transition: all 0.3s ease;
        background: white;
    }

    .nav-search-input:focus {
        width: 300px;
        border-color: #ff8c00;
        box-shadow: 0 0 8px rgba(255, 140, 0, 0.3);
    }

    /* Desktop specific adjustments */
    @media (min-width: 769px) {
        .navbar-search {
            position: relative;
        }
        
        .search-results-container {
            width: 450px;
            left: 0;
        }
    }

    /* Ensure search bar container is wide enough */
    .navbar-nav .search-nav-item {
        margin: 0 20px;
    }

    /* Mobile search container */
    .mobile-search-container {
        width: 100%;
    }

    .mobile-search-container .nav-search-input {
        width: 100% !important;
        padding-right: 40px !important;
    }

    /* Magnifying glass icon inside search bar */
    .search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #ff8c00;
        background: none;
        border: none;
        cursor: pointer;
        z-index: 2;
        font-size: 16px;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .search-icon:hover {
        color: #ff6b00;
    }

    /* Search container with icon */
    .search-with-icon {
        position: relative;
        display: inline-block;
    }

    .search-with-icon .nav-search-input {
        padding-right: 40px;
    }

    /* Remove dropdown arrow from profile */
    .nav-item.dropdown .dropdown-toggle::after {
        display: none !important;
    }

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
            
            <!-- Mobile Search Bar - Visible only on mobile -->
            <div class="mobile-search-container">
                <div class="navbar-search mobile-search">
                    <div class="search-with-icon" style="width: 100%;">
                        <input type="text" id="productSearchMobile" placeholder="Search Products..." class="nav-search-input">
                        <button type="button" class="search-icon">
                            <i class="fa fa-search"></i>
                        </button>
                        <div id="searchResultsMobile" class="search-results-container"></div>
                    </div>
                </div>
            </div>

            <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                <ul class="nav navbar-nav align-items-center">
                    <!-- Main Navigation Items -->
                    <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                    <li class="nav-item"> <a class="nav-link active" href="seafoods.php">Seafoods<span class="sr-only"></span></a> </li>
                    
                    <?php
                    if(empty($_SESSION["user_id"])) {
                        echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                              <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                    } else {
                        echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                    }
                    ?>

                    <!-- Desktop Search Bar - Visible only on desktop -->
                    <li class="nav-item search-nav-item">
                        <div class="navbar-search">
                            <div class="search-with-icon">
                                <input type="text" id="productSearch" placeholder="Search Products..." class="nav-search-input">
                                <button type="button" class="search-icon">
                                    <i class="fa fa-search"></i>
                                </button>
                                <div id="searchResults" class="search-results-container"></div>
                            </div>
                        </div>
                    </li>

                    <!-- Cart Icon -->
                    <li class="nav-item">
                        <div class="cart-container" style="position: relative;">
                            <a href="#" class="nav-link cart-icon" id="cartIcon">
                                <i class="fa fa-shopping-cart" style="font-size: 20px; color: #333;"></i>
                                <span class="cart-count-badge" id="cartCount">
                                    <?php 
                                    $cart_count = 0;
                                    if(isset($_SESSION['cart_item'])) {
                                        $cart_count = count($_SESSION['cart_item']);
                                    }
                                    echo $cart_count;
                                    ?>
                                </span>
                            </a>
                            <div class="cart-dropdown" id="cartDropdown">
                                <div class="cart-header">
                                    <h5>Your Shopping Cart</h5>
                                    <button class="close-cart" id="closeCart">&times;</button>
                                </div>
                                <div class="cart-items" id="cartItems">
                                    <?php
                                    if(isset($_SESSION['cart_item']) && !empty($_SESSION['cart_item'])) {
                                        foreach($_SESSION['cart_item'] as $item) {
                                            echo '
                                            <div class="cart-item">
                                                <div class="cart-item-details">
                                                    <h6>'.$item['title'].'</h6>
                                                    <p>'.$item['quantity'].' kg × ₱'.$item['price'].'</p>
                                                </div>
                                                <div class="cart-item-total">
                                                    ₱'.number_format($item['quantity'] * $item['price'], 2).'
                                                </div>
                                            </div>';
                                        }
                                    } else {
                                        echo '<div class="empty-cart">Your cart is empty</div>';
                                    }
                                    ?>
                                </div>
                                <div class="cart-footer">
                                    <div class="cart-subtotal">
                                        <span>Subtotal:</span>
                                        <span id="cartSubtotal">₱
                                            <?php
                                            $total = 0;
                                            if(isset($_SESSION['cart_item'])) {
                                                foreach($_SESSION['cart_item'] as $item) {
                                                    $total += ($item['price'] * $item['quantity']);
                                                }
                                            }
                                            echo number_format($total, 2);
                                            ?>
                                        </span>
                                    </div>
                                    <div class="cart-shipping">
                                        <span>Shipping:</span>
                                        <span id="cartShipping">Free</span>
                                    </div>
                                    <div class="cart-total">
                                        <span>Total:</span>
                                        <span id="cartTotal">₱
                                            <?php
                                            echo number_format($total, 2);
                                            ?>
                                        </span>
                                    </div>
                                    <div class="cart-actions">
                                        <a href="available.php" class="btn btn-view-cart">View Full Cart</a>
                                        <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <?php
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
                </ul>
            </div>
        </div>
    </nav>
</header>
        
<div class="inner-page-hero bg-image" data-image-src="images/img/2ndbackground.jpg" id="heroSection">
    <div class="container"> </div>
</div>

<section class="restaurants-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <?php 
            $ress = mysqli_query($db, "SELECT * FROM categories ORDER BY c_id");
            while($rows = mysqli_fetch_array($ress)) {
                echo '
                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                    <div class="card seafood-card text-center shadow-sm">
                        <img class="card-img-top" src="admin/Res_img/'.$rows['image'].'" alt="'.$rows['title'].'">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 text-truncate">'.$rows['title'].'</h6>
                            <p class="card-text small text-muted mb-1 text-truncate">'.$rows['address'].'</p>
                            <small class="text-muted">🕒 '.$rows['o_hr'].' - '.$rows['c_hr'].'</small>
                        </div>
                        <div class="card-footer p-2 border-0 bg-transparent">
                            <a href="available.php?res_id='.$rows['rs_id'].'" class="btn btn-sm btn-purple w-100">View</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

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
        
<script src="js/jquery.min.js"></script>
<script src="js/tether.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/animsition.min.js"></script>
<script src="js/bootstrap-slider.min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/headroom.js"></script>
<script src="js/foodpicky.min.js"></script>

<script>
// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    const cartIcon = document.getElementById('cartIcon');
    const cartDropdown = document.getElementById('cartDropdown');
    const cartCount = document.getElementById('cartCount');
    const closeCart = document.getElementById('closeCart');
    const searchInput = document.getElementById('productSearch');
    const searchResults = document.getElementById('searchResults');
    const searchInputMobile = document.getElementById('productSearchMobile');
    const searchResultsMobile = document.getElementById('searchResultsMobile');
    const searchIcons = document.querySelectorAll('.search-icon');
    const heroSection = document.getElementById('heroSection');
    let searchTimeout;

    // Initialize cart
    initializeCart();

    // Cart dropdown toggle
    cartIcon.addEventListener('click', function(e) {
        e.preventDefault();
        cartDropdown.classList.toggle('show');
        if (cartDropdown.classList.contains('show')) {
            refreshCartDropdown();
        }
        searchResults.style.display = 'none';
        searchResultsMobile.style.display = 'none';
    });

    // Close cart dropdown
    closeCart.addEventListener('click', function() {
        cartDropdown.classList.remove('show');
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.cart-container') && !event.target.closest('.cart-dropdown')) {
            cartDropdown.classList.remove('show');
        }
        if (!event.target.closest('.navbar-search')) {
            searchResults.style.display = 'none';
            searchResultsMobile.style.display = 'none';
        }
    });

    // Search functionality for desktop
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();

        if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(() => {
                fetchSearchResults(searchTerm, searchResults);
            }, 300);
        } else {
            searchResults.style.display = 'none';
        }
    });

    // Search functionality for mobile
    searchInputMobile.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim();

        if (searchTerm.length >= 2) {
            searchTimeout = setTimeout(() => {
                fetchSearchResults(searchTerm, searchResultsMobile);
            }, 300);
        } else {
            searchResultsMobile.style.display = 'none';
        }
    });

    // Search icon click functionality
    searchIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const searchContainer = this.closest('.search-with-icon');
            const input = searchContainer.querySelector('.nav-search-input');
            const resultsContainer = searchContainer.querySelector('.search-results-container');
            
            if (input.value.trim().length >= 2) {
                fetchSearchResults(input.value.trim(), resultsContainer);
            } else {
                // If search term is too short, just focus the input
                input.focus();
            }
        });
    });

    // Prevent hiding when clicking inside search results
    searchResults.addEventListener('click', function(event) {
        event.stopPropagation();
    });
    
    searchResultsMobile.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Function to fetch search results
    function fetchSearchResults(searchTerm, resultsContainer) {
        fetch(`search_products.php?search=${encodeURIComponent(searchTerm)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data, resultsContainer);
            })
            .catch(error => {
                console.error('Error fetching search results:', error);
                resultsContainer.innerHTML = '<div class="no-results">Error loading results</div>';
                resultsContainer.style.display = 'block';
            });
    }

    // Function to display search results
    function displaySearchResults(products, resultsContainer) {
        if (products.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">No products found</div>';
            resultsContainer.style.display = 'block';
            return;
        }

        let html = '';
        products.forEach(product => {
            const isOutOfStock = product.stock <= 0;
            html += `
                <div class="search-result-item">
                    <img src="admin/Res_img/dishes/${product.image}" alt="${product.title}" class="search-result-image">
                    <div class="search-result-details">
                        <div class="search-result-title">${product.title}</div>
                        <div class="search-result-price">₱${product.price} / kg</div>
                        <div class="search-result-stock ${isOutOfStock ? 'out-of-stock' : ''}">
                            ${isOutOfStock ? 'Out of Stock' : `In Stock: ${product.stock} kg`}
                        </div>
                    </div>
                    ${!isOutOfStock ? `
                        <input type="number" class="quantity-input" 
                               min="0.01" max="${product.stock}" step="0.01" 
                               value="0" placeholder="Kilos" data-product-id="${product.id}">
                        <button class="add-to-cart-btn" 
                                data-product-id="${product.id}" 
                                data-product-price="${product.price}"
                                data-product-title="${product.title}"
                                data-product-image="${product.image}">
                            Add to Cart
                        </button>
                    ` : `
                        <button class="add-to-cart-btn" disabled>Out of Stock</button>
                    `}
                </div>
            `;
        });

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';

        // Add event listeners to add to cart buttons
        resultsContainer.querySelectorAll('.add-to-cart-btn:not(:disabled)').forEach(button => {
            button.addEventListener('click', addToCart);
        });
    }

    // Function to handle add to cart
    function addToCart(event) {
        const button = event.target;
        const productId = button.getAttribute('data-product-id');
        const productPrice = parseFloat(button.getAttribute('data-product-price'));
        const productTitle = button.getAttribute('data-product-title');
        const productImage = button.getAttribute('data-product-image');
        const quantityInput = button.parentElement.querySelector('.quantity-input');
        const quantity = parseFloat(quantityInput.value);

        if (!quantity || quantity <= 0) {
            alert('Please enter a valid quantity');
            return;
        }

        // Add to cart via AJAX
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}&quantity=${quantity}&price=${productPrice}&title=${encodeURIComponent(productTitle)}&image=${encodeURIComponent(productImage)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showCartNotification('Product added to cart!');
                
                // Update cart count
                updateCartCount(data.cart_count);
                
                // Refresh cart dropdown if open
                if (cartDropdown.classList.contains('show')) {
                    refreshCartDropdown();
                }
                
                // Hide search results
                searchResults.style.display = 'none';
                searchResultsMobile.style.display = 'none';
                searchInput.value = '';
                searchInputMobile.value = '';
            } else {
                alert('Error adding to cart: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding to cart');
        });
    }

    // Function to initialize cart
    function initializeCart() {
        updateCartCount();
    }

    // Function to update cart count
    function updateCartCount() {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                cartCount.textContent = data.count;
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
    }

    // Function to refresh cart dropdown content
    function refreshCartDropdown() {
        fetch('get_cart_content.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cartItems').innerHTML = data.items_html;
                document.getElementById('cartSubtotal').textContent = data.subtotal;
                document.getElementById('cartTotal').textContent = data.total;
            })
            .catch(error => {
                console.error('Error refreshing cart:', error);
            });
    }

    // Function to show cart notification
    function showCartNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Add CSS for notification animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
});

// Function to remove item from cart
function removeCartItem(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh cart dropdown
            refreshCartDropdown();
            // Update cart count
            updateCartCount();
        } else {
            alert('Error removing item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing item from cart');
    });
}

// Confirm logout function
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}
</script>
</body>
</html>