// profile.js - Admin Profile Management with API Integration
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded - Admin Profile script starting');

    let currentEditAdminId = '';
    let rolesData = [];
    let designationsData = [];

    // Common scripts (sidebar toggle, logout, etc.)
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    }

    const headerToggle = document.getElementById('headerToggle');
    if (headerToggle) {
        headerToggle.addEventListener('click', function () {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    }

    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to sign out?')) {
                window.location.href = 'signout.php';
            }
        });
    }

    // API Helper Function
    async function apiCall(endpoint, action, data = null, method = 'GET') {
        const url = `api/${endpoint}.php?action=${action}`;
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

    // Load Roles and Designations
    async function loadRolesAndDesignations() {
        // Load roles
        const rolesResult = await apiCall('settings_api', 'get_roles');
        if (rolesResult.success) {
            rolesData = rolesResult.data;
            populateRoleDropdowns();
        }

        // Load designations
        const designationsResult = await apiCall('settings_api', 'get_designations');
        if (designationsResult.success) {
            designationsData = designationsResult.data;
            populateDesignationDropdowns();
        }

        // Load permissions
        await loadPermissions();
    }

    function populateRoleDropdowns() {
        const addRoleSelect = document.getElementById('addAdminRole');
        const editRoleSelect = document.getElementById('editAdminRole');

        const roleOptions = rolesData.map(role =>
            `<option value="${role.role_id}">${role.role_name}</option>`
        ).join('');

        addRoleSelect.innerHTML = '<option value="">Select Role</option>' + roleOptions;
        editRoleSelect.innerHTML = roleOptions;
    }

    function populateDesignationDropdowns() {
        const addDesignationSelect = document.getElementById('addAdminDesignation');
        const editDesignationSelect = document.getElementById('editAdminDesignation');

        const designationOptions = designationsData.map(designation =>
            `<option value="${designation.designation_id}">${designation.designation_name}</option>`
        ).join('');

        addDesignationSelect.innerHTML = '<option value="">Select Designation</option>' + designationOptions;
        editDesignationSelect.innerHTML = '<option value="">Select Designation</option>' + designationOptions;
    }

    // ==================== PERMISSIONS MANAGEMENT ====================

    let allPermissions = [];
    let permissionsByModule = {};

    // Load all permissions
    async function loadPermissions() {
        try {
            const response = await fetch('api/permissions_api.php?action=get_all_permissions');
            const result = await response.json();

            if (result.success) {
                allPermissions = result.data;
                groupPermissionsByModule();
                renderPermissions('add');
                renderPermissions('edit');
                setupPermissionSearch();
            }
        } catch (error) {
            console.error('Error loading permissions:', error);
        }
    }

    // Group permissions by module
    function groupPermissionsByModule() {
        permissionsByModule = {};
        allPermissions.forEach(permission => {
            if (!permissionsByModule[permission.module]) {
                permissionsByModule[permission.module] = [];
            }
            permissionsByModule[permission.module].push(permission);
        });
    }

    // Get module display name
    function getModuleDisplayName(module) {
        const moduleNames = {
            'dashboard': 'Dashboard',
            'farmers': 'Farmers Management',
            'buyers': 'Buyers Management',
            'orders': 'Orders Management',
            'prices': 'Prices Management',
            'reports': 'Reports Management',
            'farm_tools': 'Farm Tools Applications',
            'grants': 'Grant Applications',
            'loans': 'Loan Applications',
            'incentives': 'Incentives Management',
            'transport': 'Transport Management',
            'audit': 'Audit Logs',
            'admins': 'Admin Management',
            'settings': 'Settings'
        };
        return moduleNames[module] || module.charAt(0).toUpperCase() + module.slice(1);
    }

    // Render permissions in container
    function renderPermissions(formType) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        if (!container) return;

        let html = '';

        Object.keys(permissionsByModule).sort().forEach(module => {
            const permissions = permissionsByModule[module];
            const moduleId = `${formType}-${module}`;

            html += `
                <div class="permission-module" data-module="${module}">
                    <div class="permission-module-header">
                        <input type="checkbox" class="module-select-all" id="${moduleId}-all" data-module="${module}" data-form="${formType}">
                        <h6 class="permission-module-title">${getModuleDisplayName(module)}</h6>
                        <span class="permission-count">${permissions.length} permissions</span>
                    </div>
                    <div class="permission-list">
                        ${permissions.map(permission => `
                            <div class="permission-item">
                                <input 
                                    type="checkbox" 
                                    class="permission-checkbox" 
                                    id="${formType}-perm-${permission.permission_id}" 
                                    value="${permission.permission_id}"
                                    data-module="${module}"
                                    data-form="${formType}"
                                    data-permission-name="${permission.permission_name}">
                                <label for="${formType}-perm-${permission.permission_id}">
                                    ${permission.permission_label}
                                    <small>${permission.permission_name}</small>
                                </label>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        // Add event listeners for module select all
        container.querySelectorAll('.module-select-all').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const module = this.dataset.module;
                const formType = this.dataset.form;
                const moduleCheckboxes = container.querySelectorAll(
                    `.permission-checkbox[data-module="${module}"][data-form="${formType}"]`
                );
                moduleCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        });

        // Update module checkbox when individual permissions change
        container.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                updateModuleCheckbox(this.dataset.module, this.dataset.form);
            });
        });
    }

    // Update module select-all checkbox state
    function updateModuleCheckbox(module, formType) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        const moduleCheckboxes = container.querySelectorAll(
            `.permission-checkbox[data-module="${module}"][data-form="${formType}"]`
        );
        const moduleSelectAll = container.querySelector(
            `.module-select-all[data-module="${module}"][data-form="${formType}"]`
        );

        if (moduleSelectAll) {
            const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

            moduleSelectAll.checked = allChecked;
            moduleSelectAll.indeterminate = someChecked && !allChecked;
        }
    }

    // Select all permissions
    window.selectAllPermissions = function (formType) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        container.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        container.querySelectorAll('.module-select-all').forEach(cb => cb.checked = true);
    };

    // Deselect all permissions
    window.deselectAllPermissions = function (formType) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        container.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        container.querySelectorAll('.module-select-all').forEach(cb => cb.checked = false);
    };

    // Setup permission search
    function setupPermissionSearch() {
        ['add', 'edit'].forEach(formType => {
            const searchInput = document.getElementById(`${formType}PermissionSearch`);
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    filterPermissions(formType, this.value);
                });
            }
        });
    }

    // Filter permissions by search term
    function filterPermissions(formType, searchTerm) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        const modules = container.querySelectorAll('.permission-module');
        const term = searchTerm.toLowerCase();

        modules.forEach(module => {
            const items = module.querySelectorAll('.permission-item');
            let hasVisibleItems = false;

            items.forEach(item => {
                const label = item.querySelector('label').textContent.toLowerCase();
                const matches = label.includes(term);
                item.style.display = matches ? 'flex' : 'none';
                if (matches) hasVisibleItems = true;
            });

            module.classList.toggle('hidden', !hasVisibleItems);
        });
    }

    // Get selected permissions for a form
    function getSelectedPermissions(formType) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        const checkboxes = container.querySelectorAll('.permission-checkbox:checked');
        return Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    // Set selected permissions for a form
    function setSelectedPermissions(formType, permissionIds) {
        const container = document.getElementById(`${formType}PermissionsContainer`);
        container.querySelectorAll('.permission-checkbox').forEach(cb => {
            cb.checked = permissionIds.includes(parseInt(cb.value));
        });

        // Update module checkboxes
        Object.keys(permissionsByModule).forEach(module => {
            updateModuleCheckbox(module, formType);
        });
    }

    // Load admins
    async function loadAdmins(search = '', role = '', status = '') {
        const queryParams = new URLSearchParams();
        if (search) queryParams.append('search', search);
        if (role) queryParams.append('role', role);
        if (status) queryParams.append('status', status);

        const url = `api/admin_profile_api.php?action=get_admins&${queryParams.toString()}`;

        try {
            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                displayAdmins(result.data);
            } else {
                console.error('Failed to load admins:', result.message);
            }
        } catch (error) {
            console.error('Error loading admins:', error);
        }
    }

    function displayAdmins(admins) {
        const tableBody = document.getElementById('adminsTableBody');

        if (admins.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No admins found</td></tr>';
            return;
        }

        tableBody.innerHTML = admins.map(admin => {
            const statusBadge = admin.status === 'active' ?
                '<span class="badge badge-approved">Active</span>' :
                '<span class="badge badge-disabled">Disabled</span>';

            const actionButtons = admin.status === 'active' ? `
                <button class="action-btn btn-edit view-details-btn" data-adminid="${admin.admin_id}"><i class="fas fa-eye"></i></button>
                <button class="action-btn btn-edit view-logs-btn" data-adminid="${admin.admin_id}"><i class="fas fa-history"></i></button>
                <button class="action-btn btn-edit edit-admin-btn" data-adminid="${admin.admin_id}"><i class="fas fa-edit"></i></button>
                <button class="action-btn btn-disable disable-admin-btn" data-adminid="${admin.admin_id}"><i class="fas fa-ban"></i></button>
            ` : `
                <button class="action-btn btn-edit view-details-btn" data-adminid="${admin.admin_id}"><i class="fas fa-eye"></i></button>
                <button class="action-btn btn-edit view-logs-btn" data-adminid="${admin.admin_id}"><i class="fas fa-history"></i></button>
                <button class="action-btn btn-edit edit-admin-btn" data-adminid="${admin.admin_id}"><i class="fas fa-edit"></i></button>
                <button class="action-btn btn-approve enable-admin-btn" data-adminid="${admin.admin_id}"><i class="fas fa-check"></i></button>
            `;

            return `
                <tr data-adminid="${admin.admin_id}" data-status="${admin.status}" data-role="${admin.role}" data-name="${admin.name}" data-email="${admin.email}">
                    <td>${admin.name}</td>
                    <td>${admin.email}</td>
                    <td><span class="badge badge-success">${admin.role}</span></td>
                    <td>${admin.designation}</td>
                    <td>${statusBadge}</td>
                    <td>${admin.last_login}</td>
                    <td>
                        <div class="action-buttons">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Function to read file as Base64
    function readFileAsBase64(fileInput, callback) {
        const file = fileInput.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB.');
                fileInput.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                callback(e.target.result);
            };
            reader.onerror = function () {
                alert('Error reading file.');
            };
            reader.readAsDataURL(file);
        } else {
            callback(null);
        }
    }

    // Event delegation for action buttons
    document.addEventListener('click', function (e) {
        if (e.target.closest('a[href]') && !e.target.closest('.action-buttons')) {
            return;
        }

        const target = e.target.closest('.view-details-btn, .view-logs-btn, .edit-admin-btn, .disable-admin-btn, .enable-admin-btn');

        if (!target) return;

        e.preventDefault();

        const adminId = target.getAttribute('data-adminid');

        console.log('Button clicked:', { adminId, classList: target.className });

        try {
            if (target.classList.contains('view-details-btn')) {
                viewAdminDetails(adminId);
            } else if (target.classList.contains('view-logs-btn')) {
                viewAdminLogs(adminId);
            } else if (target.classList.contains('edit-admin-btn')) {
                editAdmin(adminId);
            } else if (target.classList.contains('disable-admin-btn')) {
                disableAdminModal(adminId);
            } else if (target.classList.contains('enable-admin-btn')) {
                enableAdmin(adminId);
            }
        } catch (error) {
            console.error('Error handling button click:', error);
            alert('Error: ' + error.message);
        }
    });

    // Add new admin
    document.getElementById('saveNewAdmin').addEventListener('click', async function () {
        const name = document.getElementById('addAdminName').value.trim();
        const email = document.getElementById('addAdminEmail').value.trim();
        const password = document.getElementById('addAdminPassword').value;
        const roleId = document.getElementById('addAdminRole').value;
        const designationId = document.getElementById('addAdminDesignation').value || null;
        const phone = document.getElementById('addAdminPhone').value.trim();
        const address = document.getElementById('addAdminAddress').value.trim();
        const notes = document.getElementById('addAdminNotes').value.trim();
        const profilePicInput = document.getElementById('addAdminProfilePic');

        if (!name || !email || !password || !roleId) {
            alert('Name, Email, Password, and Role are required.');
            return;
        }

        readFileAsBase64(profilePicInput, async function (profilePic) {
            const permissions = getSelectedPermissions('add');

            const data = {
                name,
                email,
                password,
                role_id: roleId,
                designation_id: designationId,
                phone,
                address,
                notes,
                profile_pic: profilePic || '',
                permissions: permissions
            };

            const result = await apiCall('admin_profile_api', 'add_admin', data, 'POST');

            if (result.success) {
                alert(result.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('addAdminModal'));
                modal.hide();
                document.getElementById('addAdminForm').reset();
                deselectAllPermissions('add');
                loadAdmins();
            } else {
                alert('Error: ' + result.message);
            }
        });
    });

    // Edit admin
    async function editAdmin(adminId) {
        currentEditAdminId = adminId;

        const url = `api/admin_profile_api.php?action=get_admin_details&admin_id=${adminId}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const admin = result.data;
            document.getElementById('editAdminName').value = admin.name;
            document.getElementById('editAdminEmail').value = admin.email;

            // Set role
            const roleOption = rolesData.find(r => r.role_name === admin.role);
            if (roleOption) {
                document.getElementById('editAdminRole').value = roleOption.role_id;
            }

            // Set designation
            const designationOption = designationsData.find(d => d.designation_name === admin.designation);
            if (designationOption) {
                document.getElementById('editAdminDesignation').value = designationOption.designation_id;
            }

            document.getElementById('editAdminPhone').value = admin.phone;
            document.getElementById('editAdminAddress').value = admin.address;
            document.getElementById('editAdminNotes').value = admin.notes;
            document.getElementById('editAdminProfilePic').value = '';

            // Load user permissions
            const permResponse = await fetch(`api/permissions_api.php?action=get_user_permissions&user_id=${adminId}`);
            const permResult = await permResponse.json();
            if (permResult.success) {
                setSelectedPermissions('edit', permResult.data);
            }
        }

        new bootstrap.Modal(document.getElementById('editAdminModal')).show();
    }

    document.getElementById('updateAdmin').addEventListener('click', async function () {
        const name = document.getElementById('editAdminName').value.trim();
        const email = document.getElementById('editAdminEmail').value.trim();
        const roleId = document.getElementById('editAdminRole').value;
        const designationId = document.getElementById('editAdminDesignation').value || null;
        const phone = document.getElementById('editAdminPhone').value.trim();
        const address = document.getElementById('editAdminAddress').value.trim();
        const notes = document.getElementById('editAdminNotes').value.trim();
        const password = document.getElementById('editAdminPassword').value.trim();
        const profilePicInput = document.getElementById('editAdminProfilePic');

        if (!name || !email || !roleId) {
            alert('Name, Email, and Role are required.');
            return;
        }

        readFileAsBase64(profilePicInput, async function (profilePic) {
            const permissions = getSelectedPermissions('edit');

            const data = {
                admin_id: currentEditAdminId,
                name,
                email,
                role_id: roleId,
                designation_id: designationId,
                phone,
                address,
                notes,
                password: password || undefined,
                profile_pic: profilePic || undefined,
                permissions: permissions
            };

            const result = await apiCall('admin_profile_api', 'update_admin', data, 'POST');

            if (result.success) {
                alert(result.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('editAdminModal'));
                modal.hide();
                loadAdmins();
            } else {
                alert('Error: ' + result.message);
            }
        });
    });

    // Disable admin modal
    function disableAdminModal(adminId) {
        currentEditAdminId = adminId;
        const row = document.querySelector(`tr[data-adminid="${adminId}"]`);
        const adminName = row ? row.getAttribute('data-name') : 'this admin';
        document.getElementById('disableConfirmText').textContent = `Disable account for ${adminName}?`;
        new bootstrap.Modal(document.getElementById('disableAdminModal')).show();
    }

    document.getElementById('confirmDisableAdmin').addEventListener('click', async function () {
        const reason = document.getElementById('disableReason').value;
        const notes = document.getElementById('disableNotes').value.trim();

        const data = {
            admin_id: currentEditAdminId,
            reason,
            notes
        };

        const result = await apiCall('admin_profile_api', 'disable_admin', data, 'POST');

        if (result.success) {
            alert(result.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('disableAdminModal'));
            modal.hide();
            document.getElementById('disableReason').value = 'inactivity';
            document.getElementById('disableNotes').value = '';
            loadAdmins();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Enable admin
    async function enableAdmin(adminId) {
        if (confirm('Enable this admin?')) {
            const data = { admin_id: adminId };
            const result = await apiCall('admin_profile_api', 'enable_admin', data, 'POST');

            if (result.success) {
                alert(result.message);
                loadAdmins();
            } else {
                alert('Error: ' + result.message);
            }
        }
    }

    // View admin details
    async function viewAdminDetails(adminId) {
        const url = `api/admin_profile_api.php?action=get_admin_details&admin_id=${adminId}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const admin = result.data;
            document.getElementById('viewAdminName').textContent = admin.name;
            document.getElementById('viewAdminEmail').textContent = admin.email;
            document.getElementById('viewAdminPhone').textContent = admin.phone || 'N/A';
            document.getElementById('viewAdminAddress').textContent = admin.address || 'N/A';
            document.getElementById('viewAdminRole').textContent = admin.role;
            document.getElementById('viewAdminDesignation').textContent = admin.designation || 'N/A';
            document.getElementById('viewAdminStatus').textContent = admin.status.charAt(0).toUpperCase() + admin.status.slice(1);
            document.getElementById('viewAdminDateJoined').textContent = admin.date_joined || 'N/A';
            document.getElementById('viewAdminLastLogin').textContent = admin.last_login || 'N/A';
            document.getElementById('viewAdminLastActivity').textContent = admin.last_activity || 'N/A';
            document.getElementById('viewAdminNotes').textContent = admin.notes || 'None';

            const profilePic = document.getElementById('adminProfilePic');
            if (admin.profile_pic) {
                profilePic.src = admin.profile_pic;
                profilePic.style.display = 'block';
            } else {
                profilePic.src = 'https://via.placeholder.com/150?text=No+Image';
                profilePic.style.display = 'block';
            }

            new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
        } else {
            alert('Error: ' + result.message);
        }
    }

    // View admin logs
    async function viewAdminLogs(adminId) {
        const url = `api/admin_profile_api.php?action=get_admin_logs&admin_id=${adminId}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.success) {
            const logsContent = document.getElementById('logsContent');

            if (result.data.length === 0) {
                logsContent.innerHTML = '<p class="text-muted">No activity logs found.</p>';
            } else {
                logsContent.innerHTML = `
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${result.data.map(log => `
                                <tr>
                                    <td>${log.timestamp}</td>
                                    <td><span class="badge badge-info">${log.action}</span></td>
                                    <td>${log.description}</td>
                                    <td>${log.ip_address}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            }

            new bootstrap.Modal(document.getElementById('viewLogsModal')).show();
        } else {
            alert('Error: ' + result.message);
        }
    }

    // Apply filters
    window.applyFilters = function () {
        const search = document.getElementById('searchAdmins').value.trim();
        const role = document.getElementById('filterRole').value;
        const status = document.getElementById('filterStatus').value;
        loadAdmins(search, role, status);
    };

    // Real-time search
    document.getElementById('searchAdmins').addEventListener('keyup', applyFilters);

    // Initialize
    loadRolesAndDesignations().then(() => {
        loadAdmins();
    });

    console.log('Admin Profile script fully initialized');
});
