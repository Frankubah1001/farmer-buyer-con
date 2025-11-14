<?php
require_once 'buyer_auth_check.php';
include 'buyerheader.php';
?>

<style>
/* Farm-themed color scheme */
:root {
    --farm-green: #4CAF50;
    --farm-light-green: #8BC34A;
    --farm-earth: #795548;
    --farm-sky: #87CEEB;
    --farm-sun: #FFC107;
    --farm-brown: #8B4513;
    --farm-light-brown: #A1887F;
    --farm-cream: #FFF9C4;
}

/* Custom farm-themed styles */
.farm-bg {
    background: linear-gradient(135deg, var(--farm-light-green) 0%, var(--farm-green) 100%);
}

.farm-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: none;
    background: rgba(255, 255, 255, 0.95);
}

.farm-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.farm-card-header {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
    border-bottom: none;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.farm-card-header::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, var(--farm-sun), var(--farm-brown));
}

.farm-card-header h6 {
    font-weight: 700;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
}

.farm-form-control {
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    padding: 12px 15px;
    transition: all 0.3s;
    background-color: #f9f9f9;
}

.farm-form-control:focus {
    border-color: var(--farm-light-green);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
    background-color: white;
}

.farm-btn {
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    position: relative;
    overflow: hidden;
}

.farm-btn::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
}

.farm-btn:hover::after {
    animation: ripple 1s ease-out;
}

@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 0.5;
    }
    100% {
        transform: scale(20, 20);
        opacity: 0;
    }
}

.btn-farm-primary {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
    color: white;
}

