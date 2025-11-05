<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'DBcon.php';

function createOrderAndUpdates(mysqli $conn, array $data): bool {
    $buyer_id            = $data['buyer_id'];
    $farmerName          = $data['farmerName'];
    $farmer_user_id      = $data['farmer_user_id'];
    $price_per_unit      = $data['price_per_unit'];
    $produce_id          = $data['produce_id'];
    $quantity_ordered    = $data['quantity_ordered'];
    $remaining_quantity  = $data['remaining_quantity'];  // string: "123" or "Item Sold"
    $delivery_address    = $data['delivery_address'];
    $delivery_date       = $data['delivery_date_raw'];
    $total_amount        = $data['total_amount'];
    $notes               = $data['notes'];
    $has_ordered         = $data['has_ordered'];

    $sql_insert = "INSERT INTO orders (
                       buyer_id, farmerName, user_id, price_per_unit, produce_id, order_date, 
                       quantity, remaining_quantity, quantity_ordered, delivery_address, 
                       delivery_date, total_amount, notes, has_ordered
                   ) VALUES (
                       ?, ?, ?, ?, ?, NOW(), 
                       ?, ?, ?, ?, 
                       ?, ?, ?, ?
                   )";

    $stmt = $conn->prepare($sql_insert);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // === DYNAMIC BINDING: Build types & params safely ===
    $params = [
        $buyer_id,
        $farmerName,
        $farmer_user_id,
        $price_per_unit,
        $produce_id,
        $quantity_ordered,
        $remaining_quantity,
        $quantity_ordered,
        $delivery_address,
        $delivery_date,
        $total_amount,
        $notes,
        $has_ordered
    ];

    // Build type string dynamically
    $types = '';
    foreach ($params as $param) {
        if (is_int($param)) {
            $types .= 'i';
        } elseif (is_float($param) || is_double($param)) {
            $types .= 'd';
        } else {
            $types .= 's'; // everything else as string (safe default)
        }
    }

    // Bind dynamically
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();

    // === Update has_order ===
    $sql_update = "UPDATE produce_listings SET has_order = 1 WHERE prod_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $produce_id);
    if (!$stmt_update->execute()) {
        throw new Exception("Update failed: " . $stmt_update->error);
    }
    $stmt_update->close();

    return true;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $buyer_id = isset($_SESSION['buyer_id']) ? intval($_SESSION['buyer_id']) : 0;
    $produce_id = intval($_POST['prod_id']);
    $has_ordered = 0;
    $quantity_ordered = intval($_POST['quantity']);
    $delivery_address = htmlspecialchars($_POST['deliveryAddress']);
    $delivery_date_raw = $_POST['deliveryDate'];
    $notes = htmlspecialchars($_POST['notes']);
    $farmerName = htmlspecialchars($_POST['farmerName']);

    // Basic validation
    if ($buyer_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to place an order.']);
        exit;
    }

    if ($produce_id <= 0 || $quantity_ordered <= 0 || empty($delivery_address) || empty($delivery_date_raw)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid order details. Please check all fields.']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $delivery_date_raw)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid delivery date format. Must be YYYY-MM-DD.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        $sql_produce_info = "SELECT pl.quantity, pl.price, u.user_id AS farmer_user_id,
                            COALESCE((SELECT remaining_quantity FROM orders WHERE produce_id = ? 
                                     AND order_id = (
                                         SELECT MAX(order_id) 
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
            
            $current_listing_quantity = intval($row_produce_info['quantity']);
            $latest_remaining_quantity = $row_produce_info['latest_remaining_quantity'] === 'Item Sold' 
                                         ? 0 
                                         : intval($row_produce_info['latest_remaining_quantity']);
            $price_per_unit = floatval(str_replace(',', '', $row_produce_info['price']));
            $farmer_user_id = intval($row_produce_info['farmer_user_id']);
            $total_amount = $price_per_unit * $quantity_ordered;

            if ($quantity_ordered > $current_listing_quantity) {
                throw new Exception("Ordered quantity ($quantity_ordered) exceeds total available quantity ($current_listing_quantity).");
            }
            if ($latest_remaining_quantity !== NULL && $quantity_ordered > $latest_remaining_quantity) {
                throw new Exception("Ordered quantity ($quantity_ordered) exceeds remaining stock ($latest_remaining_quantity).");
            }
            $new_remaining_quantity_val = $latest_remaining_quantity - $quantity_ordered;
            
            if ($new_remaining_quantity_val <= 0) {
                $remaining_quantity_db = 'Item Sold';
            } else {
                $remaining_quantity_db = (string)$new_remaining_quantity_val;
            }
            
            $stmt_produce_info->close();

            $order_data = [
                'buyer_id'           => $buyer_id,
                'farmerName'         => $farmerName,
                'farmer_user_id'     => $farmer_user_id,
                'price_per_unit'     => $price_per_unit,
                'produce_id'         => $produce_id,
                'quantity_ordered'   => $quantity_ordered,
                'remaining_quantity' => $remaining_quantity_db,
                'delivery_address'   => $delivery_address,
                'delivery_date_raw'  => $delivery_date_raw,
                'total_amount'       => $total_amount,
                'notes'              => $notes,
                'has_ordered'        => $has_ordered,
            ];

            if (createOrderAndUpdates($conn, $order_data)) {
                $conn->commit();
                echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
            } 

        } else {
            $stmt_produce_info->close();
            throw new Exception("Selected produce not found or not available today.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>