$(document).ready(function() {
    function loadProduce() {
        $.ajax({
            url: 'views/fetch_produce.php', // Adjust the path if necessary
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('tbody').empty(); // Clear existing table rows
                $.each(data, function(index, produce) {
                    $('tbody').append(`
                        <tr>
                            <td>${produce.produce}</td>
                            <td>${produce.quantity}</td>
                            <td>${produce.price}</td>
                            <td>${produce.available_date}</td>
                            <td>${produce.farmer_id}</td>
                            <td>Unknown Location</td> <td>
                                <button
                                    class="btn btn-sm btn-success"
                                    data-toggle="modal"
                                    data-target="#orderFormModal"
                                    data-produce="${produce.produce}"
                                    data-produce-id="${produce.id}"
                                    data-farmer="${produce.farmer_id}"
                                    data-price="${produce.price}"
                                >
                                    Order
                                </button>
                            </td>
                        </tr>
                    `);
                });
                // Re-initialize pagination after loading data
                initPagination();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching produce:", error);
                $('tbody').html('<tr><td colspan="7" class="text-center">Error loading produce.</td></tr>');
            }
        });
    }

    // Load produce data on page load
    loadProduce();

    $('#produceFilter, #locationFilter, #farmerFilter').on('keyup change', function() {
        // Implement your filtering logic here on the client-side
        let produce = $('#produceFilter').val().toLowerCase();
        let location = $('#locationFilter').val().toLowerCase();
        let farmer = $('#farmerFilter').val().toLowerCase();

        $('tbody tr').each(function() {
            let rowProduce = $(this).find('td:eq(0)').text().toLowerCase();
            let rowLocation = $(this).find('td:eq(5)').text().toLowerCase();
            let rowFarmer = $(this).find('td:eq(4)').text().toLowerCase();
            let showRow = true;

            if (produce && rowProduce.indexOf(produce) === -1) {
                showRow = false;
            }

            if (location && rowLocation.indexOf(location) === -1) {
                showRow = false;
            }

            if (farmer && rowFarmer.indexOf(farmer) === -1) {
                showRow = false;
            }

            if (showRow) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        // Re-initialize pagination after filtering (optional, depends on desired behavior)
        initPagination();
    });

    // Populate the order form modal
    $('#orderFormModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const produce = button.data('produce');
        const farmer = button.data('farmer');
        const price = button.data('price');
        const produceId = button.data('produce-id');

        const modal = $(this);
        modal.find('#produceName').val(produce);
        modal.find('#farmerName').val(farmer);
        modal.find('#pricePerUnit').val(price);
        modal.find('#produceName').data('produce-id', produceId); // Store produce ID for submission
    });

    // Handle order form submission via AJAX
    $('#orderForm').submit(function (event) {
        event.preventDefault();

        const formData = $(this).serialize();
        const produceId = $('#produceName').data('produce-id');
        formData +='&produce_id=' + produceId; // Add produce_id to form data

        $.ajax({
            url: 'prod_process.php', // Adjust the path if necessary
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status === 'success') {
                    alert(data.message);
                    $('#orderFormModal').modal('hide');
                    $('#orderForm')[0].reset();
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error submitting order:", error);
                alert('An error occurred while submitting the order.');
            }
        });
    });

    // Client-side pagination logic
    function initPagination() {
        const rowsPerPage = 5;
        let currentPage = 1;
        let rows = $('tbody tr:visible'); // Only paginate visible rows
        let numPages = Math.ceil(rows.length / rowsPerPage);
        const paginationContainer = $('.pagination');
        paginationContainer.empty(); // Clear previous pagination

        if (numPages > 1) {
            paginationContainer.append('<li class="page-item"><a class="page-link" href="#" data-page="prev">&laquo;</a></li>');
            for (let i = 1; i <= numPages; i++) {
                paginationContainer.append(`<li class="page-item ${i === 1 ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
            }
            paginationContainer.append('<li class="page-item"><a class="page-link" href="#" data-page="next">&raquo;</a></li>');

            showPage(currentPage, rowsPerPage);

            paginationContainer.find('.page-link').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page === 'prev') {
                    currentPage = Math.max(1, currentPage - 1);
                } else if (page === 'next') {
                    currentPage = Math.min(numPages, currentPage + 1);
                } else {
                    currentPage = parseInt(page);
                }
                showPage(currentPage, rowsPerPage);
                paginationContainer.find('.page-item').removeClass('active');
                paginationContainer.find(`[data-page="${currentPage}"]`).parent('.page-item').addClass('active');
            });
        }
    }

    function showPage(page, rowsPerPage) {
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        $('tbody tr').hide().slice(startIndex, endIndex).show();
    }

    // Initial pagination setup (after the initial load)
    // initPagination(); // Call this inside the success function after data is loaded
});
