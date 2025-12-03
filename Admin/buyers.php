<?php
// Include session timeout check
require_once 'session_check.php';
require_once 'api/DBcon.php';

$active = 'buyers';

// Fetch states for modals
$states = [];
$stateStmt = $conn->prepare("SELECT state_id, state_name FROM states");
$stateStmt->execute();
$result = $stateStmt->get_result();
while ($row = $result->fetch_assoc()) {
    $states[] = $row;
}
$stateStmt->close();

// Fetch cities for modals
$cities = [];
$cityStmt = $conn->prepare("SELECT city_id, city_name, state_id FROM cities");
$cityStmt->execute();
$result = $cityStmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}
$cityStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        <h2 class="mb-4">Buyers Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Buyers</h4>
                <div class="table-actions">
                    <button class="btn btn-agri-blue" onclick="exportBuyers()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" id="searchName" placeholder="Search by name...">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="new">New Buyer</option>
                    <option value="disabled">Disabled</option>
                </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="filterLocation" placeholder="Location...">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-agri" onclick="loadBuyers(1)">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="buyersTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Buyer Type</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registration Date</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="buyersTableBody">
                        <!-- Populated via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Buyers pagination">
                <ul class="pagination justify-content-end" id="pagination">
                    <!-- Populated via AJAX -->
                </ul>
            </nav>
        </div>
    </main>

    <!-- Edit Buyer Modal (View Only) -->
    <div class="modal fade" id="editBuyerModal" tabindex="-1" aria-labelledby="editBuyerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBuyerModalLabel">Buyer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBuyerForm">
                        <input type="hidden" id="editBuyerId" name="buyer_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="editFirstName" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastName" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="editPhone" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editGender" class="form-label">Gender</label>
                                    <input type="text" class="form-control" id="editGender" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editBuyerType" class="form-label">Buyer Type</label>
                                    <input type="text" class="form-control" id="editBuyerType" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="editAddress" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editState" class="form-label">State</label>
                                    <input type="text" class="form-control" id="editState" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCity" class="form-label">City</label>
                                    <input type="text" class="form-control" id="editCity" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editRegistrationDate" class="form-label">Registration Date</label>
                                    <input type="text" class="form-control" id="editRegistrationDate" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDisabledReason" class="form-label">Disable Reason</label>
                            <textarea class="form-control" id="editDisabledReason" rows="3" readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <div>
                                <img id="editProfilePicturePreview" class="profile-pic-preview" src="" alt="No profile picture" style="display: none;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Buyer Modal -->
    <div class="modal fade" id="disableBuyerModal" tabindex="-1" aria-labelledby="disableBuyerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableBuyerModalLabel">Disable Buyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="disableBuyerForm">
                        <input type="hidden" id="disableBuyerId" name="id">
                        <div class="mb-3">
                            <label for="disableReason" class="form-label">Reason for Disabling</label>
                            <textarea class="form-control" id="disableReason" name="disable_reason" rows="4" required placeholder="Enter the reason for disabling this buyer..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="disableBuyer()">Disable Buyer</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-pic-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        .badge-disabled {
            background-color: #dc3545;
        }
        .badge-verified {
            background-color: #198754;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
        // Load buyers via AJAX
        function loadBuyers(page = 1) {
            const search = document.getElementById('searchName').value;
            const status = document.getElementById('filterStatus').value;
            const location = document.getElementById('filterLocation').value;

            fetch(`api/buyers_api.php?page=${page}&search=${encodeURIComponent(search)}&status=${status}&location=${encodeURIComponent(location)}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('buyersTableBody');
                        tbody.innerHTML = data.data.buyers.map(buyer => {
                            let statusBadge = '';
                            let statusText = '';
                            
                            if (buyer.is_verify == 2) {
                                statusBadge = 'badge-disabled';
                                statusText = 'Disabled';
                            } else if (buyer.is_verify == 1) {
                                statusBadge = 'badge-verified';
                                statusText = 'Active';
                            } else {
                                statusBadge = 'badge-pending';
                                statusText = 'New Buyer';
                            }

                            return `
                                <tr>
                                    <td>${buyer.firstname} ${buyer.lastname}</td>
                                    <td>${buyer.buyer_type || 'N/A'}</td>
                                    <td>${buyer.email}</td>
                                    <td>${buyer.phone}</td>
                                    <td>${new Date(buyer.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                                    <td>${buyer.state_name || 'N/A'}, ${buyer.city_name || 'N/A'}</td>
                                    <td><span class="badge ${statusBadge}">${statusText}</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit edit-buyer-btn btn btn-sm btn-info text-white" data-id="${buyer.buyer_id}" title="View Details"><i class="fas fa-eye"></i></button>
                                            ${buyer.is_verify != 2 ? `
                                                <button class="action-btn btn-approve approve-buyer-btn btn btn-sm btn-success ${buyer.is_verify == 1 ? 'disabled' : ''}" data-id="${buyer.buyer_id}" title="Activate"><i class="fas fa-check"></i></button>
                                                <button class="action-btn btn-disable disable-buyer-btn btn btn-sm btn-danger" data-id="${buyer.buyer_id}" title="Disable"><i class="fas fa-ban"></i></button>
                                            ` : ''}
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');

                        // Update pagination
                        const pagination = document.getElementById('pagination');
                        pagination.innerHTML = '';
                        for (let i = 1; i <= data.data.pages; i++) {
                            pagination.innerHTML += `
                                <li class="page-item ${i === data.data.current_page ? 'active' : ''}">
                                    <a class="page-link" href="#" onclick="loadBuyers(${i}); return false;">${i}</a>
                                </li>
                            `;
                        }
                    } else {
                        console.error('Error loading buyers:', data.error);
                        alert('Error loading buyers: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error fetching buyers: ' + error.message);
                });
        }

        // Disable buyer
        function disableBuyer() {
            const form = document.getElementById('disableBuyerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = new FormData(form);
            data.append('action', 'disable');

            fetch('api/buyers_api.php', {
                method: 'POST',
                body: data
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(json => {
                    if (json.success) {
                        alert('Buyer disabled successfully!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('disableBuyerModal'));
                        modal.hide();
                        form.reset();
                        loadBuyers();
                    } else {
                        console.error('Error disabling buyer:', json.error);
                        alert('Error disabling buyer: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error disabling buyer: ' + error.message);
                });
        }

        // Export buyers to Excel
        function exportBuyers() {
            if (typeof XLSX === 'undefined') {
                console.error('SheetJS library not loaded');
                alert('Error: Excel export library failed to load. Please try again or disable browser extensions.');
                return;
            }

            const search = document.getElementById('searchName').value;
            const status = document.getElementById('filterStatus').value;
            const location = document.getElementById('filterLocation').value;

            fetch(`api/buyers_api.php?action=export&search=${encodeURIComponent(search)}&status=${status}&location=${encodeURIComponent(location)}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(json => {
                    if (json.success) {
                        if (!json.data || json.data.length === 0) {
                            alert('No buyers found to export.');
                            return;
                        }

                        const worksheet = XLSX.utils.json_to_sheet(json.data);
                        worksheet['!cols'] = [
                            { wch: 20 }, // Name
                            { wch: 15 }, // Buyer Type
                            { wch: 30 }, // Email
                            { wch: 15 }, // Phone
                            { wch: 10 }, // Gender
                            { wch: 25 }, // Address
                            { wch: 15 }, // Registration Date
                            { wch: 15 }, // State
                            { wch: 15 }, // City
                            { wch: 10 }  // Status
                        ];
                        const workbook = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(workbook, worksheet, 'Buyers');

                        const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                        const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const today = new Date();
                        const dateStr = today.toISOString().split('T')[0];
                        const filename = `buyers_export_${dateStr}.xlsx`;

                        if (typeof saveAs === 'function') {
                            saveAs(blob, filename);
                        } else {
                            const url = window.URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            window.URL.revokeObjectURL(url);
                        }

                        alert('Buyers exported successfully!');
                    } else {
                        console.error('Error exporting buyers:', json.error);
                        alert('Error exporting buyers: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error exporting buyers: ' + error.message);
                });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadBuyers();

            // Sidebar toggle
            const headerToggle = document.getElementById('headerToggle');
            if (headerToggle) {
                headerToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                    document.querySelector('.main-content').classList.toggle('expanded');
                });
            }

            // Action buttons
            document.getElementById('buyersTableBody').addEventListener('click', function(e) {
                const target = e.target.closest('.action-btn');
                if (!target) return;

                const id = target.dataset.id;
                if (target.classList.contains('edit-buyer-btn')) {
                    fetch(`api/buyers_api.php?action=get_details&id=${id}`, {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(json => {
                            if (json.success) {
                                const buyer = json.data;
                                document.getElementById('editBuyerId').value = buyer.buyer_id || '';
                                document.getElementById('editFirstName').value = buyer.firstname || '';
                                document.getElementById('editLastName').value = buyer.lastname || '';
                                document.getElementById('editEmail').value = buyer.email || '';
                                document.getElementById('editPhone').value = buyer.phone || '';
                                document.getElementById('editGender').value = buyer.gender || '';
                                document.getElementById('editBuyerType').value = buyer.buyer_type || 'N/A';
                                document.getElementById('editAddress').value = buyer.address || '';
                                document.getElementById('editState').value = buyer.state_name || '';
                                document.getElementById('editCity').value = buyer.city_name || '';
                                document.getElementById('editRegistrationDate').value = new Date(buyer.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                                document.getElementById('editDisabledReason').value = buyer.disable_reason || '';
                                
                                const preview = document.getElementById('editProfilePicturePreview');
                                if (buyer.profile_picture) {
                                    preview.src = `../${buyer.profile_picture}`;
                                    preview.style.display = 'block';
                                } else {
                                    preview.src = '';
                                    preview.style.display = 'none';
                                }
                                
                                const modal = new bootstrap.Modal(document.getElementById('editBuyerModal'));
                                modal.show();
                            } else {
                                console.error('Error fetching buyer details:', json.error);
                                alert('Error fetching buyer details: ' + json.error);
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('Error fetching buyer details: ' + error.message);
                        });
                } else if (target.classList.contains('approve-buyer-btn')) {
                    if (confirm('Activate this buyer?')) {
                        const data = new FormData();
                        data.append('action', 'approve');
                        data.append('id', id);
                        fetch('api/buyers_api.php', {
                            method: 'POST',
                            body: data
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(json => {
                                if (json.success) {
                                    alert('Buyer activated!');
                                    loadBuyers();
                                } else {
                                    console.error('Error activating buyer:', json.error);
                                    alert('Error activating buyer: ' + json.error);
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                alert('Error activating buyer: ' + error.message);
                            });
                    }
                } else if (target.classList.contains('disable-buyer-btn')) {
                    document.getElementById('disableBuyerId').value = id;
                    const modal = new bootstrap.Modal(document.getElementById('disableBuyerModal'));
                    modal.show();
                }
            });
        });
    </script>
</body>
</html>