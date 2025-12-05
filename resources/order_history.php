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
/* Force table to scroll horizontally on mobile */
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    table.table {
        width: 1000px; /* Minimum width to avoid column wrapping */
    }
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
<style>
    /* === FARM-FRESH COLOR PALETTE & ICON STYLING === */

    /* Colors - Refined for better pop and contrast */
    :root {
        --primary-green: #2E7D32;    /* Deeper Green - Primary Action/Header */
        --secondary-green: #689F38;  /* Medium Green - Accent/Buttons */
        --accent-yellow: #FFB300;    /* Richer Gold/Amber - For Ratings */
        --background-light: #F4FFF9; /* Very light, fresh background */
        --text-dark: #212121;        /* Near Black for high text contrast */
        --shadow-color: rgba(46, 139, 87, 0.15); /* Slightly stronger shadow */
        --icon-blue: #1976D2;        /* For contact/info icons */
        --icon-red: #D32F2F;         /* For error/warning states */
    }

    /* General Styling */
    .container-fluid {
        background-color: var(--background-light);
        padding-top: 20px;
        padding-bottom: 20px;
        min-height: 100vh;
    }
    .page-title {
        color: var(--primary-green);
        font-weight: 800;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
        border-bottom: 4px solid var(--secondary-green);
        display: inline-block;
        font-size: 2.5rem; /* Bigger title */
    }

    /* Filter Card */
    .filter-card {
        background-color: #fff;
        border-radius: 15px;
        border: 1px solid #C8E6C9; /* Lighter border */
        box-shadow: 0 6px 20px var(--shadow-color);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-label {
        color: var(--primary-green);
        font-weight: 600;
        font-size: 0.95rem; /* Slightly larger label */
        display: block; 
        margin-bottom: 0.3rem;
    }
    .form-control-sm {
        border-radius: 10px;
        border-color: #A5D6A7;
        color: var(--text-dark);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control-sm:focus {
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 0.25rem rgba(104, 159, 56, 0.3); 
    }

    /* Table Styling (Desktop) */
    .table-responsive {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 25px var(--shadow-color);
    }
    .table thead th {
        background-color: var(--primary-green);
        color: #fff;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem; 
        letter-spacing: 0.8px;
    }
    .table tbody tr:nth-of-type(even) {
        background-color: #F9FFF5; 
    }
    .table tbody tr:nth-of-type(odd) {
        background-color: #fff; 
    }
    .table td {
        vertical-align: middle;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    /* Rating Stars */
    .star-rating {
        color: var(--accent-yellow);
        font-size: 0.9rem;
    }

    /* View Produce Button - More vibrant action */
    .view-produce-btn {
        background-color: var(--secondary-green);
        border-color: var(--secondary-green);
        font-weight: 600;
        border-radius: 10px; 
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    .view-produce-btn:hover {
        background-color: #558B2F; 
        border-color: #558B2F;
        transform: translateY(-1px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
        color: #fff;
    }
    .pagination .page-link {
        color: var(--primary-green);
        border-radius: 8px;
        margin: 0 4px;
        transition: all 0.3s;
    }
    .pagination .page-link:hover {
        background-color: #E8F5E9;
        color: var(--text-dark);
    }

    /* === MOBILE RESPONSIVE CARDS - MORE COLORFUL === */
    .farmer-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px var(--shadow-color);
        margin-bottom: 15px;
        padding: 1rem;
        border-left: 5px solid var(--secondary-green); /* Added accent border */
        display: none; 
    }
    .card-header-icon {
        color: var(--secondary-green);
        font-size: 1.5rem;
    }
    .card-title-farmer {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .card-detail-label {
        font-size: 0.75rem; 
        color: #555;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .card-detail-value {
        font-weight: 700; 
        color: var(--text-dark);
        font-size: 0.9rem;
        line-height: 1.3;
    }
    .card-rating-section {
        border-top: 1px dashed #ddd;
        padding-top: 10px;
        margin-top: 10px;
    }
    .table-container-desktop {
        display: block;
    }
    .card-container-mobile {
        display: none;
    }

    @media (max-width: 767.98px) {
        .page-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .table-container-desktop {
            display: none; 
        }
        .card-container-mobile {
            display: block; 
        }
        .farmer-card {
            display: block;
        }
        .filter-card {
            padding: 1rem;
        }
    }

    /* === MODAL STYLING - POP & STRUCTURED === */
    #farmerDetailsModal .modal-content {
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2); 
        border: 3px solid var(--secondary-green);
    }
    #farmerDetailsModal .modal-header {
        background-color: var(--primary-green);
        color: #fff;
        border-radius: 17px 17px 0 0; 
        border-bottom: none;
        padding: 1.5rem 2rem;
    }
    #farmerDetailsModal .modal-title {
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: 0.5px;
    }
    #farmerDetailsModal .close {
        color: #fff;
        opacity: 0.9;
        font-size: 1.8rem;
    }
    #farmerDetailsModal .modal-body {
        padding: 2rem;
        background-color: var(--background-light);
    }
    /* Modal Section Headings - Very distinct */
    #farmerDetailsModal .modal-body h4, #farmerDetailsModal .modal-body h5 {
        color: var(--primary-green);
        font-weight: 700;
        margin-top: 1.8rem;
        margin-bottom: 1rem;
        padding-bottom: 0.3rem;
        border-bottom: 3px solid var(--accent-yellow); 
    }
    .form-control-plaintext {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.95rem;
        background-color: #fff; 
        border-radius: 6px;
        border: 1px solid #eee;
    }
    /* Produce/Rating Card Styling in Modal - Use clear division */
    .modal-body .card {
        border: 1px solid #E0F2F1; 
        border-left: 6px solid var(--secondary-green); 
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .modal-body .card:hover {
        transform: translateY(-2px);
    }
    .modal-body .card label {
        font-weight: 600;
        color: var(--primary-green);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    /* Modal Footer */
    #farmerDetailsModal .modal-footer {
        border-top: 1px solid #eee;
        padding: 1rem 2rem;
        background-color: #fff;
        border-radius: 0 0 17px 17px;
    }
    #farmerDetailsModal .modal-footer .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        border-radius: 10px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
    }

