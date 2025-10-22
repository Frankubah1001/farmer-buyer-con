<?php
session_start();
include 'DBcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle resend activation
    if (isset($_POST['resend_activation'])) {
        $email = trim($_POST['email']);
        $stmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ? AND is_verified = 0");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            $newToken = bin2hex(random_bytes(32));
            $updateStmt = $conn->prepare("UPDATE users SET verification_token = ? WHERE user_id = ?");
            $updateStmt->bind_param("si", $newToken, $user['user_id']);
            if ($updateStmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'New activation link sent! Check your email.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update activation token.']);
            }
            $updateStmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No unverified account found with this email.']);
        }
        $conn->close();
        exit;
    }

    // Regular login
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberMe']) && $_POST['rememberMe'] == 1;

    // Fetch user data
    $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password, is_verified, info_completed, cbn_approved, rejection_reason FROM users WHERE email = ?");
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error preparing statement.']);
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Check if account is verified
        if ($user['is_verified'] != 1) {
            echo json_encode([
                'status' => 'unverified',
                'message' => 'Account not activated. Please check your email for the activation link.',
                'email' => $email
            ]);
            exit;
        }

        // Check if account was rejected by admin
        if ($user['cbn_approved'] == 2) {
            echo json_encode([
                'status' => 'rejected',
                'message' => 'Your account has been disabled by Admin. Reason: ' . ($user['rejection_reason'] ? htmlspecialchars($user['rejection_reason']) : 'No reason provided.')
            ]);
            exit;
        }

        // Check if account is pending approval
        if ($user['cbn_approved'] == 0) {
            echo json_encode([
                'status' => 'pending',
                'message' => 'Your account is pending approval. Please wait for admin approval.'
            ]);
            exit;
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['info_completed'] = $user['info_completed'];
            $_SESSION['cbn_approved'] = $user['cbn_approved'];
            $_SESSION['LAST_ACTIVITY'] = time();

            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + (86400 * 30); // 30 days
                $expiry_date = date('Y-m-d H:i:s', $expiry);
                $updateTokenStmt = $conn->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE user_id = ?");
                $updateTokenStmt->bind_param("ssi", $token, $expiry_date, $user['user_id']);
                $updateTokenStmt->execute();
                $updateTokenStmt->close();
                setcookie("remember_token", $token, $expiry, "/");
            }

            // Simple redirect - all approved users go to dashboard
            echo json_encode([
                'status' => 'success', 
                'redirect' => 'farmersDashboard.php',
                'message' => 'Login successful! Redirecting...'
            ]);

        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    }

    $conn->close();
    exit;
}

// If not POST request, return error
echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
?>