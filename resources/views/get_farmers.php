<?php
header('Content-Type: application/json');
session_start();
include 'DBcon.php';

// --- Filtering ---
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';
$contactFilter = isset($_GET['contact']) ? $_GET['contact'] : '';
$filterParams = [];
$filterTypes = "";

// --- Function to build the WHERE clause for filtering ---
function buildWhereClause($locationFilter, $contactFilter, &$filterParams, &$filterTypes) {
    $whereClauses = [];
    $filterParams = [];
    $filterTypes = "";

    if ($locationFilter != '') {
        $whereClauses[] = "LOWER(u.address) LIKE LOWER(?)";
        $filterParams[] = "%" . $locationFilter . "%";
        $filterTypes .= "s";
    }

    if ($contactFilter != '') {
        $whereClauses[] = "u.phone LIKE ?";
        $filterParams[] = "%" . $contactFilter . "%";
        $filterTypes .= "s";
    }

    return count($whereClauses) > 0 ? "AND " . implode(" AND ", $whereClauses) : "";
}

$whereFilterClause = buildWhereClause($locationFilter, $contactFilter, $filterParams, $filterTypes);

// --- Pagination ---
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$rowsPerPage = 5;
$offset = ($page - 1) * $rowsPerPage;
$paginationParams = [$offset, $rowsPerPage];
$paginationTypes = "ii";

// --- Base Query for Data WITH AVERAGE RATING ---
$sql = "SELECT
            u.user_id,
            u.first_name,
            u.last_name,
            u.address,
            u.created_at,
            u.phone,
            AVG(r.rating) as average_rating
        FROM users u
        LEFT JOIN ratings r ON u.user_id = r.user_id
        WHERE u.user_id IN (SELECT DISTINCT user_id FROM produce_listings)
        " . $whereFilterClause . "
        GROUP BY u.user_id
        ORDER BY u.created_at DESC
        LIMIT ?, ?";

// --- Prepared Statement for Data ---
$stmt = $conn->prepare($sql);
if ($filterTypes != "") {
    $stmt->bind_param($filterTypes . $paginationTypes, ...array_merge($filterParams, $paginationParams));
} else {
    $stmt->bind_param($paginationTypes, ...$paginationParams);
}

$stmt->execute();
$result = $stmt->get_result();
$farmers = [];

while ($row = $result->fetch_assoc()) {
    $farmers[] = [
        'user_id' => $row['user_id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'location' => $row['address'],
        'contact' => $row['phone'],
        'rating' => round($row['average_rating'], 1) ?? 0, // Calculate and round average, default to 0 if no ratings
    ];
}
$stmt->close();

// --- Query for Total Count ---
$sql_count = "SELECT COUNT(*) FROM (
                    SELECT u.user_id
                    FROM users u
                    WHERE u.user_id IN (SELECT DISTINCT user_id FROM produce_listings)
                    " . $whereFilterClause . "
                 ) as count";

// --- Prepared Statement for Total Count ---
$stmt_count = $conn->prepare($sql_count);
if ($filterTypes != "") {
    $stmt_count->bind_param($filterTypes, ...$filterParams);
}
$stmt_count->execute();
$totalRows = $stmt_count->get_result()->fetch_row()[0];
$stmt_count->close();

$totalPages = ceil($totalRows / $rowsPerPage);

$conn->close();

echo json_encode([
    'farmers' => $farmers,
    'pagination' => [
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'totalRows' => $totalRows,
    ],
]);
?>