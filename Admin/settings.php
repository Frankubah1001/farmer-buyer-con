<?php
// Include session timeout check
require_once 'session_check.php';

// settings.php - Updated with separate modal popups for Add/Edit in each section
$active = 'settings';
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
        <h2 class="mb-4">System Settings</h2>
        
        <div class="row">
            <!-- General Settings Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>General Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="generalSettingsForm">
                            <div class="mb-3">
                                <label for="siteName" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="siteName" value="AgriAdmin Platform" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">Currency</label>
                                        <input type="text" class="form-control" id="currency" value="₦" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone" required>
                                            <option value="Africa/Lagos" selected>Africa/Lagos (Nigeria)</option>
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">America/New_York</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="dateFormat" class="form-label">Date Format</label>
                                <select class="form-select" id="dateFormat" required>
                                    <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                                    <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                                    <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-agri" onclick="saveGeneralSettings()">Save General Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Email/SMS Config Section -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Notification Settings</h5>
                    </div>
                    <div class="card-body">
                        <form id="notificationSettingsForm">
                            <div class="mb-3">
                                <label for="smtpHost" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="smtpHost" value="smtp.gmail.com">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtpPort" class="form-label">SMTP Port</label>
                                        <input type="number" class="form-control" id="smtpPort" value="587">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtpEmail" class="form-label">From Email</label>
                                        <input type="email" class="form-control" id="smtpEmail" value="noreply@agriadmin.com">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="smtpPassword" class="form-label">SMTP Password</label>
                                <input type="password" class="form-control" id="smtpPassword" placeholder="App password for security">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="enableSms" onchange="toggleSmsConfig()">
                                <label class="form-check-label" for="enableSms">Enable SMS Notifications</label>
                            </div>
                            <div class="mb-3" id="smsConfig" style="display: none;">
                                <label for="smsApiKey" class="form-label">SMS API Key (e.g., Twilio)</label>
                                <input type="text" class="form-control" id="smsApiKey" placeholder="Your SMS provider API key">
                            </div>
                            <button type="button" class="btn btn-agri" onclick="saveNotificationSettings()">Save Notification Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module-Specific Settings -->
        <div class="row">
            <!-- Produce Categories (for Prices/Farmers) -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-seedling me-2"></i>Produce Categories</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Manage categories for price ranges and farmer produce types.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="newProduceCategory" placeholder="Add new category (e.g., Cassava)">
                            <button class="btn btn-agri btn-sm mt-2" onclick="addProduceCategory()">Add Category</button>
                        </div>
                        <ul class="list-group" id="produceCategoriesList">
                            <!-- Dynamically populated -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Business Types (for Buyers) -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Business Types (Buyers)</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Manage buyer business types.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="newBusinessType" placeholder="Add new type (e.g., Distributor)">
                            <button class="btn btn-agri btn-sm mt-2" onclick="addBusinessType()">Add Type</button>
                        </div>
                        <ul class="list-group" id="businessTypesList">
                            <!-- Dynamically populated -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Admin Roles (for Profile) -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Admin Roles</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Manage roles and permissions.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="newRoleName" placeholder="Add new role (e.g., Auditor)">
                            <button class="btn btn-agri btn-sm mt-2" onclick="addRole()">Add Role</button>
                        </div>
                        <ul class="list-group" id="rolesList">
                            <!-- Dynamically populated -->
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Order Status Workflow (for Orders) -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Order Status Workflow</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Customize order statuses.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="newStatusName" placeholder="Add new status (e.g., In Transit)">
                            <button class="btn btn-agri btn-sm mt-2" onclick="addOrderStatus()">Add Status</button>
                        </div>
                        <ul class="list-group" id="orderStatusesList">
                            <!-- Dynamically populated -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Admin Designations -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-briefcase me-2"></i>Admin Designations</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Manage admin designations/titles.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="newDesignationName" placeholder="Add new designation (e.g., Operations Manager)">
                            <button class="btn btn-agri btn-sm mt-2" onclick="addDesignation()">Add Designation</button>
                        </div>
                        <ul class="list-group" id="designationsList">
                            <!-- Dynamically populated -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

