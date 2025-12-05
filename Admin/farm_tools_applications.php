<?php
// Include session timeout check
require_once 'session_check.php';

$active = 'farm_tools_applications';
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
        <h2 class="mb-4">Farm Tools Applications Management</h2>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3 id="totalApplications">0</h3>
                    <p>Total Applications</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-clock"></i>
                    <h3 id="pendingApplications">0</h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-check-circle"></i>
                    <h3 id="approvedApplications">0</h3>
                    <p>Approved</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <i class="fas fa-times-circle"></i>
                    <h3 id="rejectedApplications">0</h3>
                    <p>Rejected</p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="data-table-container">
            <div class="table-header">
                <h5><i class="fas fa-list"></i> Farm Tools Applications</h5>
                <div class="table-actions">
                    <select class="form-select form-select-sm" id="statusFilter" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..." style="width: 200px;">
                    <button class="btn btn-agri-blue btn-sm" onclick="refreshApplications()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Farmer</th>
                            <th>Provider</th>
                            <th>Tools Requested</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="applicationsTableBody">
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div id="paginationInfo">Showing 0 of 0 applications</div>
                <nav>
                    <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </main>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDetailsModalLabel">Farm Tools Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="applicationDetails">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="approveBtn" onclick="showApproveModal()">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button type="button" class="btn btn-danger" id="rejectBtn" onclick="showRejectModal()">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="approveModalLabel">Approve Farm Tools Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this farm tools application?</p>
                    <div class="mb-3">
                        <label for="approveNotes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="approveNotes" rows="3" placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="approveApplication()">
                        <i class="fas fa-check"></i> Confirm Approval
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Farm Tools Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this farm tools application:</p>
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Rejection Reason *</label>
                        <textarea class="form-control" id="rejectReason" rows="3" placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="rejectApplication()">
                        <i class="fas fa-times"></i> Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/scripts.js"></script>
    
    <script>
        let currentPage = 1;
        let currentApplicationId = null;
        let currentApplicationStatus = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadStatistics();
            loadApplications();

            // Event listeners
            document.getElementById('statusFilter').addEventListener('change', function() {
                currentPage = 1;
                loadApplications();
            });

            document.getElementById('searchInput').addEventListener('input', debounce(function() {
                currentPage = 1;
                loadApplications();
            }, 500));
        });

        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Load statistics
        function loadStatistics() {
            fetch('api/farm_tools_applications_api.php?action=get_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalApplications').textContent = data.data.total || 0;
                        document.getElementById('pendingApplications').textContent = data.data.pending || 0;
                        document.getElementById('approvedApplications').textContent = data.data.approved || 0;
                        document.getElementById('rejectedApplications').textContent = data.data.rejected || 0;
                    }
                })
                .catch(error => console.error('Error loading statistics:', error));
        }

        // Load applications
        function loadApplications() {
            const status = document.getElementById('statusFilter').value;
            const search = document.getElementById('searchInput').value;
            
            const params = new URLSearchParams({
                action: 'get_all',
                page: currentPage,
                limit: 10
            });

            if (status) params.append('status', status);
            if (search) params.append('search', search);

            fetch(`api/farm_tools_applications_api.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayApplications(data.data);
                        updatePagination(data.pagination);
                    } else {
                        showError('Failed to load applications');
                    }
                })
                .catch(error => {
                    console.error('Error loading applications:', error);
                    showError('Error loading applications');
                });
        }

        // Display applications in table
        function displayApplications(applications) {
            const tbody = document.getElementById('applicationsTableBody');
            
            if (applications.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No applications found</td></tr>';
                return;
            }

            tbody.innerHTML = applications.map(app => `
                <tr>
                    <td>${app.application_id}</td>
                    <td>
                        <strong>${app.farmer_name}</strong><br>
                        <small class="text-muted">${app.farmer_email}</small>
                    </td>
                    <td>${app.provider_name}</td>
                    <td>${app.tools_requested.substring(0, 50)}...</td>
                    <td>${app.quantity_needed || 'N/A'}</td>
                    <td><span class="badge ${getStatusBadgeClass(app.application_status)}">${app.application_status}</span></td>
                    <td>${formatDate(app.created_at)}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-agri-blue" onclick="viewDetails(${app.application_id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn btn-disable" onclick="deleteApplication(${app.application_id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Update pagination
        function updatePagination(pagination) {
            const paginationInfo = document.getElementById('paginationInfo');
            const paginationEl = document.getElementById('pagination');
            
            const start = (pagination.page - 1) * pagination.limit + 1;
            const end = Math.min(pagination.page * pagination.limit, pagination.total);
            
            paginationInfo.textContent = `Showing ${start}-${end} of ${pagination.total} applications`;
            
            // Build pagination buttons
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `
                <li class="page-item ${pagination.page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${pagination.page - 1}); return false;">Previous</a>
                </li>
            `;
            
            // Page numbers
            for (let i = 1; i <= pagination.pages; i++) {
                if (i === 1 || i === pagination.pages || (i >= pagination.page - 2 && i <= pagination.page + 2)) {
                    paginationHTML += `
                        <li class="page-item ${i === pagination.page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                        </li>
                    `;
                } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                    paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }
            
            // Next button
            paginationHTML += `
                <li class="page-item ${pagination.page === pagination.pages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${pagination.page + 1}); return false;">Next</a>
                </li>
            `;
            
            paginationEl.innerHTML = paginationHTML;
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            loadApplications();
        }

        // View application details
        function viewDetails(applicationId) {
            currentApplicationId = applicationId;
            
            const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            modal.show();
            
            document.getElementById('applicationDetails').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            fetch(`api/farm_tools_applications_api.php?action=get_details&id=${applicationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayApplicationDetails(data.data);
                        currentApplicationStatus = data.data.application_status;
                        
                        // Show/hide approve/reject buttons based on status
                        const approveBtn = document.getElementById('approveBtn');
                        const rejectBtn = document.getElementById('rejectBtn');
                        
                        if (data.data.application_status === 'Pending') {
                            approveBtn.style.display = 'inline-block';
                            rejectBtn.style.display = 'inline-block';
                        } else {
                            approveBtn.style.display = 'none';
                            rejectBtn.style.display = 'none';
                        }
                    } else {
                        document.getElementById('applicationDetails').innerHTML = `
                            <div class="alert alert-danger">${data.message}</div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading details:', error);
                    document.getElementById('applicationDetails').innerHTML = `
                        <div class="alert alert-danger">Error loading application details</div>
                    `;
                });
        }

        // Display application details
        function displayApplicationDetails(app) {
            const documents = app.document_paths ? JSON.parse(app.document_paths) : [];
            
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Farmer Information</h6>
                        <table class="table table-sm">
                            <tr><th>Name:</th><td>${app.farmer_name}</td></tr>
                            <tr><th>Email:</th><td>${app.farmer_email}</td></tr>
                            <tr><th>Phone:</th><td>${app.farmer_phone || 'N/A'}</td></tr>
                            <tr><th>Farm Name:</th><td>${app.farm_name || 'N/A'}</td></tr>
                            <tr><th>Farm Size:</th><td>${app.farm_size || 'N/A'}</td></tr>
                            <tr><th>Experience:</th><td>${app.farming_experience || 'N/A'} years</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-tools"></i> Tools Request Details</h6>
                        <table class="table table-sm">
                            <tr><th>Provider:</th><td>${app.provider_name}</td></tr>
                            <tr><th>Tools Requested:</th><td><strong>${app.tools_requested}</strong></td></tr>
                            <tr><th>Quantity:</th><td>${app.quantity_needed || 'N/A'}</td></tr>
                            <tr><th>Purpose:</th><td>${app.purpose}</td></tr>
                            <tr><th>Status:</th><td><span class="badge ${getStatusBadgeClass(app.application_status)}">${app.application_status}</span></td></tr>
                            <tr><th>Applied Date:</th><td>${formatDate(app.created_at)}</td></tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-tractor"></i> Farm Details</h6>
                        <table class="table table-sm">
                            <tr><th>Farm Size:</th><td>${app.farm_size || 'N/A'}</td></tr>
                            <tr><th>Current Tools:</th><td>${app.current_tools || 'None'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fas fa-paperclip"></i> Supporting Documents</h6>
                        ${documents.length > 0 ? `
                            <ul class="list-group">
                                ${documents.map((doc, index) => `
                                    <li class="list-group-item">
                                        <a href="../${doc}" target="_blank">
                                            <i class="fas fa-file"></i> Document ${index + 1}
                                        </a>
                                    </li>
                                `).join('')}
                            </ul>
                        ` : '<p class="text-muted">No documents uploaded</p>'}
                    </div>
                </div>
                
                ${app.admin_notes ? `
                    <hr>
                    <div>
                        <h6 class="text-primary mb-3"><i class="fas fa-sticky-note"></i> Admin Notes</h6>
                        <div class="alert alert-info">${app.admin_notes}</div>
                    </div>
                ` : ''}
            `;
            
            document.getElementById('applicationDetails').innerHTML = html;
        }

        // Show approve modal
        function showApproveModal() {
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewDetailsModal'));
            viewModal.hide();
            
            const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
            approveModal.show();
            
            document.getElementById('approveNotes').value = '';
        }

        // Show reject modal
        function showRejectModal() {
            const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewDetailsModal'));
            viewModal.hide();
            
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            rejectModal.show();
            
            document.getElementById('rejectReason').value = '';
        }

        // Approve application
        function approveApplication() {
            const notes = document.getElementById('approveNotes').value;
            
            fetch('api/farm_tools_applications_api.php?action=approve', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentApplicationId,
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('approveModal'));
                    modal.hide();
                    loadApplications();
                    loadStatistics();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error approving application:', error);
                showError('Error approving application');
            });
        }

        // Reject application
        function rejectApplication() {
            const reason = document.getElementById('rejectReason').value.trim();
            
            if (!reason) {
                showError('Please provide a rejection reason');
                return;
            }
            
            fetch('api/farm_tools_applications_api.php?action=reject', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: currentApplicationId,
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
                    modal.hide();
                    loadApplications();
                    loadStatistics();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error rejecting application:', error);
                showError('Error rejecting application');
            });
        }

        // Delete application
        function deleteApplication(applicationId) {
            if (!confirm('Are you sure you want to delete this application? This will hide it from the list but not remove it from the database.')) {
                return;
            }
            
            fetch('api/farm_tools_applications_api.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: applicationId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(data.message);
                    loadApplications();
                    loadStatistics();
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting application:', error);
                showError('Error deleting application');
            });
        }

        // Refresh applications
        function refreshApplications() {
            loadApplications();
            loadStatistics();
        }

        // Helper functions
        function getStatusBadgeClass(status) {
            switch (status) {
                case 'Pending': return 'badge-pending';
                case 'Approved': return 'badge-approved';
                case 'Rejected': return 'badge-disabled';
                default: return 'bg-secondary';
            }
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function showSuccess(message) {
            alert(message); // You can replace this with a better notification system
        }

        function showError(message) {
            alert('Error: ' + message); // You can replace this with a better notification system
        }
    </script>
</body>
</html>