</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid py-4">
            <div class="container" style="max-width: 1400px;">
                <h2><i class="fas fa-history me-2"></i> Order History</h2>

                <!-- Filters -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 18px;">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label text-success fw-600"><i class="fas fa-filter"></i> Filter by Status</label>
                                <select class="form-control form-control-sm" id="statusFilterOrders">
                                    <option value="">All Statuses</option>
                                    <option value="Make Payment">Make Payment</option>
                                    <option value="Processing Produce For Delivery">Processing Produce For Delivery</option>
                                    <option value="Produce On The Way">Produce On The Way</option>
                                    <option value="Produce Delivered & Confirmed">Produce Delivered & Confirmed</option>
                                    <option value="Cancel Order">Cancel Order</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label text-success fw-600"><i class="far fa-calendar-alt"></i> Filter by Date</label>
                                <input type="date" class="form-control form-control-sm" id="dateFilterOrders">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <!-- <button class="btn btn-success btn-sm w-100" id="clearFilters">
                                    <i class="fas fa-times"></i> Clear
                                </button> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="ordersTable">
                        <thead class="table-success text-white">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Farmer</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-success text-white" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-clipboard-list me-2"></i> Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Order ID</label>
                            <input type="text" class="form-control" id="detailOrderId" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="text" class="form-control" id="detailOrderDate" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" id="detailStatus" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="text" class="form-control fw-bold text-success" id="detailTotalAmount" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Farmer</label>
                            <input type="text" class="form-control" id="detailFarmer" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="detailQuantity" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="detailDeliveryDate">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Status</label>
                            <input type="text" class="form-control" id="detailPaymentStatus" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Delivery Address</label>
                            <textarea class="form-control" id="detailDeliveryAddress" rows="2"></textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <h6>Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="detailItemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Subtotal</th>
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
                    <button type="button" class="btn btn-success" id="saveChangesBtn" style="display:none;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'buyerfooter.php'; ?>
</div>

<!-- Scripts -->
 <?php include 'buyerscript.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
