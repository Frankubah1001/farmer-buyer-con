<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php';
 ?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid">

            <h1 class="h3 mb-4 text-gray-800">Welcome, <?php echo $_SESSION['firstname'] ; ?>!</h1>

            <div class="row">

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow h-100 py-2" id="recent-orders-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Recent Orders
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="recent-orders-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Produce</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="order_history.php" class="btn btn-primary btn-sm">View All Orders</a>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow h-100 py-2" id="most-produce-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fas fa-leaf fa-3x text-success mb-2"></i>
                            <div class="text-center">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Most Farm Produce Ordered
                                </div>
                                <ul class="list-unstyled mb-0">
                                </ul>
                                <div class="mt-3">
                                    <a href="buyer_view_product.php" class="btn btn-success btn-sm">View All Farm Produce</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card shadow h-100 py-2" id="farmers-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Farmers
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="farmers-table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Location</th>
                                                    <th>Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <a href="buyer_view_farmer.php" class="btn btn-info btn-sm">View All Farmers</a>
                                        <button class="btn btn-warning btn-sm ml-2" id="rateFarmerBtn" data-toggle="modal" data-target="#rateFarmerModal">Rate Farmer</button>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>

               <div class="col-xl-6 col-md-6 mb-4">
    <div class="card shadow h-100 py-2" id="order-status-card">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        See Your Order Status
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produce</th>
                                    <th>Status</th>
                                    <th>Update</th>
                                    <th>Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>

            <div class="modal fade" id="rateFarmerModal" tabindex="-1" role="dialog" aria-labelledby="rateFarmerModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rateFarmerModalLabel">Rate a Farmer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="ratingForm">
                                <div class="form-group">
                                    <label for="farmerToRate">Select Farmer:</label>
                                    <select class="form-control" id="farmerToRate" name="farmer_id" required>
                                        <option value="">-- Select a Farmer --</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="rating">Rating:</label>
                                    <select class="form-control" id="rating" name="rating" required>
                                        <option value="">-- Select Rating --</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Good</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="comment">Comment (Optional):</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Rating</button>
                            </form>
                            <div id="ratingMessage" class="mt-3"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
        <!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Send Message to Farmer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="commentForm">
                    <input type="hidden" id="commentOrderId" name="order_id">
                    <input type="hidden" id="commentFarmerId" name="farmer_id">
                    <div class="form-group">
                        <label for="commentMessage">Message:</label>
                        <textarea class="form-control" id="commentMessage" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
                <div id="commentMessageStatus" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    <?php include 'buyerfooter.php'; ?>
</div>
<?php include 'buyerscript.php'; ?>

