<?php
session_start();
include("connection/connect.php");

// Stock validation when adding to cart
if(isset($_GET['action']) && $_GET['action'] == "add" && isset($_POST['quantity'])) {
    $item_id = $_GET['id'];
    $quantity = (float)$_POST['quantity'];
    
    // Check current stock
    $stock_check = mysqli_query($db, "SELECT stock, title FROM seafoods WHERE d_id='$item_id'");
    $stock_data = mysqli_fetch_array($stock_check);
    
    if($quantity <= 0) {
        echo "<script>alert('Please enter a valid quantity!');</script>";
    } elseif($quantity > $stock_data['stock']) {
        echo "<script>alert('Not enough stock available for ".$stock_data['title']."! Maximum: ".$stock_data['stock']." kg');</script>";
    } else {
        // Proceed with adding to cart - your existing cart logic
        if(!empty($_POST["quantity"])) {
            $productByCode = mysqli_query($db, "SELECT * FROM seafoods WHERE d_id='$item_id'");
            $itemArray = mysqli_fetch_array($productByCode);
            
            if(!empty($_SESSION["cart_item"])) {
                if(array_key_exists($item_id, $_SESSION["cart_item"])) {
                    foreach($_SESSION["cart_item"] as $k => $v) {
                        if($item_id == $k) {
                            if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                $_SESSION["cart_item"][$k]["quantity"] = 0;
                            }
                            $_SESSION["cart_item"][$k]["quantity"] += $quantity;
                        }
                    }
                } else {
                    $_SESSION["cart_item"][$item_id] = array(
                        'title' => $itemArray["title"],
                        'd_id' => $item_id,
                        'quantity' => $quantity,
                        'price' => $itemArray["price"],
                        'img' => $itemArray["img"]
                    );
                }
            } else {
                $_SESSION["cart_item"][$item_id] = array(
                    'title' => $itemArray["title"],
                    'd_id' => $item_id,
                    'quantity' => $quantity,
                    'price' => $itemArray["price"],
                    'img' => $itemArray["img"]
                );
            }
        }
    }
}

// Remove item from cart
if(isset($_GET['action']) && $_GET['action'] == "remove") {
    if(!empty($_SESSION["cart_item"])) {
        foreach($_SESSION["cart_item"] as $k => $v) {
            if($_GET["id"] == $k)
                unset($_SESSION["cart_item"][$k]);
            if(empty($_SESSION["cart_item"]))
                unset($_SESSION["cart_item"]);
        }
    }
}

// Empty cart
if(isset($_GET['action']) && $_GET['action'] == "empty") {
    unset($_SESSION["cart_item"]);
}
?>