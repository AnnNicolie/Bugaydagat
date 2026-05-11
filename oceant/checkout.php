<?php
// Enable error reporting at the top
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include("connection/connect.php");
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

function function_alert() { 
    echo "<script>alert('Thank you. Your Order has been placed!');</script>"; 
    echo "<script>window.location.replace('your_orders.php');</script>"; 
}

// Check if user is logged in
if(empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit;
}

// Check if cart is empty
if(empty($_SESSION["cart_item"])) {
    header('location:seafoods.php');
    exit;
}

// Initialize variables
$item_total = 0;
$shipping_fee = 0;
$delivery_time = '3-5 days';
$user_address = []; // Initialize empty array

// Calculate order total
if(!empty($_SESSION["cart_item"])) {
    foreach ($_SESSION["cart_item"] as $item) {
        $item_total += ($item["price"] * $item["quantity"]);
    }
}

// Get user's address
$user_query = mysqli_query($db, "SELECT address, municipality, barangay FROM users WHERE u_id='".$_SESSION['user_id']."'");
if($user_query && mysqli_num_rows($user_query) > 0) {
    $user_address = mysqli_fetch_assoc($user_query);
    
    // Calculate shipping
    if(!empty($user_address['municipality'])) {
        $rate_query = mysqli_query($db, "SELECT base_fee FROM iloilo_shipping_rates 
             WHERE municipality = '".mysqli_real_escape_string($db, $user_address['municipality'])."'");
        
        if($rate_query && mysqli_num_rows($rate_query) > 0) {
            $rate = mysqli_fetch_assoc($rate_query);
            $shipping_fee = $rate['base_fee'] ?? 0;
        }
    }
} else {
    die("User address not found. Please update your profile.");
}

$grand_total = $item_total + $shipping_fee;

// Process order if submitted
if(isset($_POST['submit'])) {
    $payment_method = $_POST['mod'] ?? 'COD';
    $payment_reference = ($payment_method == 'gcash') ? ($_POST['gcash_reference'] ?? '') : '';
    
    // Validate GCash reference if GCash is selected
    if($payment_method == 'gcash') {
        if(empty($payment_reference)) {
            die("GCash reference number is required");
        }
        if(strlen($payment_reference) != 13 || !ctype_digit($payment_reference)) {
            die("GCash reference number must be 13 digits");
        }
        
        // Validate uploaded file
        if(isset($_FILES['gcash_screenshot']) && $_FILES['gcash_screenshot']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['gcash_screenshot']['type'];
            $file_size = $_FILES['gcash_screenshot']['size'];
            
            if(!in_array($file_type, $allowed_types)) {
                die("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
            }
            
            if($file_size > 5 * 1024 * 1024) { // 5MB limit
                die("File size too large. Maximum size is 5MB.");
            }
        } else {
            die("GCash screenshot is required for GCash payments.");
        }
    }
    
    // Start transaction
    mysqli_begin_transaction($db);
    
    try {
        // Prepare variables for binding
        $user_id = $_SESSION['user_id'];
        $municipality = $user_address['municipality'] ?? '';
        $barangay = $user_address['barangay'] ?? '';
        $status = 'pending'; // Default status
        
        // Debug: Check if we have all required data
        if(empty($user_id) || empty($municipality) || empty($barangay)) {
            throw new Exception("Missing required user information");
        }
        
        // 1. Insert the main order
        $order_stmt = $db->prepare("INSERT INTO orders (
            u_id,
            order_date,
            status,
            payment_method,
            payment_reference,
            shipping_fee,
            grand_total,
            delivery_location,
            barangay
        ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)");
        
        if(!$order_stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        
        $order_stmt->bind_param("isssddss", 
            $user_id,
            $status,
            $payment_method,
            $payment_reference,
            $shipping_fee,
            $grand_total,
            $municipality,
            $barangay
        );
        
        if(!$order_stmt->execute()) {
            throw new Exception("Order creation failed: " . $order_stmt->error);
        }
        
        $order_id = $db->insert_id;
        
        // 2. Handle GCash screenshot upload
        if($payment_method == 'gcash' && isset($_FILES['gcash_screenshot'])) {
            $upload_dir = "payment_proofs/";
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['gcash_screenshot']['name'], PATHINFO_EXTENSION);
            $file_name = "gcash_proof_" . $order_id . "_" . time() . "." . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if(move_uploaded_file($_FILES['gcash_screenshot']['tmp_name'], $file_path)) {
                // Insert payment proof record
                $proof_stmt = $db->prepare("INSERT INTO payment_proofs (order_id, image_path) VALUES (?, ?)");
                $proof_stmt->bind_param("is", $order_id, $file_path);
                
                if(!$proof_stmt->execute()) {
                    throw new Exception("Failed to save payment proof: " . $proof_stmt->error);
                }
                $proof_stmt->close();
            } else {
                throw new Exception("Failed to upload payment screenshot.");
            }
        }
        
        // 3. Insert all order items
        $item_stmt = $db->prepare("INSERT INTO order_items (
            order_id,
            product_id,
            product_name,
            quantity,
            price,
            subtotal
        ) VALUES (?, ?, ?, ?, ?, ?)");
        
        if(!$item_stmt) {
            throw new Exception("Prepare failed for order items: " . $db->error);
        }
        
        // 4. DEDUCT STOCK FROM PRODUCTS
        $stock_stmt = $db->prepare("UPDATE seafoods SET stock = stock - ? WHERE d_id = ?");
        
        if(!$stock_stmt) {
            throw new Exception("Prepare failed for stock update: " . $db->error);
        }
        
        foreach ($_SESSION["cart_item"] as $item) {
            $product_id = $item["d_id"];
            $product_name = $item["title"];
            $quantity = $item["quantity"];
            $price = $item["price"];
            $subtotal = $price * $quantity;
            
            // Insert order item
            $item_stmt->bind_param("iisddd",
                $order_id,
                $product_id,
                $product_name,
                $quantity,
                $price,
                $subtotal
            );
            
            if(!$item_stmt->execute()) {
                throw new Exception("Order item failed: " . $item_stmt->error);
            }
            
            // DEDUCT STOCK FROM PRODUCT
            $stock_stmt->bind_param("di", $quantity, $product_id);
            if(!$stock_stmt->execute()) {
                throw new Exception("Stock update failed: " . $stock_stmt->error);
            }
            
            // Ensure stock doesn't go below zero
            $fix_stock_sql = "UPDATE seafoods SET stock = 0 WHERE d_id = ? AND stock < 0";
            $fix_stmt = $db->prepare($fix_stock_sql);
            $fix_stmt->bind_param("i", $product_id);
            $fix_stmt->execute();
            $fix_stmt->close();
        }
        
        // Close prepared statements
        $item_stmt->close();
        $stock_stmt->close();
        $order_stmt->close();

        // Add initial order confirmation remark
        $remark_text = "Your order was submitted. Thank you for ordering!";
        $remark_stmt = $db->prepare("INSERT INTO remark (frm_id, status, remark, remarkDate) VALUES (?, 'pending', ?, NOW())");
        if($remark_stmt) {
            $remark_stmt->bind_param("is", $order_id, $remark_text);
            if(!$remark_stmt->execute()) {
                error_log("Failed to add initial remark: " . $remark_stmt->error);
            }
            $remark_stmt->close();
        }

        // Commit transaction
        mysqli_commit($db);

        // Clear cart and redirect
        unset($_SESSION["cart_item"]);
        function_alert();
        exit;
        
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($db);
        error_log("Order processing error: " . $e->getMessage());
        echo "<script>alert('Order processing failed: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Checkout</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .checkout-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .payment-option {
            margin: 25px 0;
            padding: 20px 0;
            border-top: 1px solid #eee;
        }
        #gcash-payment-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            display: none;
        }
        .qr-code {
            max-width: 200px;
            margin: 15px auto;
            display: block;
        }
        .payment-instructions {
            background: #e9f7fe;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .file-upload {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
        }
        .file-upload:hover {
            border-color: #007bff;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin: 10px auto;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container checkout-container">
        <h2 class="mb-4">Checkout</h2>
        
        <form action="" method="post" enctype="multipart/form-data">
            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-truck"></i> Delivery Information
                </div>
                <div class="card-body">
                    <?php if(!empty($user_address)): ?>
                        <p><strong>Address:</strong> <?= htmlspecialchars($user_address['address']) ?></p>
                        <p><strong>Municipality:</strong> <?= htmlspecialchars($user_address['municipality']) ?></p>
                        <p><strong>Barangay:</strong> <?= htmlspecialchars($user_address['barangay'] ?? '') ?></p>
                        <p><strong>Estimated Delivery:</strong> <?= htmlspecialchars($delivery_time) ?></p>
                        <a href="edit_profile.php" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit"></i> Update Address
                        </a>
                    <?php else: ?>
                        <div class="alert alert-danger">Please complete your address in your profile</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-receipt"></i> Order Summary
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION["cart_item"] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item["title"]) ?></td>
                                <td class="text-right"><?= $item["quantity"] ?> kg</td>
                                <td class="text-right">₱<?= number_format($item["price"], 2) ?></td>
                                <td class="text-right">₱<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Subtotal</strong></td>
                                <td class="text-right">₱<?= number_format($item_total, 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Shipping Fee</strong></td>
                                <td class="text-right">₱<?= number_format($shipping_fee, 2) ?></td>
                            </tr>
                            <tr class="table-active">
                                <td colspan="3" class="text-right"><strong>Total Amount</strong></td>
                                <td class="text-right"><strong>₱<?= number_format($grand_total, 2) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="payment-option">
                <h4><i class="fa fa-credit-card"></i> Payment Method</h4>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="mod" id="cod" value="COD" checked>
                    <label class="form-check-label" for="cod">
                        Cash on Delivery (COD)
                    </label>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="radio" name="mod" id="gcash" value="gcash">
                    <label class="form-check-label" for="gcash">
                        <img src="images/Gcash.png" alt="GCash" style="height: 60px;">
                    </label>
                </div>
                
                <!-- GCash Payment Details -->
                <div id="gcash-payment-details">
                    <div class="payment-instructions">
                        <h5><i class="fa fa-mobile"></i> GCash Payment Instructions</h5>
                        <ol>
                            <li>Send payment to <strong>09066096962 (Ocean Table)</strong></li>
                            <li>Amount: <strong>₱<?= number_format($grand_total, 2) ?></strong></li>
                            <li>Take a screenshot of your payment confirmation</li>
                            <li>Upload the screenshot and enter the 13-digit reference number below</li>
                            <li>Your order will be processed after payment verification</li>
                        </ol>
                        <img src="images/qr.jpg" alt="GCash QR Code" class="qr-code img-fluid">
                    </div>
                    
                    <div class="form-group">
                        <label for="gcash_reference">GCash Reference Number (13 digits):</label>
                        <input type="text" class="form-control" name="gcash_reference" 
                               placeholder="Enter 13-digit reference number" maxlength="13" pattern="\d{13}"
                               title="Please enter the 13-digit GCash reference number">
                        <small class="form-text text-muted">Example: 1234567890123</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="gcash_screenshot">Upload Payment Screenshot:</label>
                        <div class="file-upload">
                            <input type="file" class="form-control-file" name="gcash_screenshot" id="gcash_screenshot" 
                                   accept="image/*" required>
                            <small class="form-text text-muted">Supported formats: JPG, JPEG, PNG, GIF (Max: 5MB)</small>
                            <img id="preview" class="preview-image img-thumbnail" alt="Preview">
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="submit" class="btn btn-success btn-lg btn-block mt-4">
                    <i class="fa fa-check-circle"></i> Confirm Order
                </button>
            </div>
        </form>
    </div>

    <script src="js/jquery.min.js"></script>
   <script>
$(document).ready(function() {
    // Toggle GCash payment details
    $('input[name="mod"]').change(function() {
        if($('#gcash').is(':checked')) {
            $('#gcash-payment-details').slideDown();
            // Add required attributes for GCash
            $('input[name="gcash_reference"]').prop('required', true);
            $('input[name="gcash_screenshot"]').prop('required', true);
        } else {
            $('#gcash-payment-details').slideUp();
            // Remove required attributes for COD
            $('input[name="gcash_reference"]').prop('required', false);
            $('input[name="gcash_screenshot"]').prop('required', false);
        }
    });
    
    // Image preview
    $('#gcash_screenshot').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Form validation - FIXED for COD
    $('form').submit(function(e) {
        if($('#gcash').is(':checked')) {
            var ref = $('input[name="gcash_reference"]').val();
            if(ref.length !== 13 || !/^\d+$/.test(ref)) {
                alert('Please enter a valid 13-digit GCash reference number');
                e.preventDefault();
                return false;
            }
            
            var file = $('input[name="gcash_screenshot"]').val();
            if(!file) {
                alert('Please upload a screenshot of your GCash payment');
                e.preventDefault();
                return false;
            }
        }
        
        // For COD, just show confirmation
        if(!confirm('Are you sure you want to place this order?')) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>
</body>
</html>