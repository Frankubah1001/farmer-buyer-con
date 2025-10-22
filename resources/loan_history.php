<?php
require_once 'auth_check.php';
include 'header.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <h3 class="h3 mb-4 text-gray-800">My Loan Applications</h3>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Applications</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="platformFilter" class="form-label">Filter by Platform:</label>
                            <select class="form-control rounded-md" id="platformFilter">
                                <option value="">All Platforms</option>
                                <option value="ANCHOR_BORROWER">ANCHOR Borrower</option>
                                <option value="BANK_OF_AGRIC">Bank of Agric</option>
                                </select>
                        </div>
                        <div class="col-md-4">
                            <label for="statusFilter" class="form-label">Filter by Status:</label>
                            <select class="form-control rounded-md" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Disbursed">Disbursed</option>
                                </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary rounded-md w-100" id="applyFiltersBtn">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Your Applications History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="loanApplicationsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>App ID</th>
                                    <th>Platform</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Applied On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center" id="pagination">
                            </ul>
                    </nav>
                </div>
            </div>

        </div>
        </div>
    <?php include 'footer.php'; ?>
</div>
<div class="modal fade" id="loanDetailModal" tabindex="-1" role="dialog" aria-labelledby="loanDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-md">
            <div class="modal-header">
                <h5 class="modal-title" id="loanDetailModalLabel">Loan Application Details</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100 p-3 bg-light">
                            <h6 class="text-primary mb-3">Loan Information</h6>
                            <p><strong>Application ID:</strong> <span id="modalAppId"></span></p>
                            <p><strong>Platform:</strong> <span id="modalLoanPlatform"></span></p>
                            <p><strong>Amount:</strong> NGN <span id="modalLoanAmount"></span></p>
                            <p><strong>Purpose:</strong> <span id="modalLoanPurpose"></span></p>
                            <p><strong>Repayment Period:</strong> <span id="modalRepaymentPeriod"></span> months</p>
                            <p><strong>Status:</strong> <span id="modalApplicationStatus" class="badge"></span></p>
                            <p><strong>Applied On:</strong> <span id="modalCreatedAt"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100 p-3 bg-light">
                            <h6 class="text-primary mb-3">Bank Details</h6>
                            <p><strong>Bank Name:</strong> <span id="modalBankName"></span></p>
                            <p><strong>Account Number:</strong> <span id="modalAccountNumber"></span></p>
                            <p><strong>Account Name:</strong> <span id="modalAccountName"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card shadow-sm p-3">
                            <h6 class="text-primary mb-3">Supporting Documents</h6>
                            <div id="modalDocumentList" class="d-flex flex-wrap align-items-center">
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-md" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentViewerModal" tabindex="-1" role="dialog" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-md">
            <div class="modal-header">
                <h5 class="modal-title" id="documentViewerModalLabel">Document View</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="documentContent" style="width: 100%; height: 70vh;">
                    </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary rounded-md" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php include 'script.php'; ?>

