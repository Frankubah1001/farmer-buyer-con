<?php
session_start();
// Check if CBN user is logged in, otherwise redirect to login
if (!isset($_SESSION['cbn_user_id'])) {
    header("Location: cbn_login.php");
    exit();
}
include 'DBcon.php'; // Your database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBN Dashboard - FarmerBuyerConnection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
        }
        .card {
            border-radius: 0.5rem;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            border-bottom: 1px solid #e3e6f0;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .nav-link {
            color: #4e73df;
            font-weight: bold;
        }
        .nav-link:hover {
            color: #2e59d9;
        }
        #wrapper {
            display: flex;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            background-color: #f8f9fc;
            border-right: 1px solid #e3e6f0;
        }
        #page-content-wrapper {
            flex-grow: 1;
            padding: 20px;
        }
        .list-group-item.active {
            background-color: #4e73df !important;
            border-color: #4e73df !important;
        }
        .chart-container {
            position: relative;
            height: 40vh; /* Adjust height as needed */
            width: 100%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">CBN Portal</div>
            <div class="list-group list-group-flush">
                <a href="cbn_dashboard.php" class="list-group-item list-group-item-action bg-transparent second-text active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="cbn_manage_farmers.php" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-users me-2"></i>Manage Farmers</a>
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
                <h3 class="mb-4 text-gray-800">CBN Dashboard Overview</h3>

                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-primary text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Total Registered Farmers</div>
                                        <div class="h5 mb-0 font-weight-bold" id="total-farmers-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-warning text-dark shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Pending Farmer Approvals</div>
                                        <div class="h5 mb-0 font-weight-bold" id="pending-farmers-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-success text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Total Approved Farmers</div>
                                        <div class="h5 mb-0 font-weight-bold" id="approved-farmers-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-info text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Regulated Produce Types</div>
                                        <div class="h5 mb-0 font-weight-bold" id="regulated-produce-count">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-leaf fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card bg-secondary text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Total Loan Applications</div>
                                        <div class="h5 mb-0 font-weight-bold" id="total-loan-applications">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card bg-dark text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Approved Loan Applications</div>
                                        <div class="h5 mb-0 font-weight-bold" id="approved-loan-applications">0</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card bg-danger text-white shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                                            Total Platform Transactions Value</div>
                                        <div class="h5 mb-0 font-weight-bold" id="total-transactions-value">₦0.00</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-white">Farmer Approval Status Distribution</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="farmerApprovalChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-white">Regulated Produce Price Categories</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="producePriceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
        </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Toggle the sidebar
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("sidebarToggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };

        $(document).ready(function() {
            function fetchDashboardData() {
                $.ajax({
                    url: 'cbn_get_dashb_data.php', // New backend script for dashboard data
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            updateDashboardCards(response);
                            renderCharts(response);
                        } else {
                            console.error('Error fetching dashboard data:', response.message);
                            // Optionally display an alert on the dashboard
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error fetching dashboard data:', error);
                    }
                });
            }

            function updateDashboardCards(data) {
                $('#total-farmers-count').text(data.total_farmers);
                $('#pending-farmers-count').text(data.pending_farmers);
                $('#approved-farmers-count').text(data.approved_farmers);
                $('#regulated-produce-count').text(data.regulated_produce_types);
                $('#total-loan-applications').text(data.total_loan_applications);
                $('#approved-loan-applications').text(data.approved_loan_applications);
                $('#total-transactions-value').text('₦' + parseFloat(data.total_transactions_value).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            function renderCharts(data) {
                // Farmer Approval Status Chart (Pie Chart)
                const farmerApprovalCtx = document.getElementById('farmerApprovalChart').getContext('2d');
                new Chart(farmerApprovalCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Approved Farmers', 'Pending Farmers', 'Rejected Farmers'],
                        datasets: [{
                            data: [data.approved_farmers, data.pending_farmers, data.rejected_farmers],
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + ' farmers';
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });

                // Regulated Produce Price Categories Chart (Bar Chart)
                // This chart will show the count of produce names within certain price ranges or just by name
                // For simplicity, let's just show the top N regulated produce types by count
                const producePriceCtx = document.getElementById('producePriceChart').getContext('2d');
                new Chart(producePriceCtx, {
                    type: 'bar',
                    data: {
                        labels: data.produce_price_chart_labels, // e.g., ['Maize', 'Cassava', 'Yam']
                        datasets: [{
                            label: 'Number of Regulated Prices',
                            data: data.produce_price_chart_data, // e.g., [1, 1, 1]
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)',
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Top Regulated Produce Types'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Produce Type'
                                }
                            }
                        }
                    }
                });
            }

            fetchDashboardData();
        });
    </script>
</body>
</html>
