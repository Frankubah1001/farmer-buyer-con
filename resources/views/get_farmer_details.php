<?php
// get_farmer_details.php
header('Content-Type: application/json');

// NOTE: Ensure DBcon.php connects to the database and sets the $conn variable.
include 'DBcon.php'; 

// Function to handle database errors
function handle_db_error($conn, $stmt = null) {
    if ($stmt) {
        error_log("SQL Error: " . $stmt->error);
    } else {
        error_log("DB Connection Error: " . $conn->error);
    }
    echo json_encode(['error' => 'A database error occurred.']);
    exit;
}

// Check for the specific farmer_id parameter to determine action
if (isset($_GET['farmer_id'])) {
    // === ACTION 1: HANDLE SINGLE FARMER DETAILS (MODAL VIEW) ===
    $farmerId = intval($_GET['farmer_id']);

    if ($farmerId <= 0) {
        echo json_encode(['error' => 'Invalid Farmer ID']);
        exit;
    }

    // A. Fetch Farmer Details (This query is already correct)
    $sql_farmer = "SELECT user_id, first_name, last_name, email, phone, address FROM users WHERE user_id = ?";
    
    if (!$stmt_farmer = $conn->prepare($sql_farmer)) {
        handle_db_error($conn);
    }
    $stmt_farmer->bind_param("i", $farmerId);
    $stmt_farmer->execute();
    $result_farmer = $stmt_farmer->get_result();
    $farmer = $result_farmer->fetch_assoc();
    $stmt_farmer->close();

    // B. Fetch Produce Listings (Placeholder)
    $sql_produce = "SELECT prod_id, produce, quantity, price, available_date FROM produce_listings WHERE user_id = ?";
    if (!$stmt_produce = $conn->prepare($sql_produce)) {
        $produce_listings = []; 
    } else {
        $stmt_produce->bind_param("i", $farmerId);
        $stmt_produce->execute();
        $result_produce = $stmt_produce->get_result();
        $produce_listings = [];
        while ($row_produce = $result_produce->fetch_assoc()) {
            $produce_listings[] = $row_produce;
        }
        $stmt_produce->close();
    }

    // C. Fetch Ratings (Placeholder)
    $sql_ratings = "SELECT rating, comment, created_at FROM ratings WHERE user_id = ?";
    if (!$stmt_ratings = $conn->prepare($sql_ratings)) {
        $ratings = []; 
    } else {
        $stmt_ratings->bind_param("i", $farmerId);
        $stmt_ratings->execute();
        $result_ratings = $stmt_ratings->get_result();
        $ratings = [];
        while ($row_ratings = $result_ratings->fetch_assoc()) {
            $ratings[] = $row_ratings;
        }
        $stmt_ratings->close();
    }

    // Final output for Modal Details
    if ($farmer) {
        echo json_encode([
            'farmer' => $farmer,
            'produce_listings' => $produce_listings,
            'ratings' => $ratings,
        ]);
    } else {
        echo json_encode(['error' => 'Farmer not found']);
    }

} else {
    // === ACTION 2: HANDLE PAGINATED FARMER LIST (MAIN TABLE VIEW) ===
    
    // Define pagination and filters
    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $rowsPerPage = 5; 
    $locationFilter = isset($_GET['location']) ? trim($_GET['location']) : '';
    $contactFilter = isset($_GET['contact']) ? trim($_GET['contact']) : '';
    $offset = ($currentPage - 1) * $rowsPerPage;
    
    // Base SQL Query (u.role is REMOVED. Assumes all users are farmers)
    $sql_base = "
        FROM users u
        LEFT JOIN (
            SELECT user_id, AVG(rating) AS rating 
            FROM ratings 
            GROUP BY user_id
        ) r ON u.user_id = r.user_id
        WHERE u.cbn_approved = 1
    ";
    
    $where_clauses = [];
    $params = [];
    $types = '';

    if (!empty($locationFilter)) {
        $where_clauses[] = "u.address LIKE ?";
        $params[] = "%" . $locationFilter . "%";
        $types .= 's';
    }
    if (!empty($contactFilter)) {
        $where_clauses[] = "u.phone LIKE ?";
        $params[] = "%" . $contactFilter . "%";
        $types .= 's';
    }
    
    // Combine base WHERE clause with filters
    $sql_where = count($where_clauses) > 0 ? " AND " . implode(" AND ", $where_clauses) : "";
    
    // 1. Get Total Count for Pagination
    $sql_count = "SELECT COUNT(u.user_id) " . $sql_base . $sql_where;
    if (!$stmt_count = $conn->prepare($sql_count)) {
        handle_db_error($conn);
    }
    if (count($params) > 0) {
        $stmt_count->bind_param($types, ...$params);
    }
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $totalRows = $result_count->fetch_row()[0];
    $stmt_count->close();
    
    $totalPages = ceil($totalRows / $rowsPerPage);
    
    // 2. Fetch Farmer Data (Paginated)
    $sql_data = "
        SELECT u.user_id, u.first_name, u.last_name, u.address AS location, u.phone AS contact, u.email, r.rating
        " . $sql_base . $sql_where . "
        LIMIT ? OFFSET ?
    ";
    
    $params_data = $params;
    $types_data = $types . 'ii';
    $params_data[] = $rowsPerPage;
    $params_data[] = $offset;

    if (!$stmt_data = $conn->prepare($sql_data)) {
        handle_db_error($conn);
    }

    $stmt_data->bind_param($types_data, ...$params_data);
    $stmt_data->execute();
    $result_data = $stmt_data->get_result();
    
    $farmers = [];
    while ($row = $result_data->fetch_assoc()) {
        $row['rating'] = $row['rating'] !== null ? floatval($row['rating']) : null;
        $farmers[] = $row;
    }
    $stmt_data->close();
    
    // Final output for Main List
    echo json_encode([
        'farmers' => $farmers,
        'pagination' => [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalRows' => $totalRows,
        ]
    ]);
}

$conn->close();
?>