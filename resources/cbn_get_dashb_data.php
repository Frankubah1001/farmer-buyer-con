<?php
session_start();
require_once 'DBcon.php'; // Your database connection file

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Unauthorized access.',
    'total_farmers' => 0,
    'pending_farmers' => 0,
    'approved_farmers' => 0,
    'rejected_farmers' => 0,
    'regulated_produce_types' => 0,
    'total_loan_applications' => 0,
    'approved_loan_applications' => 0,
    'total_transactions_value' => 0.00,
    'produce_price_chart_labels' => [],
    'produce_price_chart_data' => []
];

// Check if CBN user is logged in
if (!isset($_SESSION['cbn_user_id'])) {
    echo json_encode($response);
    exit();
}

// --- Fetch Farmer Counts ---
$sql_farmers_counts = "SELECT
    COUNT(*) AS total_farmers,
    SUM(CASE WHEN cbn_approved = 0 THEN 1 ELSE 0 END) AS pending_farmers,
    SUM(CASE WHEN cbn_approved = 1 THEN 1 ELSE 0 END) AS approved_farmers,
    SUM(CASE WHEN cbn_approved = 2 THEN 1 ELSE 0 END) AS rejected_farmers
FROM users
WHERE is_verified = 1"; // Assuming 'role' column in 'users' table for farmers

$result_farmers = mysqli_query($conn, $sql_farmers_counts);
if ($result_farmers && $row = mysqli_fetch_assoc($result_farmers)) {
    $response['total_farmers'] = (int)$row['total_farmers'];
    $response['pending_farmers'] = (int)$row['pending_farmers'];
    $response['approved_farmers'] = (int)$row['approved_farmers'];
    $response['rejected_farmers'] = (int)$row['rejected_farmers'];
} else {
    error_log("CBN Dashboard: Error fetching farmer counts: " . mysqli_error($conn));
}

// --- Fetch Regulated Produce Types Count ---
$sql_regulated_produce = "SELECT COUNT(*) AS count FROM produce_prices_cbn";
$result_regulated = mysqli_query($conn, $sql_regulated_produce);
if ($result_regulated && $row = mysqli_fetch_assoc($result_regulated)) {
    $response['regulated_produce_types'] = (int)$row['count'];
} else {
    error_log("CBN Dashboard: Error fetching regulated produce count: " . mysqli_error($conn));
}

// --- Fetch Loan Application Counts ---
$sql_loan_applications_counts = "SELECT
    COUNT(*) AS total_loan_applications,
    SUM(CASE WHEN application_status = 'Approved' THEN 1 ELSE 0 END) AS approved_loan_applications
FROM loan_applications";

$result_loans = mysqli_query($conn, $sql_loan_applications_counts);
if ($result_loans && $row = mysqli_fetch_assoc($result_loans)) {
    $response['total_loan_applications'] = (int)$row['total_loan_applications'];
    $response['approved_loan_applications'] = (int)$row['approved_loan_applications'];
} else {
    error_log("CBN Dashboard: Error fetching loan application counts: " . mysqli_error($conn));
}

// --- Fetch Total Platform Transactions Value ---
// Sum of (quantity * price_per_unit) from 'orders' table
// IMPORTANT: Ensure 'price_per_unit' is clean numeric data in DB.
$sql_total_transactions_value = "SELECT SUM(o.quantity * CAST(REPLACE(o.price_per_unit, ',', '') AS DECIMAL(15,2))) AS total_value
                                FROM orders o"; // Summing all orders on the platform

$result_transactions = mysqli_query($conn, $sql_total_transactions_value);
if ($result_transactions && $row = mysqli_fetch_assoc($result_transactions)) {
    $response['total_transactions_value'] = (float)$row['total_value'];
} else {
    error_log("CBN Dashboard: Error fetching total transactions value: " . mysqli_error($conn));
}

// --- Data for Produce Price Chart (Top 5 Regulated Produce Types) ---
$sql_produce_chart_data = "SELECT produce_name, COUNT(*) as count
                           FROM produce_prices_cbn
                           GROUP BY produce_name
                           ORDER BY count DESC
                           LIMIT 5"; // Get top 5 most frequently regulated produce types

$result_produce_chart = mysqli_query($conn, $sql_produce_chart_data);
if ($result_produce_chart) {
    while ($row = mysqli_fetch_assoc($result_produce_chart)) {
        $response['produce_price_chart_labels'][] = $row['produce_name'];
        $response['produce_price_chart_data'][] = (int)$row['count'];
    }
} else {
    error_log("CBN Dashboard: Error fetching produce chart data: " . mysqli_error($conn));
}


$response['status'] = 'success';
$response['message'] = 'Dashboard data fetched successfully.';

mysqli_close($conn);
echo json_encode($response);
?>
