<?php
// session_check.php - Admin session timeout check
session_start();

// Set session timeout to 30 minutes (1800 seconds)
$timeout_duration = 1800;

// Check if user is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    // Not logged in, redirect to login
    header('Location: cbn_login.php');
    exit();
}

// Check if session has timed out
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session has expired
        session_unset();
        session_destroy();
        header('Location: cbn_login.php?timeout=1');
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>
