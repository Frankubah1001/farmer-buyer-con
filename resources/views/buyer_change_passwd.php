<?php
session_start();
require_once 'DBcon.php'; // Include your database connection file

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set header to indicate JSON response

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['buyer_id'])) {
        $response['message'] = 'User not logged in.';
        echo json_encode($response);
        exit();
    }

    $userId = $_SESSION['buyer_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

    // Basic validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmNewPassword)) {
        $response['message'] = 'All fields are required.';
        echo json_encode($response);
        exit();
    }

    if ($newPassword !== $confirmNewPassword) {
        $response['message'] = 'New passwords do not match.';
        echo json_encode($response);
        exit();
    }

    // Password complexity check (as per your HTML form's help text)
    // Your new password must be 8-20 characters long, contain letters and numbers,
    // and must not contain spaces, special characters, or emoji.
    if (strlen($newPassword) < 8 || strlen($newPassword) > 20) {
        $response['message'] = 'New password must be 8-20 characters long.';
        echo json_encode($response);
        exit();
    }
    if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/[0-9]/', $newPassword)) {
        $response['message'] = 'New password must contain letters and numbers.';
        echo json_encode($response);
        exit();
    }
    // This regex checks for spaces, common special characters, and non-ASCII (emoji)
    if (preg_match('/[\s!"#$%&\'()*+,\-.\/:;<=>?@[\]^_`{|}~]|\p{C}|[\x{1F600}-\x{1F64F}]/u', $newPassword)) {
        $response['message'] = 'New password must not contain spaces, special characters, or emoji.';
        echo json_encode($response);
        exit();
    }


    // Fetch the current hashed password from the database
    $stmt = mysqli_prepare($conn, "SELECT password FROM buyers WHERE buyer_id = ?");
    if (!$stmt) {
        $response['message'] = 'Database error: Could not prepare statement for fetching password.';
        error_log("Failed to prepare statement for fetching password: " . mysqli_error($conn));
        echo json_encode($response);
        exit();
    }
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $hashedPassword = $user['password'];

        // Verify the current password
        if (password_verify($currentPassword, $hashedPassword)) {
            // Current password is correct, hash and update the new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateStmt = mysqli_prepare($conn, "UPDATE buyers SET password = ? WHERE buyer_id = ?");
            if (!$updateStmt) {
                $response['message'] = 'Database error: Could not prepare statement for updating password.';
                error_log("Failed to prepare statement for updating password: " . mysqli_error($conn));
                echo json_encode($response);
                exit();
            }
            mysqli_stmt_bind_param($updateStmt, "si", $newHashedPassword, $userId);

            if (mysqli_stmt_execute($updateStmt)) {
                $response['status'] = 'success';
                $response['message'] = 'Password updated successfully!';
            } else {
                $response['message'] = 'Failed to update password. Please try again.';
                error_log("Error updating password: " . mysqli_stmt_error($updateStmt));
            }
            mysqli_stmt_close($updateStmt);
        } else {
            $response['message'] = 'Current password is incorrect.';
        }
    } else {
        $response['message'] = 'User not found. Please log in again.'; // Should not happen if user_id is from session
    }

    mysqli_stmt_close($stmt);
} else {
    $response['message'] = 'Invalid request method.';
}

mysqli_close($conn); // Close the database connection
echo json_encode($response);
?>
