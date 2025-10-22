<?php
include 'DBcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$farmer_id = $_SESSION['user_id'];

$where = "WHERE o.user_id = ?"; // Base WHERE clause to filter by farmer
$filterParams = [$farmer_id];
$filterTypes = "i"; // 'i' for integer (user_id)

$filter_produce = isset($_GET['produce']) && !empty($_GET['produce']) ? mysqli_real_escape_string($conn, $_GET['produce']) : '';
$filter_buyer = isset($_GET['buyer']) && !empty($_GET['buyer']) ? mysqli_real_escape_string($conn, $_GET['buyer']) : '';

if ($filter_produce) {
    $where .= " AND pl.produce LIKE ?";
    $filterParams[] = "%" . $filter_produce . "%";
    $filterTypes .= "s"; // 's' for string
}

if ($filter_buyer) {
    $where .= " AND CONCAT(b.firstname, ' ', b.lastname) LIKE ?";
    $filterParams[] = "%" . $filter_buyer . "%";
    $filterTypes .= "s";
}

$limit = 5; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT
    o.order_id,
    CONCAT(b.firstname, ' ', b.lastname) AS buyer_name,
    o.quantity AS ordered_quantity,
    pl.produce AS produce_name,
    o.price_per_unit,
    o.delivery_address,
    o.order_date,
    b.phone AS buyer_phone
FROM
    orders o
JOIN
    produce_listings pl ON o.produce_id = pl.prod_id
JOIN
    buyers b ON o.buyer_id = b.buyer_id
$where
ORDER BY o.created_at DESC
LIMIT ?, ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
    exit();
}

$bindTypes = $filterTypes . "ii"; // Add 'ii' for LIMIT and OFFSET
$bindParams = array_merge($filterParams, [$offset, $limit]); // Corrected order of parameters for LIMIT and OFFSET

$stmt->bind_param($bindTypes, ...$bindParams);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();

// --- Get Total Count for Pagination ---
$sql_count = "SELECT COUNT(o.order_id) AS total_orders
              FROM orders o
              JOIN produce_listings pl ON o.produce_id = pl.prod_id
              JOIN buyers b ON o.buyer_id = b.buyer_id
              $where";

$stmt_count = $conn->prepare($sql_count);
if ($stmt_count === false) {
    echo json_encode(['error' => 'Error preparing count statement: ' . $conn->error]);
    exit();
}
$stmt_count->bind_param($filterTypes, ...$filterParams);
$stmt_count->execute();
$total_orders = $stmt_count->get_result()->fetch_assoc()['total_orders'];
$stmt_count->close();

$total_pages = ceil($total_orders / $limit);

$conn->close();

echo json_encode([
    'data' => $data,
    'total_records' => $total_orders,
    'total_pages' => $total_pages,
    'current_page' => $page
]);
?>
