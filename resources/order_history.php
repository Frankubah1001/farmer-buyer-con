<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php'; 
?>

<style>
    /* === FARM-INSPIRED STYLES & OVERRIDES === */

    /* General Page Colors */
    .container-fluid {
        background-color: #F9FFF5; /* Very light, clean green/cream background */
    }
    .container h2 {
        color: #2E8B57; /* Sea Green - main farm color */
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid #8BC34A; /* Light Green accent border */
        display: inline-block;
    }

    /* Filter Card */
    .filter-card {
        border-radius: 18px;
        border: 1px solid #E8F5E9;
        box-shadow: 0 4px 15px rgba(46, 139, 87, 0.08);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-label {
        color: #4CAF50; /* Green */
        font-weight: 600;
        font-size: 0.9rem;
    }
    .form-control-sm {
        border-radius: 10px;
        border-color: #A5D6A7; /* Light Green border */
        transition: border-color 0.3s;
    }
    .form-control-sm:focus {
        border-color: #2E8B57;
        box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
    }

    /* Table Styling */
    .table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); /* Soft shadow */
        border: 1px solid #E8F5E9;
    }
    .table thead th {
        background-color: #4CAF50; /* Primary Green header */
        color: #fff;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .table tbody tr {
        transition: background-color 0.3s ease;
    }
    .table tbody tr:hover {
        background-color: #E8F5E9; /* Light green row hover */
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #F8FDF5;
    }
    .table td {
        vertical-align: middle;
        color: #384F38;
        font-size: 0.9rem;
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: #2E8B57; /* Darker green active button */
        border-color: #2E8B57;
    }
    .pagination .page-link {
        color: #4CAF50; /* Green link text */
        border-radius: 8px;
        margin: 0 3px;
        transition: background-color 0.3s;
    }
    .pagination .page-link:hover {
        background-color: #E8F5E9;
        color: #1B5E20;
    }
    .pagination .page-item.disabled .page-link {
        color: #BDBDBD;
    }
    .sidebar .nav-item .nav-link .badge-counter, .topbar .nav-item .nav-link .badge-counter {
    right: -0.6rem;
    margin-top: -.25rem;
}

