<?php
// get_unique_locations.php
include 'DBcon.php';

header('Content-Type: application/json');

$sql = "SELECT DISTINCT address FROM produce_listings WHERE is_deleted = FALSE ORDER BY address";
$result = mysqli_query($conn, $sql);

$locations = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $locations[] = $row['address'];
    }
}

echo json_encode($locations);

mysqli_close($conn);
?>