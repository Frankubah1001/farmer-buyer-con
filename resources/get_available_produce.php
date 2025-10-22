<?php
// get_available_produce.php
include 'DBcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$where = "WHERE pl.is_deleted = FALSE AND pl.user_id = ?"; // Modified WHERE clause to filter by logged-in user
$filter_produce = isset($_GET['produce']) && !empty($_GET['produce']) ? mysqli_real_escape_string($conn, $_GET['produce']) : '';
$filter_location = isset($_GET['location']) && !empty($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$filter_farmer = isset($_GET['farmer']) && !empty($_GET['farmer']) ? mysqli_real_escape_string($conn, $_GET['farmer']) : '';

$filterParams = [$user_id];
$filterTypes = "i";

if ($filter_produce) {
    $where .= " AND pl.produce LIKE ?";
    $filterParams[] = "%" . $filter_produce . "%";
    $filterTypes .= "s";
}

if ($filter_location) {
    $where .= " AND o.delivery_address LIKE ?";
    $filterParams[] = "%" . $filter_location . "%";
    $filterTypes .= "s";
}

if ($filter_farmer) {
    $where .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    $filterParams[] = "%" . $filter_farmer . "%";
    $filterTypes .= "s";
}

$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Modified SQL query to fetch data from multiple tables
$sql = "SELECT
            pl.prod_id AS produce_id,
            pl.produce AS produce_name,
            pl.quantity AS produce_quantity,
            pl.price AS produce_price,
            pl.available_date,
            pl.image_path,
            pl.created_at AS produce_created_at,
            pl.updated_at AS produce_updated_at,
            pl.address AS produce_address,
            pl.conditions AS produce_conditions,
            pl.visit_allowed,
            pl.visit_time,
            pl.delivery_offered,
            pl.delivery_areas,
            pl.notes AS produce_notes,
            pl.listing_date,
            pl.is_filled,
            pl.is_deleted,
            pl.available,
            pl.has_order,
            pl.order_status AS produce_order_status,
            
            b.buyer_id,
            CONCAT(b.firstname, ' ', b.lastname) AS buyer_name,
            b.email AS buyer_email,
            b.phone AS buyer_phone,
            b.gender AS buyer_gender,
            b.address AS buyer_address,
            b.created_at AS buyer_created_at,
            b.updated_at AS buyer_updated_at,
            
            o.order_id,
            o.order_date,
            o.quantity AS order_quantity,
            o.price_per_unit,
            o.delivery_address AS order_delivery_address,
            o.delivery_date,
            o.total_amount,
            o.payment_status,
            o.order_status,
            o.notes AS order_notes,
            o.created_at AS order_created_at,
            o.updated_at AS order_updated_at,
            
            u.user_id,
            u.first_name AS user_firstname,
            u.last_name AS user_lastname,
            u.email AS user_email,
            u.phone AS user_phone,
            u.gender AS user_gender,
            u.address AS user_address,
            u.created_at AS user_created_at,
            u.profile_picture
            
        FROM
            produce_listings pl
        JOIN
            orders o ON pl.prod_id = o.produce_id
        JOIN
            buyers b ON o.buyer_id = b.buyer_id
        JOIN
            users u ON pl.user_id = u.user_id  -- Join with the users table to get user details
        $where
        ORDER BY o.created_at DESC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$bindTypes = $filterTypes . "ii";
$bindParams = array_merge($filterParams, [$offset, $limit]);
$stmt->bind_param($bindTypes, ...$bindParams);
$stmt->execute();
if ($stmt->errno) {
    echo json_encode(['error' => 'Error executing statement: ' . $stmt->error]);
    exit();
}
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt->close();

// --- Query for Total Count ---
$sql_total = "SELECT COUNT(DISTINCT o.order_id) AS total
                FROM produce_listings pl
                JOIN orders o ON pl.prod_id = o.produce_id
                JOIN buyers b ON o.buyer_id = b.buyer_id
                JOIN users u ON pl.user_id = u.user_id
                $where";

$stmt_count = $conn->prepare($sql_total);
if ($stmt_count === false) {
    echo json_encode(['error' => 'Error preparing count statement: ' . $conn->error]);
    exit();
}

if (!empty($filterTypes)) {
    $stmt_count->bind_param($filterTypes, ...$filterParams);
}
$stmt_count->execute();
if ($stmt_count->errno) {
    echo json_encode(['error' => 'Error executing count statement: ' . $stmt_count->error]);
    exit();
}
$totalRows = $stmt_count->get_result()->fetch_row()[0];
$stmt_count->close();

$totalPages = ceil($totalRows / $limit);

$conn->close();

echo json_encode([
    'data' => $data,
    'total_records' => $totalRows,
    'total_pages' => $totalPages,
    'current_page' => $page
]);
?>
