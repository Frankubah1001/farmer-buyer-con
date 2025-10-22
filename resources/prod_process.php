<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();
include 'DBcon.php';

date_default_timezone_set('Africa/Lagos');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $buyer_id = isset($_SESSION['buyer_id']) ? intval($_SESSION['buyer_id']) : 0;
    $produce_id = intval($_POST['prod_id']);
    $has_ordered = 0;
    $quantity_ordered = intval($_POST['quantity']);
    $delivery_address = htmlspecialchars($_POST['deliveryAddress']);
    $delivery_date_raw = $_POST['deliveryDate'];
    $notes = htmlspecialchars($_POST['notes']);
    $farmerName = htmlspecialchars($_POST['farmerName']);

    // Simple date validation for YYYY-MM-DD format (from HTML date input)
    $delivery_date = '';
    
    if (!empty($delivery_date_raw)) {
        // HTML date input should be in YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $delivery_date_raw)) {
            // Validate it's a real date
            $date_parts = explode('-', $delivery_date_raw);
            $year = intval($date_parts[0]);
            $month = intval($date_parts[1]);
            $day = intval($date_parts[2]);
            
            if (checkdate($month, $day, $year)) {
                $delivery_date = $delivery_date_raw; // Use as-is since it's already in YYYY-MM-DD format
            }
        }
    }

    // Basic validation
    if ($buyer_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to place an order.']);
        exit;
    }

    if ($produce_id <= 0 || $quantity_ordered <= 0 || empty($delivery_address) || empty($delivery_date)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid order details. Please check all fields.',
            'debug_info' => [
                'produce_id' => $produce_id,
                'quantity' => $quantity_ordered,
                'delivery_address' => !empty($delivery_address),
                'delivery_date_received' => $delivery_date_raw,
                'delivery_date_parsed' => $delivery_date
            ]
        ]);
        exit;
    }

    $conn->begin_transaction();

    try {
        $sql_produce_info = "SELECT pl.quantity, pl.price, u.user_id AS farmer_user_id,
                            COALESCE((SELECT remaining_quantity FROM orders WHERE produce_id = ? 
                                     AND (order_date, order_id) IN (
                                         SELECT MAX(order_date), MAX(order_id) 
                                         FROM orders 
                                         WHERE produce_id = ?
                                     )), pl.quantity) AS latest_remaining_quantity
                           FROM produce_listings pl
                           JOIN users u ON pl.user_id = u.user_id
                           WHERE pl.prod_id = ? AND pl.available_date <= CURDATE() FOR UPDATE";
        $stmt_produce_info = $conn->prepare($sql_produce_info);
        $stmt_produce_info->bind_param("iii", $produce_id, $produce_id, $produce_id);
        $stmt_produce_info->execute();
        $result_produce_info = $stmt_produce_info->get_result();

        if ($result_produce_info->num_rows > 0) {
            $row_produce_info = $result_produce_info->fetch_assoc();
            $current_quantity = intval($row_produce_info['quantity']);
            $latest_remaining_quantity = $row_produce_info['latest_remaining_quantity'] === 'Item Sold' ? 0 : intval($row_produce_info['latest_remaining_quantity']);
            $price_per_unit = floatval(str_replace(',', '', $row_produce_info['price']));
            $farmer_user_id = intval($row_produce_info['farmer_user_id']);
            $total_amount = $price_per_unit * $quantity_ordered;

            if ($quantity_ordered > $current_quantity) {
                throw new Exception("Ordered quantity ($quantity_ordered) exceeds total available quantity ($current_quantity).");
            }
            if ($latest_remaining_quantity !== NULL && $quantity_ordered > $latest_remaining_quantity) {
                throw new Exception("Ordered quantity ($quantity_ordered) exceeds remaining quantity ($latest_remaining_quantity).");
            }

            $remaining_quantity = $latest_remaining_quantity - $quantity_ordered;
            if ($remaining_quantity <= 0) {
                $remaining_quantity = 'Item Sold';
            } else {
                $remaining_quantity = (string)$remaining_quantity;
            }

            $sql_insert = "INSERT INTO orders (buyer_id, farmerName, user_id, price_per_unit, produce_id, order_date, quantity, remaining_quantity, quantity_ordered, delivery_address, delivery_date, total_amount, notes, has_ordered)
                           VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isidisissisdi", $buyer_id, $farmerName, $farmer_user_id, $price_per_unit, $produce_id, $quantity_ordered, $remaining_quantity, $quantity_ordered, $delivery_address, $delivery_date, $total_amount, $notes, $has_ordered);
            
            if ($stmt_insert->execute()) {
                $sql_update_has_order = "UPDATE produce_listings SET has_order = 1 WHERE prod_id = ?";
                $stmt_update_has_order = $conn->prepare($sql_update_has_order);
                $stmt_update_has_order->bind_param("i", $produce_id);
                if (!$stmt_update_has_order->execute()) {
                    throw new Exception("Error updating order status: " . $stmt_update_has_order->error);
                }
                $stmt_update_has_order->close();

                $conn->commit();
                echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
            } else {
                throw new Exception("Error placing order: " . $stmt_insert->error);
            }
            $stmt_insert->close();
        } else {
            throw new Exception("Selected produce not found.");
        }
        $stmt_produce_info->close();
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>