<?php
session_start();
session_unset();
session_destroy();

// Clear remember me cookie if set
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header("Location: Buyer_login.php");
exit();
?>