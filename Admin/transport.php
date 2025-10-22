<?php
// transport.php - Fully developed Transport module with AJAX integration
$active = 'transport';
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
        <h2 class="mb-4">Transport Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Transport Providers</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addTransportModal">
                        <i class="fas fa-plus"></i> Add Provider
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportTransporters()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search by company name or contact..." id="searchTransport">
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-agri" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="transportTable">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Routes/Coverage</th>
                            <th>Fee Structure</th>
                            <th>Availability</th>
                            <th>Status</th>
                            <th>Added Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transportTableBody">
                        <!-- Data will be loaded dynamically via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Transport pagination">
                <ul class="pagination justify-content-end" id="paginationControls">
                    <!-- Pagination controls will be generated dynamically -->
                </ul>
            </nav>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Add Transport Provider Modal -->
    <div class="modal fade" id="addTransportModal" tabindex="-1" aria-labelledby="addTransportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransportModalLabel"><i class="fas fa-plus me-2"></i>Add New Transport Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTransportForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addCompanyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="addCompanyName" placeholder="e.g., ABC Transport" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addRoutes" class="form-label">Routes/Coverage Areas</label>
                                    <input type="text" class="form-control" id="addRoutes" placeholder="e.g., Lagos-Kano, Abuja-Enugu" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFees" class="form-label">Fee Structure</label>
                                    <input type="text" class="form-control" id="addFees" placeholder="e.g., ₦5,000/km or ₦10,000 per trip" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAvailability" class="form-label">Availability</label>
                                    <input type="text" class="form-control" id="addAvailability" placeholder="e.g., Daily 24/7 or Mon-Sat 8AM-6PM" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addContact" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="addContact" placeholder="e.g., info@company.com | 08012345678" required>
                        </div>
                        <div class="mb-3">
                            <label for="addNotes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="addNotes" rows="3" placeholder="e.g., Specializes in perishable goods"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="saveNewTransport">Save Provider</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Transport Provider Modal -->
    <div class="modal fade" id="editTransportModal" tabindex="-1" aria-labelledby="editTransportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTransportModalLabel"><i class="fas fa-edit me-2"></i>Edit Transport Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTransportForm">
                        <input type="hidden" id="editTransporterId">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCompanyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="editCompanyName" readonly required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editRoutes" class="form-label">Routes/Coverage Areas</label>
                                    <input type="text" class="form-control" id="editRoutes" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFees" class="form-label">Fee Structure</label>
                                    <input type="text" class="form-control" id="editFees" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAvailability" class="form-label">Availability</label>
                                    <input type="text" class="form-control" id="editAvailability" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editContact" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="editContact" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNotes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="editNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="updateTransport">Update Provider</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Transport Modal -->
    <div class="modal fade" id="disableTransportModal" tabindex="-1" aria-labelledby="disableTransportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableTransportModalLabel"><i class="fas fa-ban me-2"></i>Disable Transport Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="disableConfirmText">Are you sure you want to disable this provider?</p>
                    <div class="mb-3">
                        <label for="disableReason" class="form-label">Reason</label>
                        <textarea class="form-control" id="disableReason" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-disable" id="confirmDisableTransport">Disable</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Transport module script starting');

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
                        // Redirect to logout endpoint if needed
                    }
                });
            }

            // Transport module-specific variables
            let currentEditTransporterId = '';
            let currentPage = 1;
            const itemsPerPage = 10;

            // Load transporters on page load
            loadTransporters();

            // Load transporters from API with pagination and filters
            function loadTransporters(page = 1) {
                currentPage = page;
                const search = document.getElementById('searchTransport').value;
                const status = document.getElementById('filterStatus').value;

                const query = new URLSearchParams({
                    page,
                    limit: itemsPerPage,
                    search,
                    status
                }).toString();

                fetch(`api/transport_api.php?${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tbody = document.getElementById('transportTableBody');
                            tbody.innerHTML = '';
                            data.data.transporters.forEach(t => {
                                const row = document.createElement('tr');
                                row.setAttribute('data-transporter-id', t.transporter_id);
                                row.setAttribute('data-status', t.is_verified ? 'active' : 'disabled');
                                row.innerHTML = `
                                    <td>${t.company_name}</td>
                                    <td>${t.operating_areas || 'N/A'}</td>
                                    <td>₦TBD</td>
                                    <td>TBD</td>
                                    <td><span class="badge badge-blue">${t.is_verified ? 'Active' : 'Disabled'}</span></td>
                                    <td>${new Date(t.created_at).toLocaleDateString('en-GB')}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit edit-transport-btn" data-transporter-id="${t.transporter_id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn ${t.is_verified ? 'btn-disable disable-transport-btn' : 'btn-approve enable-transport-btn'}" data-transporter-id="${t.transporter_id}">
                                                <i class="fas fa-${t.is_verified ? 'ban' : 'check'}"></i>
                                            </button>
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });

                            generatePaginationControls(data.data.pagination);
                        } else {
                            alert(data.message || 'Error loading transporters');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading transporters:', error);
                        alert('Error loading transporters');
                    });
            }

            // Generate pagination controls
            function generatePaginationControls(pagination) {
                const paginationControls = document.getElementById('paginationControls');
                paginationControls.innerHTML = '';

                const { current_page, total_pages } = pagination;

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page - 1}">Previous</a>`;
                paginationControls.appendChild(prevLi);

                // Page numbers
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

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${current_page === total_pages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page + 1}">Next</a>`;
                paginationControls.appendChild(nextLi);
            }

            // Handle pagination clicks
            document.getElementById('paginationControls').addEventListener('click', function(e) {
                e.preventDefault();
                const target = e.target.closest('.page-link');
                if (target && !target.parentElement.classList.contains('disabled')) {
                    const page = parseInt(target.getAttribute('data-page'));
                    if (page) {
                        loadTransporters(page);
                    }
                }
            });

            // Event delegation for action buttons (edit/disable/enable)
            document.addEventListener('click', function(e) {
                if (e.target.closest('a[href]') && !e.target.closest('.action-buttons')) {
                    return; // Allow normal navigation
                }

                const target = e.target.closest('.edit-transport-btn, .disable-transport-btn, .enable-transport-btn');
                if (!target) return;

                e.preventDefault();
                const transporterId = target.getAttribute('data-transporter-id');

                if (target.classList.contains('edit-transport-btn')) {
                    // Fetch transporter details for edit
                    fetch(`api/transport_api.php?page=1&limit=1&search=${transporterId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.transporters.length > 0) {
                                const t = data.data.transporters[0];
                                currentEditTransporterId = t.transporter_id;
                                document.getElementById('editTransporterId').value = t.transporter_id;
                                document.getElementById('editCompanyName').value = t.company_name;
                                document.getElementById('editRoutes').value = t.operating_areas || '';
                                document.getElementById('editFees').value = '₦TBD'; // Placeholder
                                document.getElementById('editAvailability').value = 'TBD'; // Placeholder
                                document.getElementById('editContact').value = `${t.email} | ${t.phone}`;
                                document.getElementById('editNotes').value = t.notes || '';
                                new bootstrap.Modal(document.getElementById('editTransportModal')).show();
                            } else {
                                alert('Transporter details not found');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching transporter details:', error);
                            alert('Error fetching transporter details');
                        });
                } else if (target.classList.contains('disable-transport-btn')) {
                    document.getElementById('disableConfirmText').textContent = `Are you sure you want to disable this provider?`;
                    currentEditTransporterId = transporterId;
                    new bootstrap.Modal(document.getElementById('disableTransportModal')).show();
                } else if (target.classList.contains('enable-transport-btn')) {
                    fetch('api/transport_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'toggle_status', transporter_id: transporterId, is_verified: 1 })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadTransporters(currentPage);
                                alert('Transporter enabled successfully.');
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error enabling transporter:', error);
                            alert('Error enabling transporter');
                        });
                }
            });

            // Save new transport provider
            document.getElementById('saveNewTransport').addEventListener('click', function() {
                const companyName = document.getElementById('addCompanyName').value.trim();
                const routes = document.getElementById('addRoutes').value.trim();
                const fees = document.getElementById('addFees').value.trim();
                const availability = document.getElementById('addAvailability').value.trim();
                const contact = document.getElementById('addContact').value.trim();
                const notes = document.getElementById('addNotes').value.trim();

                if (!companyName || !routes || !fees || !availability || !contact) {
                    alert('All required fields are required.');
                    return;
                }

                fetch('api/transport_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add', company_name: companyName, routes, fees, availability, contact, notes })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadTransporters(currentPage);
                            bootstrap.Modal.getInstance(document.getElementById('addTransportModal')).hide();
                            document.getElementById('addTransportForm').reset();
                            alert('Transporter added successfully!');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error adding transporter:', error);
                        alert('Error adding transporter');
                    });
            });

            // Update transport provider
            document.getElementById('updateTransport').addEventListener('click', function() {
                const transporterId = document.getElementById('editTransporterId').value;
                const routes = document.getElementById('editRoutes').value.trim();
                const fees = document.getElementById('editFees').value.trim();
                const availability = document.getElementById('editAvailability').value.trim();
                const contact = document.getElementById('editContact').value.trim();
                const notes = document.getElementById('editNotes').value.trim();

                if (!transporterId || !routes || !fees || !availability || !contact) {
                    alert('All required fields are required.');
                    return;
                }

                fetch('api/transport_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update', transporter_id: transporterId, routes, fees, availability, contact, notes })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadTransporters(currentPage);
                            bootstrap.Modal.getInstance(document.getElementById('editTransportModal')).hide();
                            alert('Transporter updated successfully!');
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating transporter:', error);
                        alert('Error updating transporter');
                    });
            });

            // Confirm disable
            document.getElementById('confirmDisableTransport').addEventListener('click', function() {
                const transporterId = currentEditTransporterId;
                const reason = document.getElementById('disableReason').value.trim();

                fetch('api/transport_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'toggle_status', transporter_id: transporterId, is_verified: 0, reason })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadTransporters(currentPage);
                            bootstrap.Modal.getInstance(document.getElementById('disableTransportModal')).hide();
                            document.getElementById('disableReason').value = '';
                            alert(`Transporter disabled. Reason: ${reason || 'No notes provided'}.`);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error disabling transporter:', error);
                        alert('Error disabling transporter');
                    });
            });

            // Apply filters
            window.applyFilters = function() {
                loadTransporters(1); // Reset to page 1 when filtering
            };

            // Real-time search
            document.getElementById('searchTransport').addEventListener('keyup', function() {
                loadTransporters(1); // Reset to page 1 when searching
            });

            // Real-time filter status change
            document.getElementById('filterStatus').addEventListener('change', function() {
                loadTransporters(1); // Reset to page 1 when status changes
            });

            // Export transporters
            window.exportTransporters = function() {
                alert('Preparing transporters export...');
                window.open('api/transport_api.php?action=export', '_blank');
            };

            console.log('Transport module script fully initialized');
        });
    </script>
</body>
</html>