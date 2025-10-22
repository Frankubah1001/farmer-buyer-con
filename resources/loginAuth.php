<?php
session_start();
// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: farmerdashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Required</title>
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>OPPPPSssss!!!!!  Login Required</h2>
        <p>You must be logged in to access this page.</p>
        <a href="login.php" class="btn btn-primary">Login Now</a>
        
        <script>
        // If this page was loaded via AJAX, redirect parent window
        if (window.parent !== window) {
            window.parent.location.href = 'login.php';
        }
        </script>
    </div>
</body>
</html>