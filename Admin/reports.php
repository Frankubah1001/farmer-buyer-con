<?php
// reports.php - Reports Management Module
$active = 'reports';
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
        <h2 class="mb-4">Reports Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Reports</h4>
                <div class="table-actions">
                    <button class="btn btn-agri-blue" onclick="exportReports()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by Report ID or User..." id="searchReports">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterType">
                        <option value="">All Types</option>
                        <option value="farmer">Farmer Report</option>
                        <option value="buyer">Buyer Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-agri" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="reportsTable">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Reported User</th>
                            <th>Type</th>
                            <th>Reporter</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTableBody">
                        <!-- Data will be loaded dynamically via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Reports pagination">
                <ul class="pagination justify-content-end" id="paginationControls">
                    <!-- Pagination controls will be generated dynamically -->
                </ul>
            </nav>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- View Report Details Modal -->
    <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="viewReportModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="reportDetailsContent">
                        <!-- Dynamically populated -->
                    </div>
                </div>
                <div class="modal-footer" id="reportActionsFooter">
                    <!-- Actions buttons will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Send Warning Modal -->
    <div class="modal fade" id="sendWarningModal" tabindex="-1" aria-labelledby="sendWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="sendWarningModalLabel"><i class="fas fa-exclamation-circle me-2"></i>Send Warning Message</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>To:</strong> <span id="warningRecipient"></span></p>
                    <div class="mb-3">
                        <label for="warningMessage" class="form-label">Warning Message</label>
                        <textarea class="form-control" id="warningMessage" rows="5" placeholder="Customize message or use template..."></textarea>
                    </div>
                    <div class="alert alert-secondary" role="alert">
                        <small><strong>Template:</strong> "Warning: A report has been filed against your account for [reason]. Please review our terms. Further violations may result in account suspension."</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="sendWarningConfirm">Send Warning</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable User Modal -->
    <div class="modal fade" id="disableUserModal" tabindex="-1" aria-labelledby="disableUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="disableUserModalLabel"><i class="fas fa-user-slash me-2"></i>Disable User Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="disableConfirmText">Are you sure you want to disable this account?</p>
                    <div class="mb-3">
                        <label for="disableReason" class="form-label">Reason</label>
                        <select class="form-select" id="disableReason">
                            <option value="multiple_reports">Multiple Reports</option>
                            <option value="policy_violation">Policy Violation</option>
                            <option value="fraud">Suspected Fraud</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="disableMessage" class="form-label">Notification Message</label>
                        <textarea class="form-control" id="disableMessage" rows="3" placeholder="Message to user..."></textarea>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <small><strong>Template:</strong> "Your account has been disabled due to [reason]. Contact support for appeal."</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="disableUserConfirm">Disable & Notify</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Reports module script starting');

            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                });
            }

            const headerToggle = document.getElementById('headerToggle');
            if (headerToggle) {
                headerToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                });
            }

            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to sign out?')) {
                        alert('You have been signed out successfully.');
                    }
                });
            }

            let currentReportId = '';
            let currentReportedUserId = '';
            let currentReportedUserType = '';
            let currentReportedUserEmail = '';
            let currentPage = 1;
            const itemsPerPage = 10;

            loadReports();

            function loadReports(page = 1) {
                currentPage = page;
                const search = document.getElementById('searchReports').value;
                const status = document.getElementById('filterStatus').value;
                const type = document.getElementById('filterType').value;

                const query = new URLSearchParams({
                    page,
                    limit: itemsPerPage,
                    search,
                    status,
                    type
                }).toString();

                fetch(`api/reports_api.php?${query}`)
                    .then(response => response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    }))
                    .then(data => {
                        if (data.success) {
                            const tbody = document.getElementById('reportsTableBody');
                            tbody.innerHTML = '';
                            
                            data.data.reports.forEach(report => {
                                const row = document.createElement('tr');
                                row.setAttribute('data-reportid', report.report_id);
                                row.setAttribute('data-status', report.status);
                                row.setAttribute('data-type', report.reported_user_type);
                                row.innerHTML = `
                                    <td>#${report.report_id}</td>
                                    <td>${report.reported_user_name}</td>
                                    <td><span class="badge ${report.reported_user_type === 'Farmer' ? 'badge-warning' : 'badge-blue'}">${report.reported_user_type}</span></td>
                                    <td>${report.reporter_name}</td>
                                    <td>${report.reason}</td>
                                    <td>${new Date(report.created_at).toLocaleDateString('en-GB')}</td>
                                    <td><span class="badge ${report.status === 'pending' ? 'badge-pending' : 'badge-approved'}">${report.status}</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit view-report-btn" data-report-id="${report.report_id}">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });

                            generatePaginationControls(data.data.pagination);

                            document.querySelectorAll('.view-report-btn').forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const reportId = this.getAttribute('data-report-id');
                                    viewReportDetails(reportId);
                                });
                            });

                        } else {
                            alert(data.message || 'Error loading reports');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading reports:', error);
                        alert('Error loading reports: ' + error.message);
                    });
            }

            function generatePaginationControls(pagination) {
                const paginationControls = document.getElementById('paginationControls');
                paginationControls.innerHTML = '';

                const { current_page, total_pages } = pagination;

                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page - 1}">Previous</a>`;
                paginationControls.appendChild(prevLi);

                const maxPagesToShow = 5;
                let startPage = Math.max(1, current_page - Math.floor(maxPagesToShow / 2));
                let endPage = Math.min(total_pages, startPage + maxPagesToShow - 1);

                if (endPage - startPage + 1 < maxPagesToShow) {
                    startPage = Math.max(1, endPage - maxPagesToShow + 1);
                }

                if (startPage > 1) {
                    const firstPageLi = document.createElement('li');
                    firstPageLi.className = 'page-item';
                    firstPageLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
                    paginationControls.appendChild(firstPageLi);
                    
                    if (startPage > 2) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dotsLi);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === current_page ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    paginationControls.appendChild(pageLi);
                }

                if (endPage < total_pages) {
                    if (endPage < total_pages - 1) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dotsLi);
                    }
                    
                    const lastPageLi = document.createElement('li');
                    lastPageLi.className = 'page-item';
                    lastPageLi.innerHTML = `<a class="page-link" href="#" data-page="${total_pages}">${total_pages}</a>`;
                    paginationControls.appendChild(lastPageLi);
                }

                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${current_page === total_pages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page + 1}">Next</a>`;
                paginationControls.appendChild(nextLi);
            }

            document.getElementById('paginationControls').addEventListener('click', function(e) {
                e.preventDefault();
                const target = e.target.closest('.page-link');
                if (target && !target.parentElement.classList.contains('disabled')) {
                    const page = parseInt(target.getAttribute('data-page'));
                    if (page) {
                        loadReports(page);
                    }
                }
            });

            function viewReportDetails(reportId) {
                fetch(`api/reports_api.php?action=get_report&report_id=${reportId}`)
                    .then(response => response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    }))
                    .then(data => {
                        if (data.success) {
                            const report = data.data;
                            currentReportId = report.report_id;
                            currentReportedUserId = report.reported_cbn_user_id;
                            currentReportedUserType = report.reported_user_type;
                            currentReportedUserEmail = report.reported_user_email;

                            document.getElementById('reportDetailsContent').innerHTML = `
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Report Summary</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3"><strong>Report ID:</strong> #${report.report_id}</div>
                                                    <div class="col-md-3"><strong>Type:</strong> <span class="badge ${report.reported_user_type === 'farmer' ? 'badge-warning' : 'badge-info'}">${report.reported_user_type}</span></div>
                                                    <div class="col-md-3"><strong>Status:</strong> <span class="badge ${report.status === 'pending' ? 'badge-pending' : 'badge-approved'}">${report.status}</span></div>
                                                    <div class="col-md-3"><strong>Date:</strong> <i class="fas fa-calendar-alt me-1"></i>${new Date(report.created_at).toLocaleDateString('en-GB')}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header ${report.reported_user_type === 'farmer' ? 'bg-warning text-dark' : 'bg-info text-white'}">
                                                <h6 class="mb-0"><i class="fas fa-user-${report.reported_user_type === 'farmer' ? 'farmer' : 'circle'} me-2"></i>${report.reported_user_type} Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-user text-primary me-2"></i><strong>${report.reported_user_name}</strong></p>
                                                <p><i class="fas fa-envelope text-muted me-2"></i>${report.reported_user_email}</p>
                                                <p><i class="fas fa-phone text-muted me-2"></i>${report.reported_user_phone}</p>
                                                <p><i class="fas fa-map-marker-alt text-muted me-2"></i>${report.reported_user_address || 'N/A'}</p>
                                                <p><i class="fas fa-shield-alt text-${report.reported_user_status === 'Disabled' ? 'danger' : 'success'} me-2"></i><strong>Account Status:</strong> ${report.reported_user_status}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Reporter Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-user text-primary me-2"></i><strong>${report.reporter_name}</strong></p>
                                                <p><i class="fas fa-envelope text-muted me-2"></i>${report.reporter_email}</p>
                                                <p><i class="fas fa-phone text-muted me-2"></i>${report.reporter_phone}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Report Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-flag text-danger me-2"></i><strong>Reason:</strong> ${report.reason}</p>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Description:</label>
                                                    <p class="border p-3 bg-light rounded">${report.description || 'No description provided'}</p>
                                                </div>
                                                <p><i class="fas fa-paperclip text-info me-2"></i><strong>Evidence:</strong> ${report.evidence || 'No evidence provided'}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

                            const footer = document.getElementById('reportActionsFooter');
                            if (report.status === 'pending') {
                                footer.innerHTML = `
                                    <button type="button" class="btn btn-warning me-2" onclick="openWarningModal('${report.reported_user_name}', '${report.reported_user_email}')">
                                        <i class="fas fa-exclamation-circle me-1"></i>Send Warning
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="openDisableModal('${report.reported_user_name}', '${report.reported_user_type}', '${report.reported_user_email}')">
                                        <i class="fas fa-user-slash me-1"></i>Disable User
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Close</button>
                                `;
                            } else {
                                footer.innerHTML = `
                                    <div class="text-muted small">Report is already resolved. <i class="fas fa-check-circle"></i></div>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                `;
                            }

                            new bootstrap.Modal(document.getElementById('viewReportModal')).show();
                        } else {
                            alert(data.message || 'Error loading report details');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching report details:', error);
                        alert('Error fetching report details: ' + error.message);
                    });
            }

            window.openWarningModal = function(userName, userEmail) {
                document.getElementById('warningRecipient').textContent = `${userName} (${userEmail})`;
                document.getElementById('warningMessage').value = `Warning: A report has been filed against your account. Please review our terms. Further violations may result in account suspension.`;
                new bootstrap.Modal(document.getElementById('sendWarningModal')).show();
            };

            document.getElementById('sendWarningConfirm').addEventListener('click', function() {
                const message = document.getElementById('warningMessage').value.trim();
                if (!message) {
                    alert('Message is required.');
                    return;
                }

                fetch('api/reports_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'resolve_report', 
                        report_id: parseInt(currentReportId), 
                        resolution_action: 'warned',
                        resolution_notes: message
                    })
                })
                    .then(response => response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    }))
                    .then(data => {
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('sendWarningModal')).hide();
                            bootstrap.Modal.getInstance(document.getElementById('viewReportModal')).hide();
                            document.getElementById('warningMessage').value = '';
                            loadReports(currentPage);
                            alert(`Warning sent to ${currentReportedUserEmail}. Report marked as Resolved (Warned).`);
                        } else {
                            alert(data.message || 'Error sending warning');
                        }
                    })
                    .catch(error => {
                        console.error('Error sending warning:', error);
                        alert('Error sending warning: ' + error.message);
                    });
            });

            window.openDisableModal = function(userName, userType, userEmail) {
                document.getElementById('disableConfirmText').textContent = `Disable account for ${userName} (${userType})?`;
                document.getElementById('disableMessage').value = `Your account has been disabled due to multiple reports. Contact support for appeal.`;
                new bootstrap.Modal(document.getElementById('disableUserModal')).show();
            };

            document.getElementById('disableUserConfirm').addEventListener('click', function() {
                const reason = document.getElementById('disableReason').value;
                const message = document.getElementById('disableMessage').value.trim();
                if (!message) {
                    alert('Notification message is required.');
                    return;
                }

                fetch('api/reports_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'resolve_report', 
                        report_id: parseInt(currentReportId), 
                        resolution_action: 'disabled',
                        resolution_notes: `${reason}: ${message}`,
                        reported_user_id: parseInt(currentReportedUserId),
                        reported_user_type: currentReportedUserType
                    })
                })
                    .then(response => response.text().then(text => {
                        console.log('Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    }))
                    .then(data => {
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('disableUserModal')).hide();
                            bootstrap.Modal.getInstance(document.getElementById('viewReportModal')).hide();
                            document.getElementById('disableReason').value = 'other';
                            document.getElementById('disableMessage').value = '';
                            loadReports(currentPage);
                            alert(`Account for ${currentReportedUserEmail} disabled. Report marked as Resolved (Disabled).`);
                        } else {
                            alert(data.message || 'Error disabling user');
                        }
                    })
                    .catch(error => {
                        console.error('Error disabling user:', error);
                        alert('Error disabling user: ' + error.message);
                    });
            });

            window.applyFilters = function() {
                loadReports(1);
            };

            document.getElementById('searchReports').addEventListener('keyup', function() {
                loadReports(1);
            });

            document.getElementById('filterStatus').addEventListener('change', function() {
                loadReports(1);
            });

            document.getElementById('filterType').addEventListener('change', function() {
                loadReports(1);
            });

            window.exportReports = function() {
                alert('Preparing reports export...');
                window.open('api/reports_api.php?action=export', '_blank');
            };

            console.log('Reports module script fully initialized');
        });
    </script>
</body>
</html>