<?php
// orders_api.php
include 'DBcon.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to send JSON response
function sendResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Export to CSV functionality
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="orders_export_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: max-age=0');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output CSV headers
    fputcsv($output, [
        'Order ID', 'Buyer Name', 'Farmer Name', 'Produce', 'Quantity', 'Total Amount',
        'Order Status', 'Order Date', 'Delivery Address', 'Delivery Date', 'Payment Status', 'Notes'
    ]);

    // Fetch all orders
    $sql = "SELECT o.order_id, CONCAT(b.firstname, ' ', b.lastname) AS buyer_name, 
                   o.farmerName AS farmer_name, pl.produce, o.quantity, o.total_amount,
                   o.order_status, o.order_date, o.delivery_address, o.delivery_date,
                   o.payment_status, o.notes
            FROM orders o
            JOIN buyers b ON o.buyer_id = b.buyer_id
            JOIN produce_listings pl ON o.produce_id = pl.prod_id
            ORDER BY o.order_date DESC";
    $result = $conn->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                '#' . $row['order_id'],
                $row['buyer_name'],
                $row['farmer_name'],
                $row['produce'],
                $row['quantity'],
                '₦' . number_format($row['total_amount'], 2),
                $row['order_status'],
                date('d M Y', strtotime($row['order_date'])),
                $row['delivery_address'],
                $row['delivery_date'],
                ucfirst($row['payment_status']),
                $row['notes'] ?? ''
            ]);
        }
    }

    fclose($output);
    exit;
}

