<?php
session_start();
if (!isset($_SESSION['cbn_user_id'])) {
    header("Location: cbn_login.php");
    exit();
}
include 'DBcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prices - CBN Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f8f9fc; }
        .card { border-radius: 0.5rem; }
        .card-header { background-color: #4e73df; color: white; border-bottom: 1px solid #e3e6f0; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
        .nav-link { color: #4e73df; font-weight: bold; }
        .nav-link:hover { color: #2e59d9; }
        #wrapper { display: flex; }
        #sidebar-wrapper { min-height: 100vh; width: 250px; background-color: #f8f9fc; border-right: 1px solid #e3e6f0; }
        #page-content-wrapper { flex-grow: 1; padding: 20px; }
        .list-group-item.active { background-color: #4e73df !important; border-color: #4e73df !important; }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">CBN Portal</div>
            <div class="list-group list-group-flush">
                <a href="cbn_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="cbn_manage_farmers.php" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-users me-2"></i>Manage Farmers</a>
                <a href="cbn_manage_prices.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-tags me-2"></i>Manage Prices</a>
                <a href="cbn_logout.php" class="list-group-item list-group-item-action bg-transparent text-danger"><i class="fas fa-power-off me-2"></i>Logout</a>
            </div>
        </div>
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user me-2"></i> <?php echo htmlspecialchars($_SESSION['cbn_full_name']); ?> (<?php echo htmlspecialchars($_SESSION['cbn_role']); ?>)
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="cbn_logout.php">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid py-4">
                <h3 class="mb-4 text-gray-800">Manage Regulated Produce Prices</h3>

                <div id="alertMessage" class="alert d-none" role="alert"></div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Set and Update Produce Prices</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button class="btn btn-success rounded-md" id="addProduceBtn">Add New Produce for Regulation</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="pricesTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Produce Name</th>
                                        <th>Min Price (₦)</th>
                                        <th>Max Price (₦)</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
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
            </div>
        </div>
        </div>
    <div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="priceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-md">
                <div class="modal-header">
                    <h5 class="modal-title" id="priceModalLabel">Add/Edit Produce Price</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="priceForm">
                        <input type="hidden" id="priceId" name="price_id">
                        <div class="mb-3">
                            <label for="produceName" class="form-label">Produce Name</label>
                            <input type="text" class="form-control rounded-md" id="produceName" name="produce_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="minPrice" class="form-label">Min Price per Unit (₦)</label>
                            <input type="number" step="0.01" class="form-control rounded-md" id="minPrice" name="min_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="maxPrice" class="form-label">Max Price per Unit (₦)</label>
                            <input type="number" step="0.01" class="form-control rounded-md" id="maxPrice" name="max_price" required>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-md w-100">Save Price</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Toggle the sidebar
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("sidebarToggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };

        $(document).ready(function() {
            let currentPage = 1;
            let totalPages = 0;
            const alertMessage = $('#alertMessage');
            const priceModal = new bootstrap.Modal(document.getElementById('priceModal'));

            function loadPrices(page) {
                currentPage = page;
                $.ajax({
                    url: 'cbn_api_price.php',
                    type: 'GET',
                    dataType: 'json',
                    data: { action: 'get_prices', page: page },
                    success: function(response) {
                        if (response.status === 'success') {
                            populatePricesTable(response.data);
                            totalPages = response.total_pages;
                            populatePagination(totalPages, currentPage);
                        } else {
                            alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                            $('#pricesTable tbody').html('<tr><td colspan="6" class="text-center">' + response.message + '</td></tr>');
                            $('#pagination').empty();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching prices:', error);
                        alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text('Error loading price data.');
                        $('#pricesTable tbody').html('<tr><td colspan="6" class="text-center">Error loading price data.</td></tr>');
                        $('#pagination').empty();
                    }
                });
            }

            function populatePricesTable(prices) {
                const tbody = $('#pricesTable tbody');
                tbody.empty();

                if (prices.length === 0) {
                    tbody.html('<tr><td colspan="6" class="text-center">No regulated prices found.</td></tr>');
                    return;
                }

                prices.forEach(price => {
                    const row = `
                        <tr>
                            <td>${price.price_id}</td>
                            <td>${price.produce_name}</td>
                            <td>₦${parseFloat(price.min_price_per_unit).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>₦${parseFloat(price.max_price_per_unit).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                            <td>${new Date(price.updated_at).toLocaleString()}</td>
                            <td>
                                <button class="btn btn-warning btn-sm rounded-md edit-btn" data-id="${price.price_id}" data-name="${price.produce_name}" data-min="${price.min_price_per_unit}" data-max="${price.max_price_per_unit}">Edit</button>
                                <button class="btn btn-danger btn-sm rounded-md delete-btn" data-id="${price.price_id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function populatePagination(totalPages, currentPage) {
                const pagination = $('#pagination');
                pagination.empty();

                if (totalPages <= 1) return;

                const prevClass = currentPage === 1 ? 'disabled' : '';
                pagination.append(`<li class="page-item ${prevClass}"><a class="page-link page-number-link" href="#" data-page="${currentPage - 1}">Previous</a></li>`);

                for (let i = 1; i <= totalPages; i++) {
                    const activeClass = i === currentPage ? 'active' : '';
                    pagination.append(`<li class="page-item ${activeClass}"><a class="page-link page-number-link" href="#" data-page="${i}">${i}</a></li>`);
                }

                const nextClass = currentPage === totalPages ? 'disabled' : '';
                pagination.append(`<li class="page-item ${nextClass}"><a class="page-link page-number-link" href="#" data-page="${currentPage + 1}">Next</a></li>`);
            }

            $(document).on('click', '.page-number-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (!isNaN(page) && page > 0 && page <= totalPages) {
                    loadPrices(page);
                }
            });

            // Add new produce button click
            $('#addProduceBtn').on('click', function() {
                $('#priceForm')[0].reset(); // Clear form
                $('#priceId').val(''); // Clear hidden ID
                $('#priceModalLabel').text('Add New Produce Price');
                priceModal.show();
            });

            // Edit button click
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const min = $(this).data('min');
                const max = $(this).data('max');

                $('#priceId').val(id);
                $('#produceName').val(name);
                $('#minPrice').val(min);
                $('#maxPrice').val(max);
                $('#priceModalLabel').text('Edit Produce Price');
                priceModal.show();
            });

            // Delete button click
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this price regulation?')) {
                    $.ajax({
                        url: 'cbn_api_price.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'delete_price',
                            price_id: id,
                            cbn_user_id: <?php echo $_SESSION['cbn_user_id']; ?> // Pass CBN user ID for logging
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                alertMessage.removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                                loadPrices(currentPage); // Reload current page
                            } else {
                                alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error deleting price:', error);
                            alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text('Error deleting price.');
                        }
                    });
                }
            });

            // Handle form submission for Add/Edit
            $('#priceForm').on('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                formData.append('cbn_user_id', <?php echo $_SESSION['cbn_user_id']; ?>); // Add CBN user ID

                const action = $('#priceId').val() ? 'update_price' : 'add_price';
                formData.append('action', action);

                $.ajax({
                    url: 'cbn_api_price.php',
                    type: 'POST',
                    dataType: 'json',
                    data: Object.fromEntries(formData.entries()), // Convert FormData to plain object for jQuery AJAX
                    success: function(response) {
                        if (response.status === 'success') {
                            alertMessage.removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                            priceModal.hide();
                            loadPrices(currentPage); // Reload current page
                        } else {
                            alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error saving price:', error);
                        alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text('Error saving price.');
                    }
                });
            });

            // Initial load
            loadPrices(1);
        });
    </script>
</body>
</html>
