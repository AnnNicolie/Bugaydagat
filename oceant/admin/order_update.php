<?php
include("../connection/connect.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(strlen($_SESSION['adm_id'])==0) { 
    header('location:../login.php');
    exit();
}

if(isset($_POST['update'])) {
    $form_id = $_GET['form_id'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];
    
    // Get current status
    $query = mysqli_query($db, "SELECT status FROM users_orders WHERE o_id='$form_id'");
    $current_status = mysqli_fetch_assoc($query)['status'];
    
    // Only proceed if we have a valid form ID
    if(!empty($form_id)) {
        // Check if order_status_history table exists before trying to use it
        $table_check = mysqli_query($db, "SHOW TABLES LIKE 'order_status_history'");
        
        // Only update status history if table exists and status changed
        if(mysqli_num_rows($table_check) > 0 && $current_status != $status) {
            mysqli_query($db, "INSERT INTO order_status_history(order_id, status) VALUES('$form_id', '$status')");
        }
        
        // Store remark (assuming remark table exists)
        if(!empty($remark)) {
            mysqli_query($db, "INSERT INTO remark(frm_id, remark) VALUES('$form_id', '$remark')");
        }
        
        // Update main status
        mysqli_query($db, "UPDATE users_orders SET status='$status' WHERE o_id='$form_id'");
        
        echo "<script>
            alert('Order Details Updated Successfully');
            window.opener.location.reload();
            setTimeout(window.close, 1000);
        </script>";
    } else {
        echo "<script>alert('Invalid Order ID');</script>";
    }
}
?>

<script language="javascript" type="text/javascript"> 
function f2() {
    window.close();
}
function f3() {
    window.print(); 
}
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Order Update</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <style type="text/css" rel="stylesheet">
    .indent-small {
        margin-left: 5px;
    }
    .form-group.internal {
        margin-bottom: 0;
    }
    .dialog-panel {
        margin: 10px;
    }
    .datepicker-dropdown {
        z-index: 200 !important;
    }
    .panel-body {
        background: #e5e5e5;
        background: -moz-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%, #e5e5e5), color-stop(100%, #ffffff));
        background: -webkit-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
        background: -o-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
        background: -ms-radial-gradient(center, ellipse cover, #e5e5e5 0%, #ffffff 100%);
        background: radial-gradient(ellipse at center, #e5e5e5 0%, #ffffff 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#e5e5e5', endColorstr='#ffffff', GradientType=1);
        font: 600 15px "Open Sans", Arial, sans-serif;
    }
    label.control-label {
        font-weight: 600;
        color: #777;
    }
    table { 
        width: 650px; 
        border-collapse: collapse; 
        margin: auto;
        margin-top:50px;
    }
    tr:nth-of-type(odd) { 
        background: #eee; 
    }
    th { 
        background: #004684; 
        color: white; 
        font-weight: bold; 
    }
    td, th { 
        padding: 10px; 
        border: 1px solid #ccc; 
        text-align: left; 
        font-size: 14px;
    }
    </style>
</head>

<body>
    <div style="margin-left:50px;">
        <form name="updateticket" id="updatecomplaint" method="post"> 
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><b>Form Number</b></td>
                    <td><?php echo htmlentities($_GET['form_id']); ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><b>Status</b></td>
                    <td>
                        <select name="status" required="required">
                            <option value="">Select Status</option>
                            <option value="in process">On the way</option>
                            <option value="closed">Delivered</option>
                            <option value="rejected">Cancelled</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><b>Message</b></td>
                    <td><textarea name="remark" cols="50" rows="10" required="required"></textarea></td>
                </tr>
                <tr>
                    <td><b>Action</b></td>
                    <td>
                        <input type="submit" name="update" class="btn btn-primary" value="Submit">
                        <input type="button" class="btn btn-danger" value="Close" onclick="window.close();">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>