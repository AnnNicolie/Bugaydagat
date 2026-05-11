<!DOCTYPE html>
<html lang="en">
<?php
    include("../connection/connect.php");
    error_reporting(0);
    session_start();

    if(isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if(!empty($_POST["submit"])) {
            $loginquery = "SELECT * FROM drivers WHERE username='$username' && password='".md5($password)."' && status='active'";
            $result = mysqli_query($db, $loginquery);
            $row = mysqli_fetch_array($result);
            
            if(is_array($row)) {
                $_SESSION["driver_id"] = $row['driver_id'];
                $_SESSION["driver_name"] = $row['full_name'];
                $_SESSION["vehicle_number"] = $row['vehicle_number'];
                header("refresh:1;url=dashboard.php");
            } else {
                echo "<script>alert('Invalid Username or Password!');</script>"; 
            }
        }
    }
?>
<head>
    <meta charset="UTF-8">
    <title>Driver Login</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/login.css">
    <style>
        body {
            background: #f5f7fa;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 30px;
            font-weight: 300;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-btn:hover {
            background: #2980b9;
        }
        .demo-info {
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 5px;
            font-size: 14px;
            color: #2c3e50;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">DeliveryExpress</div>
            <h1 class="title">Driver Login</h1>
            
            <?php if(isset($error)) { ?>
                <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
            <?php } ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" name="submit" class="login-btn">Login</button>
            </form>
            
            <div class="demo-info">
                <strong>Demo Credentials:</strong><br>
                Username: driver1<br>
                Password: driver123
            </div>
            
            <a href="../index.html" class="back-link">← Back to Home</a>
        </div>
    </div>
</body>
</html>