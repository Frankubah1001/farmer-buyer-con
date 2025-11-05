<?php
// Include your database connection file
require_once '../DBcon.php'; // Adjust path as needed

header('Content-Type: application/json');

// --- Configuration ---
$perPage = 5;

// Function to fetch the logged-in buyer's latest PAID order
function getBuyerLatestPaidOrder($conn, $buyer_id) {
    $sql = "
        SELECT 
            o.order_id, o.farmerName, o.quantity, o.price_per_unit, 
            o.delivery_address, o.delivery_date, o.order_status, 
            o.produce_id, o.total_amount, o.payment_status,
            p.name as product_name
        FROM orders o
        LEFT JOIN products p ON o.produce_id = p.id
        WHERE o.buyer_id = ? AND o.payment_status = 'Paid'
        ORDER BY o.order_date DESC
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $buyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
    
    if ($order) {
        // Get transport cost from transporters table (fees column)
        $feeSql = "SELECT fees FROM transporters LIMIT 1";
        $feeResult = $conn->query($feeSql);
        
        if ($feeResult && $feeRow = $feeResult->fetch_assoc()) {
            $order['transport_cost'] = $feeRow['fees'];
        } else {
            // Fallback: use default fee
            $order['transport_cost'] = 24120;
        }
        
        // Clean up data for display
        $order['price_per_unit'] = (float)$order['price_per_unit'];
        $order['total_amount'] = (float)$order['total_amount'];
        
        // Extract delivery city from address
        $order['delivery_city'] = explode(',', $order['delivery_address'])[0] ?? 'Unknown';
        
        return $order;
    }
    
    return null;
}