$(document).ready(function() {
    const modalEl = document.getElementById('orderDetailsModal');
    const modal = new bootstrap.Modal(modalEl);
    let currentPage = 1, currentStatus = '', currentDate = '';
    let editableOrderId = null;

    function formatCurrency(amount) {
        const num = parseFloat(amount);
        return isNaN(num) ? '₦0' : '₦' + num.toLocaleString('en-NG');
    }

    function loadOrders(page = 1, status = '', date = '') {
        currentPage = page; currentStatus = status; currentDate = date;
        $('#ordersTable tbody').html('<tr><td colspan="8" class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></td></tr>');

        $.get('views/fetch_orders.php', { page, order_status: status, date }, function(res) {
            if (res.error) {
                $('#ordersTable tbody').html(`<tr><td colspan="8" class="text-center text-danger py-4">${res.error}</td></tr>`);
                return;
            }
            renderTable(res.orders || []);
            renderPagination(res.pagination || {});
        }, 'json');
    }

    function renderTable(orders) {
        const tbody = $('#ordersTable tbody');
        tbody.empty();
        if (orders.length === 0) {
            tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">No orders found</td></tr>');
            return;
        }

        orders.forEach(order => {
            const data = JSON.stringify(order).replace(/'/g, "&#39;");
            const canPay = order.order_status === 'Make Payment' && order.payment_status !== 'Paid';
            const canEdit = order.order_status === 'Order Sent';

            const payBtn = canPay
                ? `<button class="btn btn-sm btn-warning payBtn shadow-sm" data-order-id="${order.numeric_order_id}" data-order-amount="${order.total_amount_raw}" data-order-email="${order.buyer_email}">
                     Pay Now
                   </button>`
                : `<button class="btn btn-sm btn-secondary" disabled>Paid</button>`;

            const items = order.items.map(i => `${i.name} (${i.quantity} Unit${i.quantity>1?'s':''})`).join(', ');

            const actions = `
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary view-details" data-order-data='${data}' title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${canEdit ? `
                        <button class="btn btn-sm btn-outline-warning edit-order-btn" data-order-data='${data}' title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-order-btn" data-order-id="${order.numeric_order_id}" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    ` : ''}
                </div>`;

            tbody.append(`
                <tr>
                    <td><strong>${order.order_id}</strong></td>
                    <td>${order.created_at}</td>
                    <td class="fw-bold text-success">${formatCurrency(order.total_amount_raw)}</td>
                    <td><span class="badge ${getBadgeClass(order.order_status)}">${order.order_status}</span></td>
                    <td>${payBtn}</td>
                    <td><i class="fas fa-tractor text-warning me-1"></i>${order.farmer}</td>
                    <td>${items}</td>
                    <td>${actions}</td>
                </tr>
            `);
        });
    }

    function getBadgeClass(status) {
        const map = {
            'Make Payment': 'bg-warning text-dark',
            'Processing Produce For Delivery': 'bg-info text-white',
            'Produce On The Way': 'bg-primary text-white',
            'Produce Delivered & Confirmed': 'bg-success text-white',
            'Cancel Order': 'bg-danger text-white'
        };
        return map[status] || 'bg-secondary text-white';
    }

    function renderPagination(p) {
        const ul = $('#pagination');
        ul.empty();
        if (!p || p.last_page <= 1) return;

        const addPage = (num, text = num, disabled = false) => {
            ul.append(`<li class="page-item ${disabled?'disabled':''}"><a class="page-link" href="#" data-page="${num}">${text}</a></li>`);
        };

        addPage(1, 'First');
        if (p.current_page > 1) addPage(p.current_page - 1, 'Previous');

        for (let i = Math.max(1, p.current_page - 2); i <= Math.min(p.last_page, p.current_page + 2); i++) {
            ul.append(`<li class="page-item ${i===p.current_page?'active':''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
        }

        if (p.current_page < p.last_page) addPage(p.current_page + 1, 'Next');
        addPage(p.last_page, 'Last');
    }

    function showOrderDetails(order, edit = false) {
        editableOrderId = edit ? order.numeric_order_id : null;

        $('#detailOrderId').val(order.order_id);
        $('#detailOrderDate').val(order.created_at);
        $('#detailStatus').val(order.order_status);
        $('#detailTotalAmount').val(formatCurrency(order.total_amount_raw));
        $('#detailFarmer').val(order.farmer);
        $('#detailQuantity').val(order.items[0]?.quantity || 1);
        $('#detailDeliveryDate').val(order.delivery_date?.slice(0,10) || '');
        $('#detailDeliveryAddress').val(order.delivery_address || '');
        $('#detailPaymentStatus').val(order.payment_status);

        const tbody = $('#detailItemsTable tbody');
        tbody.empty();
        order.items.forEach(i => {
            tbody.append(`
                <tr>
                    <td>${i.name}</td>
                    <td>${formatCurrency(i.price_raw)}</td>
                    <td>${i.quantity}</td>
                    <td>${formatCurrency(i.price_raw * i.quantity)}</td>
                </tr>
            `);
        });

        // Toggle edit mode
        ['#detailQuantity', '#detailDeliveryDate', '#detailDeliveryAddress'].forEach(s => {
            $(s).prop('readonly', !edit);
        });
        $('#saveChangesBtn').toggle(edit);

        modal.show();
    }

    // Proper modal close cleanup
    modalEl.addEventListener('hidden.bs.modal', () => {
        ['#detailQuantity', '#detailDeliveryDate', '#detailDeliveryAddress'].forEach(s => $(s).prop('readonly', true));
        $('#saveChangesBtn').hide();
        editableOrderId = null;
    });

    // View / Edit Buttons
    $(document).on('click', '.view-details', e => showOrderDetails($(e.currentTarget).data('order-data')));
    $(document).on('click', '.edit-order-btn', e => showOrderDetails($(e.currentTarget).data('order-data'), true));

    // Save Changes
    $('#saveChangesBtn').on('click', function() {
        const data = {
            action: 'edit',
            order_id: editableOrderId,
            quantity: $('#detailQuantity').val(),
            delivery_date: $('#detailDeliveryDate').val(),
            delivery_address: $('#detailDeliveryAddress').val()
        };

        Swal.fire({
            title: 'Save Changes?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2E8B57'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('views/order_action_process.php', data, res => {
                    if (res.status === 'success') {
                        Swal.fire('Updated!', res.message, 'success');
                        modal.hide();
                        loadOrders(currentPage, currentStatus, currentDate);
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                }, 'json');
            }
        });
    });

    // Delete Order
    $(document).on('click', '.delete-order-btn', function() {
        const id = $(this).data('order-id');
        Swal.fire({
            title: 'Delete Order?',
            text: 'This cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545'
        }).then(r => {
            if (r.isConfirmed) {
                $.post('views/order_action_process.php', { action: 'delete', order_id: id }, res => {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success');
                        loadOrders(currentPage, currentStatus, currentDate);
                    }
                }, 'json');
            }
        });
    });

    // PAYMENT - FIXED: sends amount!
    $(document).on('click', '.payBtn:not([disabled])', function() {
        const btn = $(this);
        const orderId = btn.data('order-id');
        const amount = parseFloat(btn.data('order-amount'));
        const email = btn.data('order-email');

        const handler = PaystackPop.setup({
            key: 'pk_test_820ef67599b8a00fd2dbdde80e56e94fda5ed79f', // Change to live key later
            email: email,
            amount: amount * 100,
            currency: 'NGN',
            ref: 'ORD-' + orderId + '-' + Date.now(),
            callback: function(response) {
                btn.prop('disabled', true).html('Processing...');

                $.post('payment_process.php', {
                    order_id: orderId,
                    reference: response.reference,
                    amount: amount   // THIS LINE FIXES THE ERROR!
                }, function(res) {
                    if (res.success) {
                        Swal.fire('Payment Successful!', 'Your order is now being processed.', 'success');
                        loadOrders(currentPage, currentStatus, currentDate);
                    } else {
                        Swal.fire('Payment Failed', res.message || 'Verification failed', 'error');
                        btn.prop('disabled', false).html('Pay Now');
                    }
                }, 'json').fail(() => {
                    Swal.fire('Error', 'Could not connect to server', 'error');
                    btn.prop('disabled', false).html('Pay Now');
                });
            },
            onClose: function() {
                btn.prop('disabled', false).html('Pay Now');
            }
        });
        handler.openIframe();
    });

    // Filters
    $('#statusFilterOrders, #dateFilterOrders').on('change', function() {
        loadOrders(1, $('#statusFilterOrders').val(), $('#dateFilterOrders').val());
    });
    $('#clearFilters').on('click', function() {
        $('#statusFilterOrders, #dateFilterOrders').val('');
        loadOrders(1);
    });

    // Pagination
    $(document).on('click', '#pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) loadOrders(page, currentStatus, currentDate);
    });

    // Initial load
    loadOrders();
});
</script>

