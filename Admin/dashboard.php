<?php
// Include session timeout check
require_once 'session_check.php';

// dashboard.php
$active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'header.php'; ?>
<body>
    <!-- Header -->
    <header class="header">
        <button class="toggle-btn" id="headerToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="user-info">
            <div class="user-avatar">AD</div>
            <span>Admin User</span>
        </div>
    </header>

    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <h2 class="mb-4">Dashboard Overview</h2>
        
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-user-tie"></i>
                    <h3 id="approvedFarmers">0</h3>
                    <p>Approved Farmers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-users"></i>
                    <h3 id="registeredBuyers">0</h3>
                    <p>Registered Buyers</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3 id="completedOrders">0</h3>
                    <p>Completed Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-chart-line"></i>
                    <h3 id="totalRevenue">₦0</h3>
                    <p>Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Most Patronized Farmers</h4>
                        <select class="form-select form-select-sm" id="farmersChartFilter" style="width: auto;">
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>
                    <canvas id="farmersChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div class="chart-header">
                        <h4>Produce Demand Ranking</h4>
                        <select class="form-select form-select-sm" id="produceChartFilter" style="width: auto;">
                            <option value="month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="quarter">This Quarter</option>
                        </select>
                    </div>
                    <canvas id="produceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="data-table-container">
                    <div class="table-header">
                        <h4>Frequently Purchased Produce</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="frequentlyPurchasedTable">
                            <thead>
                                <tr>
                                    <th>Produce</th>
                                    <th>Orders</th>
                                    <th>Avg. Price</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="data-table-container">
                    <div class="table-header">
                        <h4>Most Purchased Items</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="mostPurchasedItemsTable">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Farmer</th>
                                    <th>Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
    <?php include 'assets/scripts.php'; ?>
    <script>
        // Initialize Charts
        const farmersCtx = document.getElementById('farmersChart').getContext('2d');
        const farmersChart = new Chart(farmersCtx, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Number of Orders', data: [], backgroundColor: 'rgba(76, 175, 80, 0.7)', borderColor: 'rgba(76, 175, 80, 1)', borderWidth: 1 }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        const produceCtx = document.getElementById('produceChart').getContext('2d');
        const produceChart = new Chart(produceCtx, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Fetch Summary Cards
        function fetchSummaryCards() {
            fetch('api/api.php?action=get_summary_cards')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    document.getElementById('approvedFarmers').textContent = data.approved_farmers;
                    document.getElementById('registeredBuyers').textContent = data.registered_buyers;
                    document.getElementById('completedOrders').textContent = data.completed_orders;
                    document.getElementById('totalRevenue').textContent = `₦${data.total_revenue}`;
                })
                .catch(error => console.error('Error fetching summary cards:', error));
        }

        // Fetch Farmers Chart
        function fetchFarmersChart() {
            const days = document.getElementById('farmersChartFilter').value;
            fetch(`api/api.php?action=get_farmers_chart&days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    farmersChart.data.labels = data.labels;
                    farmersChart.data.datasets[0].data = data.data;
                    farmersChart.update();
                })
                .catch(error => console.error('Error fetching farmers chart:', error));
        }

        // Fetch Produce Chart
        function fetchProduceChart() {
            const period = document.getElementById('produceChartFilter').value;
            fetch(`api/api.php?action=get_produce_chart&period=${period}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    produceChart.data.labels = data.labels;
                    produceChart.data.datasets = data.datasets;
                    produceChart.update();
                })
                .catch(error => console.error('Error fetching produce chart:', error));
        }

        // Event Listeners for Filters
        document.getElementById('farmersChartFilter').addEventListener('change', fetchFarmersChart);
        document.getElementById('produceChartFilter').addEventListener('change', fetchProduceChart);

        // Fetch Frequently Purchased Produce Table
        function fetchFrequentlyPurchasedProduce() {
            fetch('api/api.php?action=get_frequently_purchased_produce')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    const tbody = document.querySelector('#frequentlyPurchasedTable tbody');
                    tbody.innerHTML = '';
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.produce}</td>
                            <td>${row.order_count}</td>
                            <td>₦${parseFloat(row.avg_price).toLocaleString()}</td>
                            <td><i class="fas fa-arrow-${row.trend === 'up' ? 'up text-success' : row.trend === 'down' ? 'down text-danger' : 'minus text-secondary'}"></i></td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Error fetching frequently purchased produce:', error));
        }

        // Fetch Most Purchased Items Table
        function fetchMostPurchasedItems() {
            fetch('api/api.php?action=get_most_purchased_items')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    const tbody = document.querySelector('#mostPurchasedItemsTable tbody');
                    tbody.innerHTML = '';
                    data.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${row.produce}</td>
                            <td>${row.first_name} ${row.last_name}</td>
                            <td>${row.total_quantity}kg</td>
                            <td>₦${parseFloat(row.total_revenue).toLocaleString()}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Error fetching most purchased items:', error));
        }

        // Initial Data Fetch
        document.addEventListener('DOMContentLoaded', () => {
            fetchSummaryCards();
            fetchFarmersChart();
            fetchProduceChart();
            fetchFrequentlyPurchasedProduce();
            fetchMostPurchasedItems();
        });
    </script>
</body>
</html>