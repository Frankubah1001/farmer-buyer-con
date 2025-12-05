<?php
// Include session timeout check
require_once 'session_check.php';

// profile.php - Admin Management with Enhanced View Details Modal
// profile.php - Admin Management with Enhanced View Details Modal
$active = 'profile';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'header.php'; ?>
    <style>
        .badge-success {
            background-color:rgb(233, 167, 0);
            color: white;
        }
        
        /* Permissions Selector Styles */
        .permissions-selector {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
        }
        
        .permission-module {
            background: white;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .permission-module-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .permission-module-header input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .permission-module-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #2c3e50;
            margin: 0;
            flex: 1;
        }
        
        .permission-count {
            font-size: 0.75rem;
            color: #6c757d;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
        }
        
        .permission-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }
        
        .permission-item {
            display: flex;
            align-items: start;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .permission-item:hover {
            background-color: #f1f3f5;
        }
        
        .permission-item input[type="checkbox"] {
            margin-top: 2px;
            cursor: pointer;
        }
        
        .permission-item label {
            cursor: pointer;
            margin: 0;
            font-size: 0.875rem;
            line-height: 1.4;
            flex: 1;
        }
        
        .permission-item label small {
            display: block;
            color: #6c757d;
            font-size: 0.75rem;
        }
        
        .permission-item input[type="checkbox"]:checked + label {
            color: #0d6efd;
            font-weight: 500;
        }
        
        .permissions-selector::-webkit-scrollbar {
            width: 8px;
        }
        
        .permissions-selector::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .permissions-selector::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .permissions-selector::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .permission-module.hidden {
            display: none;
        }
    </style>
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
        <h2 class="mb-4">Admin Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Admins</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                        <i class="fas fa-plus"></i> Add New Admin
                    </button>
                    <button class="btn btn-agri-blue">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by Name or Email..." id="searchAdmins">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterRole">
                        <option value="">All Roles</option>
                        <option value="super">Super Admin</option>
                        <option value="moderator">Moderator</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-agri" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="adminsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Designation</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminsTableBody">
                    
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Admins pagination">
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

    <!-- Add New Admin Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel"><i class="fas fa-user-plus me-2"></i>Add New Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="addAdminName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="addAdminEmail" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="addAdminPassword" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminRole" class="form-label">Role</label>
                                    <select class="form-select" id="addAdminRole" required>
                                        <option value="">Select Role</option>
                                        <!-- Dynamically populated from admin_roles -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="addAdminPhone" placeholder="e.g., +234123456789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAdminAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="addAdminAddress" placeholder="e.g., 123 Main St, Lagos">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addAdminDesignation" class="form-label">Designation</label>
                            <select class="form-select" id="addAdminDesignation">
                                <option value="">Select Designation</option>
                                <!-- Dynamically populated from designations -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="addAdminProfilePic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="addAdminProfilePic" accept="image/*">
                            <div class="form-text">Upload a profile picture (JPEG, PNG, max 2MB).</div>
                        </div>
                        <div class="mb-3">
                            <label for="addAdminNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="addAdminNotes" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                        
                        <!-- Permissions Section -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Permissions</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions('add')">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions('add')">Deselect All</button>
                                </div>
                            </div>
                            <input type="text" class="form-control mb-2" id="addPermissionSearch" placeholder="Search permissions...">
                            <div class="permissions-selector" id="addPermissionsContainer">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mb-0 mt-2 text-muted">Loading permissions...</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="saveNewAdmin">Add Admin</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdminModalLabel"><i class="fas fa-edit me-2"></i>Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAdminForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="editAdminName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editAdminEmail" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminRole" class="form-label">Role</label>
                                    <select class="form-select" id="editAdminRole" required>
                                        <!-- Dynamically populated from admin_roles -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminDesignation" class="form-label">Designation</label>
                                    <select class="form-select" id="editAdminDesignation">
                                        <option value="">Select Designation</option>
                                        <!-- Dynamically populated from designations -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="editAdminPhone" placeholder="e.g., +234123456789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="editAdminAddress" placeholder="e.g., 123 Main St, Lagos">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editAdminProfilePic" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="editAdminProfilePic" accept="image/*">
                            <div class="form-text">Upload a new profile picture (JPEG, PNG, max 2MB). Leave blank to keep current.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editAdminNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="editAdminNotes" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editAdminPassword" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="editAdminPassword">
                            <div class="form-text">Password will only be updated if provided.</div>
                        </div>
                        
                        <!-- Permissions Section -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Permissions</label>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions('edit')">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllPermissions('edit')">Deselect All</button>
                                </div>
                            </div>
                            <input type="text" class="form-control mb-2" id="editPermissionSearch" placeholder="Search permissions...">
                            <div class="permissions-selector" id="editPermissionsContainer">
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mb-0 mt-2 text-muted">Loading permissions...</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="updateAdmin">Update Admin</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Admin Modal -->
    <div class="modal fade" id="disableAdminModal" tabindex="-1" aria-labelledby="disableAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="disableAdminModalLabel"><i class="fas fa-user-slash me-2"></i>Disable Admin Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="disableConfirmText">Are you sure you want to disable this admin account?</p>
                    <div class="mb-3">
                        <label for="disableReason" class="form-label">Reason</label>
                        <select class="form-select" id="disableReason">
                            <option value="inactivity">Inactivity</option>
                            <option value="violation">Policy Violation</option>
                            <option value="security">Security Issue</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="disableNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="disableNotes" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDisableAdmin">Disable</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Admin Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewDetailsModalLabel"><i class="fas fa-user me-2"></i>Admin Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img id="adminProfilePic" class="rounded-circle shadow-sm" src="" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; display: none; border: 2px solid #e9ecef;">
                        <div class="mt-2">
                            <h5 id="viewAdminName" class="mb-0"></h5>
                            <p class="text-muted mb-0" id="viewAdminDesignation"></p>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Profile Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong> <span id="viewAdminEmail"></span></p>
                                    <p><strong>Phone Number:</strong> <span id="viewAdminPhone"></span></p>
                                    <p><strong>Role:</strong> <span id="viewAdminRole"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span id="viewAdminStatus"></span></p>
                                    <p><strong>Date Joined:</strong> <span id="viewAdminDateJoined"></span></p>
                                    <p><strong>Last Login:</strong> <span id="viewAdminLastLogin"></span></p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p><strong>Address:</strong> <span id="viewAdminAddress"></span></p>
                                <p><strong>Last Activity:</strong> <span id="viewAdminLastActivity"></span></p>
                                <p><strong>Notes:</strong> <span id="viewAdminNotes"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Admin Logs Modal -->
    <div class="modal fade" id="viewLogsModal" tabindex="-1" aria-labelledby="viewLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="viewLogsModalLabel"><i class="fas fa-history me-2"></i>Admin Activity Logs</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="logsContent">
                        <!-- Dynamically populated -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/profile.js"></script>
</body>
</html>