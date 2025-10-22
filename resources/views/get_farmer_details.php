<?php
// get_farmer_details.php
header('Content-Type: application/json');
include 'DBcon.php';

if (!isset($_GET['farmer_id'])) {
    echo json_encode(['error' => 'Farmer ID is required']);
    exit;
}

$farmerId = intval($_GET['farmer_id']);

// Fetch farmer details
$sql_farmer = "SELECT user_id, first_name, last_name, email, phone, address FROM users WHERE user_id = ?";
$stmt_farmer = $conn->prepare($sql_farmer);
$stmt_farmer->bind_param("i", $farmerId);
$stmt_farmer->execute();
$result_farmer = $stmt_farmer->get_result();
$farmer = $result_farmer->fetch_assoc();
$stmt_farmer->close();

// Fetch produce listings
$sql_produce = "SELECT prod_id, produce, quantity, price, available_date FROM produce_listings WHERE user_id = ?";
$stmt_produce = $conn->prepare($sql_produce);
$stmt_produce->bind_param("i", $farmerId);
$stmt_produce->execute();
$result_produce = $stmt_produce->get_result();
$produce_listings = [];
while ($row_produce = $result_produce->fetch_assoc()) {
    $produce_listings[] = $row_produce;
}
$stmt_produce->close();

// Fetch ratings
$sql_ratings = "SELECT rating, comment, created_at FROM ratings WHERE user_id = ?";
$stmt_ratings = $conn->prepare($sql_ratings);
$stmt_ratings->bind_param("i", $farmerId);
$stmt_ratings->execute();
$result_ratings = $stmt_ratings->get_result();
$ratings = [];
while ($row_ratings = $result_ratings->fetch_assoc()) {
    $ratings[] = $row_ratings;
}
$stmt_ratings->close();

$conn->close();

if ($farmer) {
    echo json_encode([
        'farmer' => $farmer,
        'produce_listings' => $produce_listings,
        'ratings' => $ratings,
    ]);
} else {
    echo json_encode(['error' => 'Farmer not found']);
}
?>