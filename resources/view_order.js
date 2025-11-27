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
                    $('#availableProduceTable tbody').html('<tr><td colspan="12" class="text-center">' + response.error + '</td></tr>');
                    $('#pagination').empty();
                } else {
                    populateProduceTable(response.data);
                    totalPages = response.total_pages;
                    populatePagination(totalPages, currentPage);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching orders:", error);
                $('#availableProduceTable tbody').html('<tr><td colspan="12" class="text-center">Error loading data.</td></tr>');
                $('#pagination').empty();
            }
        });
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Produce Delivered & Confirmed':
                return 'badge bg-success text-white'; // Green
            case 'Produce Transported':
                return 'badge bg-primary text-white'; // Blue
            case 'Processing Produce For Delivery':
                return 'badge bg-info text-white'; // Light blue
            case 'Make Payment':
                return 'badge bg-warning text-dark'; // Yellow
            case 'Cancelled':
                return 'badge bg-secondary text-white'; // Gray
            default:
                return 'badge bg-secondary text-white'; // Default
        }
    }

    function populateProduceTable(orderListings) {
        const tbody = $('#availableProduceTable tbody');
        tbody.empty();

        if (orderListings.length === 0) {
            tbody.html('<tr><td colspan="12" class="text-center">No orders found.</td></tr>');
            return;
        }

        orderListings.forEach((order) => {
            const statusBadgeClass = getStatusBadgeClass(order.order_status);

            const row = `
            <tr data-order-id="${order.order_id}">
                <td>${order.order_id}</td>
                <td>${order.buyer_name}</td>
                <td>${order.produce_name}</td>
                <td>${order.order_quantity}</td>
                <td>${order.price_per_unit}</td>
                <td>${order.total_amount}</td>
                <td>${order.order_delivery_address}</td>
                <td>${order.order_date}</td>
                <td>${order.delivery_date}</td>
                <td>${order.buyer_phone}</td>
                <td>
                    <select class="form-control form-control-sm order-status-dropdown" data-order-id="${order.order_id}" ${order.order_status === 'Produce Delivered & Confirmed' ? 'disabled' : ''}>
                        <option value="">Select Status</option>
                        <option value="Make Payment" ${order.order_status === 'Make Payment' ? 'selected' : ''}>Make Payment</option>
                        <option value="Processing Produce For Delivery" ${order.order_status === 'Processing Produce For Delivery' ? 'selected' : ''}>Processing Produce For Delivery</option>
                        <option value="Produce Transported" ${order.order_status === 'Produce Transported' ? 'selected' : ''}>Produce Transported</option>
                        <option value="Produce Delivered & Confirmed" ${order.order_status === 'Produce Delivered & Confirmed' ? 'selected' : ''}>Produce Delivered & Confirmed</option>
                    </select>
                </td>
                <td class="status-text"><span class="${statusBadgeClass}">${order.order_status}</span></td>
                <td><span class="${statusBadgeClass}">${order.payment_status}</span></td>
            </tr>
        `;
            tbody.append(row);
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
                    if (response.email_warning) {
                        toastr.warning(response.email_warning);
                    }
                    // We don't reload the table here anymore to avoid page reload/flicker, 
                    // as we updated the UI immediately.
                } else {
                    toastr.error(response.message);
                    loadAvailableProduce(currentPage); // Reload to revert to correct state
                }
            },
            error: function (xhr, status, error) {
                console.error("Error updating order status:", error);
                toastr.error("An error occurred while updating the order status.");
                loadAvailableProduce(currentPage); // Reload to revert
            }
        });
    }

    $('#produceFilterInline, #locationFilterInline').on('keyup change', function () {
        loadAvailableProduce(1);
    });

    $(document).on('change', '.order-status-dropdown', function () {
        const orderId = $(this).data('order-id');
        const newStatus = $(this).val();

        if (newStatus === '') {
            toastr.warning('Please select a valid status');
            return;
        }

        // Immediate UI update
        const row = $(this).closest('tr');
        const statusBadge = row.find('.status-text span');
        statusBadge.text(newStatus);
        statusBadge.attr('class', getStatusBadgeClass(newStatus));

        // Disable if final status
        if (newStatus === 'Produce Delivered & Confirmed') {
            $(this).prop('disabled', true);
        }

        updateOrderStatus(orderId, newStatus);
    });

    loadAvailableProduce(1);
});