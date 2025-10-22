<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../../logs/error.log');

include 'DBcon.php';

// Check database connection
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
            <h2 style="margin-top:0; margin-bottom:15px;">Error</h2>
            <p style="margin-bottom:0;">Database connection failed. Please try again later.</p>
          </div>';
    exit;
}

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    if (empty($token)) {
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#fff3cd; color:#856404; border:1px solid #ffeeba;">
                <h2 style="margin-top:0; margin-bottom:15px;">Invalid Request</h2>
                <p style="margin-bottom:0;">This verification link appears to be incomplete.</p>
              </div>';
        exit;
    }

    // Prepare and execute the selection query
    $select_sql = "SELECT * FROM buyers WHERE verification_token = ? AND is_verify = 0";
    $select_stmt = $conn->prepare($select_sql);
    if (!$select_stmt) {
        http_response_code(500);
        error_log("Prepare failed: " . $conn->error);
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
                <h2 style="margin-top:0; margin-bottom:15px;">Error</h2>
                <p style="margin-bottom:0;">An internal error occurred. Please contact support.</p>
              </div>';
        exit;
    }
    $select_stmt->bind_param("s", $token);
    $select_stmt->execute();
    $result = $select_stmt->get_result();

    if ($result->num_rows === 1) {
        // Prepare and execute the update query with empty string instead of NULL
        $update_sql = "UPDATE buyers SET is_verify = 1, verification_token = '' WHERE verification_token = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            http_response_code(500);
            error_log("Prepare failed: " . $conn->error);
            echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
                    <h2 style="margin-top:0; margin-bottom:15px;">Error</h2>
                    <p style="margin-bottom:0;">An internal error occurred. Please contact support.</p>
                  </div>';
            exit;
        }
        $update_stmt->bind_param("s", $token);
        $update_stmt->execute();
        $update_stmt->close();

        // Success message and redirect
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#d4edda; color:#155724; border:1px solid #c3e6cb;">
                <h2 style="margin-top:0; margin-bottom:15px;">Account Activated Successfully!</h2>
                <p style="margin-bottom:0;">You will be redirected to login page...</p>
              </div>';
        header("refresh:2; url=Buyer_login.php");
        exit;
    } else {
        echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb;">
                <h2 style="margin-top:0; margin-bottom:15px;">Verification Failed</h2>
                <p style="margin-bottom:0;">Invalid or expired token. Please request a new verification link.</p>
              </div>';
    }

    $select_stmt->close();
} else {
    echo '<div style="max-width:600px; margin:50px auto; padding:20px; border-radius:8px; text-align:center; font-family:Arial,sans-serif; box-shadow:0 2px 10px rgba(0,0,0,0.1); background-color:#fff3cd; color:#856404; border:1px solid #ffeeba;">
            <h2 style="margin-top:0; margin-bottom:15px;">Invalid Request</h2>
            <p style="margin-bottom:0;">This verification link appears to be incomplete.</p>
          </div>';
}

$conn->close();
?>