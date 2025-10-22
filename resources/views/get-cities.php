<?php
include 'DBcon.php';

header('Content-Type: application/json');

if (isset($_POST['state_id'])) {
    $state_id = intval($_POST['state_id']);
    
    // Validate state_id
    if ($state_id <= 0) {
        echo json_encode(['error' => 'Invalid state ID']);
        exit;
    }

    $query = "SELECT city_id, city_name FROM cities WHERE state_id = ? ORDER BY city_name ASC";
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        echo json_encode(['error' => 'Database error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $state_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['error' => 'Query execution failed']);
        exit;
    }

    $result = mysqli_stmt_get_result($stmt);
    $cities = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = $row;
    }

    if (empty($cities)) {
        echo json_encode(['error' => 'No LGAs found for this state']);
    } else {
        echo json_encode($cities);
    }
} else {
    echo json_encode(['error' => 'State ID not provided']);
}
?>