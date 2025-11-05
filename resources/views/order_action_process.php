<?php
header('Content-Type: application/json');
session_start();

include 'DBcon.php'; // **Ensure this path is correct**

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed. Please try again later.']);
    exit;
}

if (!isset($_SESSION['buyer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$buyer_id = $_SESSION['buyer_id'];
$action = $_POST['action'] ?? '';
$order_id = intval($_POST['order_id'] ?? 0);

if ($order_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order ID.']);
    exit;
}

// --- Function to check if the order is editable/deletable ---
// Orders can only be edited/deleted if the status is 'Order Sent' (i.e., not processed yet).
function isActionAllowed($conn, $order_id, $buyer_id) {
    $stmt = $conn->prepare("SELECT order_status FROM orders WHERE order_id = ? AND buyer_id = ?");
    $stmt->bind_param("ii", $order_id, $buyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $status = strtolower(trim($row['order_status']));
        $stmt->close();
        // Allow action only if the status is exactly 'Order Sent'
        return $status === 'order sent';
    }
    $stmt->close();
    return false;
}

if ($action === 'delete') {
    if (!isActionAllowed($conn, $order_id, $buyer_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Order cannot be deleted. It may already be processing or confirmed.']);
        exit;
    }
    
    // --- DELETE ORDER ---
    $deleteStmt = $conn->prepare("DELETE FROM orders WHERE order_id = ? AND buyer_id = ?");
    $deleteStmt->bind_param("ii", $order_id, $buyer_id);
    
    if ($deleteStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Order (ORD-' . $order_id . ') has been successfully deleted.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete order: ' . $deleteStmt->error]);
    }
    $deleteStmt->close();

} elseif ($action === 'edit') {
    $new_quantity = intval($_POST['quantity'] ?? 0);
    $new_delivery_date = $_POST['delivery_date'] ?? '';
    $new_delivery_address = trim($_POST['delivery_address'] ?? '');

    if (!isActionAllowed($conn, $order_id, $buyer_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Order cannot be edited. It may already be processing or confirmed.']);
        exit;
    }

    if ($new_quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity specified.']);
        exit;
    }
    
    if (empty($new_delivery_date) || empty($new_delivery_address)) {
        echo json_encode(['status' => 'error', 'message' => 'Delivery date and address are required for editing.']);
        exit;
    }

    // --- Recalculate Total Amount ---
    $checkStmt = $conn->prepare("SELECT produce_id FROM orders WHERE order_id = ? AND buyer_id = ?");
    $checkStmt->bind_param("ii", $order_id, $buyer_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $orderRow = $checkResult->fetch_assoc();
    $checkStmt->close();

    if (!$orderRow) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found.']);
        exit;
    }

    $produce_id = $orderRow['produce_id'];

    // Get original produce price
    $priceStmt = $conn->prepare("SELECT price FROM produce_listings WHERE prod_id = ?");
    $priceStmt->bind_param("i", $produce_id);
    $priceStmt->execute();
    $priceResult = $priceStmt->get_result();
    $priceRow = $priceResult->fetch_assoc();
    $priceStmt->close();

    if (!$priceRow) {
        echo json_encode(['status' => 'error', 'message' => 'Produce item not found for price calculation.']);
        exit;
    }
    $unit_price = floatval($priceRow['price']);
    $new_total_amount = $unit_price * $new_quantity;
    
    // --- PERFORM UPDATE ---
    $updateQuery = "UPDATE orders SET 
                        quantity = ?, 
                        total_amount = ?, 
                        delivery_date = ?, 
                        delivery_address = ? 
                    WHERE order_id = ? AND buyer_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("idssii", 
                            $new_quantity, 
                            $new_total_amount, 
                            $new_delivery_date, 
                            $new_delivery_address, 
                            $order_id, 
                            $buyer_id);

    if ($updateStmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Order (ORD-' . $order_id . ') has been successfully updated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update order: ' . $updateStmt->error]);
    }
    $updateStmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
}

$conn->close();
?>