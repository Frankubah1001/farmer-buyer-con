<?php
include 'DBcon.php';

header('Content-Type: application/json');

try {
    $action = isset($_GET['action']) ? $_GET['action'] : 'listings';

    if ($action === 'locations') {
        $sql = "SELECT DISTINCT pl.address
                FROM produce_listings pl
                JOIN users u ON pl.user_id = u.user_id
                WHERE u.cbn_approved = 1 AND pl.is_deleted = 0
                ORDER BY pl.address ASC";

        $result = mysqli_query($conn, $sql);

        $locations = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $locations[] = $row['address'];
            }
        }

        echo json_encode($locations);
    } elseif ($action === 'suggestions') {
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';

        if (empty($query)) {
            echo json_encode([]);
            exit;
        }

        $sql = "SELECT DISTINCT pl.produce
                FROM produce_listings pl
                JOIN users u ON pl.user_id = u.user_id
                WHERE pl.produce LIKE ? AND u.cbn_approved = 1 AND pl.is_deleted = 0
                ORDER BY pl.produce ASC
                LIMIT 10";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . mysqli_error($conn));
            echo json_encode(['error' => 'Query preparation failed']);
            exit;
        }

        $searchTerm = "%" . $query . "%";
        mysqli_stmt_bind_param($stmt, "s", $searchTerm);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result === false) {
            error_log("Execute failed: " . mysqli_error($conn));
            echo json_encode(['error' => 'Query execution failed']);
            exit;
        }

        $suggestions = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $suggestions[] = $row['produce'] ?? 'null'; // Fallback for debugging
            }
        } else {
            error_log("No results for query: $query");
        }

        echo json_encode($suggestions);
    } else {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $sql = "SELECT
            pl.prod_id,
            pl.produce,
            pl.image_path,
            pl.address,
            pl.available_date,
            pl.quantity AS uploaded_quantity,
            pl.created_at,
            COALESCE(o.remaining_quantity, 'No Order Yet') AS remaining_quantity,
            CASE 
                WHEN o.remaining_quantity IS NULL THEN 'No Order Yet'
                WHEN o.remaining_quantity = 0 THEN 'Item Sold'
                ELSE (pl.quantity - o.remaining_quantity)
            END AS quantity_ordered,
            pl.price,
            CONCAT(u.first_name, ' ', u.last_name) AS farmer_name,
            u.address AS location,
            IFNULL(AVG(r.rating), 0) AS rating
        FROM
            produce_listings pl
        LEFT JOIN (
            SELECT
                produce_id,
                remaining_quantity
            FROM
                orders
            WHERE
                (order_date, order_id) IN (
                    SELECT
                        MAX(order_date) AS order_date,
                        MAX(order_id) AS order_id
                    FROM
                        orders
                    GROUP BY
                        produce_id
                )
        ) o ON pl.prod_id = o.produce_id
        JOIN users u ON pl.user_id = u.user_id
        LEFT JOIN ratings r ON u.user_id = r.user_id
        WHERE
            u.cbn_approved = 1
            AND pl.is_deleted = 0
            AND pl.available_date <= CURDATE()
            AND (o.remaining_quantity IS NULL OR o.remaining_quantity > 0)
            AND pl.produce LIKE ?
            AND (pl.address = ? OR ? = '')
        GROUP BY
            pl.prod_id,
            pl.produce,
            pl.image_path,
            pl.address,
            pl.available_date,
            pl.created_at,
            pl.quantity,
            pl.price,
            u.first_name,
            u.last_name,
            u.address,
            o.remaining_quantity
        ORDER BY
            pl.created_at DESC";

        $stmt = mysqli_prepare($conn, $sql);
        $searchTerm = "%" . $search . "%";
        mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $location, $location);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $uploaded_quantity = intval($row['uploaded_quantity']);
                $ordered_quantity = $row['quantity_ordered'] === 'No Order Yet' ? 0 : intval($row['quantity_ordered']);
                $remaining_quantity = $row['remaining_quantity'] === 'No Order Yet' ? $uploaded_quantity : intval($row['remaining_quantity']);

                $data[] = [
                    'prod_id' => $row['prod_id'],
                    'produce' => $row['produce'],
                    'image_path' => $row['image_path'] ? $row['image_path'] : 'assets/img/no-image.jpg',
                    'address' => $row['address'],
                    'uploaded_quantity' => $uploaded_quantity,
                    'quantity_ordered' => $ordered_quantity,
                    'remaining_quantity' => $remaining_quantity,
                    'average_rating' => floatval($row['rating'])
                ];
            }
        }

        echo json_encode($data);
    }
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?>