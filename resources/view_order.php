<?php
require_once 'auth_check.php';
include 'header.php';

// Get the order ID from the URL (if it exists)
$filterOrderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>  <div class="container-fluid">
            <h2 class="h3 mb-2 text-gray-800">Orders</h2>

            
            <div class="row mb-3 justify-content-center">
                <div class="col-md-3">
                    <label for="produceFilterInline" class="form-label">Filter by Produce:</label>
                    <input type="text" class="form-control form-control-sm" id="produceFilterInline"
                           placeholder="Enter produce name">
                </div>
                <div class="col-md-3">
                    <label for="locationFilterInline" class="form-label">Filter by Location:</label>
                    <input type="text" class="form-control form-control-sm" id="locationFilterInline"
                           placeholder="Enter location">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="availableProduceTable">
                    <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Buyer Name</th>
                        <th>Produce</th>
                        <th>Ordered Quantity</th>
                        <th>Price per Unit</th>
                        <th>Delivery Address</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Buyer Phone</th>
                        <th>Status</th>
                        <th>Delivery Status</th>
                        <th>Payment Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>
<script>
    let filterOrderId = <?php echo $filterOrderId; ?>;
</script>
<script src="view_order.js"></script>