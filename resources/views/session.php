<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// $currentPage = basename($_SERVER['PHP_SELF']);

// //$publicPages = ['farmerDashboard.php'];

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
