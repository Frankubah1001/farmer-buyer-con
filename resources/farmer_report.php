<?php
require_once 'auth_check.php';
include 'header.php';
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

.card {
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #f6c23e, #dda20a) !important;
}

.bg-gradient-info {
    background: linear-gradient(45deg, #36b9cc, #258391) !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.financial-impact {
    font-weight: bold;
}

.financial-impact.high {
    color: #e74a3b;
}

.financial-impact.medium {
    color: #f6c23e;
}

.financial-impact.low {
    color: #1cc88a;
}
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Report an Issue</h1>

            <div class="row">
                <!-- Report Issue Form Section -->
                <div class="col-xl-5 col-lg-6">
                    <div class="card shadow mb-4 border-left-warning">
                        <div class="card-header py-3 bg-gradient-warning text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-flag me-2"></i>Report an Issue</h6>
                        </div>
                        <div class="card-body">
                            <form id="reportIssueForm">
                                <div class="form-group mb-4">
                                    <label for="buyerSelect" class="font-weight-bold text-gray-700">Select Buyer to Report</label>
                                    <select class="form-control select2" id="buyerSelect" required style="width: 100%;">
                                        <option value="">Choose a buyer...</option>
                                        <option value="1" data-location="Isoko Warri" data-rating="4">Ikenna Anthony</option>
                                        <option value="2" data-location="Ibadan" data-rating="5">Jassy Ubah</option>
                                        <option value="3" data-location="Calabar" data-rating="3">Goke Ibile</option>
                                        <option value="4" data-location="Lagos" data-rating="4">Frank Ubah</option>
                                        <option value="5" data-location="Abuja" data-rating="2">Victor Markets</option>
                                    </select>
                                    <small class="form-text text-muted mt-1">Select the buyer you want to report</small>
                                </div>

                                <div id="buyerInfo" class="alert alert-info d-none mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong id="selectedBuyerName"></strong>
                                            <br>
                                            <small class="text-muted">
                                                Location: <span id="selectedBuyerLocation"></span> | 
                                                Rating: <span id="selectedBuyerRating"></span>
                                            </small>
                                        </div>
                                        <div class="text-warning" id="buyerStars"></div>
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
                                        <option value="non_payment">Non-Payment</option>
                                        <option value="late_payment">Late Payment</option>
                                        <option value="order_cancellation">Unfair Order Cancellation</option>
                                        <option value="communication">Poor Communication</option>
                                        <option value="harassment">Harassment</option>
                                        <option value="false_claims">False Quality Claims</option>
                                        <option value="contract_breach">Contract Breach</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="issueDescription" class="font-weight-bold text-gray-700">Description</label>
                                    <textarea class="form-control" id="issueDescription" rows="4" 
                                              placeholder="Please describe the issue in detail. Include order details, payment information, dates, and any relevant communication..." 
                                              required></textarea>
                                    <small class="form-text text-muted">Be as detailed as possible to help us resolve your issue quickly</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="financialImpact" class="font-weight-bold text-gray-700">Financial Impact</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₦</span>
                                        </div>
                                        <input type="number" class="form-control" id="financialImpact" 
                                               placeholder="Estimated financial loss" min="0" step="1000">
                                    </div>
                                    <small class="form-text text-muted">Optional: Enter the estimated financial impact of this issue</small>
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
                                    <small class="form-text text-muted">Photos of produce, payment receipts, chat screenshots, contract documents</small>
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
                    <div class="card shadow border-left-info">
                        <div class="card-header py-3 bg-gradient-info text-white">
                            <h6 class="m-0 font-weight-bold"><i class="fas fa-history me-2"></i>Your Report History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="reportsHistoryTable" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Report ID</th>
                                            <th>Buyer</th>
                                            <th>Order #</th>
                                            <th>Issue Type</th>
                                            <th>Financial Impact</th>
                                            <th>Date Reported</th>
                                            <th>Status</th>
                                            <th>Urgency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-warning">
                                            <td><strong>#FREP001</strong></td>
                                            <td>Victor Markets</td>
                                            <td>#25</td>
                                            <td>Non-Payment</td>
                                            <td>₦85,000</td>
                                            <td>2025-10-18</td>
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
                                            <td><strong>#FREP002</strong></td>
                                            <td>Jassy Ubah</td>
                                            <td>#19</td>
                                            <td>Late Payment</td>
                                            <td>₦45,000</td>
                                            <td>2025-10-12</td>
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
                                            <td><strong>#FREP003</strong></td>
                                            <td>Goke Ibile</td>
                                            <td>#15</td>
                                            <td>Order Cancellation</td>
                                            <td>₦32,000</td>
                                            <td>2025-10-08</td>
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
                                            <td><strong>#FREP004</strong></td>
                                            <td>Frank Ubah</td>
                                            <td>#08</td>
                                            <td>False Claims</td>
                                            <td>₦28,500</td>
                                            <td>2025-10-02</td>
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
                                        <tr class="table-warning">
                                            <td><strong>#FREP005</strong></td>
                                            <td>Ikenna Anthony</td>
                                            <td>#31</td>
                                            <td>Communication</td>
                                            <td>₦12,000</td>
                                            <td>2025-10-20</td>
                                            <td>
                                                <span class="badge badge-warning badge-pill">
                                                    <i class="fas fa-clock me-1"></i>Pending
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Medium
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Report Statistics -->
                            <div class="row mt-4">
                                <div class="col-md-3 text-center">
                                    <div class="card bg-primary text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">5</div>
                                            <div class="small">Total Reports</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="card bg-success text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">2</div>
                                            <div class="small">Resolved</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="card bg-warning text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">2</div>
                                            <div class="small">Pending</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="card bg-info text-white mb-4">
                                        <div class="card-body py-3">
                                            <div class="h5 mb-0">1</div>
                                            <div class="small">In Review</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Impact Summary -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-gray-700">
                                                <i class="fas fa-chart-line me-2"></i>Financial Impact Summary
                                            </h6>
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <div class="h5 text-danger">₦85,000</div>
                                                    <small class="text-muted">Highest Impact</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="h5 text-warning">₦40,400</div>
                                                    <small class="text-muted">Average Impact</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="h5 text-success">₦202,500</div>
                                                    <small class="text-muted">Total Impact</small>
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
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>

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

    // Handle buyer selection
    $('#buyerSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const buyerName = selectedOption.text();
        const location = selectedOption.data('location');
        const rating = selectedOption.data('rating');
        
        if (buyerName) {
            $('#selectedBuyerName').text(buyerName);
            $('#selectedBuyerLocation').text(location);
            $('#selectedBuyerRating').text(rating + '/5');
            
            // Generate stars
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    starsHtml += '<i class="fas fa-star"></i>';
                } else {
                    starsHtml += '<i class="far fa-star"></i>';
                }
            }
            $('#buyerStars').html(starsHtml);
            
            $('#buyerInfo').removeClass('d-none');
        } else {
            $('#buyerInfo').addClass('d-none');
        }
    });

    // Handle file input
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Handle financial impact input formatting
    $('#financialImpact').on('input', function() {
        let value = $(this).val();
        if (value) {
            $(this).addClass('financial-impact');
            if (value >= 50000) {
                $(this).addClass('high').removeClass('medium low');
            } else if (value >= 20000) {
                $(this).addClass('medium').removeClass('high low');
            } else {
                $(this).addClass('low').removeClass('high medium');
            }
        } else {
            $(this).removeClass('financial-impact high medium low');
        }
    });

    // Handle form submission
    $('#reportIssueForm').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            buyerId: $('#buyerSelect').val(),
            buyerName: $('#buyerSelect option:selected').text(),
            orderNumber: $('#orderNumber').val(),
            produceName: $('#produceName').val(),
            issueType: $('#issueType').val(),
            issueDescription: $('#issueDescription').val(),
            financialImpact: $('#financialImpact').val(),
            urgencyLevel: $('input[name="urgencyLevel"]:checked').val()
        };

        // Basic validation
        if (!formData.buyerId || !formData.orderNumber || !formData.produceName || !formData.issueType || !formData.issueDescription) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in all required fields.',
                confirmButtonColor: '#f6c23e'
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
                text: 'Your report has been submitted successfully. Our team will review it and take appropriate action.',
                confirmButtonColor: '#1cc88a'
            });

            // Reset form
            $('#reportIssueForm')[0].reset();
            $('#buyerSelect').val('').trigger('change');
            $('#buyerInfo').addClass('d-none');
            $('.custom-file-label').html('Choose files...');
            $('#financialImpact').removeClass('financial-impact high medium low');
            
            // Reset button
            submitBtn.html(originalText).prop('disabled', false);
            
            // In real implementation, you would refresh the reports history table here
            console.log('Farmer report submitted:', formData);
            
        }, 1500);
    });
});
</script>

