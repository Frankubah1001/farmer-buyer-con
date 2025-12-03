<?php
require_once 'buyer_auth_check.php';
include 'buyerheader.php';
?>

<!-- Ensure Font Awesome is loaded (usually in header/footer) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* === FULL ORIGINAL FARM THEME – PRESERVED === */
:root {
    --farm-green: #4CAF50;
    --farm-light-green: #8BC34A;
    --farm-earth: #795548;
    --farm-sun: #FFC107;
    --farm-brown: #8B4513;
}

.farm-bg { background: linear-gradient(135deg, var(--farm-light-green) 0%, var(--farm-green) 100%); }
.farm-card { border-radius: 15px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: all 0.3s ease; border: none; background: rgba(255,255,255,0.95); }
.farm-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }

.farm-card-header {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
    color: white; border-bottom: none; padding: 1.5rem; position: relative; overflow: hidden;
}
.farm-card-header::before {
    content: ""; position: absolute; top: 0; left: 0; right: 0; height: 5px;
    background: linear-gradient(90deg, var(--farm-sun), var(--farm-brown));
}

.farm-form-control {
    border-radius: 10px; border: 2px solid #e0e0e0; padding: 12px 15px; transition: all 0.3s; background-color: #f9f9f9;
}
.farm-form-control:focus {
    border-color: var(--farm-light-green); box-shadow: 0 0 0 3px rgba(76,175,80,0.2); background-color: white;
}

.btn-farm-primary {
    background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%);
    color: white; border-radius: 50px; padding: 12px 30px; font-weight: 600; border: none;
}
.btn-farm-primary:hover {
    background: linear-gradient(135deg, var(--farm-light-green) 0%, var(--farm-green) 100%);
    transform: translateY(-2px); box-shadow: 0 5px 15px rgba(76,175,80,0.4);
}

