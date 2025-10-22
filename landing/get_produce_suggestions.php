
<?php
include 'DBcon.php';

header('Content-Type: application/json');

try {
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';

    if (empty($query)) {
        echo json_encode([]);
        exit;
    }

    $sql = "SELECT DISTINCT produce
            FROM produce_listings
            WHERE produce LIKE ? AND cbn_approve = 1 AND is_deleted = 0
            ORDER BY produce ASC
            LIMIT 10";

    $stmt = mysqli_prepare($conn, $sql);
    $searchTerm = "%" . $query . "%";
    mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $suggestions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $suggestions[] = $row['produce'];
        }
    }

    echo json_encode($suggestions);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>