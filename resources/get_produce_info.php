<?php
// Disable display_errors to prevent output corruption. Log errors instead.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include 'DBcon.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) {
    echo json_encode(['error' => 'Farmer not logged in.']);
    exit;
}

// Handle unique filters request
if (isset($_GET['filters']) && $_GET['filters'] === 'true') {
    $sql_produce = "SELECT DISTINCT produce FROM produce_listings WHERE user_id = ? AND is_deleted = 0";
    $stmt_p = $conn->prepare($sql_produce);
    $stmt_p->bind_param('i', $user_id);
    $stmt_p->execute();
    $result_p = $stmt_p->get_result();
    $unique_produce = [];
    while ($row = $result_p->fetch_assoc()) {
        $unique_produce[] = $row['produce'];
    }
    $stmt_p->close();

    $sql_conditions = "SELECT DISTINCT conditions FROM produce_listings WHERE user_id = ? AND is_deleted = 0";
    $stmt_c = $conn->prepare($sql_conditions);
    $stmt_c->bind_param('i', $user_id);
    $stmt_c->execute();
    $result_c = $stmt_c->get_result();
    $unique_conditions = [];
    while ($row = $result_c->fetch_assoc()) {
        $unique_conditions[] = $row['conditions'];
    }
    $stmt_c->close();

    echo json_encode([
        'unique_produce' => $unique_produce,
        'unique_conditions' => $unique_conditions
    ]);
    exit;
}

// Get filter parameters
$produce = isset($_GET['produce']) ? mysqli_real_escape_string($conn, $_GET['produce']) : '';
$condition = isset($_GET['condition']) ? mysqli_real_escape_string($conn, $_GET['condition']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 5; // Fixed items per page

$offset = ($page - 1) * $per_page;

// Main query with filters for the logged-in farmer
$sql = "SELECT 
    p.prod_id, 
    p.produce, 
    p.quantity AS uploaded_quantity,
    COALESCE(o.remaining_quantity, p.quantity) AS remaining_quantity,
    (p.quantity - COALESCE(o.remaining_quantity, p.quantity)) AS quantity_ordered,
    p.price, 
    p.conditions,
    p.available_date, 
    p.address, 
    p.image_path
FROM 
    produce_listings p
LEFT JOIN (
    SELECT 
        produce_id, 
        remaining_quantity
    FROM 
        orders
    WHERE 
        (order_date, order_id) IN (
            SELECT 
                MAX(order_date) AS order_date, 
                MAX(order_id) AS order_id
            FROM 
                orders
            GROUP BY 
                produce_id
        )
) o ON p.prod_id = o.produce_id
WHERE 
    p.user_id = ?
    AND p.is_deleted = 0
    AND (p.produce LIKE ? OR ? = '')
    AND (p.conditions LIKE ? OR ? = '')
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$like_produce = "%$produce%";
$like_condition = "%$condition%";
$stmt->bind_param('issssii', $user_id, $like_produce, $produce, $like_condition, $condition, $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$produce_data = [];

while ($row = $result->fetch_assoc()) {
    // Handle remaining_quantity
    $row['remaining_quantity'] = $row['remaining_quantity'] === 'Item Sold' ? 0 : max(0, intval($row['remaining_quantity']));
    // Add flag to indicate if no orders have been placed
    $row['no_orders'] = ($row['remaining_quantity'] == $row['uploaded_quantity']);

    // Fix image path
    if (!empty($row['image_path']) && $row['image_path'] !== "0") {
        if (strpos($row['image_path'], '/farmerBuyerCon/') !== 0) {
            $row['image_path'] = "/farmerBuyerCon/resources/views/Farm_Produce_Images/" . basename($row['image_path']);
        }
    } else {
        $row['image_path'] = "/farmerBuyerCon/assets/img/no-image.jpg";
    }

    $produce_data[] = $row;
}
$stmt->close();

// Total count query with same filters
$total_sql = "SELECT COUNT(*) as total FROM produce_listings p
              WHERE p.user_id = ?
              AND p.is_deleted = 0
              AND (p.produce LIKE ? OR ? = '')
              AND (p.conditions LIKE ? OR ? = '')";
$stmt_total = $conn->prepare($total_sql);
$stmt_total->bind_param('issss', $user_id, $like_produce, $produce, $like_condition, $condition);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_rows = $result_total->fetch_assoc()['total'];
$stmt_total->close();

$total_pages = ceil($total_rows / $per_page);

echo json_encode([
    'data' => $produce_data,
    'total_pages' => $total_pages,
    'total_records' => $total_rows,
    'current_page' => $page
]);
?>