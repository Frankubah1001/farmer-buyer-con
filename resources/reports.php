<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php';
?>
<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 0.75rem + 2px);
}

.urgency-levels .form-check-label {
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 5px;
    transition: all 0.3s;
}

.urgency-levels .form-check-input:checked + .form-check-label {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.badge-pill {
    padding: 6px 12px;
    font-size: 0.75rem;
}

.table-hover tbody tr:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}


        </style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Report an Issue</h1>

            <div class="row">
                <!-- Report Issue Form Section -->
                <div class="col-xl-5 col-lg-6">
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3 bg-gradient-primary text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-flag me-2"></i>Report an Issue</h6>
                        </div>
                        <div class="card-body">
                            <form id="reportIssueForm">
                                <div class="form-group mb-4">
                                    <label for="farmerSelect" class="font-weight-bold text-gray-700">Select Farmer to Report</label>
                                    <select class="form-control select2" id="farmerSelect" required style="width: 100%;">
                                        <option value="">Choose a farmer...</option>
                                        <option value="1" data-location="Eleyele" data-rating="3">Franklin Nwawuba</option>
                                        <option value="2" data-location="Ibadan" data-rating="4">Jacinta Ubah</option>
                                        <option value="3" data-location="gfcvb" data-rating="2">Steve Gbenga</option>
                                        <option value="4" data-location="Lagos" data-rating="5">Victor Odogwu</option>
                                        <option value="5" data-location="Benin" data-rating="3">Goke Ibile</option>
                                    </select>
                                    <small class="form-text text-muted mt-1">Select the farmer you want to report</small>
                                </div>

                                <div id="farmerInfo" class="alert alert-info d-none mb-4">
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
                                    <input type="text" class="form-control" id="orderNumber" placeholder="e.g., #22" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="produceName" class="font-weight-bold text-gray-700">Produce Involved</label>
                                    <input type="text" class="form-control" id="produceName" placeholder="e.g., Tomatoes, Rice, etc." required>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="issueType" class="font-weight-bold text-gray-700">Issue Type</label>
                                    <select class="form-control select2" id="issueType" required>
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
                                    <textarea class="form-control" id="issueDescription" rows="4" 
                                              placeholder="Please describe the issue in detail. Include specific dates, quantities, and any relevant information..." 
                                              required></textarea>
                                    <small class="form-text text-muted">Be as detailed as possible to help us resolve your issue quickly</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="evidenceUpload" class="font-weight-bold text-gray-700">
                                        <i class="fas fa-paperclip me-1"></i>Upload Evidence
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
                                    <div class="urgency-levels">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyLow" value="low">
                                            <label class="form-check-label text-success" for="urgencyLow">
                                                <i class="fas fa-clock me-1"></i>Low
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyMedium" value="medium" checked>
                                            <label class="form-check-label text-warning" for="urgencyMedium">
                                                <i class="fas fa-exclamation-circle me-1"></i>Medium
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyHigh" value="high">
                                            <label class="form-check-label text-danger" for="urgencyHigh">
                                                <i class="fas fa-exclamation-triangle me-1"></i>High
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-danger btn-block btn-lg py-3">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports Section -->
                <div class="col-xl-7 col-lg-6">
                    <div class="card shadow border-left-success">
                        <div class="card-header py-3 bg-gradient-success text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i>Your Report History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="reportsHistoryTable" width="100%" cellspacing="0">
                                    <thead class="bg-light">
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
                                        <tr class="table-warning">
                                            <td><strong>#REP001</strong></td>
                                            <td>Steve Gbenga</td>
                                            <td>#16</td>
                                            <td>Wrong Quantity</td>
                                            <td>2025-10-14</td>
                                            <td>
                                                <span class="badge badge-warning badge-pill">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>#REP002</strong></td>
                                            <td>Jacinta Ubah</td>
                                            <td>#12</td>
                                            <td>Poor Quality</td>
                                            <td>2025-10-10</td>
                                            <td>
                                                <span class="badge badge-success badge-pill">
                                                    <i class="fas fa-check me-1"></i>Resolved
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>#REP003</strong></td>
                                            <td>Franklin Nwawuba</td>
                                            <td>#08</td>
                                            <td>Late Delivery</td>
                                            <td>2025-10-05</td>
                                            <td>
                                                <span class="badge badge-info badge-pill">
                                                    <i class="fas fa-spinner me-1"></i>In Review
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-clock me-1"></i>Low
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>#REP004</strong></td>
                                            <td>Victor Odogwu</td>
                                            <td>#05</td>
                                            <td>Payment Issue</td>
                                            <td>2025-10-01</td>
                                            <td>
                                                <span class="badge badge-success badge-pill">
                                                    <i class="fas fa-check me-1"></i>Resolved
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>High
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Report Statistics -->
                            <div class="row mt-4">
                                <div class="col-md-4 text-center">
                                    <div class="card bg-primary text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">4</div>
                                            <div class="small">Total Reports</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card bg-success text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">2</div>
                                            <div class="small">Resolved</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="card bg-warning text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">2</div>
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

    // Handle farmer selection
    $('#farmerSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const farmerName = selectedOption.text();
        const location = selectedOption.data('location');
        const rating = selectedOption.data('rating');
        
        if (farmerName) {
            $('#selectedFarmerName').text(farmerName);
            $('#selectedFarmerLocation').text(location);
            $('#selectedFarmerRating').text(rating + '/5');
            
            // Generate stars
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            $('#farmerStars').html(starsHtml);
            
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
        
        const formData = {
            farmerId: $('#farmerSelect').val(),
            farmerName: $('#farmerSelect option:selected').text(),
            orderNumber: $('#orderNumber').val(),
            produceName: $('#produceName').val(),
            issueType: $('#issueType').val(),
            issueDescription: $('#issueDescription').val(),
            urgencyLevel: $('input[name="urgencyLevel"]:checked').val()
        };

        // Basic validation
        if (!formData.farmerId || !formData.orderNumber || !formData.produceName || !formData.issueType || !formData.issueDescription) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all required fields.',
                confirmButtonColor: '#4e73df'
            });
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Submitting...').prop('disabled', true);

        // Simulate AJAX submission
        setTimeout(() => {
            // Success message
            Swal.fire({
                icon: 'success',
                title: 'Report Submitted!',
                text: 'Your report has been submitted successfully. Our team will review it within 24 hours.',
                confirmButtonColor: '#1cc88a'
            });

            // Reset form
            $('#reportIssueForm')[0].reset();
            $('#farmerSelect').val('').trigger('change');
            $('#farmerInfo').addClass('d-none');
            $('.custom-file-label').html('Choose files...');
            
            // Reset button
            submitBtn.html(originalText).prop('disabled', false);
            
            // In real implementation, you would refresh the reports history table here
            console.log('Report submitted:', formData);
            
        }, 1500);
    });
});
</script>

