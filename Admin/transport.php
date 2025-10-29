  <?php
// transport.php 
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
                            <th>Amount(Per Trip)</th>
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
                                    <label for="addContactPerson" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" id="addContactPerson" placeholder="e.g., John Doe" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="addEmail" placeholder="e.g., info@company.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="addPhone" placeholder="e.g., 08012345678" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addVehicleType" class="form-label">Vehicle Type</label>
                                    <input type="text" class="form-control" id="addVehicleType" placeholder="e.g., Truck" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addVehicleCapacity" class="form-label">Vehicle Capacity</label>
                                    <input type="text" class="form-control" id="addVehicleCapacity" placeholder="e.g., 10 tons" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addLicenseNumber" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="addLicenseNumber" placeholder="e.g., TRANS12345">
                        </div>
                        <div class="mb-3">
                            <label for="addOperatingAreas" class="form-label">Operating Areas</label>
                            <input type="text" class="form-control" id="addOperatingAreas" placeholder="e.g., Lagos,Kano,Abuja" required>
                        </div>
                        <div class="mb-3">
                            <label for="addAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="addAddress" rows="3" placeholder="e.g., 12 Transport Avenue, Ikeja"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addStateId" class="form-label">State</label>
                                    <select class="form-select" id="addStateId">
                                        <option value="">Select State</option>
                                        <!-- Populated dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addCityId" class="form-label">City</label>
                                    <select class="form-select" id="addCityId" disabled>
                                        <option value="">Select City</option>
                                        <!-- Populated dynamically -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFees" class="form-label">Fee Structure</label>
                                    <input type="text" class="form-control" id="addFees" placeholder="e.g., ₦5,000/km" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAvailability" class="form-label">Availability</label>
                                    <input type="text" class="form-control" id="addAvailability" placeholder="e.g., Daily 24/7" required>
                                </div>
                            </div>
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
                                    <input type="text" class="form-control" id="editCompanyName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editContactPerson" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" id="editContactPerson" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="editPhone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editVehicleType" class="form-label">Vehicle Type</label>
                                    <input type="text" class="form-control" id="editVehicleType" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editVehicleCapacity" class="form-label">Vehicle Capacity</label>
                                    <input type="text" class="form-control" id="editVehicleCapacity" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editLicenseNumber" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="editLicenseNumber">
                        </div>
                        <div class="mb-3">
                            <label for="editOperatingAreas" class="form-label">Operating Areas</label>
                            <input type="text" class="form-control" id="editOperatingAreas" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAddress" class="form-label">Address</label>
                            <textarea class="form-control" id="editAddress" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editStateId" class="form-label">State</label>
                                    <select class="form-select" id="editStateId">
                                        <option value="">Select State</option>
                                        <!-- Populated dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCityId" class="form-label">City</label>
                                    <select class="form-select" id="editCityId" disabled>
                                        <option value="">Select City</option>
                                        <!-- Populated dynamically -->
                                    </select>
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
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - Transport module script starting');

        // Format number to Nigerian currency
        function formatNigerianCurrency(amount) {
            if (!amount) return '₦0';
            
            // Remove any existing formatting and extract numbers
            const num = String(amount).replace(/[^\d.]/g, '');
            if (!num) return '₦0';
            
            // Format with commas for thousands
            const formatted = '₦' + parseFloat(num).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            return formatted;
        }

        // Format fee structure text that might contain currency
        function formatFeeStructure(feeText) {
            if (!feeText) return '₦0';
            
            // Handle common fee structure formats
            return feeText.replace(/(\d+)/g, (match) => {
                return formatNigerianCurrency(match).replace('₦', '');
            }).replace(/^/, '₦');
        }

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

        let currentEditTransporterId = '';
        let currentPage = 1;
        const itemsPerPage = 10;

        function loadStates(selectElementId) {
            fetch('api/transport_api.php?action=get_states')
                .then(response => response.text().then(text => {
                    console.log('Raw states response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + text);
                    }
                }))
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById(selectElementId);
                        select.innerHTML = '<option value="">Select State</option>';
                        data.data.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.state_id;
                            option.textContent = `${state.state_name} (${state.state_id})`;
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Error loading states:', data.message);
                        alert(data.message || 'Error loading states');
                    }
                })
                .catch(error => {
                    console.error('Error fetching states:', error);
                    alert('Error fetching states: ' + error.message);
                });
        }

        function loadCities(stateId, selectElementId, selectedCityId = null) {
            const select = document.getElementById(selectElementId);
            select.innerHTML = '<option value="">Select City</option>';
            select.disabled = !stateId;

            if (stateId) {
                fetch(`api/transport_api.php?action=get_cities&state_id=${stateId}`)
                    .then(response => response.text().then(text => {
                        console.log('Raw cities response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    }))
                    .then(data => {
                        if (data.success) {
                            data.data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.city_id;
                                option.textContent = `${city.city_name} (${city.city_id})`;
                                if (selectedCityId && city.city_id == selectedCityId) {
                                    option.selected = true;
                                }
                                select.appendChild(option);
                            });
                        } else {
                            console.error('Error loading cities:', data.message);
                            alert(data.message || 'Error loading cities');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        alert('Error fetching cities: ' + error.message);
                    });
            }
        }

        loadStates('addStateId');
        loadStates('editStateId');

        document.getElementById('addStateId').addEventListener('change', function() {
            const stateId = this.value ? parseInt(this.value) : null;
            loadCities(stateId, 'addCityId');
        });

        document.getElementById('editStateId').addEventListener('change', function() {
            const stateId = this.value ? parseInt(this.value) : null;
            loadCities(stateId, 'editCityId');
        });

        loadTransporters();

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
                        const tbody = document.getElementById('transportTableBody');
                        tbody.innerHTML = '';
                        data.data.transporters.forEach(t => {
                            const row = document.createElement('tr');
                            row.setAttribute('data-transporter-id', t.transporter_id);
                            row.setAttribute('data-status', t.is_verified ? 'active' : 'disabled');
                            row.innerHTML = `
                                <td>${t.company_name}</td>
                                <td>${t.operating_areas || 'N/A'}</td>
                                <td>${formatFeeStructure(t.fees) || '₦TBD'}</td>
                                <td>${t.availability || 'TBD'}</td>
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

                        document.querySelectorAll('.edit-transport-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const transporterId = this.getAttribute('data-transporter-id');
                                currentEditTransporterId = transporterId;
                                fetch(`api/transport_api.php?action=get_transporter&transporter_id=${transporterId}`)
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
                                            const t = data.data;
                                            document.getElementById('editTransporterId').value = t.transporter_id;
                                            document.getElementById('editCompanyName').value = t.company_name || '';
                                            document.getElementById('editContactPerson').value = t.contact_person || '';
                                            document.getElementById('editEmail').value = t.email || '';
                                            document.getElementById('editPhone').value = t.phone || '';
                                            document.getElementById('editVehicleType').value = t.vehicle_type || '';
                                            document.getElementById('editVehicleCapacity').value = t.vehicle_capacity || '';
                                            document.getElementById('editLicenseNumber').value = t.license_number || '';
                                            document.getElementById('editOperatingAreas').value = t.operating_areas || '';
                                            document.getElementById('editAddress').value = t.address || '';
                                            document.getElementById('editFees').value = t.fees || '';
                                            document.getElementById('editAvailability').value = t.availability || '';
                                            document.getElementById('editNotes').value = t.notes || '';
                                            loadStates('editStateId');
                                            document.getElementById('editStateId').value = t.state_id || '';
                                            loadCities(t.state_id, 'editCityId', t.city_id);
                                            new bootstrap.Modal(document.getElementById('editTransportModal')).show();
                                        } else {
                                            alert(data.message || 'Transporter details not found');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching transporter details:', error);
                                        alert('Error fetching transporter details: ' + error.message);
                                    });
                            });
                        });

                        document.querySelectorAll('.disable-transport-btn, .enable-transport-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                currentEditTransporterId = this.getAttribute('data-transporter-id');
                                const isDisable = this.classList.contains('disable-transport-btn');
                                
                                if (isDisable) {
                                    document.getElementById('disableConfirmText').textContent = 'Are you sure you want to disable this provider?';
                                    document.getElementById('confirmDisableTransport').textContent = 'Disable';
                                    document.getElementById('disableReason').value = '';
                                    new bootstrap.Modal(document.getElementById('disableTransportModal')).show();
                                } else {
                                    if (confirm('Are you sure you want to activate this provider?')) {
                                        fetch('api/transport_api.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ 
                                                action: 'toggle_status', 
                                                transporter_id: parseInt(currentEditTransporterId), 
                                                is_verified: 1, 
                                                reason: '' 
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
                                                loadTransporters(currentPage);
                                                alert('Transporter activated successfully.');
                                            } else {
                                                alert(data.message || 'Error activating transporter');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error activating transporter:', error);
                                            alert('Error activating transporter: ' + error.message);
                                        });
                                    }
                                }
                            });
                        });
                    } else {
                        alert(data.message || 'Error loading transporters');
                    }
                })
                .catch(error => {
                    console.error('Error loading transporters:', error);
                    alert('Error loading transporters: ' + error.message);
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
                    loadTransporters(page);
                }
            }
        });

        document.getElementById('saveNewTransport').addEventListener('click', function() {
            const companyName = document.getElementById('addCompanyName').value.trim();
            const contactPerson = document.getElementById('addContactPerson').value.trim();
            const email = document.getElementById('addEmail').value.trim();
            const phone = document.getElementById('addPhone').value.trim();
            const vehicleType = document.getElementById('addVehicleType').value.trim();
            const vehicleCapacity = document.getElementById('addVehicleCapacity').value.trim();
            const licenseNumber = document.getElementById('addLicenseNumber').value.trim() || null;
            const operatingAreas = document.getElementById('addOperatingAreas').value.trim();
            const address = document.getElementById('addAddress').value.trim() || null;
            const stateId = document.getElementById('addStateId').value ? parseInt(document.getElementById('addStateId').value) : null;
            const cityId = document.getElementById('addCityId').value ? parseInt(document.getElementById('addCityId').value) : null;
            const fees = document.getElementById('addFees').value.trim();
            const availability = document.getElementById('addAvailability').value.trim();
            const notes = document.getElementById('addNotes').value.trim();

            if (!companyName || !contactPerson || !email || !phone || !vehicleType || !vehicleCapacity || !operatingAreas || !fees || !availability) {
                alert('All required fields are required.');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Invalid email format.');
                return;
            }

            if (!phone.match(/^\d{10,11}$/)) {
                alert('Phone must be 10-11 digits.');
                return;
            }

            fetch('api/transport_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'add', 
                    company_name: companyName, 
                    contact_person: contactPerson, 
                    email, 
                    phone, 
                    vehicle_type: vehicleType, 
                    vehicle_capacity: vehicleCapacity, 
                    license_number: licenseNumber, 
                    operating_areas: operatingAreas, 
                    address, 
                    state_id: stateId, 
                    city_id: cityId, 
                    fees, 
                    availability, 
                    notes 
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
                        loadTransporters(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('addTransportModal')).hide();
                        document.getElementById('addTransportForm').reset();
                        document.getElementById('addCityId').innerHTML = '<option value="">Select City</option>';
                        document.getElementById('addCityId').disabled = true;
                        alert('Transporter added successfully!');
                    } else {
                        alert(data.message || 'Error adding transporter');
                    }
                })
                .catch(error => {
                    console.error('Error adding transporter:', error);
                    alert('Error adding transporter: ' + error.message);
                });
        });

        document.getElementById('updateTransport').addEventListener('click', function() {
            const transporterId = document.getElementById('editTransporterId').value;
            const companyName = document.getElementById('editCompanyName').value.trim();
            const contactPerson = document.getElementById('editContactPerson').value.trim();
            const email = document.getElementById('editEmail').value.trim();
            const phone = document.getElementById('editPhone').value.trim();
            const vehicleType = document.getElementById('editVehicleType').value.trim();
            const vehicleCapacity = document.getElementById('editVehicleCapacity').value.trim();
            const licenseNumber = document.getElementById('editLicenseNumber').value.trim() || null;
            const operatingAreas = document.getElementById('editOperatingAreas').value.trim();
            const address = document.getElementById('editAddress').value.trim() || null;
            const stateId = document.getElementById('editStateId').value ? parseInt(document.getElementById('editStateId').value) : null;
            const cityId = document.getElementById('editCityId').value ? parseInt(document.getElementById('editCityId').value) : null;
            const fees = document.getElementById('editFees').value.trim();
            const availability = document.getElementById('editAvailability').value.trim();
            const notes = document.getElementById('editNotes').value.trim();

            if (!transporterId || !companyName || !contactPerson || !email || !phone || !vehicleType || !vehicleCapacity || !operatingAreas || !fees || !availability) {
                alert('All required fields are required.');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Invalid email format.');
                return;
            }

            if (!phone.match(/^\d{10,11}$/)) {
                alert('Phone must be 10-11 digits.');
                return;
            }

            fetch('api/transport_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'update', 
                    transporter_id: parseInt(transporterId), 
                    company_name: companyName, 
                    contact_person: contactPerson, 
                    email, 
                    phone, 
                    vehicle_type: vehicleType, 
                    vehicle_capacity: vehicleCapacity, 
                    license_number: licenseNumber, 
                    operating_areas: operatingAreas, 
                    address, 
                    state_id: stateId, 
                    city_id: cityId, 
                    fees, 
                    availability, 
                    notes 
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
                        loadTransporters(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('editTransportModal')).hide();
                        document.getElementById('editTransportForm').reset();
                        document.getElementById('editCityId').innerHTML = '<option value="">Select City</option>';
                        document.getElementById('editCityId').disabled = true;
                        alert('Transporter updated successfully!');
                    } else {
                        alert(data.message || 'Error updating transporter');
                    }
                })
                .catch(error => {
                    console.error('Error updating transporter:', error);
                    alert('Error updating transporter: ' + error.message);
                });
        });

        document.getElementById('confirmDisableTransport').addEventListener('click', function() {
            const transporterId = currentEditTransporterId;
            const reason = document.getElementById('disableReason').value.trim();

            fetch('api/transport_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'toggle_status', 
                    transporter_id: parseInt(transporterId), 
                    is_verified: 0, 
                    reason 
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
                        loadTransporters(currentPage);
                        bootstrap.Modal.getInstance(document.getElementById('disableTransportModal')).hide();
                        document.getElementById('disableReason').value = '';
                        alert(`Transporter disabled successfully. Reason: ${reason || 'No notes provided'}.`);
                    } else {
                        alert(data.message || 'Error disabling transporter');
                    }
                })
                .catch(error => {
                    console.error('Error disabling transporter:', error);
                    alert('Error disabling transporter: ' + error.message);
                });
        });

        window.applyFilters = function() {
            loadTransporters(1);
        };

        document.getElementById('searchTransport').addEventListener('keyup', function() {
            loadTransporters(1);
        });

        document.getElementById('filterStatus').addEventListener('change', function() {
            loadTransporters(1);
        });

        window.exportTransporters = function() {
            alert('Preparing transporters export...');
            window.open('api/transport_api.php?action=export', '_blank');
        };

        console.log('Transport module script fully initialized');
    });
</script>
</body>
</html>