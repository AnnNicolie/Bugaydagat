<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include("../connection/connect.php");

// Initialize error variable
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = md5($_POST['password']);
    
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
    
    if (!$db) {
        $login_error = "Database connection failed.";
    } else {
        $sql = "";
        
        if ($user_type === 'admin') {
            $sql = "SELECT adm_id, username, password FROM admin WHERE username = ?";
        } else if ($user_type === 'delivery') {
            // CORRECTED: Check delivery_drivers table
            $sql = "SELECT driver_id, username, password, full_name, status FROM delivery_drivers WHERE username = ? AND status = 1";
        } else {
            $login_error = "Please select a user type.";
        }
        
        if (!empty($sql)) {
            $stmt = $db->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    if ($user_type === 'admin') {
                        $stmt->bind_result($id, $db_username, $hashed_password);
                    } else if ($user_type === 'delivery') {
                        $stmt->bind_result($id, $db_username, $hashed_password, $full_name, $status);
                    }
                    
                    if ($stmt->fetch()) {
                        if ($password === $hashed_password) {
                            if ($user_type === 'admin') {
                                $_SESSION['adm_id'] = $id;
                                $_SESSION['username'] = $db_username;
                                header("location: dashboard.php");
                                exit;
                            } else if ($user_type === 'delivery') {
                                $_SESSION['driver_id'] = $id;
                                $_SESSION['driver_username'] = $db_username;
                                $_SESSION['driver_name'] = $full_name;
                                header("location: driver_dashboard.php");
                                exit;
                            }
                        } else {
                            $login_error = "Invalid username or password.";
                        }
                    }
                } else {
                    $login_error = "Invalid username or password, or account is inactive.";
                }
                $stmt->close();
            } else {
                $login_error = "Database error. Please try again.";
            }
        }
    }
}
?>

<head>
  <meta charset="UTF-8">
  <title>Login - Bugay Dagat</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900'>
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Montserrat:400,700'>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
  
  <style type="text/css">
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
        border-color: #2196f3;
        box-shadow: 0 0 8px rgba(33, 150, 243, 0.2);
        background: #ffffff;
    }

    .form input::placeholder {
        color: #9e9e9e;
    }

    .form button {
        font-family: "Roboto", sans-serif;
        text-transform: uppercase;
        outline: 0;
        background: #2196f3;
        width: 100%;
        border: 0;
        padding: 15px;
        color: #FFFFFF;
        font-size: 14px;
        transition: all 0.3 ease;
        cursor: pointer;
        border-radius: 6px;
        font-weight: bold;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .form button:hover,.form button:active,.form button:focus {
        background: #1976d2;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
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
        /* Removed border, shadow, and circular styling */
    }

    /* User Type Selection Styles */
    .user-type-selection {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 25px;
    }

    .user-type-option {
      text-align: center;
      padding: 20px;
      background-color: #f9f9f9;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 500;
      color: #666666;
      font-size: 16px;
      border-radius: 10px;
      border: 2px solid #eaeaea;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100px;
    }

    .user-type-option:hover {
      background-color: #e3f2fd;
      color: #1976d2;
      border-color: #2196f3;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(33, 150, 243, 0.15);
    }

    .user-type-option.active {
      background-color: #2196f3;
      color: white;
      border-color: #1976d2;
    }

    .option-icon {
      font-size: 32px;
      margin-bottom: 10px;
    }

    .option-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 5px;
    }

    .option-description {
      font-size: 12px;
      color: inherit;
      opacity: 0.8;
    }

    .admin-option {
      border-top: 4px solid #2196f3;
    }

    .delivery-option {
      border-top: 4px solid #4caf50;
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

    .info-message {
      background-color: #e3f2fd;
      color: #1565c0;
      border: 1px solid #2196f3;
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
        color: #2196f3;
        font-weight: 700;
    }
  </style>
</head>

<body>
    
    <div class="form">
        <div class="login-title"><span class="brand-highlight">Bugay Dagat</span> Portal</div>
       
        
        <div class="logo-container">
            <!-- Your logo without any blue circle styling -->
            <img src="images/logo6.png" alt="Bugay Dagat Logo" class="logo">
        </div>
        
        <!-- Display error messages -->
        <?php if (!empty($login_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <!-- User Type Selection -->
        <div class="user-type-selection">
            <!-- Admin Option - Updated with laptop icon -->
            <div class="user-type-option admin-option" onclick="window.location.href='admin_login.php'">
                <div class="option-icon">
                    <i class="fa fa-laptop"></i>
                </div>
                <div class="option-title">Admin Login</div>
                <div class="option-description">System Administrator Access</div>
            </div>
            
            <!-- Delivery Driver Option -->
            <div class="user-type-option delivery-option" onclick="window.location.href='driver_login.php'">
                <div class="option-icon">
                    <i class="fa fa-motorcycle"></i>
                </div>
                <div class="option-title">Delivery Driver Login</div>
                <div class="option-description">Click to login as delivery driver</div>
            </div>
        </div>

     
         
        </div>

    </div>
    
    <div class="watermark">Bugay Dagat Management System</div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    
    <script>
        // Add active class to clicked option
        $(document).ready(function() {
            $('.user-type-option').click(function() {
                $('.user-type-option').removeClass('active');
                $(this).addClass('active');
                
                // Add a small delay before redirecting for better UX
                setTimeout(function() {
                    // The actual redirect happens via the onclick attribute
                }, 300);
            });
        });
    </script>
</body>
</html>