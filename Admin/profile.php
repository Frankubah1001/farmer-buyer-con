<?php
// profile.php - Admin Management with Enhanced View Details Modal
// profile.php - Admin Management with Enhanced View Details Modal
$active = 'profile';
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
                        <!-- Sample data - In real app, load from DB -->
                        <tr data-adminid="ADMIN001" data-status="active" data-role="super" data-name="Admin User" data-email="admin@example.com">
                            <td>Admin User</td>
                            <td>admin@example.com</td>
                            <td><span class="badge badge-primary">Super Admin</span></td>
                            <td>Platform Owner</td>
                            <td><span class="badge badge-approved">Active</span></td>
                            <td>07 Oct 2023, 14:30 PM</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-details-btn" data-adminid="ADMIN001"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn btn-edit view-logs-btn" data-adminid="ADMIN001"><i class="fas fa-history"></i></button>
                                    <button class="action-btn btn-edit edit-admin-btn" data-adminid="ADMIN001"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-disable disable-admin-btn" data-adminid="ADMIN001"><i class="fas fa-ban"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-adminid="ADMIN002" data-status="active" data-role="moderator" data-name="Operations Manager" data-email="ops@agriadmin.com">
                            <td>Operations Manager</td>
                            <td>ops@agriadmin.com</td>
                            <td><span class="badge badge-info">Moderator</span></td>
                            <td>Orders & Reports</td>
                            <td><span class="badge badge-approved">Active</span></td>
                            <td>06 Oct 2023, 11:15 AM</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-details-btn" data-adminid="ADMIN002"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn btn-edit view-logs-btn" data-adminid="ADMIN002"><i class="fas fa-history"></i></button>
                                    <button class="action-btn btn-edit edit-admin-btn" data-adminid="ADMIN002"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-disable disable-admin-btn" data-adminid="ADMIN002"><i class="fas fa-ban"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-adminid="ADMIN003" data-status="disabled" data-role="viewer" data-name="Support Viewer" data-email="support@agriadmin.com">
                            <td>Support Viewer</td>
                            <td>support@agriadmin.com</td>
                            <td><span class="badge badge-secondary">Viewer</span></td>
                            <td>Read-Only Support</td>
                            <td><span class="badge badge-disabled">Disabled</span></td>
                            <td>03 Oct 2023, 09:45 AM</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-details-btn" data-adminid="ADMIN003"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn btn-edit view-logs-btn" data-adminid="ADMIN003"><i class="fas fa-history"></i></button>
                                    <button class="action-btn btn-edit edit-admin-btn" data-adminid="ADMIN003"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-approve enable-admin-btn" data-adminid="ADMIN003"><i class="fas fa-check"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr data-adminid="ADMIN004" data-status="active" data-role="moderator" data-name="Finance Admin" data-email="finance@agriadmin.com">
                            <td>Finance Admin</td>
                            <td>finance@agriadmin.com</td>
                            <td><span class="badge badge-info">Moderator</span></td>
                            <td>Payments & Invoices</td>
                            <td><span class="badge badge-approved">Active</span></td>
                            <td>07 Oct 2023, 10:20 AM</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-details-btn" data-adminid="ADMIN004"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn btn-edit view-logs-btn" data-adminid="ADMIN004"><i class="fas fa-history"></i></button>
                                    <button class="action-btn btn-edit edit-admin-btn" data-adminid="ADMIN004"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-disable disable-admin-btn" data-adminid="ADMIN004"><i class="fas fa-ban"></i></button>
                                </div>
                            </td>
                        </tr>
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
                                        <option value="super">Super Admin</option>
                                        <option value="moderator">Moderator</option>
                                        <option value="viewer">Viewer</option>
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
                            <input type="text" class="form-control" id="addAdminDesignation" placeholder="e.g., Operations Manager">
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
                                        <option value="super">Super Admin</option>
                                        <option value="moderator">Moderator</option>
                                        <option value="viewer">Viewer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAdminDesignation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="editAdminDesignation">
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
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Admin Profile script starting');

            // Initialize settingsData with enhanced admin profile details
            let settingsData = JSON.parse(localStorage.getItem('agriAdminSettings')) || {
                admins: {
                    'ADMIN001': { 
                        name: 'Admin User', 
                        email: 'admin@example.com', 
                        phone: '+2348031234567', 
                        address: '123 Admin St, Lagos', 
                        role: 'super', 
                        designation: 'Platform Owner', 
                        status: 'active', 
                        dateJoined: '01 Jan 2023', 
                        lastLogin: '07 Oct 2023, 14:30 PM', 
                        lastActivity: 'Updated settings on 07 Oct 2023', 
                        notes: 'Primary administrator', 
                        profilePic: '' 
                    },
                    'ADMIN002': { 
                        name: 'Operations Manager', 
                        email: 'ops@agriadmin.com', 
                        phone: '+2348059876543', 
                        address: '456 Ops Rd, Abuja', 
                        role: 'moderator', 
                        designation: 'Orders & Reports', 
                        status: 'active', 
                        dateJoined: '15 Mar 2023', 
                        lastLogin: '06 Oct 2023, 11:15 AM', 
                        lastActivity: 'Processed order ORD002 on 06 Oct 2023', 
                        notes: 'Manages order workflows', 
                        profilePic: '' 
                    },
                    'ADMIN003': { 
                        name: 'Support Viewer', 
                        email: 'support@agriadmin.com', 
                        phone: '+2348071112223', 
                        address: '789 Support Ave, Port Harcourt', 
                        role: 'viewer', 
                        designation: 'Read-Only Support', 
                        status: 'disabled', 
                        dateJoined: '10 Aug 2023', 
                        lastLogin: '03 Oct 2023, 09:45 AM', 
                        lastActivity: 'Viewed reports on 03 Oct 2023', 
                        notes: 'Temporary support staff', 
                        profilePic: '' 
                    },
                    'ADMIN004': { 
                        name: 'Finance Admin', 
                        email: 'finance@agriadmin.com', 
                        phone: '+2348095556667', 
                        address: '101 Finance Blvd, Kano', 
                        role: 'moderator', 
                        designation: 'Payments & Invoices', 
                        status: 'active', 
                        dateJoined: '20 Jun 2023', 
                        lastLogin: '07 Oct 2023, 10:20 AM', 
                        lastActivity: 'Processed payout on 07 Oct 2023', 
                        notes: 'Handles financial transactions', 
                        profilePic: '' 
                    }
                }
            };

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

            // Admin module-specific variables
            let currentEditAdminId = '';

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
                    reader.onload = function(e) {
                        callback(e.target.result);
                    };
                    reader.onerror = function() {
                        alert('Error reading file.');
                    };
                    reader.readAsDataURL(file);
                } else {
                    callback(null);
                }
            }

            // Event delegation for action buttons
            document.addEventListener('click', function(e) {
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
            document.getElementById('saveNewAdmin').addEventListener('click', function() {
                const name = document.getElementById('addAdminName').value.trim();
                const email = document.getElementById('addAdminEmail').value.trim();
                const password = document.getElementById('addAdminPassword').value;
                const role = document.getElementById('addAdminRole').value;
                const designation = document.getElementById('addAdminDesignation').value.trim();
                const phone = document.getElementById('addAdminPhone').value.trim();
                const address = document.getElementById('addAdminAddress').value.trim();
                const notes = document.getElementById('addAdminNotes').value.trim();
                const profilePicInput = document.getElementById('addAdminProfilePic');

                if (!name || !email || !password || !role) {
                    alert('Name, Email, Password, and Role are required.');
                    return;
                }

                readFileAsBase64(profilePicInput, function(profilePic) {
                    const newId = `ADMIN${Math.floor(Math.random() * 1000) + 5}`;
                    const tableBody = document.getElementById('adminsTableBody');
                    const newRow = tableBody.insertRow();
                    newRow.setAttribute('data-adminid', newId);
                    newRow.setAttribute('data-status', 'active');
                    newRow.setAttribute('data-role', role);
                    newRow.setAttribute('data-name', name);
                    newRow.setAttribute('data-email', email);
                    newRow.innerHTML = `
                        <td>${name}</td>
                        <td>${email}</td>
                        <td><span class="badge ${role === 'super' ? 'badge-primary' : role === 'moderator' ? 'badge-info' : 'badge-secondary'}">${role.charAt(0).toUpperCase() + role.slice(1)}</span></td>
                        <td>${designation || 'N/A'}</td>
                        <td><span class="badge badge-approved">Active</span></td>
                        <td>${new Date().toLocaleString()}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-edit view-details-btn" data-adminid="${newId}"><i class="fas fa-eye"></i></button>
                                <button class="action-btn btn-edit view-logs-btn" data-adminid="${newId}"><i class="fas fa-history"></i></button>
                                <button class="action-btn btn-edit edit-admin-btn" data-adminid="${newId}"><i class="fas fa-edit"></i></button>
                                <button class="action-btn btn-disable disable-admin-btn" data-adminid="${newId}"><i class="fas fa-ban"></i></button>
                            </div>
                        </td>
                    `;

                    settingsData.admins[newId] = {
                        name,
                        email,
                        phone: phone || 'N/A',
                        address: address || 'N/A',
                        role,
                        designation: designation || 'N/A',
                        status: 'active',
                        dateJoined: new Date().toLocaleDateString(),
                        lastLogin: new Date().toLocaleString(),
                        lastActivity: 'Account created on ' + new Date().toLocaleDateString(),
                        notes,
                        profilePic: profilePic || ''
                    };
                    localStorage.setItem('agriAdminSettings', JSON.stringify(settingsData));

                    const modal = bootstrap.Modal.getInstance(document.getElementById('addAdminModal'));
                    modal.hide();
                    document.getElementById('addAdminForm').reset();
                    document.getElementById('addAdminProfilePic').value = '';
                    alert('New admin added successfully!');
                });
            });

            // Edit admin
            function editAdmin(adminId) {
                currentEditAdminId = adminId;
                const admin = settingsData.admins[adminId];
                if (admin) {
                    document.getElementById('editAdminName').value = admin.name;
                    document.getElementById('editAdminEmail').value = admin.email;
                    document.getElementById('editAdminRole').value = admin.role;
                    document.getElementById('editAdminDesignation').value = admin.designation;
                    document.getElementById('editAdminPhone').value = admin.phone;
                    document.getElementById('editAdminAddress').value = admin.address;
                    document.getElementById('editAdminNotes').value = admin.notes;
                    document.getElementById('editAdminProfilePic').value = '';
                }
                new bootstrap.Modal(document.getElementById('editAdminModal')).show();
            }

            document.getElementById('updateAdmin').addEventListener('click', function() {
                const name = document.getElementById('editAdminName').value.trim();
                const email = document.getElementById('editAdminEmail').value.trim();
                const role = document.getElementById('editAdminRole').value;
                const designation = document.getElementById('editAdminDesignation').value.trim();
                const phone = document.getElementById('editAdminPhone').value.trim();
                const address = document.getElementById('editAdminAddress').value.trim();
                const notes = document.getElementById('editAdminNotes').value.trim();
                const password = document.getElementById('editAdminPassword').value.trim();
                const profilePicInput = document.getElementById('editAdminProfilePic');

                if (!name || !email || !role) {
                    alert('Name, Email, and Role are required.');
                    return;
                }

                readFileAsBase64(profilePicInput, function(profilePic) {
                    const rows = document.querySelectorAll('#adminsTableBody tr[data-adminid]');
                    rows.forEach(row => {
                        if (row.getAttribute('data-adminid') === currentEditAdminId) {
                            row.cells[0].textContent = name;
                            row.cells[1].textContent = email;
                            row.cells[2].innerHTML = `<span class="badge ${role === 'super' ? 'badge-primary' : role === 'moderator' ? 'badge-info' : 'badge-secondary'}">${role.charAt(0).toUpperCase() + role.slice(1)}</span>`;
                            row.cells[3].textContent = designation || 'N/A';
                            row.setAttribute('data-name', name);
                            row.setAttribute('data-email', email);
                            row.setAttribute('data-role', role);
                        }
                    });

                    settingsData.admins[currentEditAdminId] = {
                        ...settingsData.admins[currentEditAdminId],
                        name,
                        email,
                        phone: phone || 'N/A',
                        address: address || 'N/A',
                        role,
                        designation: designation || 'N/A',
                        notes,
                        profilePic: profilePic || settingsData.admins[currentEditAdminId].profilePic,
                        lastActivity: 'Profile updated on ' + new Date().toLocaleDateString()
                    };
                    localStorage.setItem('agriAdminSettings', JSON.stringify(settingsData));

                    const modal = bootstrap.Modal.getInstance(document.getElementById('editAdminModal'));
                    modal.hide();
                    document.getElementById('editAdminProfilePic').value = '';
                    alert('Admin updated successfully!');
                });
            });

            // Disable admin modal
            function disableAdminModal(adminId) {
                currentEditAdminId = adminId;
                const admin = settingsData.admins[adminId];
                document.getElementById('disableConfirmText').textContent = `Disable account for ${admin.name}?`;
                new bootstrap.Modal(document.getElementById('disableAdminModal')).show();
            }

            document.getElementById('confirmDisableAdmin').addEventListener('click', function() {
                const reason = document.getElementById('disableReason').value;
                const notes = document.getElementById('disableNotes').value.trim();

                const rows = document.querySelectorAll('#adminsTableBody tr[data-adminid]');
                rows.forEach(row => {
                    if (row.getAttribute('data-adminid') === currentEditAdminId) {
                        row.cells[4].innerHTML = '<span class="badge badge-disabled">Disabled</span>';
                        row.cells[6].innerHTML = `
                            <div class="action-buttons">
                                <button class="action-btn btn-edit view-details-btn" data-adminid="${currentEditAdminId}"><i class="fas fa-eye"></i></button>
                                <button class="action-btn btn-edit view-logs-btn" data-adminid="${currentEditAdminId}"><i class="fas fa-history"></i></button>
                                <button class="action-btn btn-edit edit-admin-btn" data-adminid="${currentEditAdminId}"><i class="fas fa-edit"></i></button>
                                <button class="action-btn btn-approve enable-admin-btn" data-adminid="${currentEditAdminId}"><i class="fas fa-check"></i></button>
                            </div>
                        `;
                        row.setAttribute('data-status', 'disabled');
                    }
                });

                settingsData.admins[currentEditAdminId].status = 'disabled';
                settingsData.admins[currentEditAdminId].lastActivity = `Account disabled on ${new Date().toLocaleDateString()}`;
                localStorage.setItem('agriAdminSettings', JSON.stringify(settingsData));

                const modal = bootstrap.Modal.getInstance(document.getElementById('disableAdminModal'));
                modal.hide();
                document.getElementById('disableReason').value = 'other';
                document.getElementById('disableNotes').value = '';
                alert(`Admin disabled. Reason: ${reason}. Notes: ${notes || 'None'}.`);
            });

            // Enable admin
            function enableAdmin(adminId) {
                if (confirm('Enable this admin?')) {
                    const rows = document.querySelectorAll('#adminsTableBody tr[data-adminid]');
                    rows.forEach(row => {
                        if (row.getAttribute('data-adminid') === adminId) {
                            row.cells[4].innerHTML = '<span class="badge badge-approved">Active</span>';
                            row.cells[6].innerHTML = `
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit view-details-btn" data-adminid="${adminId}"><i class="fas fa-eye"></i></button>
                                    <button class="action-btn btn-edit view-logs-btn" data-adminid="${adminId}"><i class="fas fa-history"></i></button>
                                    <button class="action-btn btn-edit edit-admin-btn" data-adminid="${adminId}"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-disable disable-admin-btn" data-adminid="${adminId}"><i class="fas fa-ban"></i></button>
                                </div>
                            `;
                            row.setAttribute('data-status', 'active');
                        }
                    });

                    settingsData.admins[adminId].status = 'active';
                    settingsData.admins[adminId].lastActivity = `Account enabled on ${new Date().toLocaleDateString()}`;
                    localStorage.setItem('agriAdminSettings', JSON.stringify(settingsData));
                    alert('Admin enabled.');
                }
            }

            // View admin details
            function viewAdminDetails(adminId) {
                const admin = settingsData.admins[adminId];
                if (admin) {
                    document.getElementById('viewAdminName').textContent = admin.name;
                    document.getElementById('viewAdminEmail').textContent = admin.email;
                    document.getElementById('viewAdminPhone').textContent = admin.phone || 'N/A';
                    document.getElementById('viewAdminAddress').textContent = admin.address || 'N/A';
                    document.getElementById('viewAdminRole').textContent = admin.role.charAt(0).toUpperCase() + admin.role.slice(1);
                    document.getElementById('viewAdminDesignation').textContent = admin.designation || 'N/A';
                    document.getElementById('viewAdminStatus').textContent = admin.status.charAt(0).toUpperCase() + admin.status.slice(1);
                    document.getElementById('viewAdminDateJoined').textContent = admin.dateJoined || 'N/A';
                    document.getElementById('viewAdminLastLogin').textContent = admin.lastLogin || 'N/A';
                    document.getElementById('viewAdminLastActivity').textContent = admin.lastActivity || 'N/A';
                    document.getElementById('viewAdminNotes').textContent = admin.notes || 'None';
                    const profilePic = document.getElementById('adminProfilePic');
                    if (admin.profilePic) {
                        profilePic.src = admin.profilePic;
                        profilePic.style.display = 'block';
                    } else {
                        profilePic.src = 'https://via.placeholder.com/150?text=No+Image';
                        profilePic.style.display = 'block';
                    }
                    new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
                }
            }

            // View admin logs
            function viewAdminLogs(adminId) {
                const demoLogs = {
                    'ADMIN001': [
                        { action: 'Logged in', timestamp: '07 Oct 2023, 14:30 PM', ip: '192.168.1.1', details: 'Dashboard access' },
                        { action: 'Updated Order #ORD001', timestamp: '06 Oct 2023, 16:20 PM', ip: '192.168.1.1', details: 'Status to Shipped' },
                        { action: 'Viewed Reports', timestamp: '05 Oct 2023, 10:45 AM', ip: '192.168.1.2', details: 'REP001 details' }
                    ],
                    'ADMIN002': [
                        { action: 'Logged in', timestamp: '07 Oct 2023, 11:15 AM', ip: '10.0.0.5', details: 'Orders management' },
                        { action: 'Resolved Report REP002', timestamp: '06 Oct 2023, 15:30 PM', ip: '10.0.0.5', details: 'Sent warning' },
                        { action: 'Added Transport Provider', timestamp: '04 Oct 2023, 13:00 PM', ip: '10.0.0.6', details: 'ABC Transport' }
                    ],
                    'ADMIN003': [
                        { action: 'Last Login', timestamp: '03 Oct 2023, 09:45 AM', ip: '172.16.0.1', details: 'Disabled since then' },
                        { action: 'Viewed Profile', timestamp: '02 Oct 2023, 14:10 PM', ip: '172.16.0.1', details: 'Self-view' }
                    ],
                    'ADMIN004': [
                        { action: 'Logged in', timestamp: '07 Oct 2023, 10:20 AM', ip: '192.168.0.10', details: 'Finance dashboard' },
                        { action: 'Processed Payout', timestamp: '06 Oct 2023, 12:45 PM', ip: '192.168.0.10', details: 'To Farmer John' }
                    ]
                };

                const logs = demoLogs[adminId] || [{ message: 'No logs found' }];
                const admin = settingsData.admins[adminId];
                const adminName = admin ? admin.name : 'Unknown';

                document.getElementById('logsContent').innerHTML = `
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Logs for ${adminName}</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Action</th>
                                            <th>Timestamp</th>
                                            <th>IP Address</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${logs.map(log => `
                                            <tr>
                                                <td><span class="badge bg-secondary">${log.action}</span></td>
                                                <td>${log.timestamp}</td>
                                                <td><span class="badge bg-info">${log.ip}</span></td>
                                                <td>${log.details}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                new bootstrap.Modal(document.getElementById('viewLogsModal')).show();
            }

            // Filter table
            function applyFilters() {
                const search = document.getElementById('searchAdmins').value.toLowerCase();
                const roleFilter = document.getElementById('filterRole').value;
                const statusFilter = document.getElementById('filterStatus').value;
                const rows = document.querySelectorAll('#adminsTableBody tr[data-adminid]');
                rows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const email = row.cells[1].textContent.toLowerCase();
                    const role = row.getAttribute('data-role');
                    const status = row.getAttribute('data-status');

                    let show = true;

                    if (search && !name.includes(search) && !email.includes(search)) {
                        show = false;
                    }
                    if (roleFilter && role !== roleFilter) {
                        show = false;
                    }
                    if (statusFilter && status !== statusFilter) {
                        show = false;
                    }

                    row.style.display = show ? '' : 'none';
                });
            }

            // Real-time search
            document.getElementById('searchAdmins').addEventListener('keyup', applyFilters);

            console.log('Admin Profile script fully initialized');
        });
    </script>
</body>
</html>