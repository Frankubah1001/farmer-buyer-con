<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'DBcon.php';

header('Content-Type: application/json');

// Modified query without user_id filter
$query = "SELECT
    u.user_id,
    CONCAT(u.first_name, ' ', u.last_name) AS farmer_name,
    u.email,
    u.phone,
    u.address AS location,
    u.created_at AS joined_date,
    pl.prod_id,
    pl.produce,
    pl.image_path,
    pl.address AS produce_address,
    pl.available_date,
    pl.created_at AS produce_created,
    COALESCE(o.remaining_quantity, 'No Order Yet') AS remaining_quantity,
    CASE 
        WHEN o.remaining_quantity IS NULL THEN 'No Order Yet'
        WHEN o.remaining_quantity = 0 THEN 'Item Sold'
        ELSE (pl.quantity - o.remaining_quantity)
    END AS quantity_ordered,
    pl.quantity,
    pl.price,
    IFNULL(AVG(r.rating), 0) AS rating
FROM
    users u
JOIN produce_listings pl ON u.user_id = pl.user_id
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
LEFT JOIN ratings r ON u.user_id = r.user_id
WHERE
    pl.is_deleted = 0
    AND pl.available_date <= CURDATE()
    AND (o.remaining_quantity IS NULL OR o.remaining_quantity > 0)
GROUP BY
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.phone,
    u.address,
    u.created_at,
    pl.prod_id,
    pl.produce,
    pl.image_path,
    pl.address,
    pl.available_date,
    pl.created_at,
    pl.quantity,
    pl.price,
    o.remaining_quantity
ORDER BY
    pl.created_at DESC";

$result = $conn->query($query);

$produce = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produce[] = $row;
    }
}

echo json_encode($produce);
$conn->close();
?>