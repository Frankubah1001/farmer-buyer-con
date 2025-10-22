<?php
// Assuming this file (get_profile_info.php) is in the same directory as profile.php
session_start();
include 'DBcon.php';

if (!isset($_SESSION['email'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$email = $_SESSION['email'];
$stmt = mysqli_prepare($conn, "SELECT first_name, last_name, phone, state_id, city_id, address, profile_picture FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$response = ['firstname' => '', 'lastname' => '', 'phone' => '', 'state' => '', 'lga' => '', 'address' => '', 'profile_picture' => ''];

if ($user) {
    $response['firstname'] = $user['first_name'] ?? '';
    $response['lastname'] = $user['last_name'] ?? '';
    $response['phone'] = $user['phone'] ?? '';
    $response['address'] = $user['address'] ?? '';
    $response['profile_picture'] = $user['profile_picture'] ?? '';

    // Fetch state name
    if ($user['state_id']) {
        $stateStmt = mysqli_prepare($conn, "SELECT state_name FROM states WHERE state_id = ?");
        mysqli_stmt_bind_param($stateStmt, "i", $user['state_id']);
        mysqli_stmt_execute($stateStmt);
        $stateResult = mysqli_stmt_get_result($stateStmt);
        $stateData = mysqli_fetch_assoc($stateResult);
        $response['state'] = $stateData['state_name'] ?? '';
        mysqli_stmt_close($stateStmt);
    }

    // Fetch LGA (city) name
    if ($user['city_id']) {
        $lgaStmt = mysqli_prepare($conn, "SELECT city_name FROM cities WHERE city_id = ?");
        mysqli_stmt_bind_param($lgaStmt, "i", $user['city_id']);
        mysqli_stmt_execute($lgaStmt);
        $lgaResult = mysqli_stmt_get_result($lgaStmt);
        $lgaData = mysqli_fetch_assoc($lgaResult);
        $response['city_name'] = $lgaData['city_name'] ?? '';
        mysqli_stmt_close($lgaStmt);
    }
}

mysqli_close($conn);
echo json_encode($response);
?>