<!-- Modals for Edit Actions -->
        <div class="modal fade" id="editProduceModal" tabindex="-1" aria-labelledby="editProduceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProduceModalLabel">Edit Produce Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editProduceForm">
                            <input type="hidden" id="editProduceItemType" value="produce">
                            <input type="hidden" id="editProduceOriginalName">
                            <div class="mb-3">
                                <label for="editProduceItemName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editProduceItemName" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-agri" onclick="saveEditItem('produce', 'editProduceModal', 'editProduceItemName', 'editProduceOriginalName')">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editBusinessModal" tabindex="-1" aria-labelledby="editBusinessModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBusinessModalLabel">Edit Business Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editBusinessForm">
                            <input type="hidden" id="editBusinessItemType" value="business">
                            <input type="hidden" id="editBusinessOriginalName">
                            <div class="mb-3">
                                <label for="editBusinessItemName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editBusinessItemName" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-agri" onclick="saveEditItem('business', 'editBusinessModal', 'editBusinessItemName', 'editBusinessOriginalName')">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editRoleForm">
                            <input type="hidden" id="editRoleItemType" value="role">
                            <input type="hidden" id="editRoleOriginalName">
                            <div class="mb-3">
                                <label for="editRoleItemName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editRoleItemName" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-agri" onclick="saveEditItem('role', 'editRoleModal', 'editRoleItemName', 'editRoleOriginalName')">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editOrderStatusModal" tabindex="-1" aria-labelledby="editOrderStatusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrderStatusModalLabel">Edit Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editOrderStatusForm">
                            <input type="hidden" id="editOrderStatusItemType" value="orderStatus">
                            <input type="hidden" id="editOrderStatusOriginalName">
                            <div class="mb-3">
                                <label for="editOrderStatusItemName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editOrderStatusItemName" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-agri" onclick="saveEditItem('orderStatus', 'editOrderStatusModal', 'editOrderStatusItemName', 'editOrderStatusOriginalName')">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDesignationModal" tabindex="-1" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDesignationModalLabel">Edit Designation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editDesignationForm">
                            <input type="hidden" id="editDesignationItemType" value="designation">
                            <input type="hidden" id="editDesignationOriginalName">
                            <div class="mb-3">
                                <label for="editDesignationItemName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="editDesignationItemName" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-agri" onclick="saveEditItem('designation', 'editDesignationModal', 'editDesignationItemName', 'editDesignationOriginalName')">Save</button>
                    </div>
                </div>
            </div>
            <?php include 'footer.php'; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Wait for DOM to be fully loaded
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded - Settings module script starting');

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
                            window.location.href = 'signout.php';
                        }
                    });
                }

                // API Helper Function
                async function apiCall(action, data = null, method = 'GET') {
                    const url = `api/settings_api.php?action=${action}`;
                    const options = {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    };
                    
                    if (data && method === 'POST') {
                        options.body = JSON.stringify(data);
                    }
                    
                    try {
                        const response = await fetch(url, options);
                        const result = await response.json();
                        return result;
                    } catch (error) {
                        console.error('API Error:', error);
                        return { success: false, message: 'Network error occurred' };
                    }
                }

                // Load initial data
                loadSettingsData();

                // ============ GENERAL SETTINGS ============
                window.saveGeneralSettings = async function() {
                    const data = {
                        siteName: document.getElementById('siteName').value,
                        currency: document.getElementById('currency').value,
                        timezone: document.getElementById('timezone').value,
                        dateFormat: document.getElementById('dateFormat').value
                    };
                    
                    const result = await apiCall('save_general_settings', data, 'POST');
                    if (result.success) {
                        alert(result.message || 'General settings saved successfully!');
                    } else {
                        alert('Error: ' + (result.message || 'Failed to save settings'));
                    }
                }

                // ============ NOTIFICATION SETTINGS ============
                window.saveNotificationSettings = async function() {
                    const data = {
                        smtpHost: document.getElementById('smtpHost').value,
                        smtpPort: document.getElementById('smtpPort').value,
                        smtpEmail: document.getElementById('smtpEmail').value,
                        smtpPassword: document.getElementById('smtpPassword').value,
                        enableSms: document.getElementById('enableSms').checked,
                        smsApiKey: document.getElementById('smsApiKey').value
                    };
                    
                    const result = await apiCall('save_notification_settings', data, 'POST');
                    if (result.success) {
                        alert(result.message || 'Notification settings saved successfully!');
                    } else {
                        alert('Error: ' + (result.message || 'Failed to save settings'));
                    }
                }

                // Toggle SMS Config
                window.toggleSmsConfig = function() {
                    document.getElementById('smsConfig').style.display = document.getElementById('enableSms').checked ? 'block' : 'none';
                }

                // ============ PRODUCE CATEGORIES ============
                async function loadProduceCategories() {
                    const result = await apiCall('get_produce_categories');
                    if (result.success) {
                        const list = document.getElementById('produceCategoriesList');
                        if (result.data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-muted">No categories found</li>';
                            return;
                        }
                        list.innerHTML = result.data.map(cat => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${cat.category_name}
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="prepareEditModal('produce', '${cat.category_name}')">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('produce', '${cat.category_name}')">Delete</button>
                                </div>
                            </li>
                        `).join('');
                    }
                }

                window.addProduceCategory = async function() {
                    const name = document.getElementById('newProduceCategory').value.trim();
                    if (!name) {
                        alert('Please enter a category name');
                        return;
                    }
                    
                    const result = await apiCall('add_produce_category', { name }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('newProduceCategory').value = '';
                        loadProduceCategories();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // ============ BUSINESS TYPES ============
                async function loadBusinessTypes() {
                    const result = await apiCall('get_business_types');
                    if (result.success) {
                        const list = document.getElementById('businessTypesList');
                        if (result.data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-muted">No business types found</li>';
                            return;
                        }
                        list.innerHTML = result.data.map(type => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${type.type_name}
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="prepareEditModal('business', '${type.type_name}')">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('business', '${type.type_name}')">Delete</button>
                                </div>
                            </li>
                        `).join('');
                    }
                }

                window.addBusinessType = async function() {
                    const name = document.getElementById('newBusinessType').value.trim();
                    if (!name) {
                        alert('Please enter a business type name');
                        return;
                    }
                    
                    const result = await apiCall('add_business_type', { name }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('newBusinessType').value = '';
                        loadBusinessTypes();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // ============ ADMIN ROLES ============
                async function loadRoles() {
                    const result = await apiCall('get_roles');
                    if (result.success) {
                        const list = document.getElementById('rolesList');
                        if (result.data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-muted">No roles found</li>';
                            return;
                        }
                        list.innerHTML = result.data.map(role => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${role.role_name}
                                <div>
                                    <span class="badge bg-primary">Full Access</span>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="prepareEditModal('role', '${role.role_name}')">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('role', '${role.role_name}')">Delete</button>
                                </div>
                            </li>
                        `).join('');
                    }
                }

                window.addRole = async function() {
                    const name = document.getElementById('newRoleName').value.trim();
                    if (!name) {
                        alert('Please enter a role name');
                        return;
                    }
                    
                    const result = await apiCall('add_role', { name }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('newRoleName').value = '';
                        loadRoles();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // ============ ORDER STATUSES ============
                async function loadOrderStatuses() {
                    const result = await apiCall('get_order_statuses');
                    if (result.success) {
                        const list = document.getElementById('orderStatusesList');
                        if (result.data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-muted">No statuses found</li>';
                            return;
                        }
                        list.innerHTML = result.data.map(status => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${status.status_name}
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="prepareEditModal('orderStatus', '${status.status_name}')">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('orderStatus', '${status.status_name}')">Delete</button>
                                </div>
                            </li>
                        `).join('');
                    }
                }

                window.addOrderStatus = async function() {
                    const name = document.getElementById('newStatusName').value.trim();
                    if (!name) {
                        alert('Please enter a status name');
                        return;
                    }
                    
                    const result = await apiCall('add_order_status', { name }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('newStatusName').value = '';
                        loadOrderStatuses();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // ============ DESIGNATIONS ============
                async function loadDesignations() {
                    const result = await apiCall('get_designations');
                    if (result.success) {
                        const list = document.getElementById('designationsList');
                        if (result.data.length === 0) {
                            list.innerHTML = '<li class="list-group-item text-muted">No designations found</li>';
                            return;
                        }
                        list.innerHTML = result.data.map(designation => `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${designation.designation_name}
                                <div>
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="prepareEditModal('designation', '${designation.designation_name}')">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('designation', '${designation.designation_name}')">Delete</button>
                                </div>
                            </li>
                        `).join('');
                    }
                }

                window.addDesignation = async function() {
                    const name = document.getElementById('newDesignationName').value.trim();
                    if (!name) {
                        alert('Please enter a designation name');
                        return;
                    }
                    
                    const result = await apiCall('add_designation', { name }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        document.getElementById('newDesignationName').value = '';
                        loadDesignations();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // ============ COMMON FUNCTIONS ============
                
                // Prepare Edit Modal
                window.prepareEditModal = function(type, itemName) {
                    const modalId = 'edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'Modal';
                    const modal = new bootstrap.Modal(document.getElementById(modalId));
                    document.getElementById('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'ItemName').value = itemName;
                    document.getElementById('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'OriginalName').value = itemName;
                    modal.show();
                }

                // Save Edited Item
                window.saveEditItem = async function(type, modalId, inputId, originalInputId) {
                    const newName = document.getElementById(inputId).value.trim();
                    const originalName = document.getElementById(originalInputId).value;

                    if (!newName) {
                        alert('Name is required.');
                        return;
                    }

                    let action;
                    switch (type) {
                        case 'produce': action = 'update_produce_category'; break;
                        case 'business': action = 'update_business_type'; break;
                        case 'role': action = 'update_role'; break;
                        case 'orderStatus': action = 'update_order_status'; break;
                        case 'designation': action = 'update_designation'; break;
                        default: return;
                    }

                    const result = await apiCall(action, { oldName: originalName, newName: newName }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        modal.hide();
                        loadAllLists();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // Delete Item
                window.deleteItem = async function(type, itemName) {
                    if (!confirm(`Are you sure you want to delete "${itemName}"?`)) {
                        return;
                    }

                    let action;
                    switch (type) {
                        case 'produce': action = 'delete_produce_category'; break;
                        case 'business': action = 'delete_business_type'; break;
                        case 'role': action = 'delete_role'; break;
                        case 'orderStatus': action = 'delete_order_status'; break;
                        case 'designation': action = 'delete_designation'; break;
                        default: return;
                    }

                    const result = await apiCall(action, { name: itemName }, 'POST');
                    if (result.success) {
                        alert(result.message);
                        loadAllLists();
                    } else {
                        alert('Error: ' + result.message);
                    }
                }

                // Load all lists
                function loadAllLists() {
                    loadProduceCategories();
                    loadBusinessTypes();
                    loadRoles();
                    loadOrderStatuses();
                    loadDesignations();
                }

                // Load initial data
                async function loadSettingsData() {
                    // Load general settings
                    const generalResult = await apiCall('get_general_settings');
                    if (generalResult.success && generalResult.data) {
                        document.getElementById('siteName').value = generalResult.data.site_name || 'AgriAdmin Platform';
                        document.getElementById('currency').value = generalResult.data.currency || '₦';
                        document.getElementById('timezone').value = generalResult.data.timezone || 'Africa/Lagos';
                        document.getElementById('dateFormat').value = generalResult.data.date_format || 'dd/mm/yyyy';
                    }

                    // Load notification settings
                    const notifResult = await apiCall('get_notification_settings');
                    if (notifResult.success && notifResult.data) {
                        document.getElementById('smtpHost').value = notifResult.data.smtp_host || '';
                        document.getElementById('smtpPort').value = notifResult.data.smtp_port || '';
                        document.getElementById('smtpEmail').value = notifResult.data.smtp_email || '';
                        document.getElementById('smtpPassword').value = notifResult.data.smtp_password || '';
                        document.getElementById('enableSms').checked = notifResult.data.enable_sms === '1';
                        document.getElementById('smsApiKey').value = notifResult.data.sms_api_key || '';
                        toggleSmsConfig();
                    }

                    // Load all lists
                    loadAllLists();
                }

                console.log('Settings module script fully initialized');
            });
        </script>
    </main>
</body>
</html>