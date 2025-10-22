<?php
session_start();

$timeout_duration = 1800; // 30 minutes

if (!isset($_SESSION['buyer_id'])) {
    header("Location: ./buyer_login.php");
    exit();
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ./buyer_login.php?session=expired");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity
?>