.farm-badge { border-radius: 50px; padding: 6px 12px; font-size: 0.75rem; font-weight: 600; }
.badge-status-pending { background-color: #FFF8E1; color: #FF9800; }
.badge-status-resolved { background-color: #E8F5E9; color: var(--farm-green); }
.badge-status-review { background-color: #E3F2FD; color: #2196F3; }
.badge-urgency-low { background-color: #E8F5E9; color: var(--farm-green); }
.badge-urgency-medium { background-color: #FFF8E1; color: #FF9800; }
.badge-urgency-high { background-color: #FFEBEE; color: #F44336; }

.farm-table { border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
.farm-table thead { background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%); color: white; }
.farm-table th, .farm-table td { padding: 15px; vertical-align: middle; }
.farm-table tbody tr:hover { background-color: #f5f5f5; }

.farm-stats-card { border-radius: 10px; padding: 20px; text-align: center; color: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s; }
.farm-stats-card:hover { transform: translateY(-5px); }
.stats-total { background: linear-gradient(135deg, var(--farm-green) 0%, var(--farm-light-green) 100%); }
.stats-resolved { background: linear-gradient(135deg, #66BB6A 0%, #4CAF50 100%); }
.stats-pending { background: linear-gradient(135deg, #FFB74D 0%, #FF9800 100%); }

.farm-alert {
    border-radius: 10px; padding: 15px; background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
    border-left: 5px solid var(--farm-green);
}

.page-title { color: var(--farm-green); font-weight: 700; position: relative; display: inline-block; margin-bottom: 2rem; }
.page-title::after { content: ""; position: absolute; bottom: -10px; left: 0; width: 50px; height: 3px; background: linear-gradient(90deg, var(--farm-green), var(--farm-sun)); border-radius: 3px; }

.farm-bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234caf50' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
}
</style>

<div id="content-wrapper" class="d-flex flex-column farm-bg-pattern">
<div id="content">
<?php include 'buyertopbar.php'; ?>

<div class="container-fluid py-4">
    <h1 class="h2 mb-4 page-title">Report an Issue</h1>

    <div class="row">

        <!-- REPORT FORM -->
        <div class="col-xl-5 col-lg-6">
            <div class="card farm-card shadow mb-4">
                <div class="card-header farm-card-header py-3 text-white">
                    <h6 class="m-0 font-weight-bold">Report an Issue</h6>
                </div>
                <div class="card-body p-4">
                    <form id="reportIssueForm">

                        <div class="form-group mb-4">
                            <label for="farmerSelect" class="font-weight-bold text-gray-700">Select Farmer to Report</label>
                            <select class="form-control farm-form-control select2" id="farmerSelect" required style="width:100%;">
                                <option value="">Loading farmers...</option>
                            </select>
                        </div>

                        <div id="farmerInfo" class="farm-alert d-none mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="selectedFarmerName"></strong><br>
                                    <small class="text-muted">Location: <span id="selectedFarmerLocation"></span></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="orderNumber" class="font-weight-bold text-gray-700">Order Number</label>
                            <input type="text" class="form-control farm-form-control" id="orderNumber" placeholder="e.g., #12345" required>
                        </div>

                        <div class="form-group mb-4">
                            <label for="produceName" class="font-weight-bold text-gray-700">Produce Involved</label>
                            <select class="form-control farm-form-control select2" id="produceName" required disabled>
                                <option value="">Select a farmer first...</option>
                            </select>
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
                            <textarea class="form-control farm-form-control" id="issueDescription" rows="4" placeholder="Describe the issue in detail..." required></textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700">Upload Evidence <small class="text-muted">(Optional)</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="evidenceUpload" accept="image/*,.pdf,.doc,.docx">
                                <label class="custom-file-label" for="evidenceUpload">Choose files...</label>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-700">Urgency Level</label>
                            <div class="urgency-levels d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyLow" value="low">
                                    <label class="form-check-label urgency-low" for="urgencyLow">Low</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyMedium" value="medium" checked>
                                    <label class="form-check-label urgency-medium" for="urgencyMedium">Medium</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="urgencyLevel" id="urgencyHigh" value="high">
                                    <label class="form-check-label urgency-high" for="urgencyHigh">High</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-farm-primary btn-block py-3">
                            Submit Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- REPORT HISTORY & STATS -->
        <div class="col-xl-7 col-lg-6">
            <div class="card farm-card shadow">
                <div class="card-header farm-card-header py-3 text-white">
                    <h6 class="m-0 font-weight-bold">Your Report History</h6>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table farm-table" id="reportsHistoryTable">
                            <thead>
                                <tr>
                                    <th>Report ID</th>
                                    <th>Farmer</th>
                                    <th>Order #</th>
                                    <th>Issue Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Urgency</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="7" class="text-center py-4 text-muted">Loading reports...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="farm-stats-card stats-total text-center mb-3">
                                <div class="h3 mb-0" id="statTotal">0</div>
                                <div class="small">Total Reports</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="farm-stats-card stats-resolved text-center mb-3">
                                <div class="h3 mb-0" id="statResolved">0</div>
                                <div class="small">Resolved</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="farm-stats-card stats-pending text-center mb-3">
                                <div class="h3 mb-0" id="statPending">0</div>
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

<!-- External Libraries -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({ theme: 'bootstrap4' });

    // Load all data
    loadFarmers();
    loadReportsHistory();
    loadReportStats();

    // Farmer selection
    $('#farmerSelect').on('change', function() {
        const $opt = $(this).find('option:selected');
        const farmerId = $(this).val();
        const name = $opt.text().trim();
        const location = $opt.data('location') || 'Not specified';

        if (farmerId) {
            $('#selectedFarmerName').text(name);
            $('#selectedFarmerLocation').text(location);
            $('#farmerInfo').removeClass('d-none');
            loadFarmerProduce(farmerId);
        } else {
            $('#farmerInfo').addClass('d-none');
            $('#produceName').html('<option value="">Select a farmer first...</option>').prop('disabled', true).trigger('change');
        }
    });

    function loadFarmerProduce(farmerId) {
        const $sel = $('#produceName');
        $sel.html('<option>Loading produce...</option>').prop('disabled', true);

        $.get('views/report.php?action=get_farmer_produce&farmer_id=' + farmerId, function(res) {
            $sel.empty().append('<option value="">Select produce...</option>');
            if (res.success && res.produce?.length > 0) {
                res.produce.forEach(p => {
                    $sel.append(`<option value="${p.produce}">${p.produce} (Available: ${p.quantity})</option>`);
                });
                $sel.prop('disabled', false);
            } else {
                $sel.append('<option value="">No produce found</option>');
            }
            $sel.trigger('change');
        }, 'json');
    }

    // Load farmers
    function loadFarmers() {
        $.get('views/report.php?action=get_farmers', function(res) {
            const $sel = $('#farmerSelect');
            $sel.empty().append('<option value="">Choose a farmer...</option>');
            if (res.success && res.farmers?.length > 0) {
                res.farmers.forEach(f => {
                    $sel.append(
                        $('<option></option>')
                            .val(f.user_id)
                            .text(`${f.first_name} ${f.last_name}`)
                            .data('location', f.location || '')
                    );
                });
            } else {
                $sel.append('<option value="" disabled>No farmers available</option>');
            }
            $sel.trigger('change.select2');
        }, 'json');
    }

    // Load report history
    function loadReportsHistory() {
        $.get('views/report.php?action=get_reports_history', function(res) {
            const $tbody = $('#reportsHistoryTable tbody');
            $tbody.empty();
            if (!res.success || !res.reports || res.reports.length === 0) {
                $tbody.append('<tr><td colspan="7" class="text-center py-5 text-muted">No reports submitted yet</td></tr>');
                return;
            }
            res.reports.forEach(r => {
                const status = getStatusBadge(r.status);
                const urgency = getUrgencyBadge(r.urgency_level);
                $tbody.append(`
                    <tr>
                        <td><strong>${r.report_id}</strong></td>
                        <td>${r.farmer_name}</td>
                        <td>${r.order_number}</td>
                        <td>${r.issue_type}</td>
                        <td>${r.date_reported}</td>
                        <td>${status}</td>
                        <td>${urgency}</td>
                    </tr>
                `);
            });
        }, 'json');
    }

    // Load stats
    function loadReportStats() {
        $.get('views/report.php?action=get_report_stats', function(res) {
            if (res.success && res.stats) {
                $('#statTotal').text(res.stats.total || 0);
                $('#statResolved').text(res.stats.resolved || 0);
                $('#statPending').text(res.stats.pending || 0);
            }
        }, 'json');
    }

    function getStatusBadge(s) {
        const key = (s || '').toLowerCase();
        const map = { pending: 'Pending', resolved: 'Resolved', 'in review': 'In Review' };
        const cls = key === 'resolved' ? 'badge-status-resolved' : key === 'in review' ? 'badge-status-review' : 'badge-status-pending';
        return `<span class="farm-badge ${cls}">${map[key] || 'Pending'}</span>`;
    }

    function getUrgencyBadge(u) {
        const key = (u || '').toLowerCase();
        const map = { low: 'Low', medium: 'Medium', high: 'High' };
        const cls = key === 'low' ? 'badge-urgency-low' : key === 'high' ? 'badge-urgency-high' : 'badge-urgency-medium';
        return `<span class="farm-badge ${cls}">${map[key] || 'Medium'}</span>`;
    }

    // File input label
    $('.custom-file-input').on('change', function() {
        const fileName = this.files[0]?.name || 'Choose files...';
        $(this).next('.custom-file-label').html(fileName);
    });

    // Submit report
    $('#reportIssueForm').on('submit', function(e) {
        e.preventDefault();

        // Create FormData and manually append all fields with correct names
        const formData = new FormData();
        formData.append('action', 'submit_report');
        formData.append('farmerId', $('#farmerSelect').val());
        formData.append('orderNumber', $('#orderNumber').val());
        formData.append('produceName', $('#produceName').val());
        formData.append('issueType', $('#issueType').val());
        formData.append('issueDescription', $('#issueDescription').val());
        formData.append('urgencyLevel', $('input[name="urgencyLevel"]:checked').val());
        
        // Append file if present
        if ($('#evidenceUpload')[0].files[0]) {
            formData.append('evidence', $('#evidenceUpload')[0].files[0]);
        }

        const $btn = $(this).find('button[type="submit"]');
        $btn.html('Submitting...').prop('disabled', true);

        $.ajax({
            url: 'views/report.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Success!', res.message || 'Report submitted successfully!', 'success');
                    $('#reportIssueForm')[0].reset();
                    $('#farmerSelect, #produceName').val('').trigger('change');
                    $('#farmerInfo').addClass('d-none');
                    $('#produceName').html('<option value="">Select a farmer first...</option>').prop('disabled', true);
                    $('.custom-file-label').html('Choose files...');
                    loadReportsHistory();
                    loadReportStats();
                } else {
                    Swal.fire('Error', res.message || 'Failed to submit report.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire('Error', 'Network error. Please try again.', 'error');
            },
            complete: function() {
                $btn.html('Submit Report').prop('disabled', false);
            }
        });
    });
});
</script>