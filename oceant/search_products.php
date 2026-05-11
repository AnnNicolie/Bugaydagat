<?php
include("connection/connect.php");
error_reporting(0);
session_start();

header('Content-Type: application/json');

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = mysqli_real_escape_string($db, $_GET['search']);
    
    $sql = "SELECT d.d_id as id, d.title, d.price, d.stock, d.img as image, 
                   c.title as category_name 
            FROM seafoods d 
            LEFT JOIN categories c ON d.rs_id = c.rs_id 
            WHERE d.title LIKE '%$searchTerm%' 
               OR d.slogan LIKE '%$searchTerm%'
               OR c.title LIKE '%$searchTerm%'
            ORDER BY d.title 
            LIMIT 10";
    
    $result = mysqli_query($db, $sql);
    $products = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = array(
            'id' => $row['id'],
            'title' => $row['title'],
            'price' => $row['price'],
            'stock' => $row['stock'],
            'image' => $row['image'],
            'category' => $row['category_name']
        );
    }
    
    echo json_encode($products);
} else {
    echo json_encode(array());
}
?>