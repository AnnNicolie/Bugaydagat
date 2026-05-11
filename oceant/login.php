<!DOCTYPE html>
<html lang="en">
  <?php
session_start();
include("connection/connect.php"); // Include your database connection

// Initialize error variable
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = md5($_POST['password']); // Using md5 to match your registration

    // Check if database connection is working
    if (!$db) {
        $login_error = "Database connection failed.";
    } else {
        $sql = "SELECT u_id, username, password, status FROM users WHERE username = ?";
        $stmt = $db->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $db_username, $hashed_password, $status);
                if ($stmt->fetch()) {
                    // Check if account is active (status = 1)
                    if ($status != 1) {
                        $login_error = "Your account is deactivated. Please contact support.";
                    }
                    // Compare the MD5 hashes
                    else if ($password === $hashed_password) {
                        // Login successful
                        $_SESSION['user_id'] = $id;
                        $_SESSION['username'] = $db_username;
                        header("location: index.php"); // Redirect to home page
                        exit;
                    } else {
                        $login_error = "Invalid username or password.";
                    }
                }
            } else {
                $login_error = "Invalid username or password.";
            }
            $stmt->close();
        } else {
            $login_error = "Database error. Please try again.";
        }
    }
}
?>

<head>
  <meta charset="UTF-8">
  <title>Login - Seafood Market</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel='stylesheet prefetch' href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style type="text/css">
    /* General Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #fff9e6;
      color: #333;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar Styling */
    .navbar {
      background: linear-gradient(135deg, #ffd700, #ffa500) !important;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .navbar-brand img {
      width: 120px;
      height: auto;
      border-radius: 10px;
    }

    .navbar-nav .nav-link {
      color: #333 !important;
      font-weight: bold;
      font-size: 16px;
      padding: 10px 15px;
      transition: all 0.3s ease-in-out;
    }

    .navbar-nav .nav-link:hover {
      color: #fff !important;
      background-color: rgba(255,255,255,0.2);
      border-radius: 5px;
    }

    .navbar-nav .nav-item .active {
      background-color: rgba(255,255,255,0.3);
      border-radius: 5px;
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

    /* Main Content */
    .main-content {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
      background: linear-gradient(rgba(255, 215, 0, 0.1), rgba(255, 165, 0, 0.1));
    }

    /* Login Container */
    .login-container {
      background: white;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(255, 165, 0, 0.2);
      width: 100%;
      max-width: 450px;
      overflow: hidden;
    }

    .login-header {
      background: linear-gradient(135deg, #ffd700, #ffa500);
      padding: 80px 20px 40px; 
      text-align: center;
      color: white;
    }

    .login-header h2 {
      margin: 0;
      font-size: 28px;
      font-weight: 700;
    }

    .login-header p {
      margin: 5px 0 0;
      opacity: 0.9;
      font-size: 16px;
    }

    .login-body {
      padding: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      padding: 12px 15px;
      font-size: 16px;
      transition: all 0.3s;
      width: 100%;
      box-sizing: border-box;
    }

    .form-control:focus {
      border-color: #ffa500;
      box-shadow: 0 0 0 0.2rem rgba(255, 165, 0, 0.25);
    }

    .input-icon {
      position: relative;
    }

    .input-icon i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #ffa500;
    }

    .input-icon input {
      padding-left: 45px;
    }

    .btn-login {
      background: linear-gradient(to right, #ffd700, #ffa500);
      border: none;
      color: #333;
      padding: 12px;
      font-size: 18px;
      font-weight: 600;
      border-radius: 8px;
      width: 100%;
      transition: all 0.3s;
      margin-top: 10px;
      cursor: pointer;
    }

    .btn-login:hover {
      background: linear-gradient(to right, #ffa500, #ff8c00);
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 165, 0, 0.4);
    }

    .login-footer {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }

    .login-footer a {
      color: #ff8c00;
      text-decoration: none;
      font-weight: 500;
    }

    .login-footer a:hover {
      text-decoration: underline;
    }

    .message {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      font-weight: 500;
    }

    .error {
      background-color: #ffe6e6;
      color: #d63031;
      border: 1px solid #ff9999;
    }

    .success {
      background-color: #e6ffe6;
      color: #00b894;
      border: 1px solid #99cc99;
    }

    /* Footer Styles */
    .footer {
      background: linear-gradient(135deg, #ffd700, #ffb347);
      color: #5a3700;
      padding: 25px 0 12px;
      box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.1);
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

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .main-content {
        padding: 20px 15px;
      }
      
      .login-container {
        max-width: 100%;
      }
      
      .login-body {
        padding: 20px;
      }
    }
    /* Add these styles to your existing CSS */

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
  </style>
</head>

<body>

  <!-- Login Section -->
  <div class="main-content">
    <div class="login-container">
      <div class="login-header">
        <h2>Login to Your Account</h2>
        <p>Access your Seafood Market account</p>
      </div>
      
      <div class="login-body">
        <!-- Display error messages -->
        <?php if (!empty($login_error)): ?>
            <div class="message error"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success"><?php echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <form action="" method="post">
            <div class="form-group input-icon">
                <i class="fas fa-user-circle"></i>
                <input type="text" class="form-control" placeholder="Username" name="username" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            
            <div class="form-group input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" placeholder="Password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login" name="submit">Login</button>
        </form>
        
        <div class="login-footer">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/animsition.min.js"></script>
  <script src="js/foodpicky.min.js"></script>
</body>
</html>