<script>
    async function loadDashboardData() {
        try {
            const response = await fetch('views/buyerdashboard.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.error) {
                console.error('Error from backend:', data.error);
                return;
            }

            updateRecentOrderCard(data.recent_orders);
            updateMostPurchasedCard(data.most_purchased_produce);
            updateFarmersCard(data.farmers);
            populateFarmerDropdown(data.all_farmers); // Populate the dropdown

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function updateRecentOrderCard(recentOrders) {
        const tableBody = document.querySelector("#recent-orders-table tbody");
        tableBody.innerHTML = '';

        if (recentOrders && Array.isArray(recentOrders) && recentOrders.length > 0) {
            recentOrders.forEach(order => {
                const row = tableBody.insertRow();
                row.insertCell().textContent = order.order_id.replace('ORD-', '');
                row.insertCell().textContent = order.order_date.split(' ')[0];
                row.insertCell().textContent = order.produce;
                row.insertCell().textContent = order.quantity;
                row.insertCell().textContent = order.total_amount;
            });
        } else {
            const row = tableBody.insertRow();
            const cell = row.insertCell();
            cell.setAttribute('colspan', 5);
            cell.textContent = "No recent orders";
            cell.style.textAlign = "center";
        }
    }

    function updateMostPurchasedCard(mostPurchased) {
        const mostPurchasedList = document.querySelector("#most-produce-card .list-unstyled");
        mostPurchasedList.innerHTML = '';

        if (mostPurchased) {
            mostPurchasedList.innerHTML = `<li>${mostPurchased.produce} (Total Quantity: ${mostPurchased.quantity})</li>`;
        } else {
            mostPurchasedList.innerHTML = "<p>No past orders</p>";
        }
    }

    function updateFarmersCard(farmers) {
        const tableBody = document.querySelector("#farmers-table tbody");
        tableBody.innerHTML = '';

        if (farmers && Array.isArray(farmers) && farmers.length > 0) {
            farmers.forEach(farmer => {
                const row = tableBody.insertRow();
                row.insertCell().textContent = `${farmer.first_name} ${farmer.last_name}`;
                row.insertCell().textContent = farmer.location;

                // Create star rating display
                const ratingCell = row.insertCell();
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += i <= farmer.rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star"></i>';
                }
                ratingCell.innerHTML = stars;
            });
        } else {
            const row = tableBody.insertRow();
            const cell = row.insertCell();
            cell.setAttribute('colspan', 3);
            cell.textContent = "No patronised farmers found.";
            cell.style.textAlign = "center";
        }
    }

    function populateFarmerDropdown(farmers) {
        const farmerSelect = document.getElementById('farmerToRate');
        farmerSelect.innerHTML = '<option value="">-- Select a Farmer --</option>';
        if (farmers && Array.isArray(farmers)) {
            farmers.forEach(farmer => {
                const option = document.createElement('option');
                option.value = farmer.user_id;
                option.textContent = `${farmer.first_name} ${farmer.last_name}`;
                farmerSelect.appendChild(option);
            });
        }
    }
// Add this function to update the order status card
// Update the status change event listener
$(document).on('change', '.status-select', function() {
    const selectElement = this;
    const orderId = $(this).data('order-id');
    const newStatus = $(this).val();
    
    // Show loading state
    $(this).prop('disabled', true);
    $(this).after('<span class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true"></span>');
    
    $.ajax({
        url: 'views/update_order_status.php',
        type: 'POST',
        dataType: 'json',
        data: {
            order_id: orderId.replace('ORD-', ''),
            new_status: newStatus
        },
        success: function(response) {
            // Remove loading state
            $(selectElement).prop('disabled', false);
            $(selectElement).next('.spinner-border').remove();
            
            if (response.status === 'success') {
                alert('Order status updated successfully');
                location.reload();
                // The dropdown will retain its value since we're not resetting it
            } else {
                toastr.error('Failed to update order status: ' + response.message);
                // Reset to previous value if update failed
                $(selectElement).val($(selectElement).data('previous-value'));
            }
        },
        error: function(xhr, status, error) {
            // Remove loading state
            $(selectElement).prop('disabled', false);
            $(selectElement).next('.spinner-border').remove();
            
            toastr.error('Error updating order status: ' + error);
            // Reset to previous value on error
            $(selectElement).val($(selectElement).data('previous-value'));
        }
    });
});

// Update the updateOrderStatusCard function to store previous value
function updateOrderStatusCard(orderStatus) {
    const orderStatusCard = document.querySelector("#order-status-card");
    const tableBody = orderStatusCard.querySelector("tbody");
    tableBody.innerHTML = '';

    if (orderStatus) {
        const row = tableBody.insertRow();
        row.insertCell().textContent = orderStatus.produce;
        
        const statusCell = row.insertCell();
        statusCell.textContent = orderStatus.status || 'Pending';
        
        const updateCell = row.insertCell();
        const select = document.createElement('select');
        select.className = 'form-control status-select';
        select.dataset.orderId = orderStatus.order_id;
        select.dataset.previousValue = orderStatus.status || 'Pending'; // Store current value
        
        const statusOptions = [
            'Delivery Confirmed', 'Delivery Not Received', 
            'Still Awaiting Delivery', 'Delivery Cancelled'
        ];
        
        statusOptions.forEach(option => {
            const optElement = document.createElement('option');
            optElement.value = option;
            optElement.textContent = option;
            if (option === (orderStatus.status || 'Pending')) {
                optElement.selected = true;
            }
            select.appendChild(optElement);
        });
        
        updateCell.appendChild(select);
        
        const commentCell = row.insertCell();
        const commentBtn = document.createElement('button');
        commentBtn.className = 'btn btn-primary btn-sm comment-btn';
        commentBtn.textContent = 'Comment';
        commentBtn.dataset.toggle = 'modal';
        commentBtn.dataset.target = '#commentModal';
        commentBtn.dataset.orderId = orderStatus.order_id;
        commentBtn.dataset.farmerId = orderStatus.farmer_id;
        commentCell.appendChild(commentBtn);
    } else {
        const row = tableBody.insertRow();
        const cell = row.insertCell();
        cell.setAttribute('colspan', '5');
        cell.textContent = "No recent orders with status available";
        cell.style.textAlign = "center";
    }
}
// Update the loadDashboardData function
async function loadDashboardData() {
    try {
        const response = await fetch('views/buyerdashboard.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        if (data.error) {
            console.error('Error from backend:', data.error);
            return;
        }

        updateRecentOrderCard(data.recent_orders);
        updateMostPurchasedCard(data.most_purchased_produce);
        updateFarmersCard(data.farmers);
        populateFarmerDropdown(data.all_farmers);
        updateOrderStatusCard(data.order_status);

    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

// Add event listener for status change
$(document).on('change', '.status-select', function() {
    const orderId = $(this).data('order-id');
    const newStatus = $(this).val();
    
    $.ajax({
        url: 'views/update_order_status.php',
        type: 'POST',
        dataType: 'json',
        data: {
            order_id: orderId.replace('ORD-', ''),
            new_status: newStatus
        },
        success: function(response) {
            if (response.status === 'success') {
                toastr.success('Order status updated successfully');
                loadDashboardData(); // Refresh the data
            } else {
                toastr.error('Failed to update order status: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            toastr.error('Error updating order status: ' + error);
        }
    });
});

// Handle comment button click
$(document).on('click', '.comment-btn', function() {
    const orderId = $(this).data('order-id');
    const farmerId = $(this).data('farmer-id');
    $('#commentOrderId').val(orderId.replace('ORD-', ''));
    $('#commentFarmerId').val(farmerId);
    $('#commentMessage').val('');
    $('#commentMessageStatus').removeClass('alert-danger alert-success').text('');
});

// Handle comment form submission
$('#commentForm').submit(function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.ajax({
        url: 'views/submit_order_comment.php',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(response) {
            $('#commentMessageStatus').removeClass('alert-danger alert-success');
            if (response.status === 'success') {
                $('#commentMessageStatus').addClass('alert-success').text(response.message);
                $('#commentForm')[0].reset();
                setTimeout(() => {
                    $('#commentModal').modal('hide');
                }, 1500);
            } else {
                $('#commentMessageStatus').addClass('alert-danger').text(response.message);
            }
        },
        error: function(xhr, status, error) {
            $('#commentMessageStatus').removeClass('alert-danger alert-success')
                .addClass('alert-danger')
                .text('An error occurred while sending your message.');
        }
    });
});
    $(document).ready(function() {
        loadDashboardData();

        $('#ratingForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: 'views/submit_rating.php', // Backend script to handle rating submission
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    $('#ratingMessage').removeClass('alert-danger alert-success');
                    if (response.status === 'success') {
                        $('#ratingMessage').addClass('alert-success').text(response.message);
                        $('#ratingForm')[0].reset();
                        $('#rateFarmerModal').modal('hide');
                        loadDashboardData(); // Reload data to update farmer ratings
                    } else {
                        $('#ratingMessage').addClass('alert-danger').text(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error submitting rating:', error);
                    $('#ratingMessage').removeClass('alert-danger alert-success').addClass('alert-danger').text('An error occurred while submitting the rating.');
                }
            });
        });
    });
</script>