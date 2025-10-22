$(document).ready(function() {
    let orderDetailsModal = null;
    let currentStatus = '';
    let currentDate = '';
    let currentPage = 1; // Track the current page

    // Initialize modal
    orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

    // Load order statuses from the database and populate the filter
    function loadOrderStatuses() {
        $.ajax({
            url: 'fetch_order_statuses.php', // This path is CORRECT - same directory
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error("Error loading order statuses:", response.error);
                    return;
                }
                const statusFilter = $('#statusFilterOrders');
                response.statuses.forEach(status => {
                    statusFilter.append(`<option value="${status}">${status}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching order statuses:", error);
            }
        });
    }

    // Load orders function
    function loadOrders(page = 1, status = '', date = '') {
        currentPage = page;
        currentStatus = status;
        currentDate = date;

        $.ajax({
            url: 'views/fetch_orders.php', // This path is CORRECT - in the 'views' subdirectory
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
            $tbody.html('<tr><td colspan="7" class="text-center">No orders found</td></tr>');
            return;
        }

        orders.forEach(order => {
            // Convert the order object to a JSON string
            const orderDataString = JSON.stringify(order);
            $tbody.append(`
                <tr>
                    <td>${order.order_id}</td>
                    <td>${order.created_at}</td>
                    <td>${order.total_amount}</td>
                    <td><span class="badge ${getStatusBadgeClass(order.order_status)}">${order.order_status}</span></td>
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

        if (lastPage <= 1) return; // No pagination needed

        // Previous button
        $pagination.append(`
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    &laquo;
                </a>
            </li>
        `);

        // Page numbers - display a maximum of 5 pages
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

        // Next button
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
            case 'not ordered yet':
                return 'badge-warning';
            case 'processing':
                return 'badge-primary';
            case 'delivering':
                return 'badge-success';
            case 'produce on the way':
                return 'badge-danger';
            case 'delivered confirmed':
                return 'badge-success'; // Assuming 'Delivered Confirmed' is also a success state
            // case 'pending':
            //     return 'badge-secondary';
            // case 'cancelled':
            //     return 'badge-danger';
            // case 'delivered':
            //     return 'badge-success';
            default:
                return 'badge-secondary';
        }
    }

    // Show error
    function showError(message) {
        $('#ordersTable tbody').html(`<tr><td colspan="7" class="text-center text-danger">${message}</td></tr>`);
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
        $('#detailDeliveryDate').val(order.delivery_date);

        const $itemsTable = $('#detailItemsTable tbody');
        $itemsTable.empty();

        order.items.forEach(item => {
            $itemsTable.append(`
                <tr>
                    <td>${item.name}</td>
                    <td>${item.price}</td>
                    <td>${item.quantity}</td>
                </tr>
            `);
        });

        orderDetailsModal.show();
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
            showOrderDetails(JSON.parse(orderData)); // Parse the JSON string back into an object
        } catch (e) {
            console.error("Error retrieving order data:", e);
        }
    });

    // Initial load
    loadOrderStatuses(); // Load the status options from the database
    loadOrders(1); // Load the first page of orders initially
});
