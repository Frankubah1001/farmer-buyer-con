<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
// If already approved, redirect to dashboard
if (isset($_SESSION['cbn_approved']) && $_SESSION['cbn_approved'] == 1) { // Check cbn_approved == 1 for approved
    header('Location: farmersDashboard.php');
    exit;
}
// If rejected, redirect to login with a message (handled by login.view.php)
if (isset($_SESSION['cbn_approved']) && $_SESSION['cbn_approved'] == 2) {
    // It's better to force a re-login here, and login.view.php will show the rejection message.
    session_destroy();
    header('Location: login.php?status=rejected');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Awaiting Approval</title>
    <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="asset/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .card {
            padding: 40px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="card shadow-lg my-5">
            <h1 class="h4 text-gray-900 mb-4">Application Under Review</h1>
            <p class="mb-4">Thank you for submitting your details and documents. Your application is now under review.</p>
            <p class="mb-4">You will receive an email notification once your application has been approved or rejected. Until then, you will not be able to access your dashboard.</p>
            <a href="logout.php" class="btn btn-primary">Logout</a>
        </div>
    </div>
    <script src="asset/vendor/jquery/jquery.min.js"></script>
    <script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="asset/js/sb-admin-2.min.js"></script>
</body>
</html>