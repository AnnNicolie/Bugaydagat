<?php
include("../connection/connect.php");
error_reporting(0);
session_start();

mysqli_query($db,"DELETE FROM seafoods WHERE d_id = '".$_GET['menu_del']."'");
header("location:all_available.php");  

?>
