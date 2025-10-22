<?php
// get_unique_filters.php
include 'DBcon.php';

header('Content-Type: application/json');

$data = [];

// Get unique produce values
$sql_produce = "SELECT DISTINCT produce FROM produce_listings WHERE is_deleted = FALSE ORDER BY produce";
$result_produce = mysqli_query($conn, $sql_produce);
if ($result_produce) {
    $produce = [];
    while ($row = mysqli_fetch_assoc($result_produce)) {
        $produce[] = $row['produce'];
    }
    $data['produce'] = $produce;
} else {
    $data['produce_error'] = "Error fetching produce: " . mysqli_error($conn);
}

// Get unique condition values
$sql_condition = "SELECT DISTINCT conditions FROM produce_listings WHERE is_deleted = FALSE AND conditions IS NOT NULL ORDER BY conditions";
$result_condition = mysqli_query($conn, $sql_condition);
if ($result_condition) {
    $conditions = [];
    while ($row = mysqli_fetch_assoc($result_condition)) {
        $conditions[] = $row['conditions'];
    }
    $data['conditions'] = $conditions;
} else {
    $data['conditions_error'] = "Error fetching conditions: " . mysqli_error($conn);
}

echo json_encode($data);

mysqli_close($conn);
?>