<?php
session_start();
require_once 'DBcon.php'; // Include your database connection file

// Enable error reporting for debugging (REMOVE IN PRODUCTION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Set header to indicate JSON response

$response = ['data' => [], 'total_pages' => 0, 'error' => ''];

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['error'] = 'User not logged in.';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];

// Check if a single application ID is requested
$singleApplicationId = isset($_GET['single_id']) ? intval($_GET['single_id']) : 0;

$whereClauses = ["user_id = ?"]; // Always filter by user_id
$params = [$userId];
$types = 'i'; // Type for user_id

if ($singleApplicationId > 0) {
    // If a single ID is requested, override other filters and pagination
    $whereClauses[] = "application_id = ?";
    $params[] = $singleApplicationId;
    $types .= 'i';
    $itemsPerPage = 1; // Only one item
    $offset = 0; // First item
} else {
    // Normal pagination and filtering
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $platformFilter = isset($_GET['platform']) ? $_GET['platform'] : '';
    $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
    $itemsPerPage = 5; // Number of applications per page
    $offset = ($page - 1) * $itemsPerPage;

    // Add platform filter
    if (!empty($platformFilter)) {
        $whereClauses[] = "loan_platform = ?";
        $params[] = $platformFilter;
        $types .= 's';
    }

    // Add status filter
    if (!empty($statusFilter)) {
        $whereClauses[] = "application_status = ?";
        $params[] = $statusFilter;
        $types .= 's';
    }
}

$whereSql = "WHERE " . implode(" AND ", $whereClauses);

// --- 1. Get total number of applications for pagination (only if not single ID request) ---
if ($singleApplicationId == 0) {
    $countSql = "SELECT COUNT(*) AS total_applications FROM loan_applications " . $whereSql;
    $countStmt = mysqli_prepare($conn, $countSql);

    if ($countStmt) {
        // Use a temporary params array for count query, excluding limit/offset
        $countParams = $params;
        $countTypes = $types;

        if (!empty($countParams)) {
            mysqli_stmt_bind_param($countStmt, $countTypes, ...$countParams);
        }
        mysqli_stmt_execute($countStmt);
        $countResult = mysqli_stmt_get_result($countStmt);
        $totalApplications = mysqli_fetch_assoc($countResult)['total_applications'];
        mysqli_stmt_close($countStmt);
        $response['total_pages'] = ceil($totalApplications / $itemsPerPage);
    } else {
        $response['error'] = 'Database error counting applications: ' . mysqli_error($conn);
        echo json_encode($response);
        mysqli_close($conn);
        exit();
    }
} else {
    // For single ID request, total pages is 1
    $response['total_pages'] = 1;
}


// --- 2. Fetch applications for the current page or single application ---
$sql = "SELECT application_id, user_id, loan_platform, loan_amount, loan_purpose,
               repayment_period_months, document_paths, application_status,
               bank_name, account_number, account_name, created_at, updated_at
        FROM loan_applications
        " . $whereSql . "
        ORDER BY created_at DESC"; // Keep order by for consistency, though not strictly needed for single ID

if ($singleApplicationId == 0) {
    $sql .= " LIMIT ?, ?"; // Add limit/offset only for list view
}


$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    $fullParams = $params;
    $fullTypes = $types;

    if ($singleApplicationId == 0) {
        // Append limit and offset types and parameters for list view
        $fullTypes .= 'ii';
        $fullParams = array_merge($params, [$offset, $itemsPerPage]);
    }

    mysqli_stmt_bind_param($stmt, $fullTypes, ...$fullParams);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $applications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Decode document_paths if it's stored as JSON
        // Ensure it's always an array for frontend consistency
        if (!empty($row['document_paths'])) {
            $decodedPaths = json_decode($row['document_paths'], true);
            $row['document_paths'] = is_array($decodedPaths) ? $decodedPaths : [];
        } else {
            $row['document_paths'] = [];
        }

        // Ensure bank details are not null; convert to empty string if null
        $row['bank_name'] = $row['bank_name'] ?? '';
        $row['account_number'] = $row['account_number'] ?? '';
        $row['account_name'] = $row['account_name'] ?? '';

        $applications[] = $row;
    }
    mysqli_stmt_close($stmt);

    $response['data'] = $applications;
    $response['error'] = ''; // Clear error if data fetched successfully

} else {
    $response['error'] = 'Database error fetching applications: ' . mysqli_error($conn);
}

mysqli_close($conn);
echo json_encode($response);
?>