<script>
$(document).ready(function() {
    let currentPage = 1;
    let totalPages = 0;

    function loadLoanApplications(page) {
        currentPage = page;
        const platformFilter = $('#platformFilter').val();
        const statusFilter = $('#statusFilter').val();

        $.ajax({
            url: 'get_loan_applications_backend.php', // Backend script to fetch data
            type: 'GET',
            dataType: 'json',
            data: {
                page: page,
                platform: platformFilter,
                status: statusFilter
            },
            success: function(response) {
                if (response.error) {
                    $('#loanApplicationsTable tbody').html('<tr><td colspan="7" class="text-center">' + response.error + '</td></tr>');
                    $('#pagination').empty();
                } else {
                    populateLoanApplicationsTable(response.data);
                    totalPages = response.total_pages;
                    populatePagination(totalPages, currentPage);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching loan applications:", error);
                $('#loanApplicationsTable tbody').html('<tr><td colspan="7" class="text-center">Error loading loan applications.</td></tr>');
                $('#pagination').empty();
            }
        });
    }

    function populateLoanApplicationsTable(applications) {
        const tbody = $('#loanApplicationsTable tbody');
        tbody.empty();

        if (applications.length === 0) {
            tbody.html('<tr><td colspan="7" class="text-center">No loan applications found.</td></tr>');
            return;
        }

        applications.forEach(app => {
            const row = `
                <tr>
                    <td>${app.application_id}</td>
                    <td>${app.loan_platform}</td>
                    <td>NGN ${parseFloat(app.loan_amount).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td>${app.loan_purpose.substring(0, 50)}${app.loan_purpose.length > 50 ? '...' : ''}</td>
                    <td><span class="badge ${getStatusBadgeClass(app.application_status)}">${app.application_status}</span></td>
                    <td>${new Date(app.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-info btn-sm rounded-md view-details-btn" data-toggle="modal" data-target="#loanDetailModal" data-application-id="${app.application_id}">View Details</button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Pending':
                return 'bg-warning text-dark'; // Bootstrap 5: bg-warning text-dark
            case 'Approved':
                return 'bg-success'; // Bootstrap 5: bg-success
            case 'Rejected':
                return 'bg-danger'; // Bootstrap 5: bg-danger
            case 'Disbursed':
                return 'bg-primary'; // Bootstrap 5: bg-primary
            default:
                return 'bg-secondary'; // Bootstrap 5: bg-secondary
        }
    }

    function populatePagination(totalPages, currentPage) {
        const pagination = $('#pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        // Previous button
        const prevClass = currentPage === 1 ? 'disabled' : '';
        pagination.append(`<li class="page-item ${prevClass}"><a class="page-link page-number-link" href="#" data-page="${currentPage - 1}">Previous</a></li>`);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            pagination.append(`<li class="page-item ${activeClass}"><a class="page-link page-number-link" href="#" data-page="${i}">${i}</a></li>`);
        }

        // Next button
        const nextClass = currentPage === totalPages ? 'disabled' : '';
        pagination.append(`<li class="page-item ${nextClass}"><a class="page-link page-number-link" href="#" data-page="${currentPage + 1}">Next</a></li>`);
    }

    // Event listeners for filters and pagination
    $('#applyFiltersBtn').on('click', function() {
        loadLoanApplications(1); // Reset to first page when filters change
    });

    $(document).on('click', '.page-number-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (!isNaN(page) && page > 0 && page <= totalPages) {
            loadLoanApplications(page);
        }
    });

    // Handle modal display and data loading
    $('#loanDetailModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget); // Button that triggered the modal
        const applicationId = button.data('application-id'); // Extract info from data-* attributes

        // Clear previous modal content
        $('#modalAppId').text('');
        $('#modalLoanPlatform').text('');
        $('#modalLoanAmount').text('');
        $('#modalLoanPurpose').text('');
        $('#modalRepaymentPeriod').text('');
            $('#modalApplicationStatus').text('');
        $('#modalCreatedAt').text('');
        $('#modalBankName').text('');
        $('#modalAccountNumber').text('');
        $('#modalAccountName').text('');
        $('#modalDocumentList').empty();


        // Fetch full details for the specific application
        $.ajax({
            url: 'get_loan_applications_backend.php', // Use the same backend, but request single ID
            type: 'GET',
            dataType: 'json',
            data: { single_id: applicationId },
            success: function(response) {
                if (response.error) {
                    console.error("Error fetching single loan application details:", response.error);
                    // Display error in modal or as an alert
                    $('#modalLoanPurpose').text('Error loading details.');
                } else if (response.data && response.data.length > 0) {
                    const app = response.data[0]; // Get the single application object

                    // Populate modal fields
                    $('#modalAppId').text(app.application_id);
                    $('#modalLoanPlatform').text(app.loan_platform);
                    $('#modalLoanAmount').text(parseFloat(app.loan_amount).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#modalLoanPurpose').text(app.loan_purpose);
                    $('#modalRepaymentPeriod').text(app.repayment_period_months);

                    const statusBadge = $('#modalApplicationStatus');
                    statusBadge.text(app.application_status);
                    statusBadge.removeClass().addClass('badge ' + getStatusBadgeClass(app.application_status)); // Apply status badge class

                    $('#modalCreatedAt').text(new Date(app.created_at).toLocaleString()); // More detailed date/time

                    $('#modalBankName').text(app.bank_name || 'N/A');
                    $('#modalAccountNumber').text(app.account_number || 'N/A');
                    $('#modalAccountName').text(app.account_name || 'N/A');

                    const docList = $('#modalDocumentList');
                    if (app.document_paths && app.document_paths.length > 0) {
                        app.document_paths.forEach(docPath => {
                            // CORRECTED PATH: Prepend 'views/' to the docPath
                            const correctedDocPath = 'views/' + docPath;
                            const fileName = docPath.split('/').pop(); // Extract file name
                            const fileExt = fileName.split('.').pop().toLowerCase();
                            let docHtml = '';

                            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                                docHtml = `
                                    <div class="document-preview-box border rounded-md p-2 m-1 text-center">
                                        <a href="#" class="view-document-link" data-file-path="${correctedDocPath}" data-file-type="image">
                                            <img src="${correctedDocPath}" alt="${fileName}" class="img-fluid rounded-md" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                            <small class="d-block text-truncate mt-1" style="max-width: 80px;">${fileName}</small>
                                        </a>
                                    </div>
                                `;
                            } else if (fileExt === 'pdf') {
                                docHtml = `
                                    <div class="document-preview-box border rounded-md p-2 m-1 text-center">
                                        <a href="#" class="view-document-link" data-file-path="${correctedDocPath}" data-file-type="pdf">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-file-earmark-pdf-fill text-danger" viewBox="0 0 16 16">
                                                <path d="M5.521 12.671c-.04-.11-.12-.196-.226-.233a.62.62 0 0 0-.256-.039c-.104.02-.244.09-.374.237-.13.147-.24.3-.31.42-.07.12-.11.2-.11.248 0 .048.04.09.09.124.05.033.14.05.244.023a.8.8 0 0 0 .256-.039c.106-.037.196-.123.226-.233.04-.11.07-.223.07-.346zm2.296 0c-.04-.11-.12-.196-.226-.233a.62.62 0 0 0-.256-.039c-.104.02-.244.09-.374.237-.13.147-.24.3-.31.42-.07.12-.11.2-.11.248 0 .048.04.09.09.124.05.033.14.05.244.023a.8.8 0 0 0 .256-.039c.106-.037.196-.123.226-.233.04-.11.07-.223.07-.346zm2.296 0c-.04-.11-.12-.196-.226-.233a.62.62 0 0 0-.256-.039c-.104.02-.244.09-.374.237-.13.147-.24.3-.31.42-.07.12-.11.2-.11.248 0 .048.04.09.09.124.05.033.14.05.244.023a.8.8 0 0 0 .256-.039c.106-.037.196-.123.226-.233.04-.11.07-.223.07-.346z"/>
                                                <path d="M2 2a2 2 0 0 1 2-2h5.667a2 2 0 0 1 1.416.589l3.333 3.333A2 2 0 0 1 16 5.667V14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm5 2.5a.5.5 0 0 0-1 0v5.793L4.146 8.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l3.5-3.5a.5.5 0 0 0-.708-.708L7.5 10.293V4.5z"/>
                                            </svg>
                                            <small class="d-block text-truncate mt-1" style="max-width: 80px;">${fileName}</small>
                                        </a>
                                    </div>
                                `;
                            } else { // For other document types (doc, docx, etc.)
                                docHtml = `
                                    <div class="document-preview-box border rounded-md p-2 m-1 text-center">
                                        <a href="${correctedDocPath}" target="_blank"> <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-file-earmark-text-fill text-secondary" viewBox="0 0 16 16">
                                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4.5 11a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7zM4.5 13a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
                                            </svg>
                                            <small class="d-block text-truncate mt-1" style="max-width: 80px;">${fileName}</small>
                                        </a>
                                    </div>
                                `;
                            }
                            docList.append(docHtml);
                        });
                    } else {
                        docList.append('<div class="col-12"><p class="text-muted">No supporting documents uploaded.</p></div>');
                    }

                } else {
                    $('#modalLoanPurpose').text('Application details not found.');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error fetching single loan application:", error);
                $('#modalLoanPurpose').text('Error loading details due to network issue.');
            }
        });
    });

    // Handle clicks on document links within the loan detail modal
    $(document).on('click', '#modalDocumentList .view-document-link', function(event) {
        event.preventDefault(); // Prevent opening in new tab

        const filePath = $(this).data('file-path');
        const fileType = $(this).data('file-type');
        const documentContentDiv = $('#documentContent');

        documentContentDiv.empty(); // Clear previous content

        if (fileType === 'image') {
            documentContentDiv.append(`<img src="${filePath}" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;">`);
        } else if (fileType === 'pdf') {
            documentContentDiv.append(`<iframe src="${filePath}" style="width: 100%; height: 100%; border: none;"></iframe>`);
        } else {
            // For other file types, provide a download link or open in new tab as fallback
            documentContentDiv.append(`<p class="text-center">Preview not available for this file type. <a href="${filePath}" target="_blank">Click here to download/view in new tab</a>.</p>`);
        }

        // Show the document viewer modal
        $('#documentViewerModal').modal('show');
    });


    // Initial load of applications
    loadLoanApplications(1);
});
</script>