.sidebar .nav-item .nav-link .badge-counter, .topbar .nav-item .nav-link .badge-counter {
    position: absolute;
    transform: scale(.7);
    transform-origin: top right;
    right: .25rem;
    margin-top: -.25rem;
}
.badge-danger {
    color: #fff;
    background-color: #e74a3b;
}
.badge {
    display: inline-block;
    padding: .25em .4em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .35rem;
    transition: color .15s 
ease-in-out, background-color .15s 
ease-in-out, border-color .15s 
ease-in-out, box-shadow .15s 
ease-in-out;
}



    /* Order Sent (Pending) */
    .badge-order-sent {
        background-color: #FFECB3; /* Light Yellow */
        color: #FFB300; /* Orange/Gold text */
        border: 1px solid #FFB300;
    }
    /* Processing Produce (In Progress) */
    .badge-processing-produce {
        background-color: #B3E5FC; /* Light Blue */
        color: #0288D1; /* Darker Blue text */
        border: 1px solid #0288D1;
    }
    /* Make Payment (Critical Action) */
    .badge-make-payment {
        background-color: #FFB300; /* Gold/Yellow */
        color: #795548; /* Brown text */
        border: 1px solid #E69A00;
    }
    /* Produce Delivered Confirmed (Completed) */
    .badge-delivered-confirmed {
        background-color: #C8E6C9; /* Light Green */
        color: #2E8B57; /* Dark Green text */
        border: 1px solid #2E8B57;
    }
    /* Default/Other */
    .badge-secondary {
        background-color: #E0E0E0;
        color: #616161;
    }

    /* PAY BUTTON */
    .payBtn {
        background-color: #FFC107; /* Action Yellow/Gold */
        border-color: #FFC107;
        color: #384F38; /* Dark text for contrast */
        font-weight: 700;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .payBtn:hover:not(:disabled) {
        background-color: #e0a800;
        border-color: #e0a800;
        transform: translateY(-1px);
    }
    .payBtn.paid {
        background-color: #6c757d !important; /* Gray */
        border-color: #6c757d !important;
        color: #fff;
        cursor: not-allowed;
    }
    .view-details {
        background-color: #8BC34A; /* Light Green */
        border-color: #8BC34A;
        font-weight: 600;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        line-height: 1;
    }
    .view-details:hover {
        background-color: #7CB342;
        border-color: #7CB342;
    }

    /* --- EDIT/DELETE ICONS --- */
    .action-icon {
        cursor: pointer;
        font-size: 1.1rem;
        margin: 0 5px;
        transition: color 0.2s;
        background: none;
        border: none;
        padding: 0;
    }
    .edit-order-btn i {
        color: #007bff; /* Blue for Edit */
    }
    .edit-order-btn:hover i {
        color: #0056b3;
    }
    .delete-order-btn i {
        color: #dc3545; /* Red for Delete */
    }
    .delete-order-btn:hover i {
        color: #a71d2a;
    }
    
    /* --- MODAL STYLING --- */
    #orderDetailsModal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    #orderDetailsModal .modal-header {
        background: linear-gradient(90deg, #2E8B57, #4CAF50);
        color: #fff;
        border-radius: 20px 20px 0 0;
        border-bottom: none;
    }
    #orderDetailsModal .modal-title {
        font-weight: 700;
    }
    #orderDetailsModal .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }
    /* Default readonly style */
    #orderDetailsModal .form-control[readonly] {
        background-color: #F8FDF5;
        border-color: #A5D6A7;
        font-weight: 600;
    }
    /* Editable style */
    #orderDetailsModal .form-control.editable {
        background-color: #fff; /* White background when editable */
        border: 1px solid #2E8B57;
    }
    #orderDetailsModal .form-control.editable:focus {
        box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
    }
    #detailItemsTable thead th {
        background-color: #C8E6C9; /* Light green header in modal */
        color: #1B5E20;
    }
    #detailItemsTable tbody tr:nth-of-type(odd) {
        background-color: #F8FDF5;
    }
    /* Style for the Save Changes button in the modal */
    #saveChangesBtn {
        background-color: #2E8B57; /* Match primary farm green */
        border-color: #2E8B57;
        font-weight: 600;
        border-radius: 10px;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid">
            <div class="container mt-4" style="max-width: 1400px; margin: auto; padding: 1.5rem;">
                <h2><i class="fas fa-history fa-fw mr-2"></i>Order History</h2>

                <div class="filter-card shadow-sm">
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <label for="statusFilterOrders" class="form-label"><i class="fas fa-filter mr-1"></i> Filter by Status:</label>
                            <select class="form-control form-control-sm" id="statusFilterOrders">
                                <option value="">All Statuses</option>
                                <option value="Order Sent">Order Sent</option>
                                <option value="Processing Produce">Processing Produce</option>
                                <option value="Make Payment">Make Payment</option>
                                <option value="Produce Delivered Confirmed">Delivered Confirmed</option>
                                <option value="Produce on the way">On the Way</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <label for="dateFilterOrders" class="form-label"><i class="far fa-calendar-alt mr-1"></i> Filter by Date:</label>
                            <input type="date" class="form-control form-control-sm" id="dateFilterOrders">
                        </div>
                         <div class="col-12 col-md-4 d-flex align-items-end">
                            </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Order Date</th>
                                <th>Total Amount</th>
                                <th><i class="fas fa-info-circle"></i> Status</th>
                                <th><i class="fas fa-credit-card"></i> Payment</th>
                                <th>Farmer</th>
                                <th>Items Overview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-clipboard-list mr-2"></i>Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-barcode"></i> Order ID</label>
                                <input type="text" class="form-control" id="detailOrderId" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="far fa-calendar-alt"></i> Order Date</label>
                                <input type="text" class="form-control" id="detailOrderDate" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-chart-line"></i> Status</label>
                                <input type="text" class="form-control" id="detailStatus" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><i class="fas fa-money-bill-wave"></i> Total Amount</label>
                                <input type="text" class="form-control" id="detailTotalAmount" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-tractor"></i> Farmer</label>
                            <input type="text" class="form-control" id="detailFarmer" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-balance-scale"></i> Quantity (Units/Bags)</label>
                            <input type="number" class="form-control" id="detailQuantity" name="quantity" min="1" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                            <textarea class="form-control" id="detailDeliveryAddress" name="delivery_address" rows="2" readonly></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-truck-moving"></i> Delivery Date</label>
                            <input type="date" class="form-control" id="detailDeliveryDate" name="delivery_date" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Order Items</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="detailItemsTable">
                                    <thead>
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
                    <button type="button" class="btn btn-primary" id="saveChangesBtn" style="display:none;"><i class="fas fa-save mr-2"></i>Save Changes</button>
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
    let editableOrderId = null; 

    // --- NEW HELPER FUNCTIONS FOR FORMATTING ---

    function formatCurrency(amount) {
        const number = parseFloat(amount);
        if (isNaN(number)) return 'N/A';
        return number.toLocaleString('en-NG', {
            style: 'currency',
            currency: 'NGN',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).replace('NGN', '₦');
    }
    
    // Initialize modals
    if (document.getElementById('orderDetailsModal')) {
        orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'), {
            backdrop: 'static', 
            keyboard: true
        });
    }

    // Manual close button handler
    $('#orderDetailsModal .btn-close, #orderDetailsModal .btn-secondary').on('click', function() {
        if (orderDetailsModal) {
            setModalFieldsEditable(false);
            orderDetailsModal.hide();
        }
    });

    // --- Core Load & Render Functions ---

    function loadOrders(page = 1, status = '', date = '') {
        currentPage = page;
        currentStatus = status;
        currentDate = date;
        
        $('#ordersTable tbody').html('<tr><td colspan="8" class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i> Loading order history...</td></tr>');

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

    function renderTable(orders) {
        const $tbody = $('#ordersTable tbody');
        $tbody.empty();

        if (orders.length === 0) {
            $tbody.html('<tr><td colspan="8" class="text-center p-4"><i class="fas fa-search-minus mr-2"></i> No orders found.</td></tr>');
            return;
        }

        orders.forEach(order => {
            const orderDataString = JSON.stringify(order).replace(/'/g, "&#39;"); 
            const isPayable = order.order_status === 'Make Payment' && order.payment_status !== 'Paid';
            const isEditable = order.order_status === 'Order Sent'; // Only allow edit/delete if 'Order Sent'
            
            const payBtnClass = isPayable ? 'btn-warning payBtn' : 'btn-secondary payBtn paid';
            const payBtnIcon = order.payment_status === 'Paid' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-money-bill-wave"></i>';
            const payBtnText = order.payment_status === 'Paid' ? 'Paid' : 'Pay';
            const payBtnDisabled = !isPayable ? 'disabled' : '';
            
            const formattedTotalAmount = formatCurrency(order.total_amount_raw);

            const itemsSummary = order.items.map(item => {
                const quantity = item.quantity ? `${item.quantity} Unit` : 'N/A';
                return `${item.name} (${quantity})`;
            }).join(', ');
            
            // ACTION BUTTONS WITH ICONS
            const actionButtons = `
                <div class="d-flex justify-content-start align-items-center">
                    <button class="btn btn-sm view-details"
                            title="View Details"
                            data-order-data='${orderDataString}'>
                        <i class="fas fa-eye"></i>
                    </button>
                    ${isEditable ? `
                        <button class="edit-order-btn action-icon" title="Edit Order" data-order-data='${orderDataString}'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-order-btn action-icon" title="Delete Order" data-order-id="${order.numeric_order_id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    ` : ''}
                </div>
            `;
            
            $tbody.append(`
                <tr>
                    <td>${order.order_id}</td>
                    <td>${order.created_at}</td>
                    <td class="font-weight-bold text-success">${formattedTotalAmount}</td>
                    <td><span class="badge ${getStatusBadgeClass(order.order_status)}">${order.order_status}</span></td>
                    <td>
                        <button class="btn ${payBtnClass}" ${payBtnDisabled}
                            data-order-id="${order.numeric_order_id}"
                            data-order-amount="${order.total_amount_raw}"
                            data-order-email="${order.buyer_email}">
                            ${payBtnIcon} ${payBtnText}
                        </button>
                    </td>
                    <td><i class="fas fa-tractor text-warning mr-1"></i>${order.farmer}</td>
                    <td>${itemsSummary}</td>
                    <td>${actionButtons}</td>
                </tr>
            `);
        });
    }
    
    // Status badge class
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'order sent':
                return 'badge-order-sent';
            case 'processing produce':
                return 'badge-processing-produce';
            case 'make payment':
                return 'badge-make-payment';
            case 'produce on the way':
                return 'badge-secondary'; 
            case 'produce delivered confirmed':
                return 'badge-delivered-confirmed';
            default:
                return 'badge-secondary';
        }
    }
    
    function showError(message) {
        $('#ordersTable tbody').html(`<tr><td colspan="8" class="text-center text-danger p-4"><i class="fas fa-times-circle mr-2"></i> ${message}</td></tr>`);
        $('#pagination').empty();
    }
    
    // Pagination logic (kept unchanged for brevity)
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

    // --- Modal Control Functions ---

    function setModalFieldsEditable(isEditable) {
        // Fields made editable in the modal
        const fields = ['#detailDeliveryAddress', '#detailDeliveryDate', '#detailQuantity'];
        fields.forEach(field => {
            const $field = $(field);
            $field.prop('readonly', !isEditable);
            $field.toggleClass('editable', isEditable);
        });
        $('#saveChangesBtn').toggle(isEditable);
    }
    
    function showOrderDetails(order, isEdit = false) {
        editableOrderId = isEdit ? order.numeric_order_id : null;
        
        // Populate static fields
        $('#detailOrderId').val(order.order_id);
        $('#detailOrderDate').val(order.created_at);
        $('#detailStatus').val(order.order_status);
        $('#detailTotalAmount').val(formatCurrency(order.total_amount_raw));
        $('#detailFarmer').val(order.farmer);

        // Populate editable fields
        $('#detailDeliveryAddress').val(order.delivery_address);
        $('#detailDeliveryDate').val(order.delivery_date.slice(0, 10)); 
        
        const itemQuantity = order.items.length > 0 ? order.items[0].quantity : 0;
        $('#detailQuantity').val(itemQuantity); 

        // Set edit state
        setModalFieldsEditable(isEdit);

        const $itemsTable = $('#detailItemsTable tbody');
        $itemsTable.empty();

        order.items.forEach(item => {
            $itemsTable.append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${formatCurrency(item.price_raw)}</td>
                    <td>${item.quantity}</td>
                    <td><span class="badge ${order.payment_status === 'Paid' ? 'badge-delivered-confirmed' : 'badge-make-payment'}">${order.payment_status}</span></td>
                </tr>
            `);
        });

        if (orderDetailsModal) {
            orderDetailsModal.show();
        }
    }

    // --- Action Handlers (Edit, Delete, Save) ---
    
    // Handle Save Changes button click
    $('#saveChangesBtn').click(function() {
        if (!editableOrderId) return;
        
        // Gather new data
        const new_quantity = $('#detailQuantity').val();
        const new_delivery_date = $('#detailDeliveryDate').val();
        const new_delivery_address = $('#detailDeliveryAddress').val();
        
        if (new_quantity <= 0 || !new_delivery_date || !new_delivery_address) {
            showErrorAlert('Please ensure quantity is positive, and delivery date/address are provided.');
            return;
        }

        Swal.fire({
            title: 'Confirm Edit',
            text: 'Are you sure you want to save these changes to your order?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2E8B57',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save Changes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'views/order_action_process.php', 
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'edit',
                        order_id: editableOrderId,
                        quantity: new_quantity,
                        delivery_date: new_delivery_date,
                        delivery_address: new_delivery_address
                    },
                    beforeSend: function() {
                        Swal.fire({ title: 'Updating Order...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            Swal.fire('Updated!', response.message, 'success').then(() => {
                                setModalFieldsEditable(false);
                                orderDetailsModal.hide();
                                loadOrders(currentPage, currentStatus, currentDate); 
                            });
                        } else {
                            showErrorAlert(response.message);
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        showErrorAlert('Error submitting changes. Please check the console.');
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            }
        });
    });

    // Handle Delete button click
    $(document).on('click', '.delete-order-btn', function() {
        const orderId = $(this).data('order-id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete Order ORD-${orderId}. This cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'views/order_action_process.php', 
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete',
                        order_id: orderId
                    },
                    beforeSend: function() {
                        Swal.fire({ title: 'Deleting Order...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => { Swal.showLoading(); } });
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status === 'success') {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                loadOrders(currentPage, currentStatus, currentDate); 
                            });
                        } else {
                            showErrorAlert(response.message);
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        showErrorAlert('Error deleting order. Please check the console.');
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            }
        });
    });

    // Handle Edit button click (opens modal in edit mode)
    $(document).on('click', '.edit-order-btn', function() {
        try {
            const orderData = $(this).data('order-data');
            showOrderDetails(orderData, true); // Edit mode
        } catch (e) {
            console.error("Error retrieving order data for edit:", e);
            showErrorAlert("Could not retrieve order details for editing.");
        }
    });
    
    // Handle View Details button click (opens modal in view mode)
    $(document).on('click', '.view-details', function() {
        try {
            const orderData = $(this).data('order-data');
            showOrderDetails(orderData, false); // View mode
        } catch (e) {
            console.error("Error retrieving order data for view:", e);
            showErrorAlert("Could not retrieve order details.");
        }
    });
    
    // Payment and error functions (kept unchanged)
    function showErrorAlert(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        } else {
            alert(message);
        }
    }
    
    $(document).on('click', '.payBtn:not(.paid)', function() {
        const orderId = $(this).data('order-id');
        const amount = parseFloat($(this).data('order-amount'));
        const email = $(this).data('order-email');
        processPayment(orderId, amount, email);
    });
    
    function processPayment(orderId, amount, email) {
        const handler = PaystackPop.setup({
            key: 'pk_test_820ef67599b8a00fd2dbdde80e56e94fda5ed79f',
            email: email,
            amount: amount * 100,
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
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Successful',
                                text: 'Payment processed and confirmation email sent.',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                loadOrders(currentPage, currentStatus, currentDate);
                            });
                        } else {
                            showErrorAlert('Payment verification failed: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showErrorAlert('Error processing payment: ' + error);
                    }
                });
            },
            onClose: function() {
                console.log('Payment window closed');
            }
        });
        handler.openIframe();
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

    // Initial load of orders
    loadOrders(1);
});
</script>