.btn-farm-primary:hover {
    background: linear-gradient(135deg, var(--farm-light-green) 0%, var(--farm-green) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
}

.btn-farm-secondary {
    background: linear-gradient(135deg, var(--farm-brown) 0%, var(--farm-light-brown) 100%);
    color: white;
}

.btn-farm-secondary:hover {
    background: linear-gradient(135deg, var(--farm-light-brown) 0%, var(--farm-brown) 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(139, 69, 19, 0.4);
}

.farm-badge {
    border-radius: 50px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-urgency-low {
    background-color: #E8F5E9;
    color: var(--farm-green);
}

.badge-urgency-medium {
    background-color: #FFF8E1;
    color: #FF9800;
}

.badge-urgency-high {
    background-color: #FFEBEE;
    color: #F44336;
}

.badge-status-pending {
    background-color: #FFF8E1;
    color: #FF9800;
}

.badge-status-resolved {
    background-color: #E8F5E9;
    color: var(--farm-green);
}

.badge-status-review {
    background-color: #E3F2FD;
    color: #2196F3;
}

.farm-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.farm-table thead {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
    color: white;
}

.farm-table th {
    border: none;
    font-weight: 600;
    padding: 15px;
}

.farm-table td {
    padding: 15px;
    vertical-align: middle;
    border-bottom: 1px solid #e0e0e0;
}

.farm-table tbody tr {
    transition: all 0.3s;
}

.farm-table tbody tr:hover {
    background-color: #f5f5f5;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.farm-stats-card {
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    color: white;
    transition: all 0.3s;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.farm-stats-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: rgba(255,255,255,0.3);
}

.farm-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stats-total {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
}

.stats-resolved {
    background: linear-gradient(135deg, #66BB6A 0%, #4CAF50 100%);
}

.stats-pending {
    background: linear-gradient(135deg, #FFB74D 0%, #FF9800 100%);
}

.farm-alert {
    border-radius: 10px;
    border: none;
    padding: 15px;
    background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
    border-left: 5px solid var(--farm-green);
}

.urgency-levels .form-check-label {
    font-weight: 500;
    padding: 10px 15px;
    border-radius: 10px;
    transition: all 0.3s;
    border: 2px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
}

.urgency-levels .form-check-input:checked + .form-check-label {
    background-color: #f8f9fa;
    border: 2px solid var(--farm-light-green);
    box-shadow: 0 0 10px rgba(76, 175, 80, 0.2);
}

.urgency-low {
    color: var(--farm-green);
}

.urgency-medium {
    color: #FF9800;
}

.urgency-high {
    color: #F44336;
}

.select2-container--default .select2-selection--single {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    height: calc(1.5em + 1.5rem);
    padding: 0.75rem;
    background-color: #f9f9f9;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 1.5rem);
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--farm-light-green);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
}

.custom-file-label {
    border-radius: 10px;
    border: 2px solid #e0e0e0;
    padding: 5px 15px;
    background-color: #f9f9f9;
}

.custom-file-input:focus ~ .custom-file-label {
    border-color: var(--farm-light-green);
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
}

.page-title {
    color: var(--farm-green);
    font-weight: 700;
    position: relative;
    display: inline-block;
    margin-bottom: 2rem;
}

.page-title::after {
    content: "";
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, var(--farm-green), var(--farm-sun));
    border-radius: 3px;
}

.farm-icon {
    margin-right: 8px;
    color: var(--farm-green);
}

.farm-bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234caf50' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
}
</style>

<div id="content-wrapper" class="d-flex flex-column farm-bg-pattern">
<div id="content">
<?php include 'buyertopbar.php'; ?>

<div class="container-fluid py-4">
<h1 class="h2 mb-4 page-title"><i class="fas fa-flag farm-icon"></i>Report an Issue</h1>

<div class="row">
<!-- Report Issue Form Section -->
<div class="col-xl-5 col-lg-6">
<div class="card farm-card shadow mb-4">
<div class="card-header farm-card-header py-3 text-white">
<h6 class="m-0 font-weight-bold"><i class="fas fa-flag me-2 mr-2"></i>Report an Issue</h6>
</div>
<div class="card-body p-4">
<form id="reportIssueForm">
<div class="form-group mb-4">
<label for="farmerSelect" class="font-weight-bold text-gray-700">Select Farmer to Report</label>
<select class="form-control farm-form-control select2" id="farmerSelect" required style="width: 100%;">
<option value="">Choose a farmer...</option>
<option value="1" data-location="Eleyele" data-rating="3">Franklin Nwawuba</option>
<option value="2" data-location="Ibadan" data-rating="4">Jacinta Ubah</option>
<option value="3" data-location="gfcvb" data-rating="2">Steve Gbenga</option>
<option value="4" data-location="Lagos" data-rating="5">Victor Odogwu</option>
<option value="5" data-location="Benin" data-rating="3">Goke Ibile</option>
</select>
<small class="form-text text-muted mt-1">Select the farmer you want to report</small>
</div>

<div id="farmerInfo" class="farm-alert d-none mb-4">
<div class="d-flex justify-content-between align-items-center">
<div>
<strong id="selectedFarmerName"></strong>
<br>
<small class="text-muted">
Location: <span id="selectedFarmerLocation"></span> |
Rating: <span id="selectedFarmerRating"></span>
</small>
</div>
<div class="text-warning" id="farmerStars"></div>
</div>
</div>

<div class="form-group mb-4">
<label for="orderNumber" class="font-weight-bold text-gray-700">Order Number</label>
<input type="text" class="form-control farm-form-control" id="orderNumber" placeholder="e.g., #22" required>
</div>

<div class="form-group mb-4">
<label for="produceName" class="font-weight-bold text-gray-700">Produce Involved</label>
<input type="text" class="form-control farm-form-control" id="produceName" placeholder="e.g., Tomatoes, Rice, etc." required>
</div>

<div class="form-group mb-4">
<label for="issueType" class="font-weight-bold text-gray-700">Issue Type</label>
<select class="form-control farm-form-control select2" id="issueType" required>
<option value="">Select Issue Type</option>
<option value="quality">Poor Quality Produce</option>
<option value="quantity">Wrong Quantity</option>
<option value="delivery">Late Delivery</option>
<option value="payment">Payment Issue</option>
<option value="communication">Poor Communication</option>
<option value="fraud">Suspected Fraud</option>
<option value="other">Other</option>
</select>
</div>

<div class="form-group mb-4">
<label for="issueDescription" class="font-weight-bold text-gray-700">Description</label>
<textarea class="form-control farm-form-control" id="issueDescription" rows="4"
placeholder="Please describe the issue in detail. Include specific dates, quantities, and any relevant information..."
required></textarea>
<small class="form-text text-muted">Be as detailed as possible to help us resolve your issue quickly</small>
</div>

<div class="form-group mb-4">
<label for="evidenceUpload" class="font-weight-bold text-gray-700">
<i class="fas fa-paperclip me-1 mr-2"></i>Upload Evidence
<small class="text-muted">(Optional)</small>
</label>
<div class="custom-file">
<input type="file" class="custom-file-input" id="evidenceUpload"
accept="image/*,.pdf,.doc,.docx,.txt">
<label class="custom-file-label" for="evidenceUpload">Choose files...</label>
</div>
<small class="form-text text-muted">Photos, documents, or screenshots that support your claim</small>
</div>

<div class="form-group mb-4">
<label for="urgencyLevel" class="font-weight-bold text-gray-700">Urgency Level</label>
<div class="urgency-levels d-flex justify-content-between">
<div class="form-check">
<input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyLow" value="low">
<label class="form-check-label urgency-low mr-2" for="urgencyLow">
<i class="fas fa-leaf me-1 mr-2"></i>Low
</label>
</div>
<div class="form-check">
<input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyMedium" value="medium" checked>
<label class="form-check-label urgency-medium mr-2" for="urgencyMedium">
<i class="fas fa-exclamation-circle me-1 mr-2"></i>Medium
</label>
</div>
<div class="form-check">
<input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyHigh" value="high">
<label class="form-check-label urgency-high mr-2" for="urgencyHigh">
<i class="fas fa-exclamation-triangle me-1 mr-2"></i>High
</label>
</div>
</div>
</div>

<button type="submit" class="btn btn-farm-primary btn-block py-3">
<i class="fas fa-paper-plane me-2"></i> Submit Report
</button>
</form>
</div>
</div>
</div>

<!-- Recent Reports Section -->
<div class="col-xl-7 col-lg-6">
<div class="card farm-card shadow">
<div class="card-header farm-card-header py-3 text-white">
<h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2 mr-2"></i>Your Report History</h6>
</div>
<div class="card-body p-4">
<div class="table-responsive">
<table class="table farm-table" id="reportsHistoryTable" width="100%" cellspacing="0">
<thead>
<tr>
<th>Report ID</th>
<th>Farmer</th>
<th>Order #</th>
<th>Issue Type</th>
<th>Date Reported</th>
<th>Status</th>
<th>Urgency</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>#REP001</strong></td>
<td>Steve Gbenga</td>
<td>#16</td>
<td>Wrong Quantity</td>
<td>2025-10-14</td>
<td>
<span class="farm-badge badge-status-pending">
<i class="fas fa-clock me-1"></i>Pending
</span>
</td>
<td>
<span class="farm-badge badge-urgency-high">
<i class="fas fa-exclamation-triangle me-1 mr-2"></i>High
</span>
</td>
</tr>
<tr>
<td><strong>#REP002</strong></td>
<td>Jacinta Ubah</td>
<td>#12</td>
<td>Poor Quality</td>
<td>2025-10-10</td>
<td>
<span class="farm-badge badge-status-resolved">
<i class="fas fa-check me-1 mr-2"></i>Resolved
</span>
</td>
<td>
<span class="farm-badge badge-urgency-medium">
<i class="fas fa-exclamation-circle me-1 mr-2"></i>Medium
</span>
</td>
</tr>
<tr>
<td><strong>#REP003</strong></td>
<td>Franklin Nwawuba</td>
<td>#08</td>
<td>Late Delivery</td>
<td>2025-10-05</td>
<td>
<span class="farm-badge badge-status-review">
<i class="fas fa-spinner me-1 mr-2"></i>In Review
</span>
</td>
<td>
<span class="farm-badge badge-urgency-low">
<i class="fas fa-leaf me-1 mr-2"></i>Low
</span>
</td>
</tr>
<tr>
<td><strong>#REP004</strong></td>
<td>Victor Odogwu</td>
<td>#05</td>
<td>Payment Issue</td>
<td>2025-10-01</td>
<td>
<span class="farm-badge badge-status-resolved">
<i class="fas fa-check me-1 mr-2"></i>Resolved
</span>
</td>
<td>
<span class="farm-badge badge-urgency-high">
<i class="fas fa-exclamation-triangle me-1 mr-2"></i>High
</span>
</td>
</tr>
</tbody>
</table>
</div>

<!-- Report Statistics -->
<div class="row mt-4">
<div class="col-md-4 text-center">
<div class="farm-stats-card stats-total mb-4">
<div class="h3 mb-0">4</div>
<div class="small">Total Reports</div>
</div>
</div>
<div class="col-md-4 text-center">
<div class="farm-stats-card stats-resolved mb-4">
<div class="h3 mb-0">2</div>
<div class="small">Resolved</div>
</div>
</div>
<div class="col-md-4 text-center">
<div class="farm-stats-card stats-pending mb-4">
<div class="h3 mb-0">2</div>
<div class="small">Pending</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

<?php include 'buyerfooter.php'; ?>
</div>

<?php include 'buyerscript.php'; ?>
<!-- Include Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<!-- Include Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- Include SweetAlert2 for beautiful alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Load farmers, reports history and stats on page load
    loadFarmers();
    loadReportsHistory();
    loadReportStats();

    // Handle farmer selection
    $('#farmerSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const farmerName = selectedOption.text();
        const location = selectedOption.data('location');
        const farmerId = $(this).val();

        console.log('Farmer selected:', farmerId, farmerName, location);

        if (farmerId && farmerId !== '') {
            $('#selectedFarmerName').text(farmerName);
            $('#selectedFarmerLocation').text(location || 'Location not specified');
            $('#farmerInfo').removeClass('d-none');
        } else {
            $('#farmerInfo').addClass('d-none');
        }
    });

    // Handle file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Handle form submission
    $('#reportIssueForm').submit(function(e) {
        e.preventDefault();

        const farmerId = $('#farmerSelect').val();
        const orderNumber = $('#orderNumber').val();
        const produceName = $('#produceName').val();
        const issueType = $('#issueType').val();
        const issueDescription = $('#issueDescription').val();
        const urgencyLevel = $('input[name="urgencyLevel"]:checked').val();

        console.log('Form data:', {
            farmerId: farmerId,
            orderNumber: orderNumber,
            produceName: produceName,
            issueType: issueType,
            issueDescription: issueDescription,
            urgencyLevel: urgencyLevel
        });

        // Basic validation
        if (!farmerId || !orderNumber || !produceName || !issueType || !issueDescription) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all required fields.',
                confirmButtonColor: '#4e73df'
            });
            return;
        }

        // Create FormData object
        const formData = new FormData();
        formData.append('action', 'submit_report');
        formData.append('farmerId', farmerId);
        formData.append('orderNumber', orderNumber);
        formData.append('produceName', produceName);
        formData.append('issueType', issueType);
        formData.append('issueDescription', issueDescription);
        formData.append('urgencyLevel', urgencyLevel);

        // Add file if selected
        const fileInput = $('#evidenceUpload')[0];
        if (fileInput && fileInput.files[0]) {
            formData.append('evidence', fileInput.files[0]);
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Submitting...').prop('disabled', true);

        // Submit via AJAX
        $.ajax({
            url: 'views/report.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Server response:', response);
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Report Submitted!',
                        text: response.message,
                        confirmButtonColor: '#1cc88a'
                    });

                    // Reset form
                    $('#reportIssueForm')[0].reset();
                    $('#farmerSelect').val('').trigger('change');
                    $('#farmerInfo').addClass('d-none');
                    $('.custom-file-label').html('Choose files...');

                    // Reload reports history and stats
                    loadReportsHistory();
                    loadReportStats();
                } else {
                    let errorMessage = response.message || 'Submission failed. Please try again.';
                    if (response.debug) {
                        console.error('Debug info:', response.debug);
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: errorMessage,
                        confirmButtonColor: '#e74a3b'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting the report. Please try again.',
                    confirmButtonColor: '#e74a3b'
                });
            },
            complete: function() {
                // Reset button
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Function to load farmers for dropdown
    function loadFarmers() {
        $.ajax({
            url: 'views/report.php?action=get_farmers',
            type: 'GET',
            success: function(response) {
                console.log('Farmers loaded:', response);
                if (response.success) {
                    updateFarmersDropdown(response.farmers);
                } else {
                    console.error('Failed to load farmers:', response.message);
                    loadStaticFarmers();
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load farmers:', error);
                loadStaticFarmers();
            }
        });
    }

    // Function to update farmers dropdown
    function updateFarmersDropdown(farmers) {
        const farmerSelect = $('#farmerSelect');
        farmerSelect.empty();
        farmerSelect.append('<option value="">Choose a farmer...</option>');
        
        if (farmers && farmers.length > 0) {
            farmers.forEach(farmer => {
                farmerSelect.append(
                    $('<option></option>')
                        .val(farmer.user_id)
                        .text(`${farmer.first_name} ${farmer.last_name}`)
                        .data('location', farmer.location || '')
                );
            });
            console.log('Farmers dropdown populated with', farmers.length, 'farmers');
        } else {
            console.warn('No farmers found in database');
            farmerSelect.append('<option value="" disabled>No farmers available</option>');
        }
        
        // Refresh Select2
        farmerSelect.trigger('change.select2');
    }

    // Fallback function if API fails
    function loadStaticFarmers() {
        const staticFarmers = [
            { user_id: 1, first_name: 'Franklin', last_name: 'Nwawuba', location: 'Eleyele' },
            { user_id: 2, first_name: 'Jacinta', last_name: 'Ubah', location: 'Ibadan' },
            { user_id: 3, first_name: 'Steve', last_name: 'Gbenga', location: 'Bauchi' },
            { user_id: 4, first_name: 'Victor', last_name: 'Odogwu', location: 'Lagos' },
            { user_id: 5, first_name: 'Goke', last_name: 'Ibile', location: 'Benin' }
        ];
        updateFarmersDropdown(staticFarmers);
    }

    // Function to load reports history
    function loadReportsHistory() {
        $.ajax({
            url: 'views/report.php?action=get_reports_history',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    updateReportsTable(response.reports);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load reports history:', error);
            }
        });
    }

    // Function to load report statistics
    function loadReportStats() {
        $.ajax({
            url: 'views/report.php?action=get_report_stats',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    updateReportStats(response.stats);
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load report statistics:', error);
            }
        });
    }

    // Function to update reports table
    function updateReportsTable(reports) {
        const tbody = $('#reportsHistoryTable tbody');
        tbody.empty();

        if (reports.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-3"></i>
                        <p>No reports submitted yet</p>
                    </td>
                </tr>
            `);
            return;
        }

        reports.forEach(report => {
            const statusBadge = getStatusBadge(report.status);
            const urgencyBadge = getUrgencyBadge(report.urgency_level);
            
            const row = `
                <tr>
                    <td><strong>${report.report_id}</strong></td>
                    <td>${report.farmer_name}</td>
                    <td>${report.order_number}</td>
                    <td>${report.issue_type}</td>
                    <td>${report.date_reported}</td>
                    <td>${statusBadge}</td>
                    <td>${urgencyBadge}</td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Function to update report statistics
    function updateReportStats(stats) {
        $('.farm-stats-card.stats-total .h3').text(stats.total);
        $('.farm-stats-card.stats-resolved .h3').text(stats.resolved);
        $('.farm-stats-card.stats-pending .h3').text(stats.pending);
    }

    // Helper function to get status badge HTML
    function getStatusBadge(status) {
        const badges = {
            'pending': '<span class="farm-badge badge-status-pending"><i class="fas fa-clock me-1"></i>Pending</span>',
            'resolved': '<span class="farm-badge badge-status-resolved"><i class="fas fa-check me-1"></i>Resolved</span>',
            'in review': '<span class="farm-badge badge-status-review"><i class="fas fa-spinner me-1"></i>In Review</span>'
        };
        return badges[status] || badges.pending;
    }

    // Helper function to get urgency badge HTML
    function getUrgencyBadge(urgency) {
        const badges = {
            'low': '<span class="farm-badge badge-urgency-low"><i class="fas fa-leaf me-1"></i>Low</span>',
            'medium': '<span class="farm-badge badge-urgency-medium"><i class="fas fa-exclamation-circle me-1"></i>Medium</span>',
            'high': '<span class="farm-badge badge-urgency-high"><i class="fas fa-exclamation-triangle me-1"></i>High</span>'
        };
        return badges[urgency] || badges.medium;
    }
});
</script>