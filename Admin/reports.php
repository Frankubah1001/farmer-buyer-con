<?php
// reports.php - Fully developed Reports module
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
                    <button class="btn btn-agri-blue">
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
                        <!-- Sample data - In real app, load from DB -->
                        <tr data-reportid="REP001" data-status="pending" data-type="farmer" data-reported="John Adewale" data-reporter="Aisha Bello" data-date="2023-10-01">
                            <td>#REP001</td>
                            <td>John Adewale</td>
                            <td><span class="badge badge-warning">Farmer</span></td>
                            <td>Aisha Bello</td>
                            <td>Fraudulent Produce</td>
                            <td>01 Oct 2023</td>
                            <td><span class="badge badge-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-report-btn" data-reportid="REP001"><i class="fas fa-eye"></i> View Details</button>
                                </div>
                            </td>
                        </tr>
                        <tr data-reportid="REP002" data-status="pending" data-type="buyer" data-reported="Oluwaseun Adebayo" data-reporter="Musa Ibrahim" data-date="2023-10-05">
                            <td>#REP002</td>
                            <td>Oluwaseun Adebayo</td>
                            <td><span class="badge badge-info">Buyer</span></td>
                            <td>Musa Ibrahim</td>
                            <td>Non-Payment</td>
                            <td>05 Oct 2023</td>
                            <td><span class="badge badge-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-report-btn" data-reportid="REP002"><i class="fas fa-eye"></i> View Details</button>
                                </div>
                            </td>
                        </tr>
                        <tr data-reportid="REP003" data-status="resolved" data-type="farmer" data-reported="John Adewale" data-reporter="Chinedu Okonkwo" data-date="2023-10-10">
                            <td>#REP003</td>
                            <td>John Adewale</td>
                            <td><span class="badge badge-warning">Farmer</span></td>
                            <td>Chinedu Okonkwo</td>
                            <td>Poor Quality Delivery</td>
                            <td>10 Oct 2023</td>
                            <td><span class="badge badge-approved">Resolved (Warned)</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-report-btn" data-reportid="REP003"><i class="fas fa-eye"></i> View Details</button>
                                </div>
                            </td>
                        </tr>
                        <tr data-reportid="REP004" data-status="resolved" data-type="buyer" data-reported="Fatima Yusuf" data-reporter="Musa Ibrahim" data-date="2023-10-15">
                            <td>#REP004</td>
                            <td>Fatima Yusuf</td>
                            <td><span class="badge badge-info">Buyer</span></td>
                            <td>Musa Ibrahim</td>
                            <td>Harassment</td>
                            <td>15 Oct 2023</td>
                            <td><span class="badge badge-disabled">Resolved (Disabled)</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-report-btn" data-reportid="REP004"><i class="fas fa-eye"></i> View Details</button>
                                </div>
                            </td>
                        </tr>
                        <!-- More rows can be added dynamically -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Reports pagination">
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
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
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Reports module script starting');

            // Common scripts (sidebar toggle, logout, etc.)
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

            // Reports module-specific variables
            let currentReportId = '';
            let currentReportedUser = '';
            let currentUserType = ''; // 'farmer' or 'buyer'
            let currentReportStatus = '';

            // Event delegation for View buttons
            document.addEventListener('click', function(e) {
                // Skip if it's a link (e.g., sidebar navigation) or non-action button
                if (e.target.closest('a[href]') && !e.target.closest('.action-buttons')) {
                    return; // Allow normal navigation
                }

                const target = e.target.closest('.view-report-btn');

                if (!target) return;

                e.preventDefault();

                currentReportId = target.getAttribute('data-reportid');

                console.log('View button clicked:', { reportId: currentReportId });

                try {
                    // Always allow view
                    viewReportDetails(currentReportId);
                } catch (error) {
                    console.error('Error handling view click:', error);
                    alert('Error: ' + error.message);
                }
            });

            // View report details with admin actions
            function viewReportDetails(reportId) {
                // In real app, fetch from server
                // Demo data (expanded)
                const demoReports = {
                    'REP001': {
                        reportId: 'REP001',
                        reported: {
                            type: 'farmer',
                            name: 'John Adewale',
                            email: 'john.adewale@farm.com',
                            phone: '08012345678',
                            address: '45 Farm Road, Ibadan, Oyo State',
                            status: 'Active'
                        },
                        reporter: {
                            name: 'Aisha Bello',
                            email: 'aisha.bello@market.com',
                            phone: '08098765432'
                        },
                        reason: 'Fraudulent Produce',
                        description: 'The farmer supplied rotten tomatoes. Photos attached as evidence.',
                        evidence: 'photos/tomatoes_rot.jpg (2 images)',
                        date: '01 Oct 2023',
                        status: 'pending'
                    },
                    'REP002': {
                        reportId: 'REP002',
                        reported: {
                            type: 'buyer',
                            name: 'Oluwaseun Adebayo',
                            email: 'seun.adebayo@retail.ng',
                            phone: '08045678901',
                            address: '78 Shop Lane, Garki, Abuja',
                            status: 'Active'
                        },
                        reporter: {
                            name: 'Musa Ibrahim',
                            email: 'musa.ibrahim@agro.com',
                            phone: '08056789012'
                        },
                        reason: 'Non-Payment',
                        description: 'Buyer received rice but refused to pay full amount, claiming quality issues.',
                        evidence: 'chat_logs/payment_dispute.pdf',
                        date: '05 Oct 2023',
                        status: 'pending'
                    },
                    'REP003': {
                        reportId: 'REP003',
                        reported: {
                            type: 'farmer',
                            name: 'John Adewale',
                            email: 'john.adewale@farm.com',
                            phone: '08012345678',
                            address: '45 Farm Road, Ibadan, Oyo State',
                            status: 'Warned'
                        },
                        reporter: {
                            name: 'Chinedu Okonkwo',
                            email: 'chinedu.okonkwo@export.ng',
                            phone: '08067890123'
                        },
                        reason: 'Poor Quality Delivery',
                        description: 'Maize delivered was infested with pests. Admin warned farmer.',
                        evidence: 'photos/maize_infested.jpg',
                        date: '10 Oct 2023',
                        status: 'resolved'
                    },
                    'REP004': {
                        reportId: 'REP004',
                        reported: {
                            type: 'buyer',
                            name: 'Fatima Yusuf',
                            email: 'fatima.yusuf@process.com',
                            phone: '08078901234',
                            address: '34 Process Ave, Kaduna State',
                            status: 'Disabled'
                        },
                        reporter: {
                            name: 'Musa Ibrahim',
                            email: 'musa.ibrahim@agro.com',
                            phone: '08056789012'
                        },
                        reason: 'Harassment',
                        description: 'Buyer sent abusive messages to farmer. Account disabled.',
                        evidence: 'chat_logs/harassment_screenshots.png',
                        date: '15 Oct 2023',
                        status: 'resolved'
                    }
                };

                const report = demoReports[reportId] || { message: 'No details found' };
                currentReportedUser = report.reported.name;
                currentUserType = report.reported.type;
                currentReportStatus = report.status;

                // Structured content
                document.getElementById('reportDetailsContent').innerHTML = `
                    <div class="row g-3">
                        <!-- Report Summary -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Report Summary</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3"><strong>Report ID:</strong> #${report.reportId}</div>
                                        <div class="col-md-3"><strong>Type:</strong> <span class="badge ${report.reported.type === 'farmer' ? 'badge-warning' : 'badge-info'}">${report.reported.type}</span></div>
                                        <div class="col-md-3"><strong>Status:</strong> <span class="badge ${report.status === 'pending' ? 'badge-pending' : 'badge-approved'}">${report.status}</span></div>
                                        <div class="col-md-3"><strong>Date:</strong> <i class="fas fa-calendar-alt me-1"></i>${report.date}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reported User Info -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header ${report.reported.type === 'farmer' ? 'bg-warning text-dark' : 'bg-info text-white'}">
                                    <h6 class="mb-0"><i class="fas fa-user-${report.reported.type === 'farmer' ? 'farmer' : 'circle'} me-2"></i>${report.reported.type} Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><i class="fas fa-user text-primary me-2"></i><strong>${report.reported.name}</strong></p>
                                    <p><i class="fas fa-envelope text-muted me-2"></i>${report.reported.email}</p>
                                    <p><i class="fas fa-phone text-muted me-2"></i>${report.reported.phone}</p>
                                    <p><i class="fas fa-map-marker-alt text-muted me-2"></i>${report.reported.address}</p>
                                    <p><i class="fas fa-shield-alt text-${report.reported.status === 'Disabled' ? 'danger' : 'success'} me-2"></i><strong>Account Status:</strong> ${report.reported.status}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reporter Info -->
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0"><i class="fas fa-user-check me-2"></i>Reporter Information</h6>
                                </div>
                                <div class="card-body">
                                    <p><i class="fas fa-user text-primary me-2"></i><strong>${report.reporter.name}</strong></p>
                                    <p><i class="fas fa-envelope text-muted me-2"></i>${report.reporter.email}</p>
                                    <p><i class="fas fa-phone text-muted me-2"></i>${report.reporter.phone}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Report Details -->
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Report Details</h6>
                                </div>
                                <div class="card-body">
                                    <p><i class="fas fa-flag text-danger me-2"></i><strong>Reason:</strong> ${report.reason}</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Description:</label>
                                        <p class="border p-3 bg-light rounded">${report.description}</p>
                                    </div>
                                    <p><i class="fas fa-paperclip text-info me-2"></i><strong>Evidence:</strong> ${report.evidence}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Dynamic actions based on status
                const footer = document.getElementById('reportActionsFooter');
                if (report.status === 'pending') {
                    footer.innerHTML = `
                        <button type="button" class="btn btn-warning me-2" onclick="openWarningModal('${report.reported.name}', '${report.reported.email}')">
                            <i class="fas fa-exclamation-circle me-1"></i>Send Warning
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDisableModal('${report.reported.name}', '${report.reported.type}', '${report.reported.email}')">
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
            }

            // Open Warning Modal
            window.openWarningModal = function(userName, userEmail) {
                document.getElementById('warningRecipient').textContent = `${userName} (${userEmail})`;
                document.getElementById('warningMessage').value = `Warning: A report has been filed against your account for fraudulent produce. Please review our terms. Further violations may result in account suspension.`;
                new bootstrap.Modal(document.getElementById('sendWarningModal')).show();
            };

            // Confirm Send Warning
            document.getElementById('sendWarningConfirm').addEventListener('click', function() {
                const message = document.getElementById('warningMessage').value.trim();
                if (!message) {
                    alert('Message is required.');
                    return;
                }

                // In real app, send email/SMS via server
                // Demo: Update report status
                updateReportStatus(currentReportId, 'resolved', 'Warned', message);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('sendWarningModal'));
                modal.hide();
                document.getElementById('warningMessage').value = '';
                alert(`Warning sent to ${currentReportedUser}. Report marked as Resolved (Warned).`);
            });

            // Open Disable Modal
            window.openDisableModal = function(userName, userType, userEmail) {
                document.getElementById('disableConfirmText').textContent = `Disable account for ${userName} (${userType})?`;
                document.getElementById('disableMessage').value = `Your account has been disabled due to multiple reports. Contact support for appeal.`;
                new bootstrap.Modal(document.getElementById('disableUserModal')).show();
            };

            // Confirm Disable
            document.getElementById('disableUserConfirm').addEventListener('click', function() {
                const reason = document.getElementById('disableReason').value;
                const message = document.getElementById('disableMessage').value.trim();
                if (!message) {
                    alert('Notification message is required.');
                    return;
                }

                // In real app, disable user & send notification
                // Demo: Update report status
                updateReportStatus(currentReportId, 'resolved', 'Disabled', `${reason}: ${message}`);

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('disableUserModal'));
                modal.hide();
                document.getElementById('disableReason').value = 'other';
                document.getElementById('disableMessage').value = '';
                alert(`Account for ${currentReportedUser} disabled. Notification sent. Report marked as Resolved (Disabled).`);
            });

            // Update report status (demo function)
            function updateReportStatus(reportId, status, action, notes) {
                const rows = document.querySelectorAll('#reportsTableBody tr[data-reportid]');
                rows.forEach(row => {
                    if (row.getAttribute('data-reportid') === reportId) {
                        row.cells[6].innerHTML = `<span class="badge badge-approved">Resolved (${action})</span>`;
                        row.setAttribute('data-status', status);
                        // In real app, update DB
                    }
                });
            }

            // Apply filters
            window.applyFilters = function() {
                const search = document.getElementById('searchReports').value.toLowerCase();
                const statusFilter = document.getElementById('filterStatus').value;
                const typeFilter = document.getElementById('filterType').value;
                const rows = document.querySelectorAll('#reportsTableBody tr[data-reportid]');
                rows.forEach(row => {
                    const reportId = row.getAttribute('data-reportid').toLowerCase();
                    const reported = row.cells[1].textContent.toLowerCase();
                    const type = row.getAttribute('data-type');
                    const status = row.getAttribute('data-status');

                    let show = true;

                    if (search && !reportId.includes(search) && !reported.includes(search)) {
                        show = false;
                    }
                    if (statusFilter && status !== statusFilter) {
                        show = false;
                    }
                    if (typeFilter && type !== typeFilter) {
                        show = false;
                    }

                    row.style.display = show ? '' : 'none';
                });
            };

            // Real-time search
            document.getElementById('searchReports').addEventListener('keyup', applyFilters);

            console.log('Reports module script fully initialized');
        });
    </script>
</body>
</html>