// Handle GET requests
try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        // Pagination parameters
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;

        // Filter parameters
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
        $fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';
        $toDate = isset($_GET['toDate']) ? $_GET['toDate'] : '';

        // Build SQL query for counting total records
        $count_sql = "SELECT COUNT(*) as total 
                      FROM orders o
                      JOIN buyers b ON o.buyer_id = b.buyer_id
                      JOIN produce_listings pl ON o.produce_id = pl.prod_id
                      WHERE 1=1";
        if ($search) {
            $count_sql .= " AND (o.paystack_reference LIKE ? OR o.order_id LIKE ? OR b.firstname LIKE ? OR b.lastname LIKE ? OR o.farmerName LIKE ? OR pl.produce LIKE ?)";
        }
        if ($status) {
            $count_sql .= " AND o.order_status = ?";
        }
        if ($fromDate) {
            $count_sql .= " AND o.order_date >= ?";
        }
        if ($toDate) {
            $count_sql .= " AND o.order_date <= ?";
        }

        $count_stmt = $conn->prepare($count_sql);
        if ($search && $status && $fromDate && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('sssssssss', $like, $like, $like, $like, $like, $like, $status, $fromDate, $toDateEnd);
        } elseif ($search && $fromDate && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('ssssssss', $like, $like, $like, $like, $like, $like, $fromDate, $toDateEnd);
        } elseif ($search && $status) {
            $like = "%$search%";
            $count_stmt->bind_param('sssssss', $like, $like, $like, $like, $like, $like, $status);
        } elseif ($search && $fromDate) {
            $like = "%$search%";
            $count_stmt->bind_param('sssssss', $like, $like, $like, $like, $like, $like, $fromDate);
        } elseif ($search && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('sssssss', $like, $like, $like, $like, $like, $like, $toDateEnd);
        } elseif ($search) {
            $like = "%$search%";
            $count_stmt->bind_param('ssssss', $like, $like, $like, $like, $like, $like);
        } elseif ($status && $fromDate && $toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('sss', $status, $fromDate, $toDateEnd);
        } elseif ($status) {
            $count_stmt->bind_param('s', $status);
        } elseif ($fromDate && $toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('ss', $fromDate, $toDateEnd);
        } elseif ($fromDate) {
            $count_stmt->bind_param('s', $fromDate);
        } elseif ($toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $count_stmt->bind_param('s', $toDateEnd);
        }

        $count_stmt->execute();
        $total_records = $count_stmt->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        // Build SQL query for paginated data
        $sql = "SELECT o.order_id, CONCAT(b.firstname, ' ', b.lastname) AS buyer_name, 
                       o.farmerName AS farmer_name, pl.produce, o.quantity, o.total_amount,
                       o.order_status, o.order_date, b.email AS buyer_email, b.phone AS buyer_phone,
                       b.address AS buyer_address, u.email AS farmer_email, u.phone AS farmer_phone,
                       u.farm_full_address AS farmer_address, o.price_per_unit, o.delivery_address,
                       o.delivery_date, o.payment_status, o.payment_date, o.paystack_reference,
                       o.notes, o.created_at, o.updated_at
                FROM orders o
                JOIN buyers b ON o.buyer_id = b.buyer_id
                JOIN produce_listings pl ON o.produce_id = pl.prod_id
                JOIN users u ON o.user_id = u.user_id
                WHERE 1=1";
        if ($search) {
            $sql .= " AND (o.paystack_reference LIKE ? OR o.order_id LIKE ? OR b.firstname LIKE ? OR b.lastname LIKE ? OR o.farmerName LIKE ? OR pl.produce LIKE ?)";
        }
        if ($status) {
            $sql .= " AND o.order_status = ?";
        }
        if ($fromDate) {
            $sql .= " AND o.order_date >= ?";
        }
        if ($toDate) {
            $sql .= " AND o.order_date <= ?";
        }
        $sql .= " ORDER BY o.order_date DESC LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters dynamically based on filters
        if ($search && $status && $fromDate && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('sssssssssii', $like, $like, $like, $like, $like, $like, $status, $fromDate, $toDateEnd, $limit, $offset);
        } elseif ($search && $fromDate && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('ssssssssii', $like, $like, $like, $like, $like, $like, $fromDate, $toDateEnd, $limit, $offset);
        } elseif ($search && $status) {
            $like = "%$search%";
            $stmt->bind_param('sssssssii', $like, $like, $like, $like, $like, $like, $status, $limit, $offset);
        } elseif ($search && $fromDate) {
            $like = "%$search%";
            $stmt->bind_param('sssssssii', $like, $like, $like, $like, $like, $like, $fromDate, $limit, $offset);
        } elseif ($search && $toDate) {
            $like = "%$search%";
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('sssssssii', $like, $like, $like, $like, $like, $like, $toDateEnd, $limit, $offset);
        } elseif ($search) {
            $like = "%$search%";
            $stmt->bind_param('ssssssii', $like, $like, $like, $like, $like, $like, $limit, $offset);
        } elseif ($status && $fromDate && $toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('sssii', $status, $fromDate, $toDateEnd, $limit, $offset);
        } elseif ($status) {
            $stmt->bind_param('sii', $status, $limit, $offset);
        } elseif ($fromDate && $toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('ssii', $fromDate, $toDateEnd, $limit, $offset);
        } elseif ($fromDate) {
            $stmt->bind_param('sii', $fromDate, $limit, $offset);
        } elseif ($toDate) {
            $toDateEnd = $toDate . ' 23:59:59';
            $stmt->bind_param('sii', $toDateEnd, $limit, $offset);
        } else {
            $stmt->bind_param('ii', $limit, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = [
                'order_id' => $row['order_id'],
                'buyer_name' => $row['buyer_name'],
                'farmer_name' => $row['farmer_name'],
                'produce' => $row['produce'],
                'quantity' => $row['quantity'],
                'total_amount' => $row['total_amount'],
                'order_status' => $row['order_status'],
                'order_date' => $row['order_date'],
                'buyer_email' => $row['buyer_email'],
                'buyer_phone' => $row['buyer_phone'],
                'buyer_address' => $row['buyer_address'],
                'farmer_email' => $row['farmer_email'],
                'farmer_phone' => $row['farmer_phone'],
                'farmer_address' => $row['farmer_address'],
                'price_per_unit' => $row['price_per_unit'],
                'delivery_address' => $row['delivery_address'],
                'delivery_date' => $row['delivery_date'],
                'payment_status' => $row['payment_status'],
                'payment_date' => $row['payment_date'],
                'paystack_reference' => $row['paystack_reference'],
                'notes' => $row['notes'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
        }

        sendResponse(true, '', [
            'orders' => $orders,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records,
                'limit' => $limit
            ]
        ]);
    }

} catch (Exception $e) {
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>