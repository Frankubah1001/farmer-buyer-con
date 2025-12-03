<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// api.php
header('Content-Type: application/json');
include 'DBcon.php'; // Adjust path to point to DBcon.php in the root directory

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_summary_cards':
        getSummaryCards($conn);
        break;
    case 'get_farmers_chart':
        getFarmersChart($conn);
        break;
    case 'get_produce_chart':
        getProduceChart($conn);
        break;
    case 'get_frequently_purchased_produce':
        getFrequentlyPurchasedProduce($conn);
        break;
    case 'get_most_purchased_items':
        getMostPurchasedItems($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        exit;
}

// Summary Cards
function getSummaryCards($conn) {
    try {
        // Approved Farmers
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE cbn_approved = 1");
        $approved_farmers = $result->fetch_assoc()['count'];
        $result->free();

        // Registered Buyers
        $result = $conn->query("SELECT COUNT(*) as count FROM buyers WHERE is_verify = 1");
        $registered_buyers = $result->fetch_assoc()['count'];
        $result->free();

        // Completed Orders
        $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE order_status = 'Produce Delivered Confirmed'");
        $completed_orders = $result->fetch_assoc()['count'];
        $result->free();

        // Total Revenue
        $result = $conn->query("SELECT SUM(payment_amount) as total FROM orders WHERE payment_status = 'Paid'");
        $row = $result->fetch_assoc();
        $total_revenue = $row['total'] ?? 0;
        $result->free();

        echo json_encode([
            'approved_farmers' => $approved_farmers,
            'registered_buyers' => $registered_buyers,
            'completed_orders' => $completed_orders,
            'total_revenue' => number_format($total_revenue, 2)
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Most Patronized Farmers Chart
function getFarmersChart($conn) {
    try {
        $days = isset($_GET['days']) ? (int)$_GET['days'] : 30;
        $stmt = $conn->prepare("
            SELECT u.first_name, u.last_name, COUNT(o.order_id) as order_count
            FROM users u
            JOIN orders o ON u.user_id = o.user_id
            WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY u.user_id, u.first_name, u.last_name
            ORDER BY order_count DESC
            LIMIT 5
        ");
        $stmt->bind_param('i', $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $labels = [];
        $order_counts = [];
        foreach ($data as $row) {
            $labels[] = $row['first_name'] . ' ' . $row['last_name'];
            $order_counts[] = $row['order_count'];
        }

        echo json_encode([
            'labels' => $labels,
            'data' => $order_counts
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Produce Demand Ranking Chart
function getProduceChart($conn) {
    try {
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        
        $labels = [];
        $dateFormat = '';
        $startDate = '';
        $endDate = '';

        if ($period == 'month') {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $dateFormat = '%d'; // Day of month (01-31)
            
            $daysInMonth = date('t');
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = sprintf("%02d", $i);
            }
        } elseif ($period == 'last_month') {
            $startDate = date('Y-m-01', strtotime('last month'));
            $endDate = date('Y-m-t', strtotime('last month'));
            $dateFormat = '%d';
            
            $daysInMonth = date('t', strtotime('last month'));
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = sprintf("%02d", $i);
            }
        } elseif ($period == 'quarter') {
            // Current Quarter
            $currentMonth = date('n');
            $startMonth = floor(($currentMonth - 1) / 3) * 3 + 1;
            $startDate = date('Y') . '-' . sprintf("%02d", $startMonth) . '-01';
            $endDate = date('Y-m-t'); 
            $dateFormat = '%b'; // Month name
            
            for ($i = 0; $i < 3; $i++) {
                $m = $startMonth + $i;
                $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            }
        } else {
            // Default to month
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $dateFormat = '%d';
            $daysInMonth = date('t');
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $labels[] = sprintf("%02d", $i);
            }
        }

        $sql = "
            SELECT p.produce, 
                   DATE_FORMAT(o.order_date, '$dateFormat') as time_unit, 
                   COUNT(o.order_id) as order_count
            FROM produce_listings p
            JOIN orders o ON p.prod_id = o.produce_id
            WHERE o.order_date >= '$startDate' AND o.order_date <= '$endDate 23:59:59'
            GROUP BY p.produce, time_unit
            ORDER BY o.order_date
        ";

        $result = $conn->query($sql);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        $datasets = [];
        $produce_types = array_unique(array_column($data, 'produce'));

        foreach ($produce_types as $produce) {
            $counts = [];
            foreach ($labels as $label) {
                $found = false;
                foreach ($data as $row) {
                    if ($row['produce'] == $produce && $row['time_unit'] == $label) {
                        $counts[] = $row['order_count'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) $counts[] = 0;
            }
            // Generate a consistent color based on produce name string
            $hash = md5($produce);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));

            $datasets[] = [
                'label' => $produce,
                'data' => $counts,
                'borderColor' => "rgba($r, $g, $b, 1)",
                'backgroundColor' => "rgba($r, $g, $b, 0.1)",
                'tension' => 0.4
            ];
        }

        echo json_encode([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Frequently Purchased Produce Table
function getFrequentlyPurchasedProduce($conn) {
    try {
        $result = $conn->query("
            SELECT p.produce, 
                   COUNT(o.order_id) as order_count,
                   AVG(o.price_per_unit) as avg_price,
                   'stable' as trend
            FROM produce_listings p
            JOIN orders o ON p.prod_id = o.produce_id
            GROUP BY p.produce
            ORDER BY order_count DESC
            LIMIT 5
        ");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Most Purchased Items Table
function getMostPurchasedItems($conn) {
    try {
        $result = $conn->query("
            SELECT p.produce, 
                   u.first_name, 
                   u.last_name, 
                   SUM(o.quantity) as total_quantity,
                   SUM(o.total_amount) as total_revenue
            FROM produce_listings p
            JOIN orders o ON p.prod_id = o.produce_id
            JOIN users u ON p.user_id = u.user_id
            GROUP BY p.produce, u.user_id, u.first_name, u.last_name
            ORDER BY total_revenue DESC
            LIMIT 5
        ");
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $result->free();

        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>