<?php
// Include session timeout check
require_once 'session_check.php';

// Use the centralized DB connection from the api directory
require_once 'api/DBcon.php';
// farmers.php - Full farmers management page
$active = 'farmers';

// Fetch states for modals
$states = [];
// Assuming $conn is available from DBcon.php
$stateStmt = $conn->prepare("SELECT state_id, state_name FROM states");
$stateStmt->execute();
$result = $stateStmt->get_result();
while ($row = $result->fetch_assoc()) {
    $states[] = $row;
}
$stateStmt->close(); // Close statement

// Fetch cities for modals
$cities = [];
$cityStmt = $conn->prepare("SELECT city_id, city_name, state_id FROM cities");
$cityStmt->execute();
$result = $cityStmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}
$cityStmt->close(); // Close statement
?>

<!DOCTYPE html>
<html lang="en">
    <!-- FIX 1a: Added Bootstrap 5 CSS link -->
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

  <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="mb-4">Farmers Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Farmers</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#addFarmerModal">
                        <i class="fas fa-plus"></i> Add Farmer
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportFarmers()">
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
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterDate">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" id="filterLocation" placeholder="Location...">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-agri" onclick="loadFarmers(1)">Search</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="farmersTable">
                    <thead>
                        <tr>
                           <th>Name</th>
                            <th>Produce</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registration Date</th>
                            <th>Location</th>
                            <th>Status</th>
                           
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="farmersTableBody">
                        <!-- Populated via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Farmers pagination">
                <ul class="pagination justify-content-end" id="pagination">
                    <!-- Populated via AJAX -->
                </ul>
            </nav>
        </div>
    </main>

   <div class="modal fade" id="addFarmerModal" tabindex="-1" aria-labelledby="addFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFarmerModalLabel">Add New Farmer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFarmerForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="addFirstName" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="addLastName" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="addEmail" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="addPhone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addGender" class="form-label">Gender</label>
                                    <select class="form-select" id="addGender" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="addAddress" name="address" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addState" class="form-label">State</label>
                                    <select class="form-select" id="addState" name="state_id" required>
                                        <option value="">Select State</option>
                                        <?php foreach ($states as $state): ?>
                                            <option value="<?php echo $state['state_id']; ?>"><?php echo htmlspecialchars($state['state_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addCity" class="form-label">City</label>
                                    <select class="form-select" id="addCity" name="city_id" required>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addCacNumber" class="form-label">CAC Number</label>
                                    <input type="text" class="form-control" id="addCacNumber" name="cac_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addNin" class="form-label">NIN</label>
                                    <input type="text" class="form-control" id="addNin" name="nin">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFarmName" class="form-label">Farm Name</label>
                                    <input type="text" class="form-control" id="addFarmName" name="farm_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFarmSize" class="form-label">Farm Size (hectares)</label>
                                    <input type="number" step="0.01" class="form-control" id="addFarmSize" name="farm_size">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFarmFullAddress" class="form-label">Farm Full Address</label>
                                    <textarea class="form-control" id="addFarmFullAddress" name="farm_full_address" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addLandOwnershipType" class="form-label">Land Ownership Type</label>
                                    <select class="form-select" id="addLandOwnershipType" name="land_ownership_type">
                                        <option value="">Select Ownership Type</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Leased">Leased</option>
                                        <option value="Rented">Rented</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addFarmingExperience" class="form-label">Farming Experience (years)</label>
                                    <input type="number" class="form-control" id="addFarmingExperience" name="farming_experience">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addProfilePicture" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="addProfilePicture" name="profile_picture" accept="image/jpeg,image/png">
                                    <small class="form-text text-muted">JPG or PNG, max 2MB</small>
                                </div>
                            </div>
                        </div>
                        <!-- Document Upload Section -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="addCacDocument" class="form-label">CAC Document</label>
                                    <input type="file" class="form-control" id="addCacDocument" name="cacDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="addNinDocument" class="form-label">NIN Document</label>
                                    <input type="file" class="form-control" id="addNinDocument" name="ninDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="addLandDocument" class="form-label">Land Ownership Document</label>
                                    <input type="file" class="form-control" id="addLandDocument" name="landDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="addFarmer()">Save Farmer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Farmer Modal -->
    <div class="modal fade" id="editFarmerModal" tabindex="-1" aria-labelledby="editFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFarmerModalLabel">Edit Farmer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editFarmerForm" enctype="multipart/form-data">
                        <input type="hidden" id="editFarmerId" name="user_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFirstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editLastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="editLastName" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="editEmail" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="editPhone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editGender" class="form-label">Gender</label>
                                    <select class="form-select" id="editGender" name="gender" required>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="editAddress" name="address" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editState" class="form-label">State</label>
                                    <select class="form-select" id="editState" name="state_id" required>
                                        <option value="">Select State</option>
                                        <?php foreach ($states as $state): ?>
                                            <option value="<?php echo $state['state_id']; ?>"><?php echo htmlspecialchars($state['state_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCity" class="form-label">City</label>
                                    <select class="form-select" id="editCity" name="city_id" required>
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCacNumber" class="form-label">CAC Number</label>
                                    <input type="text" class="form-control" id="editCacNumber" name="cac_number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editNin" class="form-label">NIN</label>
                                    <input type="text" class="form-control" id="editNin" name="nin">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFarmName" class="form-label">Farm Name</label>
                                    <input type="text" class="form-control" id="editFarmName" name="farm_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFarmSize" class="form-label">Farm Size (hectares)</label>
                                    <input type="number" step="0.01" class="form-control" id="editFarmSize" name="farm_size">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFarmFullAddress" class="form-label">Farm Full Address</label>
                                    <textarea class="form-control" id="editFarmFullAddress" name="farm_full_address" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editLandOwnershipType" class="form-label">Land Ownership Type</label>
                                    <select class="form-select" id="editLandOwnershipType" name="land_ownership_type">
                                        <option value="">Select Ownership Type</option>
                                        <option value="Owned">Owned</option>
                                        <option value="Leased">Leased</option>
                                        <option value="Rented">Rented</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFarmingExperience" class="form-label">Farming Experience (years)</label>
                                    <input type="number" class="form-control" id="editFarmingExperience" name="farming_experience">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editProfilePicture" class="form-label">Profile Picture</label>
                                    <img id="editProfilePicturePreview" class="profile-pic-preview" src="" alt="No profile picture" style="display: none;">
                                    <input type="file" class="form-control" id="editProfilePicture" name="profile_picture" accept="image/jpeg,image/png">
                                    <small class="form-text text-muted">JPG or PNG, max 2MB</small>
                                </div>
                            </div>
                        </div>
                        <!-- Document Management Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Current Documents</h6>
                                <div id="currentDocuments" class="mb-3">
                                    <!-- Documents will be loaded here -->
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editCacDocument" class="form-label">Update CAC Document</label>
                                    <input type="file" class="form-control" id="editCacDocument" name="cacDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editNinDocument" class="form-label">Update NIN Document</label>
                                    <input type="file" class="form-control" id="editNinDocument" name="ninDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editLandDocument" class="form-label">Update Land Document</label>
                                    <input type="file" class="form-control" id="editLandDocument" name="landDocument" accept="image/jpeg,image/png,application/pdf">
                                    <small class="form-text text-muted">JPG, PNG or PDF, max 5MB</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" onclick="editFarmer()">Update Farmer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Farmer Modal -->
    <div class="modal fade" id="disableFarmerModal" tabindex="-1" aria-labelledby="disableFarmerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disableFarmerModalLabel">Disable Farmer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="disableFarmerForm">
                        <input type="hidden" id="disableFarmerId" name="id">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason for Disabling</label>
                            <textarea class="form-control" id="rejectionReason" name="rejection_reason" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="disableFarmer()">Disable Farmer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawals Modal -->
    <div class="modal fade" id="withdrawalsModal" tabindex="-1" aria-labelledby="withdrawalsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalsModalLabel">Withdrawal Requests - <span id="farmerNameDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="withdrawalsTableContainer">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Bank Name</th>
                                    <th>Account Number</th>
                                    <th>Account Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="withdrawalsTableBody">
                                <!-- Populated via AJAX -->
                            </tbody>
                        </table>
                        <div id="noWithdrawalsMessage" class="text-center text-muted py-4" style="display: none;">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>No withdrawal requests found for this farmer.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .document-item {
            background-color: #f8f9fa;
            transition: background-color 0.2s;
        }
        .document-item:hover {
            background-color: #e9ecef;
        }
        .profile-pic-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        
        /* Green Pagination Styling */
        .pagination .page-link {
            color: #4CAF50;
        }
        .pagination .page-item.active .page-link {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: white;
        }
        .pagination .page-link:hover {
            background-color: #C8E6C9;
            border-color: #4CAF50;
            color: #4CAF50;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script>
        const cities = <?php echo json_encode($cities); ?>;

        // Load cities based on state selection
        function loadCities(selectId, stateId, selectedCityId = null) {
            const citySelect = document.getElementById(selectId);
            citySelect.innerHTML = '<option value="">Select City</option>';
            cities.filter(city => city.state_id == stateId).forEach(city => {
                const option = document.createElement('option');
                option.value = city.city_id;
                option.textContent = city.city_name;
                if (city.city_id == selectedCityId) {
                    option.selected = true;
                }
                citySelect.appendChild(option);
            });
        }

        // Function to display current documents in edit modal
        function displayCurrentDocuments(documents) {
            const container = document.getElementById('currentDocuments');
            if (!documents || documents.length === 0) {
                container.innerHTML = '<p class="text-muted">No documents uploaded</p>';
                return;
            }

            container.innerHTML = documents.map(doc => {
                const fileName = doc.document_name || doc.document_path.split('/').pop();
                const fileType = fileName.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png'].includes(fileType);
                const isPDF = fileType === 'pdf';
                
                return `
                    <div class="document-item mb-2 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas ${isImage ? 'fa-image' : isPDF ? 'fa-file-pdf' : 'fa-file'} me-2"></i>
                                <span>${fileName}</span>
                            </div>
                            <div>
                                <a href="../${doc.document_path}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="../${doc.document_path}" download="${fileName}" class="btn btn-sm btn-outline-success" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Load farmers via AJAX
        function loadFarmers(page = 1) {
            const search = document.getElementById('searchName').value;
            const status = document.getElementById('filterStatus').value;
            const date = document.getElementById('filterDate').value;
            const location = document.getElementById('filterLocation').value;

            fetch(`api/farmers_api.php?page=${page}&search=${encodeURIComponent(search)}&status=${status}&date=${date}&location=${encodeURIComponent(location)}`, {
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
                        const tbody = document.getElementById('farmersTableBody');
                        tbody.innerHTML = data.data.farmers.map(farmer => `
                            <tr>
                                <td>${farmer.first_name} ${farmer.last_name}</td>
                                <td>${farmer.crops_produced || 'N/A'}</td>
                                <td>${farmer.email}</td>
                                <td>${farmer.phone}</td>
                                <td>${new Date(farmer.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</td>
                                <td>${farmer.farm_full_address || 'N/A'}</td>
                                <td><span class="badge ${farmer.cbn_approved == 1 ? 'bg-success' : farmer.cbn_approved == 2 ? 'bg-danger' : 'bg-warning'}">${farmer.cbn_approved == 1 ? 'Approved' : farmer.cbn_approved == 2 ? 'Disabled' : 'Pending'}</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn btn-edit edit-farmer-btn btn btn-sm btn-info text-white" data-id="${farmer.user_id}" title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn btn-approve approve-farmer-btn btn btn-sm btn-success ${farmer.cbn_approved == 1 ? 'disabled' : ''}" data-id="${farmer.user_id}" title="Approve"><i class="fas fa-check"></i></button>
                                        <button class="action-btn btn-disable disable-farmer-btn btn btn-sm btn-danger ${farmer.cbn_approved == 2 ? 'disabled' : ''}" data-id="${farmer.user_id}" title="Disable"><i class="fas fa-ban"></i></button>
                                        <button class="action-btn btn-withdrawals withdrawals-btn btn btn-sm btn-warning" data-id="${farmer.user_id}" data-name="${farmer.first_name} ${farmer.last_name}" title="Withdrawals"><i class="fas fa-wallet"></i></button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');

                        // Update pagination
                        const pagination = document.getElementById('pagination');
                        pagination.innerHTML = '';
                        for (let i = 1; i <= data.data.pages; i++) {
                            pagination.innerHTML += `
                                <li class="page-item ${i === data.data.current_page ? 'active' : ''}">
                                    <a class="page-link" href="#" onclick="loadFarmers(${i}); return false;">${i}</a>
                                </li>
                            `;
                        }
                    } else {
                        console.error('Error loading farmers:', data.error);
                        alert('Error loading farmers: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error fetching farmers: ' + error.message);
                });
        }

        // Add farmer
        function addFarmer() {
            const form = document.getElementById('addFarmerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = new FormData(form);
            data.append('action', 'add');

            fetch('api/farmers_api.php', {
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
                        alert('Farmer added successfully! A welcome email has been sent.');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addFarmerModal'));
                        modal.hide();
                        form.reset();
                        loadCities('addCity', document.getElementById('addState').value);
                        loadFarmers();
                    } else {
                        console.error('Error adding farmer:', json.error);
                        alert('Error adding farmer: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error adding farmer: ' + error.message);
                });
        }

        // Edit farmer
        function editFarmer() {
            const form = document.getElementById('editFarmerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = new FormData(form);
            data.append('action', 'edit');

            fetch('api/farmers_api.php', {
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
                        alert('Farmer updated successfully!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editFarmerModal'));
                        modal.hide();
                        loadFarmers();
                    } else {
                        console.error('Error updating farmer:', json.error);
                        alert('Error updating farmer: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error updating farmer: ' + error.message);
                });
        }

        // Disable farmer
        function disableFarmer() {
            const form = document.getElementById('disableFarmerForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const data = new FormData(form);
            data.append('action', 'disable');

            fetch('api/farmers_api.php', {
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
                        alert('Farmer disabled successfully!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('disableFarmerModal'));
                        modal.hide();
                        form.reset();
                        loadFarmers();
                    } else {
                        console.error('Error disabling farmer:', json.error);
                        alert('Error disabling farmer: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error disabling farmer: ' + error.message);
                });
        }

        // Export farmers to Excel
        function exportFarmers() {
            if (typeof XLSX === 'undefined') {
                console.error('SheetJS library not loaded');
                alert('Error: Excel export library failed to load. Please try again or disable browser extensions.');
                return;
            }

            const search = document.getElementById('searchName').value;
            const status = document.getElementById('filterStatus').value;
            const date = document.getElementById('filterDate').value;
            const location = document.getElementById('filterLocation').value;

            fetch(`api/farmers_api.php?action=export&search=${encodeURIComponent(search)}&status=${status}&date=${date}&location=${encodeURIComponent(location)}`, {
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
                            alert('No farmers found to export.');
                            return;
                        }

                        const worksheet = XLSX.utils.json_to_sheet(json.data);
                        worksheet['!cols'] = [
                            { wch: 20 }, // Name
                            { wch: 20 }, // Produce
                            { wch: 30 }, // Email
                            { wch: 15 }, // Phone
                            { wch: 15 }, // Registration Date
                            { wch: 20 }, // Location
                            { wch: 10 }, // Status
                            { wch: 15 }, // State
                            { wch: 15 }  // City
                        ];
                        const workbook = XLSX.utils.book_new();
                        XLSX.utils.book_append_sheet(workbook, worksheet, 'Farmers');

                        const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
                        const blob = new Blob([excelBuffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const today = new Date();
                        const dateStr = today.toISOString().split('T')[0];
                        const filename = `farmers_export_${dateStr}.xlsx`;

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

                        alert('Farmers exported successfully!');
                    } else {
                        console.error('Error exporting farmers:', json.error);
                        alert('Error exporting farmers: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error exporting farmers: ' + error.message);
                });
        }

        // Load withdrawals for a specific farmer
        function loadWithdrawals(farmerId) {
            fetch(`api/withdrawals_api.php?action=get_farmer_withdrawals&farmer_id=${farmerId}`, {
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
                        const tbody = document.getElementById('withdrawalsTableBody');
                        const noDataMsg = document.getElementById('noWithdrawalsMessage');
                        
                        if (!json.data || json.data.length === 0) {
                            tbody.innerHTML = '';
                            noDataMsg.style.display = 'block';
                            return;
                        }
                        
                        noDataMsg.style.display = 'none';
                        tbody.innerHTML = json.data.map(w => {
                            const statusClass = w.status === 'Approved' ? 'success' : w.status === 'Pending' ? 'warning' : 'danger';
                            const actionButtons = w.status === 'Pending' ? `
                                <button class="btn btn-sm btn-success" onclick="updateWithdrawalStatus(${w.withdrawal_id}, 'Approved')" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateWithdrawalStatus(${w.withdrawal_id}, 'Rejected')" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : `<span class="text-muted">No action</span>`;
                            
                            return `
                                <tr>
                                    <td>${w.withdrawal_id}</td>
                                    <td>${w.request_date}</td>
                                    <td>₦${parseFloat(w.amount).toLocaleString('en-NG', {minimumFractionDigits: 2})}</td>
                                    <td>${w.bank_name}</td>
                                    <td>${w.account_number}</td>
                                    <td>${w.account_name}</td>
                                    <td><span class="badge bg-${statusClass}">${w.status}</span></td>
                                    <td>${actionButtons}</td>
                                </tr>
                            `;
                        }).join('');
                    } else {
                        console.error('Error loading withdrawals:', json.error);
                        alert('Error loading withdrawals: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error fetching withdrawals: ' + error.message);
                });
        }

        // Update withdrawal status (Approve/Reject)
        function updateWithdrawalStatus(withdrawalId, status, manualProcess = false) {
            const action = status === 'Approved' ? 'approve' : 'reject';
            let confirmMsg = status === 'Approved' ? 
                'Approve this withdrawal request?' : 
                'Reject this withdrawal request?';
            
            if (manualProcess) {
                confirmMsg = 'Process this withdrawal MANUALLY (no Paystack transfer)? This will just mark it as Approved in the database.';
            }
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            const data = new FormData();
            data.append('action', action);
            data.append('withdrawal_id', withdrawalId);
            if (manualProcess) {
                data.append('manual_process', 'true');
            }
            
            fetch('api/withdrawals_api.php', {
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
                        alert(json.message || `Withdrawal ${status.toLowerCase()} successfully!`);
                        // Reload the withdrawals table
                        const modal = document.getElementById('withdrawalsModal');
                        const farmerId = modal.dataset.farmerId;
                        
                        if (farmerId) {
                            loadWithdrawals(farmerId);
                        } else {
                            // Fallback if ID not found (should not happen with fix)
                            console.warn('Could not find farmer ID to reload withdrawals');
                        }
                    } else {
                        console.error('Error updating withdrawal:', json.error);
                        if (json.can_manual_process) {
                            if (confirm(`Paystack Error: ${json.error}\n\nDo you want to mark this as paid MANUALLY instead?`)) {
                                updateWithdrawalStatus(withdrawalId, 'Approved', true);
                                return;
                            }
                        }
                        alert('Error updating withdrawal: ' + json.error);
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error updating withdrawal: ' + error.message);
                });
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadFarmers();

            // Sidebar toggle
            const headerToggle = document.getElementById('headerToggle');
            if (headerToggle) {
                headerToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                    document.querySelector('.main-content').classList.toggle('expanded');
                });
            }

            // State change for cities
            const addState = document.getElementById('addState');
            if (addState) {
                addState.addEventListener('change', function() {
                    loadCities('addCity', this.value);
                });
            }
            const editState = document.getElementById('editState');
            if (editState) {
                editState.addEventListener('change', function() {
                    loadCities('editCity', this.value);
                });
            }

            // Action buttons
            document.getElementById('farmersTableBody').addEventListener('click', function(e) {
                const target = e.target.closest('.action-btn');
                if (!target) return;

                const id = target.dataset.id;
                if (target.classList.contains('edit-farmer-btn')) {
                    fetch(`api/farmers_api.php?action=get_details&id=${id}`, {
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
                                const farmer = json.data;
                                document.getElementById('editFarmerId').value = farmer.user_id || '';
                                document.getElementById('editFirstName').value = farmer.first_name || '';
                                document.getElementById('editLastName').value = farmer.last_name || '';
                                document.getElementById('editEmail').value = farmer.email || '';
                                document.getElementById('editPhone').value = farmer.phone || '';
                                document.getElementById('editGender').value = farmer.gender || 'Male';
                                document.getElementById('editAddress').value = farmer.address || '';
                                document.getElementById('editState').value = farmer.state_id || '';
                                document.getElementById('editCacNumber').value = farmer.cac_number || '';
                                document.getElementById('editNin').value = farmer.nin || '';
                                document.getElementById('editFarmName').value = farmer.farm_name || '';
                                document.getElementById('editFarmSize').value = farmer.farm_size || '';
                                document.getElementById('editFarmFullAddress').value = farmer.farm_full_address || '';
                                document.getElementById('editLandOwnershipType').value = farmer.land_ownership_type || '';
                                document.getElementById('editFarmingExperience').value = farmer.farming_experience || '';
                                const preview = document.getElementById('editProfilePicturePreview');
                                if (farmer.profile_picture) {
                                    preview.src = `../${farmer.profile_picture}`;
                                    preview.style.display = 'block';
                                } else {
                                    preview.src = '';
                                    preview.style.display = 'none';
                                }
                                loadCities('editCity', farmer.state_id || '', farmer.city_id || '');
                                
                                // Display documents
                                displayCurrentDocuments(farmer.documents || []);
                                
                                const modal = new bootstrap.Modal(document.getElementById('editFarmerModal'));
                                modal.show();
                            } else {
                                console.error('Error fetching farmer details:', json.error);
                                alert('Error fetching farmer details: ' + json.error);
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            alert('Error fetching farmer details: ' + error.message);
                        });
                } else if (target.classList.contains('approve-farmer-btn')) {
                    if (confirm('Approve this farmer?')) {
                        const data = new FormData();
                        data.append('action', 'approve');
                        data.append('id', id);
                        fetch('api/farmers_api.php', {
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
                                    alert('Farmer approved!');
                                    loadFarmers();
                                } else {
                                    console.error('Error approving farmer:', json.error);
                                    alert('Error approving farmer: ' + json.error);
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                                alert('Error approving farmer: ' + error.message);
                            });
                    }
                } else if (target.classList.contains('disable-farmer-btn')) {
                    document.getElementById('disableFarmerId').value = id;
                    const modal = new bootstrap.Modal(document.getElementById('disableFarmerModal'));
                    modal.show();
                } else if (target.classList.contains('withdrawals-btn')) {
                    const farmerName = target.dataset.name;
                    document.getElementById('farmerNameDisplay').textContent = farmerName;
                    
                    // Store farmer ID in modal for later use
                    const modalElement = document.getElementById('withdrawalsModal');
                    modalElement.dataset.farmerId = id;
                    
                    loadWithdrawals(id);
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });

            // Initial city load for add modal
            const initialStateId = document.getElementById('addState').value;
            if (initialStateId) loadCities('addCity', initialStateId);
        });
    </script>
</body>
</html>