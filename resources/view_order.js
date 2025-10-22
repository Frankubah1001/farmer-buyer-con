$(document).ready(function () {
    let currentPage = 1;
    let totalPages = 0;
    let filterOrderId = window.filterOrderId || 0;

    function loadAvailableProduce(page) {
        currentPage = page;
        const produceFilter = $('#produceFilterInline').val();
        const locationFilter = $('#locationFilterInline').val();
        const orderIdFilter = filterOrderId > 0 ? filterOrderId : '';

        $.ajax({
            url: 'get_available_produce.php',
            type: 'GET',
            data: { page: page, produce: produceFilter, location: locationFilter, order_id: orderIdFilter },
            dataType: 'json',
            success: function (response) {
                if (response.error) {
                    $('#availableProduceTable tbody').html('<tr><td colspan="10" class="text-center">' + response.error + '</td></tr>');
                    $('#pagination').empty();
                } else {
                    populateProduceTable(response.data);
                    totalPages = response.total_pages;
                    populatePagination(totalPages, currentPage);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching orders:", error);
                $('#availableProduceTable tbody').html('<tr><td colspan="10" class="text-center">Error loading data.</td></tr>');
                $('#pagination').empty();
            }
        });
    }

    function getStatusBadgeClass(status) {
        switch(status) {
            case 'Delivery Confirmed':
                return 'badge bg-success text-white'; // Green
            case 'Still Awaiting Delivery':
                return 'badge bg-primary text-white'; // Blue
            case 'Delivery Not Received':
                return 'badge bg-danger text-white'; // Light blue
            case 'Delivery Cancelled':
                return 'badge bg-secondary text-white'; // Red
            default:
                return ''; // Gray
        }
    }

    function populateProduceTable(orderListings) {
    const tbody = $('#availableProduceTable tbody');
    tbody.empty();

    if (orderListings.length === 0) {
        tbody.html('<tr><td colspan="11" class="text-center">No orders found.</td></tr>');
        return;
    }

    orderListings.forEach((order) => {
        const statusBadgeClass = getStatusBadgeClass(order.order_status);
        // Check if payment_status is 'Paid' to disable specific options
        const isPaid = order.payment_status === 'Paid';
        const row = `
            <tr data-order-id="${order.order_id}">
                <td>${order.order_id}</td>
                <td>${order.buyer_name}</td>
                <td>${order.produce_name}</td>
                <td>${order.order_quantity}</td>
                <td>${order.price_per_unit}</td>
                <td>${order.order_delivery_address}</td>
                <td>${order.order_date}</td>
                <td>${order.delivery_date}</td>
                <td>${order.buyer_phone}</td>
                <td>
                    <select class="form-control form-control-sm order-status-dropdown" data-order-id="${order.order_id}" ${order.order_status === 'Produce Delivered Confirmed' || order.order_status === 'Cancelled' ? 'disabled' : ''}>
                        <option value="">Select Status</option>
                        <option value="Processing Produce" ${order.order_status === 'Processing Produce' ? 'selected' : ''} ${isPaid ? 'disabled' : ''}>Processing</option>
                        <option value="Make Payment" ${order.order_status === 'Make Payment' ? 'selected' : ''} ${isPaid ? 'disabled' : ''}>Make Payment</option>
                        <option value="Produce On the Way" ${order.order_status === 'Produce On the Way' ? 'selected' : ''} ${isPaid ? 'disabled' : ''}>Produce On the Way</option>
                        <option value="Produce Delivered Confirmed" ${order.order_status === 'Produce Delivered Confirmed' ? 'selected' : ''}>Produce Delivered Confirmed</option>
                        <option value="Cancelled" ${order.order_status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
                    </select>
                </td>
                <td><span class="${statusBadgeClass}">${order.order_status}</span></td>
                <td><span class="${statusBadgeClass}">${order.payment_status}</span></td>
            </tr>
        `;
        tbody.append(row);

        // Disable dropdown if order status is "Delivered Confirmed" or "Cancelled" on initial load
        if (order.order_status === 'Cancelled' || order.order_status === 'Produce Delivered Confirmed') {
            const dropdown = tbody.find(`.order-status-dropdown[data-order-id="${order.order_id}"]`);
            dropdown.prop('disabled', true);
        }
    });
}

    function populatePagination(totalPages, currentPage) {
        const pagination = $('#pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        const prevLi = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
              <a class="page-link page-number-link" href="#" data-page="${currentPage - 1}">Previous</a>
          </li>`;
        pagination.append(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            const pageLi = `<li class="page-item ${activeClass}">
              <a class="page-link page-number-link" href="#" data-page="${i}">${i}</a>
          </li>`;
            pagination.append(pageLi);
        }

        const nextLi = `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
              <a class="page-link page-number-link" href="#" data-page="${currentPage + 1}">Next</a>
          </li>`;
        pagination.append(nextLi);
    }

    $(document).on('click', '.page-number-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (!isNaN(page)) {
            loadAvailableProduce(page);
        }
    });

    function updateOrderStatus(orderId, newStatus) {
        $.ajax({
            url: 'update_order_status.php',
            type: 'POST',
            data: { order_id: orderId, status: newStatus },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    loadAvailableProduce(currentPage);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error updating order status:", error);
                toastr.error("An error occurred while updating the order status.");
            }
        });
    }

    $('#produceFilterInline, #locationFilterInline').on('keyup change', function() {
        loadAvailableProduce(1);
    });

    $(document).on('change', '.order-status-dropdown', function() {
        const orderId = $(this).data('order-id');
        const newStatus = $(this).val();
        
        if (newStatus === '') {
            toastr.warning('Please select a valid status');
            return;
        }
        
        updateOrderStatus(orderId, newStatus);
        
        // Disable dropdown for certain statuses
        if (newStatus === 'Produce Delivered Confirmed' || newStatus === 'Cancelled') {
            $(this).prop('disabled', true);
        }
    });

    loadAvailableProduce(1);
});