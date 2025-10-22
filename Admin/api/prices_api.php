<?php
// prices_api.php
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

// Export to Excel functionality (unchanged)
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="price_ranges_export_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    $sql = "SELECT * FROM price_ranges ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    echo "Price Ranges Export\n\n";
    echo "Produce Type\tMin Price (₦)\tMax Price (₦)\tStatus\tReason\tCreated Date\tUpdated Date\n";
    
    while($price = $result->fetch_assoc()) {
        $produce_type = str_replace(["\t", "\n", "\r"], ' ', $price['produce_type']);
        $reason = str_replace(["\t", "\n", "\r"], ' ', $price['reason'] ?? '');
        
        echo $produce_type . "\t";
        echo "₦" . number_format($price['min_price'], 2) . "\t";
        echo "₦" . number_format($price['max_price'], 2) . "\t";
        echo ucfirst($price['status']) . "\t";
        echo $reason . "\t";
        echo $price['created_at'] . "\t";
        echo $price['updated_at'] . "\n";
    }
    exit;
}

try {
    // Get all price ranges with pagination
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        // Pagination parameters
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10; // Default 10 items per page
        $offset = ($page - 1) * $limit;

        // Get total number of records for pagination
        $count_sql = "SELECT COUNT(*) as total FROM price_ranges";
        $count_result = $conn->query($count_sql);
        $total_records = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $limit);

        // Fetch paginated data
        $sql = "SELECT * FROM price_ranges ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            throw new Exception("Database error: " . $conn->error);
        }
        
        $prices = [];
        while($row = $result->fetch_assoc()) {
            $prices[] = $row;
        }
        
        // Include pagination info in response
        sendResponse(true, '', [
            'prices' => $prices,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total_records,
                'limit' => $limit
            ]
        ]);
    }

    // Handle POST requests (unchanged)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            $required_fields = ['produce_type', 'min_price', 'max_price'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    sendResponse(false, "Field '$field' is required");
                }
            }
            
            $produce_type = $conn->real_escape_string($_POST['produce_type']);
            $min_price = floatval($_POST['min_price']);
            $max_price = floatval($_POST['max_price']);
            
            if ($min_price >= $max_price) {
                sendResponse(false, "Minimum price must be less than maximum price");
            }
            
            $check_sql = "SELECT price_id FROM price_ranges WHERE produce_type = '$produce_type'";
            $check_result = $conn->query($check_sql);
            if ($check_result->num_rows > 0) {
                sendResponse(false, "Price range for '$produce_type' already exists");
            }
            
            $sql = "INSERT INTO price_ranges (produce_type, min_price, max_price) 
                    VALUES ('$produce_type', '$min_price', '$max_price')";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Price range added successfully');
            } else {
                throw new Exception('Error adding price range: ' . $conn->error);
            }
        }

        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            if (empty($_POST['price_id'])) {
                sendResponse(false, "Price ID is required");
            }
            
            $price_id = intval($_POST['price_id']);
            $min_price = floatval($_POST['min_price']);
            $max_price = floatval($_POST['max_price']);
            
            if ($min_price >= $max_price) {
                sendResponse(false, "Minimum price must be less than maximum price");
            }
            
            $sql = "UPDATE price_ranges SET 
                    min_price = '$min_price', 
                    max_price = '$max_price',
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE price_id = $price_id";
            
            if ($conn->query($sql)) {
                if ($conn->affected_rows > 0) {
                    sendResponse(true, 'Price range updated successfully');
                } else {
                    sendResponse(false, 'No changes made or price range not found');
                }
            } else {
                throw new Exception('Error updating price range: ' . $conn->error);
            }
        }

        if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
            if (empty($_POST['price_id']) || empty($_POST['current_status'])) {
                sendResponse(false, "Price ID and current status are required");
            }
            
            $price_id = intval($_POST['price_id']);
            $current_status = $conn->real_escape_string($_POST['current_status']);
            $reason = $conn->real_escape_string($_POST['reason'] ?? '');
            $new_status = $current_status === 'active' ? 'disabled' : 'active';
            
            $sql = "UPDATE price_ranges SET 
                    status = '$new_status', 
                    reason = '$reason',
                    updated_at = CURRENT_TIMESTAMP 
                    WHERE price_id = $price_id";
            
            if ($conn->query($sql)) {
                sendResponse(true, 'Status updated successfully', ['new_status' => $new_status]);
            } else {
                throw new Exception('Error updating status: ' . $conn->error);
            }
        }

        sendResponse(false, "Invalid action specified");
    }

} catch (Exception $e) {
    sendResponse(false, $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>