<?php
// incentives.php
$active = 'incentives';
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
        <h2 class="mb-4">Incentives Management</h2>
        
        <!-- Loan Companies Section -->
        <div class="data-table-container mb-5">
            <div class="table-header">
                <h4>Loan Companies</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addLoanCompanyModal">
                        <i class="fas fa-plus"></i> Add Loan Company
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportLoanCompanies()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="loanCompaniesTable">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Interest Rate</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="loanCompaniesBody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Farm Tools Section -->
        <div class="data-table-container mb-5">
            <div class="table-header">
                <h4>Farm Tools for Application</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addFarmToolModal">
                        <i class="fas fa-plus"></i> Add Farm Tool
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportFarmTools()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="farmToolsTable">
                    <thead>
                        <tr>
                            <th>Tool Name</th>
                            <th>Description</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="farmToolsBody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grant Providers Section -->
        <div class="data-table-container">
            <div class="table-header">
                <h4>Grant Providers</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addGrantProviderModal">
                        <i class="fas fa-plus"></i> Add Grant Provider
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportGrantProviders()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="grantProvidersTable">
                    <thead>
                        <tr>
                            <th>Provider Name</th>
                            <th>Grant Amount</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="grantProvidersBody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Modals -->
    <!-- Add Loan Company Modal -->
    <div class="modal fade" id="addLoanCompanyModal" tabindex="-1" aria-labelledby="addLoanCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLoanCompanyModalLabel">Add New Loan Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addLoanCompanyForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="companyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="companyName" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="interestRate" class="form-label">Interest Rate (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="interestRate" name="interest_rate" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="terms" class="form-label">Terms</label>
                            <textarea class="form-control" id="terms" name="terms" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="contactDetails" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="contactDetails" name="contact_details" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="addLoanCompany()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Loan Company Modal -->
    <div class="modal fade" id="editLoanCompanyModal" tabindex="-1" aria-labelledby="editLoanCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLoanCompanyModalLabel">Edit Loan Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLoanCompanyForm">
                        <input type="hidden" id="editCompanyId" name="company_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCompanyName" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="editCompanyName" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editInterestRate" class="form-label">Interest Rate (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="editInterestRate" name="interest_rate" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTerms" class="form-label">Terms</label>
                            <textarea class="form-control" id="editTerms" name="terms" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editContactDetails" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="editContactDetails" name="contact_details" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="updateLoanCompany()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Farm Tool Modal -->
    <div class="modal fade" id="addFarmToolModal" tabindex="-1" aria-labelledby="addFarmToolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFarmToolModalLabel">Add Farm Tool</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFarmToolForm">
                        <div class="mb-3">
                            <label for="toolName" class="form-label">Tool Name</label>
                            <input type="text" class="form-control" id="toolName" name="tool_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="toolDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="toolDescription" name="description" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="addFarmTool()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Farm Tool Modal -->
    <div class="modal fade" id="editFarmToolModal" tabindex="-1" aria-labelledby="editFarmToolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFarmToolModalLabel">Edit Farm Tool</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editFarmToolForm">
                        <input type="hidden" id="editToolId" name="tool_id">
                        <div class="mb-3">
                            <label for="editToolName" class="form-label">Tool Name</label>
                            <input type="text" class="form-control" id="editToolName" name="tool_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editToolDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editToolDescription" name="description" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="updateFarmTool()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Grant Provider Modal -->
    <div class="modal fade" id="addGrantProviderModal" tabindex="-1" aria-labelledby="addGrantProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGrantProviderModalLabel">Add Grant Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="addGrantProviderForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="providerName" class="form-label">Provider Name</label>
                                    <input type="text" class="form-control" id="providerName" name="provider_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="grantAmount" class="form-label">Grant Amount</label>
                                    <input type="text" class="form-control" id="grantAmount" name="grant_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="grantTerms" class="form-label">Terms</label>
                            <textarea class="form-control" id="grantTerms" name="terms" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="grantContact" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="grantContact" name="contact_details" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="addGrantProvider()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Grant Provider Modal -->
    <div class="modal fade" id="editGrantProviderModal" tabindex="-1" aria-labelledby="editGrantProviderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGrantProviderModalLabel">Edit Grant Provider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGrantProviderForm">
                        <input type="hidden" id="editProviderId" name="provider_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProviderName" class="form-label">Provider Name</label>
                                    <input type="text" class="form-control" id="editProviderName" name="provider_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editGrantAmount" class="form-label">Grant Amount</label>
                                    <input type="text" class="form-control" id="editGrantAmount" name="grant_amount" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editGrantTerms" class="form-label">Terms</label>
                            <textarea class="form-control" id="editGrantTerms" name="terms" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editGrantContact" class="form-label">Contact Details</label>
                            <input type="text" class="form-control" id="editGrantContact" name="contact_details" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="updateGrantProvider()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Load data when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadLoanCompanies();
        loadFarmTools();
        loadGrantProviders();
    });

    // Loan Companies Functions
    function loadLoanCompanies() {
        fetch('api/loan_companies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('loanCompaniesBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(company => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${company.company_name}</td>
                            <td>${company.interest_rate}%</td>
                            <td>${new Date(company.created_at).toLocaleDateString()}</td>
                            <td><span class="badge ${company.status === 'Active' ? 'badge-approved' : 'badge-pending'}">${company.status}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="editLoanCompany(${company.company_id})"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn ${company.status === 'Active' ? 'btn-disable' : 'btn-enable'}" onclick="toggleLoanCompanyStatus(${company.company_id}, '${company.status}')">
                                        <i class="fas ${company.status === 'Active' ? 'fa-ban' : 'fa-check'}"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error loading loan companies:', error));
    }

    function addLoanCompany() {
        const formData = new FormData(document.getElementById('addLoanCompanyForm'));
        formData.append('action', 'add');
        
        fetch('api/loan_companies.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('addLoanCompanyModal'));
                modal.hide();
                // Reset form
                document.getElementById('addLoanCompanyForm').reset();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error adding loan company:', error));
    }

    function editLoanCompany(companyId) {
        fetch('api/loan_companies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const company = data.data.find(c => c.company_id == companyId);
                    if (company) {
                        document.getElementById('editCompanyId').value = company.company_id;
                        document.getElementById('editCompanyName').value = company.company_name;
                        document.getElementById('editInterestRate').value = company.interest_rate;
                        document.getElementById('editTerms').value = company.terms;
                        document.getElementById('editContactDetails').value = company.contact_details;
                        // Show modal using Bootstrap
                        var modal = new bootstrap.Modal(document.getElementById('editLoanCompanyModal'));
                        modal.show();
                    }
                }
            })
            .catch(error => console.error('Error loading company data:', error));
    }

    function updateLoanCompany() {
        const formData = new FormData(document.getElementById('editLoanCompanyForm'));
        formData.append('action', 'update');

        fetch('api/loan_companies.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('editLoanCompanyModal'));
                modal.hide();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error updating loan company:', error));
    }

    function toggleLoanCompanyStatus(companyId, currentStatus) {
        if (confirm(`Are you sure you want to ${currentStatus === 'Active' ? 'disable' : 'enable'} this loan company?`)) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('company_id', companyId);
            formData.append('current_status', currentStatus);

            fetch('api/loan_companies.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Reload page after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error toggling status:', error));
        }
    }

    // Farm Tools Functions
    function loadFarmTools() {
        fetch('api/farm_tools.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('farmToolsBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(tool => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${tool.tool_name}</td>
                            <td>${tool.description}</td>
                            <td>${new Date(tool.created_at).toLocaleDateString()}</td>
                            <td><span class="badge ${tool.status === 'Available' ? 'badge-approved' : 'badge-pending'}">${tool.status}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="editFarmTool(${tool.tool_id})"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-disable" onclick="deleteFarmTool(${tool.tool_id})"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error loading farm tools:', error));
    }

    function addFarmTool() {
        const formData = new FormData(document.getElementById('addFarmToolForm'));
        formData.append('action', 'add');

        fetch('api/farm_tools.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('addFarmToolModal'));
                modal.hide();
                // Reset form
                document.getElementById('addFarmToolForm').reset();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error adding farm tool:', error));
    }

    function editFarmTool(toolId) {
        fetch('api/farm_tools.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tool = data.data.find(t => t.tool_id == toolId);
                    if (tool) {
                        document.getElementById('editToolId').value = tool.tool_id;
                        document.getElementById('editToolName').value = tool.tool_name;
                        document.getElementById('editToolDescription').value = tool.description;
                        // Show modal using Bootstrap
                        var modal = new bootstrap.Modal(document.getElementById('editFarmToolModal'));
                        modal.show();
                    }
                }
            })
            .catch(error => console.error('Error loading tool data:', error));
    }

    function updateFarmTool() {
        const formData = new FormData(document.getElementById('editFarmToolForm'));
        formData.append('action', 'update');

        fetch('api/farm_tools.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('editFarmToolModal'));
                modal.hide();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error updating farm tool:', error));
    }

    function deleteFarmTool(toolId) {
        if (confirm('Are you sure you want to delete this farm tool?')) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('tool_id', toolId);

            fetch('api/farm_tools.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Reload page after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error deleting farm tool:', error));
        }
    }

    // Grant Providers Functions
    function loadGrantProviders() {
        fetch('api/grant_providers.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('grantProvidersBody');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(provider => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${provider.provider_name}</td>
                            <td>${provider.grant_amount}</td>
                            <td>${new Date(provider.created_at).toLocaleDateString()}</td>
                            <td><span class="badge ${provider.status === 'Active' ? 'badge-approved' : 'badge-pending'}">${provider.status}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="editGrantProvider(${provider.provider_id})"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn ${provider.status === 'Active' ? 'btn-disable' : 'btn-enable'}" onclick="toggleGrantProviderStatus(${provider.provider_id}, '${provider.status}')">
                                        <i class="fas ${provider.status === 'Active' ? 'fa-ban' : 'fa-check'}"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error loading grant providers:', error));
    }

    function addGrantProvider() {
        const formData = new FormData(document.getElementById('addGrantProviderForm'));
        formData.append('action', 'add');
        
        fetch('api/grant_providers.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('addGrantProviderModal'));
                modal.hide();
                // Reset form
                document.getElementById('addGrantProviderForm').reset();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error adding grant provider:', error));
    }

    function editGrantProvider(providerId) {
        fetch('api/grant_providers.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const provider = data.data.find(p => p.provider_id == providerId);
                    if (provider) {
                        document.getElementById('editProviderId').value = provider.provider_id;
                        document.getElementById('editProviderName').value = provider.provider_name;
                        document.getElementById('editGrantAmount').value = provider.grant_amount;
                        document.getElementById('editGrantTerms').value = provider.terms;
                        document.getElementById('editGrantContact').value = provider.contact_details;
                        // Show modal using Bootstrap
                        var modal = new bootstrap.Modal(document.getElementById('editGrantProviderModal'));
                        modal.show();
                    }
                }
            })
            .catch(error => console.error('Error loading provider data:', error));
    }

    function updateGrantProvider() {
        const formData = new FormData(document.getElementById('editGrantProviderForm'));
        formData.append('action', 'update');
        
        fetch('api/grant_providers.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Close modal using Bootstrap
                var modal = bootstrap.Modal.getInstance(document.getElementById('editGrantProviderModal'));
                modal.hide();
                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error updating grant provider:', error));
    }

    function toggleGrantProviderStatus(providerId, currentStatus) {
        if (confirm(`Are you sure you want to ${currentStatus === 'Active' ? 'disable' : 'enable'} this grant provider?`)) {
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('provider_id', providerId);
            formData.append('current_status', currentStatus);
            
            fetch('api/grant_providers.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Reload page after 1 second
                    // setTimeout(() => {
                    //     location.reload();
                    // }, 1000);
                 } //else {
                //     alert(data.message);
                // }
            })
            .catch(error => console.error('Error toggling status:', error));
        }
    }

    // Export Functions - Updated to use embedded API
function exportLoanCompanies() {
    // Show loading message
    alert('Preparing loan companies export...');
    
    // Open export URL in new window/tab
    window.open('api/loan_companies.php?action=export', '_blank');
}

function exportFarmTools() {
    // Show loading message
    alert('Preparing farm tools export...');
    
    // Open export URL in new window/tab
    window.open('api/farm_tools.php?action=export', '_blank');
}

function exportGrantProviders() {
    // Show loading message
    alert('Preparing grant providers export...');
    
    // Open export URL in new window/tab
    window.open('api/grant_providers.php?action=export', '_blank');
}
</script>
</body>
</html>