// --- Handle Order Fetch Request ---
if (isset($_GET['action']) && $_GET['action'] === 'fetch_order') {
    try {
        $buyer_id = (int)($_GET['buyer_id'] ?? 1); 
        $order = getBuyerLatestPaidOrder($conn, $buyer_id);
        
        if ($order) {
            echo json_encode(['success' => true, 'order' => $order]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No paid orders found for this buyer.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

// --- NEW: Handle Location Fetch Request for Dropdown ---
if (isset($_GET['action']) && $_GET['action'] === 'fetch_locations') {
    try {
        $locations = [];
        
        // Query to get distinct, non-empty locations, prioritizing City/State combination
        $locSql = "
            SELECT DISTINCT 
                COALESCE(
                    NULLIF(TRIM(CONCAT(c.city_name, ', ', s.state_name)), ', '), 
                    NULLIF(TRIM(t.operating_areas), '')
                ) as display_location
            FROM transporters t
            LEFT JOIN states s ON t.state_id = s.state_id 
            LEFT JOIN cities c ON t.city_id = c.city_id
            HAVING display_location IS NOT NULL AND TRIM(display_location) != ''
            ORDER BY display_location ASC
        ";
        
        $result = $conn->query($locSql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $locations[] = $row['display_location'];
            }
            echo json_encode(['success' => true, 'locations' => $locations]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database query failed for locations.']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
// --- END NEW LOCATION FETCH ---


// --- Main API Logic for Transporters ---
try {
    // Get parameters for Transporter list
    $page          = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search        = $_GET['search'] ?? '';
    $location      = $_GET['location'] ?? ''; // Filter value (e.g., 'Lagos, Lagos')
    $status        = $_GET['status'] ?? '';
    $offset        = ($page - 1) * $perPage;
    
    // Sanitize input
    $search_safe = '%' . $conn->real_escape_string($search) . '%';
    $location_safe = $conn->real_escape_string($location);
    
    // --- Build WHERE Clause and Parameters for Filtering ---
    $where = "WHERE 1=1";
    $filterParams = [];
    $filterTypes = '';

    if (!empty($search)) {
        $where .= " AND (t.company_name LIKE ? OR t.contact_person LIKE ? OR t.email LIKE ? OR t.phone LIKE ?)";
        $filterParams[] = $search_safe;
        $filterParams[] = $search_safe;
        $filterParams[] = $search_safe;
        $filterParams[] = $search_safe;
        $filterTypes .= 'ssss';
    }

    // **FIXED LOCATION FILTERING LOGIC**
    if (!empty($location)) {
        // The filter must match the combined location string OR the operating_areas field
        $where .= " AND (
                        TRIM(CONCAT(c.city_name, ', ', s.state_name)) = ? 
                        OR t.operating_areas LIKE ?
                    )";
        $filterParams[] = $location_safe;
        $filterParams[] = '%' . $location_safe . '%';
        $filterTypes .= 'ss';
    }

    if (!empty($status)) {
        if ($status === 'verified') {
            $where .= " AND t.is_verified = 1";
        } elseif ($status === 'pending') {
            $where .= " AND t.is_verified = 0";
        }
    }

    // --- 1. Get Total Count for Pagination ---
    $countSql = "SELECT COUNT(*) as total FROM transporters t 
                 LEFT JOIN states s ON t.state_id = s.state_id 
                 LEFT JOIN cities c ON t.city_id = c.city_id 
                 $where";
    $countStmt = $conn->prepare($countSql);
    
    if (!empty($filterParams)) {
        $countBindNames = [$filterTypes];
        for ($i=0; $i<count($filterParams); $i++) {
            $countBindNames[] = &$filterParams[$i];
        }
        call_user_func_array([$countStmt, 'bind_param'], $countBindNames);
    }
    
    $countStmt->execute();
    $totalResult = $countStmt->get_result()->fetch_assoc();
    $totalItems = $totalResult['total'];
    $totalPages = ceil($totalItems / $perPage);
    $countStmt->close();

    // --- 2. Fetch Paginated Transporter Data ---
    $sql = "
        SELECT 
            t.transporter_id, t.company_name, t.contact_person, t.email, t.phone, 
            t.vehicle_type, t.vehicle_capacity, t.fees, t.operating_areas, 
            t.address, t.license_number, t.is_verified,
            s.state_name, c.city_name
        FROM transporters t
        LEFT JOIN states s ON t.state_id = s.state_id
        LEFT JOIN cities c ON t.city_id = c.city_id
        $where
        ORDER BY t.is_verified DESC, t.company_name ASC
        LIMIT ? OFFSET ?
    ";
    
    // Prepare final parameters for the main query: Filters + LIMIT + OFFSET
    $fetchParams = $filterParams;
    $fetchTypes = $filterTypes . 'ii';
    
    $fetchParams[] = $perPage;
    $fetchParams[] = $offset;

    $fetchStmt = $conn->prepare($sql);
    
    if (!empty($fetchParams)) {
        $fetchBindNames = [$fetchTypes];
        for ($i=0; $i<count($fetchParams); $i++) {
            $fetchBindNames[] = &$fetchParams[$i];
        }
        call_user_func_array([$fetchStmt, 'bind_param'], $fetchBindNames);
    }

    $fetchStmt->execute();
    $result = $fetchStmt->get_result();
    
    $transporters = [];
    while ($row = $result->fetch_assoc()) {
        $location_display = ($row['city_name'] && $row['state_name']) 
                                ? $row['city_name'] . ', ' . $row['state_name']
                                : ($row['operating_areas'] ?: 'Multiple Locations');
        
        $transporters[] = [
            'id'            => $row['transporter_id'],
            'company'       => $row['company_name'] ?: 'Individual Transporter',
            'contact_person'=> $row['contact_person'],
            'phone'         => $row['phone'],
            'email'         => $row['email'],
            'vehicles'      => $row['vehicle_type'] . ' (' . $row['vehicle_capacity'] . ')',
            'operating_areas' => $row['operating_areas'] ?: 'Nationwide',
            'location'      => $location_display, // Use the combined display for the table
            'price'         => $row['fees'] ?? 24120,
            'is_verified'   => (bool)$row['is_verified'],
            'license_number' => $row['license_number']
        ];
    }
    
    $fetchStmt->close();
    
    // Success response
    echo json_encode([
        'success' => true,
        'data' => [
            'transporters'  => $transporters,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'totalItems'    => $totalItems,
            'perPage'       => $perPage
        ]
    ]);

} catch (Exception $e) {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'General API Error: ' . $e->getMessage()
    ]);
}

?>