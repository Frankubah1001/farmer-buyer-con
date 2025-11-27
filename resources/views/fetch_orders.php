<?php
header('Content-Type: application/json');
session_start();

include 'DBcon.php';

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed. Please try again later. Error: ' . ($conn->connect_error ?? 'Unknown')]);
    exit;
}

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['error' => 'Unauthorized access. Please log in.']);
    exit;
}

$buyer_id = $_SESSION['buyer_id'];
$status_filter = $_GET['order_status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 5;

$query = "SELECT SQL_CALC_FOUND_ROWS
                o.order_id,
                DATE_FORMAT(o.created_at, '%Y-%m-%d %H:%i:%s') AS created_at,
                o.total_amount AS total_amount_raw,
                FORMAT(o.total_amount, 2) AS total_amount_formatted,
                o.order_status,
                o.payment_status,
                DATE_FORMAT(o.payment_date, '%Y-%m-%d %H:%i:%s') AS payment_date,
                CONCAT(f.first_name, ' ', f.last_name) AS farmer,
                o.delivery_date,
                o.delivery_address,
                o.paystack_reference,
                o.payment_amount,
                b.email AS buyer_email
              FROM orders o
              LEFT JOIN buyers b ON o.buyer_id = b.buyer_id
              LEFT JOIN users f ON o.user_id = f.user_id
              WHERE o.buyer_id = ?";

$params = [$buyer_id];
$types = "i";

if (!empty($status_filter)) {
    $query .= " AND o.order_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($date_filter)) {
    $query .= " AND DATE(o.created_at) = ?";
    $params[] = $date_filter;
    $types .= "s";
}

$query .= " ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = ($page - 1) * $per_page;
$types .= "ii";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Database query preparation failed (orders): ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Database query execution failed (orders): ' . $stmt->error]);
    exit;
}
$result = $stmt->get_result();

// Get total number of rows for pagination
$total_result = $conn->query("SELECT FOUND_ROWS()");
$total_orders = ($total_result) ? $total_result->fetch_row()[0] : 0;

$orders_data = [];
while ($row = $result->fetch_assoc()) {
    $orders_data[$row['order_id']] = [
        'order_id'             => 'ORD-' . $row['order_id'],
        'numeric_order_id'     => $row['order_id'],
        'created_at'           => $row['created_at'],
        'total_amount_raw'     => floatval($row['total_amount_raw']),
        'total_amount'         => '₦' . $row['total_amount_formatted'],
        'order_status'         => $row['order_status'] ?? 'Order Sent',
        'payment_status'       => $row['payment_status'],
        'payment_date'         => $row['payment_date'],
        'farmer'               => $row['farmer'],
        'delivery_date'        => $row['delivery_date'],
        'delivery_address'     => $row['delivery_address'] ?? 'Not specified',
        'buyer_email'          => $row['buyer_email'],
        'items'                => []
    ];
}
$stmt->close();

// Fetch items for each order
if (!empty($orders_data)) {
    $itemQuery = "SELECT
                        pl.produce AS name,
                        pl.price AS price_raw,
                        FORMAT(pl.price, 2) AS price_formatted,
                        o.quantity
                     FROM orders o
                     JOIN produce_listings pl ON o.produce_id = pl.prod_id
                     WHERE o.order_id = ?";

    $itemStmt = $conn->prepare($itemQuery);
    if (!$itemStmt) {
        echo json_encode(['error' => 'Database query preparation failed (items): ' . $conn->error]);
        exit;
    }

    foreach ($orders_data as $numeric_order_id => &$order) {
        $itemStmt->bind_param("i", $numeric_order_id);
        if (!$itemStmt->execute()) {
            error_log("Item query execution failed for order $numeric_order_id: " . $itemStmt->error);
            continue;
        }
        $itemResult = $itemStmt->get_result();
        while ($itemRow = $itemResult->fetch_assoc()) {
            $order['items'][] = [
                'name'      => $itemRow['name'] ?? 'N/A',
                'price'     => '₦' . $itemRow['price_formatted'],
                'price_raw' => floatval($itemRow['price_raw']),
                'quantity'  => intval($itemRow['quantity'])
            ];
        }
    }
    $itemStmt->close();
}

echo json_encode([
    'orders'     => array_values($orders_data),
    'pagination' => [
        'total'        => intval($total_orders),
        'per_page'     => $per_page,
        'current_page' => $page,
        'last_page'    => ceil($total_orders / $per_page)
    ]
]);

$conn->close();
?>