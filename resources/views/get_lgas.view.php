<?php
include 'DBcon.php';

if (isset($_GET['state_id']) && !empty($_GET['state_id'])) {
    $stateId = mysqli_real_escape_string($conn, $_GET['state_id']);
    $lgaResult = mysqli_query($conn, "SELECT city_id, city_name FROM cities WHERE state_id = $stateId ORDER BY city_name ASC");
    $lgas = [];
    while ($row = mysqli_fetch_assoc($lgaResult)) {
        $lgas[] = ['id' => $row['city_id'], 'name' => $row['city_name']];
    }
    mysqli_free_result($lgaResult);
    echo json_encode($lgas);
} else {
    echo json_encode([]); // Return empty array if no state is selected
}
mysqli_close($conn);
?>