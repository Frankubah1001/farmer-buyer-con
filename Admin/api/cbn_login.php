<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include 'DBcon.php'; 

$response = ['success' => false, 'message' => 'Invalid Request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    
    $username_or_email = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_or_email) || empty($password)) {
        $response['message'] = 'Please enter both username/email and password.';
        echo json_encode($response);
        close_db_connection($conn);
        exit;
    }

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT cbn_user_id, password, full_name, role 
            FROM cbn_users 
            WHERE username = ? OR email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stored_password = $user['password'];
        $authenticated = false;

        // Check for modern password_hash (recommended)
        if (password_verify($password, $stored_password)) {
            $authenticated = true;
        } 
        // Check for MD5 (for compatibility with older records)
        elseif (strlen($stored_password) === 32 && md5($password) === $stored_password) {
            $authenticated = true;
        }
        
        if ($authenticated) {
            // Success: Set Session Variables
            $_SESSION['cbn_user_id'] = $user['cbn_user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            $conn->query("UPDATE cbn_users SET last_login = NOW() WHERE cbn_user_id = " . $user['cbn_user_id']);

            $response['success'] = true;
            $response['message'] = 'Authentication successful.';
        } else {
            $response['message'] = 'Invalid credentials.';
        }

    } else {
        $response['message'] = 'Invalid credentials.';
    }

    $stmt->close();
}

echo json_encode($response);
// Line 72 (or similar) where the function was previously called, now it's defined!
close_db_connection($conn); 
?>
