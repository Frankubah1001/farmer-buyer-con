<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php'; 
?>

<style>
    /* Order Details Modal Styles */
    #orderDetailsModal .modal-body {
        padding: 20px;
    }

    #orderDetailsModal .table {
        margin-top: 15px;
    }
    #orderDetailsModal .form-control[readonly] {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    .payBtn.paid {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        cursor: not-allowed;
    }
    .pagination .page-item.active .page-link {
        background-color: #28a745;
        border-color: #28a745;
    }
    .pagination .page-link {
        color: #28a745;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
    .input-group button {
        border-color: #ced4da;
    }
    .payBtn {
        line-height: 1;
    }
    .input-group button:hover {
        background-color: #f8f9fa;
    }
    #orderDetailsModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    .btn-primary {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }
    .btn-primary:hover {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }
    #detailItemsTable {
        background-color: white;
    }

    #orderDetailsModal .table th {
        background-color: #f8f9fa;
    }

    #orderDetailsModal .badge {
        font-size: 0.9em;
        padding: 0.4em 0.6em;
    }
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        border-radius: 0.25rem;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .payBtn.paid {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid">
            <div class="container mt-4">
                <h2>Order History</h2>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="statusFilterOrders" class="form-label">Filter by Status:</label>
                        <select class="form-control form-control-sm" id="statusFilterOrders">
                            <option value="">All Statuses</option>
                            <option value="Order Sent">Order Sent</option>
                            <option value="Processing">Processing</option>
                            <option value="Make Payment">Make Payment</option>
                            <option value="Produce Delivered Confirmed">Produce Delivered Confirmed</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dateFilterOrders" class="form-label">Filter by Date:</label>
                        <input type="date" class="form-control form-control-sm" id="dateFilterOrders">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Make Payment</th>
                                <th>Farmer</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ORD-2025-001</td>
                                <td>2025-04-28</td>
                                <td>$120.00</td>
                                <td>Delivered</td>
                                <td>John Doe</td>
                                <td>Tomatoes, Beans</td>
                                <td><button class="btn btn-sm btn-primary view-details">View Details</button></td>
                            </tr>
                            <tr>
                                <td>ORD-2025-002</td>
                                <td>2025-05-01</td>
                                <td>$85.50</td>
                                <td>Processing</td>
                                <td>Jane Smith</td>
                                <td>Rice</td>
                                <td><button class="btn btn-sm btn-primary">View Details</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center" id="pagination">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Order ID</label>
                                <input type="text" class="form-control" id="detailOrderId" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Order Date</label>
                                <input type="text" class="form-control" id="detailOrderDate" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" id="detailStatus" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Amount</label>
                                <input type="text" class="form-control" id="detailTotalAmount" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Farmer</label>
                            <input type="text" class="form-control" id="detailFarmer" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="detailDeliveryAddress" rows="2" readonly></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Date</label>
                            <input type="text" class="form-control" id="detailDeliveryDate" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Items</label>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="detailItemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Payment Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php include 'buyerfooter.php'; ?>
</div>

<?php include 'buyerscript.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
   $(document).ready(function() {
    let orderDetailsModal = null;
    let currentStatus = '';
    let currentDate = '';
    let currentPage = 1;
    let currentOrderId = null;
    let currentOrderAmount = null;

    // Initialize modals
    orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'), {
        backdrop: 'static', // Optional: Prevent closing by clicking outside (remove if not desired)
        keyboard: true // Allow closing with Escape key
    });

    // Manual close button handler as a fallback
    $('#orderDetailsModal .btn-close, #orderDetailsModal .btn-secondary').on('click', function() {
        orderDetailsModal.hide();
    });

        // Initialize modals
        // orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

        // Load orders function
        function loadOrders(page = 1, status = '', date = '') {
            currentPage = page;
            currentStatus = status;
            currentDate = date;

            $.ajax({
                url: 'views/fetch_orders.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    page: page,
                    order_status: status,
                    date: date
                },
                success: function(response) {
                    if (response.error) {
                        showError(response.error);
                        return;
                    }
                    renderTable(response.orders);
                    renderPagination(response.pagination);
                },
                error: function(xhr, status, error) {
                    showError('Error loading orders: ' + error);
                    console.error(xhr.responseText);
                }
            });
        }

        // Render table
        function renderTable(orders) {
            const $tbody = $('#ordersTable tbody');
            $tbody.empty();

            if (orders.length === 0) {
                $tbody.html('<tr><td colspan="8" class="text-center">No orders found</td></tr>');
                return;
            }

            orders.forEach(order => {
                const orderDataString = JSON.stringify(order);
                const isPayable = order.order_status === 'Make Payment' && order.payment_status !== 'Paid';
                const payBtnClass = isPayable ? 'btn-primary payBtn' : 'btn-secondary payBtn paid';
                const payBtnText = order.payment_status === 'Paid' ? 'Paid' : 'Pay';
                const payBtnDisabled = !isPayable ? 'disabled' : '';
                
                $tbody.append(`
                    <tr>
                        <td>${order.order_id}</td>
                        <td>${order.created_at}</td>
                        <td>${order.total_amount}</td>
                        <td><span class="badge ${getStatusBadgeClass(order.order_status)}">${order.order_status}</span></td>
                        <td>
                            <button class="btn ${payBtnClass}" ${payBtnDisabled}
                                data-order-id="${order.numeric_order_id}"
                                data-order-amount="${order.total_amount_raw}"
                                data-order-email="${order.buyer_email}">
                                ${payBtnText}
                            </button>
                        </td>
                        <td>${order.farmer}</td>
                        <td>${order.items.map(item => item.name + " (" + item.quantity + " Bags)").join(', ')}</td>
                        <td>
                            <button class="btn btn-sm btn-primary view-details"
                                    data-bs-toggle="modal"
                                    data-bs-target="#orderDetailsModal"
                                    data-order-data='${orderDataString}'>
                                View Details
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        // Render pagination
        function renderPagination(pagination) {
            const $pagination = $('#pagination');
            $pagination.empty();
            const lastPage = pagination.last_page;

            if (lastPage <= 1) return;

            $pagination.append(`
                <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                        &laquo;
                    </a>
                </li>
            `);

            let startPage = Math.max(1, pagination.current_page - 2);
            let endPage = Math.min(lastPage, pagination.current_page + 2);

            if (endPage - startPage < 4) {
                if (startPage === 1) {
                    endPage = Math.min(lastPage, startPage + 4);
                } else {
                    startPage = Math.max(1, endPage - 4);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                $pagination.append(`
                    <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            $pagination.append(`
                <li class="page-item ${pagination.current_page === lastPage ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                        &raquo;
                    </a>
                </li>
            `);
        }

        // Status badge class
        function getStatusBadgeClass(status) {
            switch (status.toLowerCase()) {
                case 'order sent':
                    return 'badge-warning';
                case 'processing produce':
                    return 'badge-primary';
                case 'make payment':
                    return 'badge-success';
                case 'produce on the way':
                    return 'badge-danger';
                case 'produce delivered confirmed':
                    return 'badge-success';
                default:
                    return 'badge-secondary';
            }
        }

        // Show error
        function showError(message) {
            $('#ordersTable tbody').html(`<tr><td colspan="8" class="text-center text-danger">${message}</td></tr>`);
            $('#pagination').empty();
        }

       // Show order details
    function showOrderDetails(order) {
        $('#detailOrderId').val(order.order_id);
        $('#detailOrderDate').val(order.created_at);
        $('#detailStatus').val(order.order_status);
        $('#detailTotalAmount').val(order.total_amount);
        $('#detailFarmer').val(order.farmer);
        $('#detailDeliveryAddress').val(order.delivery_address);
        $('#detailDeliveryDate').val(order.delivery_date); // Ensure this matches backend format

        const $itemsTable = $('#detailItemsTable tbody');
        $itemsTable.empty();

        order.items.forEach(item => {
            $itemsTable.append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${item.price}</td>
                    <td>${item.quantity}</td>
                    <td>${order.payment_status}</td>
                </tr>
            `);
        });

        orderDetailsModal.show();
    }

        // Process payment with Paystack
        function processPayment(orderId, amount, email) {
            const handler = PaystackPop.setup({
                key: 'pk_test_820ef67599b8a00fd2dbdde80e56e94fda5ed79f', // Replace with your Paystack test public key
                email: email,
                amount: amount * 100, // Convert to kobo
                currency: 'NGN',
                ref: 'ORD-' + orderId + '-' + Math.floor(Math.random() * 1000000000 + 1),
                callback: function(response) {
                    $.ajax({
                        url: 'payment_process.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            order_id: orderId,
                            reference: response.reference,
                            amount: amount
                        },
                        success: function(response) {
                            console.log('AJAX Success:', response); // Debug log
                            if (response.success) {
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Payment Successful',
                                        text: 'Payment processed and confirmation email sent.',
                                        showConfirmButton: false,
                                        timer: 3000
                                    }).then(() => {
                                        location.reload(true); // Reload page
                                    });
                                } else {
                                    console.warn('Swal is not defined, reloading directly');
                                    location.reload(true); // Fallback reload if Swal fails
                                }
                            } else {
                                showErrorAlert('Payment verification failed: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            showErrorAlert('Error processing payment: ' + error);
                            console.error('AJAX Error:', xhr.responseText);
                        }
                    });
                },
                onClose: function() {
                    console.log('Payment window closed');
                }
            });
            handler.openIframe();
        }

        // Show error alert
        function showErrorAlert(message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            } else {
                alert(message); // Fallback if Swal is not loaded
            }
        }

        // Event listeners
        $('#statusFilterOrders, #dateFilterOrders').change(function() {
            loadOrders(1, $('#statusFilterOrders').val(), $('#dateFilterOrders').val());
        });

        $(document).on('click', '#pagination .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadOrders(page, currentStatus, currentDate);
        });

       
        $(document).on('click', '.view-details', function() {
        try {
            const orderData = $(this).data('order-data');
            showOrderDetails(orderData);
        } catch (e) {
            console.error("Error retrieving order data:", e);
            showErrorAlert("Could not retrieve order details.");
        }
         });
        // Pay button click handler
        $(document).on('click', '.payBtn:not(.paid)', function() {
            const orderId = $(this).data('order-id');
            const amount = parseFloat($(this).data('order-amount'));
            const email = $(this).data('order-email');
            processPayment(orderId, amount, email);
        });

        // Initial load of orders
        loadOrders(1);
    });
</script>