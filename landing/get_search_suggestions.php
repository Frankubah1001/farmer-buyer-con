<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DBcon.php';

header('Content-Type: application/json');

try {
    $query = isset($_GET['query']) ? mysqli_real_escape_string($conn, trim($_GET['query'])) : '';
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }

    $suggestions = [];
    // Search produce and farmer details
    $sql = "SELECT pl.produce AS value, CONCAT(pl.produce, ' - ', u.full_name, ' (', pl.address, ')') AS label
            FROM produce_listings pl
            JOIN users u ON pl.user_id = u.user_id
            WHERE pl.produce LIKE ? OR u.full_name LIKE ? OR pl.address LIKE ?
            LIMIT 5";
    $searchTerm = "%$query%";
    $stmt = mysqli_prepare($conn, $sql);
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    echo json_encode($suggestions);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching suggestions: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>