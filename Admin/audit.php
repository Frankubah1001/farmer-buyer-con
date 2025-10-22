<?php
// audit.php - Audit Trail Module
$active = 'audit';
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

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="mb-4">Audit Trail</h2>
        
        <!-- Summary Charts -->
        <div class="row mb-4">
            <!-- Actions Per Module Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Actions Per Module</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="actionsPerModuleChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- User Activity Over Time Chart -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>User Activity Over Time</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Active Admins Chart -->
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Top Active Admins</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topAdminsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h4>Audit Logs</h4>
                <div class="table-actions">
                    <button class="btn btn-agri-blue" onclick="exportAuditToExcel()">
                        <i class="fas fa-download"></i> Export to Excel
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by User or Action..." id="searchAudit">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterModule">
                        <option value="">All Modules</option>
                        <option value="dashboard">Dashboard</option>
                        <option value="farmers">Farmers</option>
                        <option value="buyers">Buyers</option>
                        <option value="incentives">Incentives</option>
                        <!-- Add other modules -->
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterDate">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-agri" onclick="applyAuditFilters()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="auditTable">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>User</th>
                            <th>Module</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody id="auditTableBody">
                        <!-- Dynamically populated -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Audit pagination">
                <ul class="pagination justify-content-end" id="auditPagination">
                    <!-- Dynamically populated -->
                </ul>
            </nav>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentPage = 1;
            const itemsPerPage = 10;

            // Sample audit data (in real app, fetch from DB)
            let auditData = JSON.parse(localStorage.getItem('auditData')) || [
                { timestamp: '2023-10-07 14:30', user: 'Admin User', module: 'farmers', action: 'Added Farmer', details: 'Added John Doe' },
                { timestamp: '2023-10-06 11:15', user: 'Operations Manager', module: 'orders', action: 'Updated Order', details: 'Status to Shipped for ORD001' },
                { timestamp: '2023-10-05 10:45', user: 'Finance Admin', module: 'incentives', action: 'Approved Loan', details: 'Loan for Farmer B' },
                // Add more sample logs
            ];

            // Load Charts
            loadCharts();

            // Load Audit Table
            loadAuditTable(currentPage);

            function loadCharts() {
                // Actions Per Module (Bar Chart)
                const actionsPerModuleCtx = document.getElementById('actionsPerModuleChart').getContext('2d');
                new Chart(actionsPerModuleCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Farmers', 'Buyers', 'Orders', 'Incentives', 'Reports'],
                        datasets: [{
                            label: 'Actions',
                            data: [50, 30, 70, 40, 20],
                            backgroundColor: 'rgba(40, 167, 69, 0.6)'
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // User Activity Over Time (Line Chart)
                const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
                new Chart(userActivityCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Activity Level',
                            data: [65, 59, 80, 81, 56, 55],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            fill: false
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Top Active Admins (Pie Chart)
                const topAdminsCtx = document.getElementById('topAdminsChart').getContext('2d');
                new Chart(topAdminsCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Admin User', 'Operations Manager', 'Finance Admin', 'Support Viewer'],
                        datasets: [{
                            data: [300, 250, 200, 150],
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });
            }

            function loadAuditTable(page = 1) {
                const tableBody = document.getElementById('auditTableBody');
                tableBody.innerHTML = '';
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const paginatedLogs = auditData.slice(start, end);

                paginatedLogs.forEach(log => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${log.timestamp}</td>
                        <td>${log.user}</td>
                        <td>${log.module}</td>
                        <td>${log.action}</td>
                        <td>${log.details}</td>
                    `;
                    tableBody.appendChild(tr);
                });

                loadAuditPagination(auditData.length, page);
            }

            function loadAuditPagination(totalItems, currentPage) {
                const pagination = document.getElementById('auditPagination');
                pagination.innerHTML = '';
                const totalPages = Math.ceil(totalItems / itemsPerPage);

                const prevLi = document.createElement('li');
                prevLi.classList.add('page-item', currentPage === 1 ? 'disabled' : '');
                prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadAuditTable(${currentPage - 1})">Previous</a>`;
                pagination.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.classList.add('page-item', i === currentPage ? 'active' : '');
                    li.innerHTML = `<a class="page-link" href="#" onclick="loadAuditTable(${i})">${i}</a>`;
                    pagination.appendChild(li);
                }

                const nextLi = document.createElement('li');
                nextLi.classList.add('page-item', currentPage === totalPages ? 'disabled' : '');
                nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadAuditTable(${currentPage + 1})">Next</a>`;
                pagination.appendChild(nextLi);
            }

            function applyAuditFilters() {
                // Implement filtering logic (for demo, reload)
                loadAuditTable();
            }

            function exportAuditToExcel() {
                const wb = XLSX.utils.book_new();
                const wsData = [["Timestamp", "User", "Module", "Action", "Details"]];
                auditData.forEach(log => {
                    wsData.push([log.timestamp, log.user, log.module, log.action, log.details]);
                });
                const ws = XLSX.utils.aoa_to_sheet(wsData);
                XLSX.utils.book_append_sheet(wb, ws, "Audit Trail");
                XLSX.writeFile(wb, 'audit_trail.xlsx');
            }
        });
    </script>
</body>
</html>