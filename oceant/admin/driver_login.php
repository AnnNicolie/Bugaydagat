<!DOCTYPE html>
<html lang="en">
<?php
    include("../connection/connect.php");
    error_reporting(0);
    session_start();

    // Initialize message variables
    $message = "";
    $success = "";

    if(isset($_POST['submit'])) {
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $password = mysqli_real_escape_string($db, $_POST['password']);
        
        if(!empty($username) && !empty($password)) {
            // Check delivery_drivers table
            $loginquery = "SELECT * FROM delivery_drivers WHERE username='$username' AND password='".md5($password)."' AND status=1";
            $result = mysqli_query($db, $loginquery);
            $row = mysqli_fetch_array($result);
            
            if(is_array($row)) {
                $_SESSION["driver_id"] = $row['driver_id'];
                $_SESSION["driver_username"] = $row['username'];
                $_SESSION["driver_name"] = $row['full_name'];
                header("Location: driver_dashboard.php");
                exit();
            } else {
                $message = "Invalid Username or Password!";
            }
        } else {
            $message = "Please enter both username and password!";
        }
    }
?>
<head>
    <meta charset="UTF-8">
    <title>Driver Login - Bugay Dagat</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/login.css">
    
    <style>
        body {
            background: #ffffff;
            color: #333333;
            font-family: 'RobotoDraft', 'Roboto', sans-serif;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.05) 0%, rgba(76, 175, 80, 0.05) 100%);
            z-index: -1;
        }

        .container {
            position: relative;
            max-width: 460px;
            width: 100%;
            margin: 0 auto 100px;
        }

        .info {
            padding: 20px;
            text-align: center;
        }

        .info h1 {
            font-weight: 400;
            margin-top: 0;
            color: #333333;
            font-size: 2.5rem;
        }

        .form {
            position: relative;
            z-index: 1;
            background: #ffffff;
            max-width: 400px;
            margin: 0 auto;
            padding: 40px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            border: 1px solid #eaeaea;
        }

        .form input {
            font-family: "Roboto", sans-serif;
            outline: 0;
            background: #f9f9f9;
            width: 100%;
            border: 0;
            margin: 0 0 15px;
            padding: 15px;
            box-sizing: border-box;
            font-size: 14px;
            color: #333333;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form input:focus {
            border-color: #4caf50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
            background: #ffffff;
        }

        .form input::placeholder {
            color: #9e9e9e;
        }

        .form button {
            font-family: "Roboto", sans-serif;
            text-transform: uppercase;
            outline: 0;
            background: #4caf50;
            width: 100%;
            border: 0;
            padding: 15px;
            color: #FFFFFF;
            font-size: 14px;
            -webkit-transition: all 0.3 ease;
            transition: all 0.3 ease;
            cursor: pointer;
            border-radius: 6px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .form button:hover,.form button:active,.form button:focus {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .form .message {
            margin: 15px 0 0;
            color: #666666;
            font-size: 12px;
        }

        .form .message a {
            color: #4caf50;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form .message a:hover {
            color: #45a049;
            text-decoration: underline;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 140px;
            height: auto;
            max-height: 140px;
            object-fit: contain;
            /* Clean logo without background, border, or shadow */
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            font-size: 14px;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #f44336;
        }

        .success {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #4caf50;
        }

        .brand-title {
            font-size: 2.8rem;
            font-weight: bold;
            color: #4caf50;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            background: linear-gradient(45deg, #45a049, #66bb6a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .login-title {
            color: #333333;
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .login-subtitle {
            color: #666666;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }
        
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            color: rgba(0, 0, 0, 0.2);
            font-size: 12px;
            z-index: -1;
        }
        
        .brand-highlight {
            color: #4caf50;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="form">
        <div class="login-title"><span class="brand-highlight">Bugay Dagat</span> - Driver Portal</div>
        <div class="login-subtitle">Delivery Driver Login</div>
        
        <div class="logo-container">
            <!-- Clean logo without background, border, or shadow -->
            <img src="images/logo6.png" alt="Bugay Dagat Logo" class="logo">
        </div>
        
        <!-- Display error messages -->
        <?php if (!empty($message)): ?>
            <div class="message error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <!-- Driver Login Form -->
        <form class="login-form" action="" method="post">
            <input type="text" placeholder="Username" name="username" required/>
            <input type="password" placeholder="Password" name="password" required/>
            <button type="submit" name="submit">Login as Driver</button>
        </form>
        
        <div class="message">
            <a href="index.php">← Back to Main Portal</a>
        </div>
    </div>
    
    <div class="watermark">Bugay Dagat Delivery System</div>
    
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
</body>
</html>