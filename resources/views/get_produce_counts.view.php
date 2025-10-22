<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include 'DBcon.php';

header('Content-Type: application/json');

$sql = "SELECT produce, COUNT(*) as count FROM produce_listings 
        WHERE (produce = 'Beans' OR produce = 'Yam' OR produce = 'Rice') AND user_id = ? 
        GROUP BY produce"; // Corrected SQL

$result = mysqli_query($conn, $sql);

$counts = array();
$totalCount = 0; // Initialize total count

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $counts[$row['produce']] = (int)$row['count'];
        $totalCount += (int)$row['count']; // Add to total count
    }
}

$response = array(
    'counts' => $counts,
    'total' => $totalCount
);

echo json_encode($response);

mysqli_close($conn);
?>