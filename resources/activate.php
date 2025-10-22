<?php
include 'DBcon.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE verification_token = '$token' AND is_verified = 0");

    if (mysqli_num_rows($query) === 1) {
        mysqli_query($conn, "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = '$token'");
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#d4edda; color:#155724; border:1px solid #c3e6cb;">
                <h2 style="margin-top:0; margin-bottom:15px;">Account Activated Successfully!</h2>
                <p style="margin-bottom:0;">You will be redirected to login page...</p>
              </div>';
        // Redirect after 4 seconds
        header("refresh:4; url=login.php");
    } else {
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
                <h2 style="margin-top:0; margin-bottom:15px;">Verification Failed</h2>
                <p style="margin-bottom:0;">Invalid or expired token. Please request a new verification link.</p>
              </div>';
    }
} else {
    echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#fff3cd; color:#856404; border:1px solid #ffeeba;">
            <h2 style="margin-top:0; margin-bottom:15px;">Invalid Request</h2>
            <p style="margin-bottom:0;">This verification link appears to be incomplete.</p>
          </div>';
}
?>