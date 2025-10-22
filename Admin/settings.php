<?php
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
                            <button class="btn btn-agri btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addProduceModal" onclick="prepareAddModal('produce', 'Add Produce Category', 'addProduceModal')">Add Category</button>
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
                            <button class="btn btn-agri btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addBusinessModal" onclick="prepareAddModal('business', 'Add Business Type', 'addBusinessModal')">Add Type</button>
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
                            <button class="btn btn-agri btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addRoleModal" onclick="prepareAddModal('role', 'Add Role', 'addRoleModal')">Add Role</button>
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
                            <button class="btn btn-agri btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addOrderStatusModal" onclick="prepareAddModal('orderStatus', 'Add Order Status', 'addOrderStatusModal')">Add Status</button>
                        </div>
                        <ul class="list-group" id="orderStatusesList">
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
                            alert('You have been signed out successfully.');
                            
                        }
                        windows.location('signout.php');
                    });
                }

                // Settings module functions (demo: use localStorage for persistence; prod: DB)
                let settingsData = JSON.parse(localStorage.getItem('agriAdminSettings')) || {
                    general: { siteName: 'AgriAdmin Platform', currency: '₦', timezone: 'Africa/Lagos', dateFormat: 'dd/mm/yyyy' },
                    notifications: { smtpHost: 'smtp.gmail.com', smtpPort: '587', smtpEmail: 'noreply@agriadmin.com', enableSms: false },
                    produceCategories: ['Tomatoes', 'Rice', 'Maize', 'Beans', 'Yam'],
                    businessTypes: ['Wholesaler', 'Retailer', 'Exporter', 'Processor'],
                    roles: ['Super Admin', 'Moderator', 'Viewer'],
                    orderStatuses: ['Pending', 'Shipped', 'Completed', 'Cancelled'],
                    templates: ['Fraud Warning', 'Quality Issue'],
                    incentiveCategories: ['Loans', 'Farm Tools', 'Grants'],
                    feeFormulas: ['Base KM Rate', 'Flat Trip Rate']
                };

                // Load initial data
                loadSettingsData();

                // General Settings Save
                window.saveGeneralSettings = function() {
                    settingsData.general.siteName = document.getElementById('siteName').value;
                    settingsData.general.currency = document.getElementById('currency').value;
                    settingsData.general.timezone = document.getElementById('timezone').value;
                    settingsData.general.dateFormat = document.getElementById('dateFormat').value;
                    saveAndAlert('General settings saved!');
                }

                // Notification Settings Save
                window.saveNotificationSettings = function() {
                    settingsData.notifications.smtpHost = document.getElementById('smtpHost').value;
                    settingsData.notifications.smtpPort = document.getElementById('smtpPort').value;
                    settingsData.notifications.smtpEmail = document.getElementById('smtpEmail').value;
                    settingsData.notifications.enableSms = document.getElementById('enableSms').checked;
                    if (settingsData.notifications.enableSms) {
                        settingsData.notifications.smsApiKey = document.getElementById('smsApiKey').value;
                    }
                    saveAndAlert('Notification settings saved!');
                }

                // Toggle SMS Config
                window.toggleSmsConfig = function() {
                    document.getElementById('smsConfig').style.display = document.getElementById('enableSms').checked ? 'block' : 'none';
                }

                // Prepare Add Modal
                window.prepareAddModal = function(type, title, modalId) {
                    document.getElementById(modalId + 'Label').textContent = title;
                    document.getElementById(type + 'ItemName').value = '';
                    new bootstrap.Modal(document.getElementById(modalId)).show();
                }

                // Prepare Edit Modal
                window.prepareEditModal = function(type, itemName) {
                    const modalId = 'edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'Modal';
                    document.getElementById(modalId + 'Label').textContent = `Edit ${type.charAt(0).toUpperCase() + type.slice(1)}`;
                    document.getElementById('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'ItemName').value = itemName;
                    document.getElementById('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'OriginalName').value = itemName;
                    new bootstrap.Modal(document.getElementById(modalId)).show();
                }

                // Save Item from Add Modal
                window.saveItem = function(type, modalId, inputId) {
                    const name = document.getElementById(inputId).value.trim();

                    if (!name) {
                        alert('Name is required.');
                        return;
                    }

                    let listKey;
                    switch (type) {
                        case 'produce': listKey = 'produceCategories'; break;
                        case 'business': listKey = 'businessTypes'; break;
                        case 'role': listKey = 'roles'; break;
                        case 'orderStatus': listKey = 'orderStatuses'; break;
                        default: return;
                    }

                    const currentList = settingsData[listKey];
                    if (currentList.includes(name)) {
                        alert('Item already exists.');
                        return;
                    }

                    currentList.push(name);
                    saveAndAlert(`${type} added!`);
                    loadAllLists();
                    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                    modal.hide();
                    document.getElementById(inputId).value = '';
                    document.getElementById('new' + type.charAt(0).toUpperCase() + type.slice(1)).value = '';
                }

                // Save Edited Item
                window.saveEditItem = function(type, modalId, inputId, originalInputId) {
                    const newName = document.getElementById(inputId).value.trim();
                    const originalName = document.getElementById(originalInputId).value;

                    if (!newName) {
                        alert('Name is required.');
                        return;
                    }

                    let listKey;
                    switch (type) {
                        case 'produce': listKey = 'produceCategories'; break;
                        case 'business': listKey = 'businessTypes'; break;
                        case 'role': listKey = 'roles'; break;
                        case 'orderStatus': listKey = 'orderStatuses'; break;
                        default: return;
                    }

                    const currentList = settingsData[listKey];
                    if (newName !== originalName && currentList.includes(newName)) {
                        alert('Item already exists.');
                        return;
                    }

                    const index = currentList.indexOf(originalName);
                    if (index !== -1) {
                        currentList[index] = newName;
                        saveAndAlert(`${type} updated!`);
                        loadAllLists();
                        const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                        modal.hide();
                    }
                }

                // Delete Item (with confirm)
                window.deleteItem = function(type, itemName) {
                    if (confirm(`Delete ${itemName}?`)) {
                        let listKey;
                        switch (type) {
                            case 'produce': listKey = 'produceCategories'; break;
                            case 'business': listKey = 'businessTypes'; break;
                            case 'role': listKey = 'roles'; break;
                            case 'orderStatus': listKey = 'orderStatuses'; break;
                            default: return;
                        }
                        settingsData[listKey] = settingsData[listKey].filter(item => item !== itemName);
                        saveAndAlert(`${type} deleted!`);
                        loadAllLists();
                    }
                }

                // Load specific lists
                function loadProduceCategories() {
                    const list = document.getElementById('produceCategoriesList');
                    list.innerHTML = settingsData.produceCategories.map(cat => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${cat}
                            <div>
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editProduceModal" onclick="prepareEditModal('produce', '${cat}')">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('produce', '${cat}')">Delete</button>
                            </div>
                        </li>
                    `).join('');
                }

                function loadBusinessTypes() {
                    const list = document.getElementById('businessTypesList');
                    list.innerHTML = settingsData.businessTypes.map(type => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${type}
                            <div>
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editBusinessModal" onclick="prepareEditModal('business', '${type}')">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('business', '${type}')">Delete</button>
                            </div>
                        </li>
                    `).join('');
                }

                function loadRoles() {
                    const list = document.getElementById('rolesList');
                    list.innerHTML = settingsData.roles.map(role => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${role}
                            <div>
                                <span class="badge bg-primary">Full Access</span>
                                <button class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#editRoleModal" onclick="prepareEditModal('role', '${role}')">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('role', '${role}')">Delete</button>
                            </div>
                        </li>
                    `).join('');
                }

                function loadOrderStatuses() {
                    const list = document.getElementById('orderStatusesList');
                    list.innerHTML = settingsData.orderStatuses.map(status => `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${status}
                            <div>
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editOrderStatusModal" onclick="prepareEditModal('orderStatus', '${status}')">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('orderStatus', '${status}')">Delete</button>
                            </div>
                        </li>
                    `).join('');
                }

                // Load all lists
                function loadAllLists() {
                    loadProduceCategories();
                    loadBusinessTypes();
                    loadRoles();
                    loadOrderStatuses();
                }

                // Utility: Save to localStorage & Alert
                function saveAndAlert(message) {
                    localStorage.setItem('agriAdminSettings', JSON.stringify(settingsData));
                    alert(message);
                }

                // Load initial data
                function loadSettingsData() {
                    loadAllLists();
                    document.getElementById('siteName').value = settingsData.general.siteName;
                    document.getElementById('currency').value = settingsData.general.currency;
                    document.getElementById('timezone').value = settingsData.general.timezone;
                    document.getElementById('dateFormat').value = settingsData.general.dateFormat;
                    document.getElementById('smtpHost').value = settingsData.notifications.smtpHost;
                    document.getElementById('smtpPort').value = settingsData.notifications.smtpPort;
                    document.getElementById('smtpEmail').value = settingsData.notifications.smtpEmail;
                    document.getElementById('enableSms').checked = settingsData.notifications.enableSms;
                    toggleSmsConfig();
                }

                console.log('Settings module script fully initialized');
            });
        </script>
    </main>
</body>
</html>