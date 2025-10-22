
<?php
include 'DBcon.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT DISTINCT pl.address
            FROM produce_listings pl
            JOIN users u ON pl.user_id = u.user_id
            WHERE pl.cbn_approve = 1 AND pl.is_deleted = 0
            ORDER BY pl.address ASC";

    $result = mysqli_query($conn, $sql);

    $locations = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $locations[] = $row['address'];
        }
    }

    echo json_encode($locations);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>