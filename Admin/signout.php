<?php
session_start();
session_unset();
session_destroy();


header("Location: cbn_login.php");
exit();
?>