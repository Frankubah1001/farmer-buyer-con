<?php
session_start();

$timeout_duration = 1800; // 30 minutes

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ./login.php?session=expired");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity
?>
