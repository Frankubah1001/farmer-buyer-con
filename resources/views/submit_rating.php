<?php
header('Content-Type: application/json');
session_start();
include '../DBcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['buyer_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Buyer not authenticated.']);
        exit;
    }
    $rated_by_user_id = $_SESSION['buyer_id'];

    $farmer_id = isset($_POST['farmer_id']) ? intval($_POST['farmer_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : null;

    if ($farmer_id <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid rating data. Please check farmer and rating.']);
        exit;
    }

    // Allow a buyer to rate farmers (even if their user ID matches a farmer's)
    // if ($rated_by_user_id === $farmer_id) {
    //     echo json_encode(['status' => 'error', 'message' => 'You cannot rate yourself.']);
    //     exit;
    // }

    // Check if the buyer has already rated this farmer
    $check_rating_sql = "SELECT r.rat_id
                           FROM ratings r
                           JOIN users u ON r.user_id = u.user_id
                           WHERE r.user_id = ? AND r.buyer_id = ?";
    $check_rating_stmt = $conn->prepare($check_rating_sql);
    $check_rating_stmt->bind_param("ii", $farmer_id, $rated_by_user_id);
    $check_rating_stmt->execute();
    $check_rating_result = $check_rating_stmt->get_result();

    if ($check_rating_result->num_rows > 0) {
        // Buyer has already rated
        echo json_encode(['status' => 'error', 'message' => 'You have already rated this farmer.']);
        $check_rating_stmt->close();
        $conn->close();
        exit;
    }
    $check_rating_stmt->close();

    // Insert the rating into the database
    $insert_rating_sql = "INSERT INTO ratings (user_id, buyer_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())";
    $insert_rating_stmt = $conn->prepare($insert_rating_sql);
    $insert_rating_stmt->bind_param("iiis", $farmer_id, $rated_by_user_id, $rating, $comment);

    if ($insert_rating_stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Rating submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting rating: ' . $insert_rating_stmt->error]);
    }

    $insert_rating_stmt->close();
    $conn->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>