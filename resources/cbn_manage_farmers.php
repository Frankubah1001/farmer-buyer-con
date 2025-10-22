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
    <title>Manage Farmers - CBN Portal</title>
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
        .badge-pending { background-color: #ffc107; color: #212529; }
        .badge-approved { background-color: #28a745; color: #fff; }
        .badge-rejected { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">CBN Portal</div>
            <div class="list-group list-group-flush">
                <a href="cbn_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="cbn_manage_farmers.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-users me-2"></i>Manage Farmers</a>
                <a href="cbn_manage_prices.php" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tags me-2"></i>Manage Prices</a>
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
                <h3 class="mb-4 text-gray-800">Manage Farmer Registrations</h3>

                <div id="alertMessage" class="alert d-none" role="alert"></div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Farmers Awaiting Approval</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="farmersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Status</th>
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

            function loadFarmers(page) {
                currentPage = page;
                $.ajax({
                    url: 'cbn_api_farmers.php',
                    type: 'GET',
                    dataType: 'json',
                    data: { action: 'get_farmers', page: page, status: 'pending' }, // Fetch only pending farmers
                    success: function(response) {
                        if (response.status === 'success') {
                            populateFarmersTable(response.data);
                            totalPages = response.total_pages;
                            populatePagination(totalPages, currentPage);
                        } else {
                            alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                            $('#farmersTable tbody').html('<tr><td colspan="7" class="text-center">' + response.message + '</td></tr>');
                            $('#pagination').empty();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching farmers:', error);
                        alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text('Error loading farmers data.');
                        $('#farmersTable tbody').html('<tr><td colspan="7" class="text-center">Error loading farmers data.</td></tr>');
                        $('#pagination').empty();
                    }
                });
            }

            function populateFarmersTable(farmers) {
                const tbody = $('#farmersTable tbody');
                tbody.empty();

                if (farmers.length === 0) {
                    tbody.html('<tr><td colspan="7" class="text-center">No pending farmer registrations found.</td></tr>');
                    return;
                }

                farmers.forEach(farmer => {
                    const statusText = getFarmerStatusText(farmer.cbn_approved);
                    const statusBadgeClass = getFarmerStatusBadgeClass(farmer.cbn_approved);

                    const row = `
                        <tr>
                            <td>${farmer.user_id}</td>
                            <td>${farmer.first_name} ${farmer.last_name}</td>
                            <td>${farmer.email}</td>
                            <td>${farmer.phone}</td>
                            <td>${farmer.address || 'N/A'}</td>
                            <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
                            <td>
                                ${farmer.cbn_approved == 0 ? `
                                <button class="btn btn-success btn-sm rounded-md approve-btn" data-user-id="${farmer.user_id}">Approve</button>
                                <button class="btn btn-danger btn-sm rounded-md reject-btn" data-user-id="${farmer.user_id}">Reject</button>
                                ` : 'N/A'}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }

            function getFarmerStatusText(status) {
                switch (status) {
                    case 0: return 'Pending';
                    case 1: return 'Approved';
                    case 2: return 'Rejected';
                    default: return 'Unknown';
                }
            }

            function getFarmerStatusBadgeClass(status) {
                switch (status) {
                    case 0: return 'badge-warning text-dark';
                    case 1: return 'badge-success';
                    case 2: return 'badge-danger';
                    default: return 'badge-secondary';
                }
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
                    loadFarmers(page);
                }
            });

            // Handle Approve/Reject clicks
            $(document).on('click', '.approve-btn, .reject-btn', function() {
                const userId = $(this).data('user-id');
                const newStatus = $(this).hasClass('approve-btn') ? 1 : 2; // 1 for Approved, 2 for Rejected

                $.ajax({
                    url: 'cbn_api_farmers.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_status',
                        user_id: userId,
                        status: newStatus,
                        cbn_user_id: <?php echo $_SESSION['cbn_user_id']; ?> // Pass CBN user ID for logging
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alertMessage.removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                            loadFarmers(currentPage); // Reload current page to reflect changes
                        } else {
                            alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error updating farmer status:', error);
                        alertMessage.removeClass('d-none alert-success').addClass('alert-danger').text('Error updating farmer status.');
                    }
                });
            });

            // Initial load
            loadFarmers(1);
        });
    </script>
</body>
</html>
