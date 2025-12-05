<?php
require_once 'auth_check.php';
include 'header.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <h3>Apply for Loan</h3>

            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Loan Application Form</h6>
                        </div>
                        <div class="card-body">
                            <div id="alertMessage" class="alert d-none" role="alert"></div>
                            <form id="applyLoanForm">
                                <div class="mb-3">
                                    <label for="loanPlatform" class="form-label">Choose Loan Platform</label>
                                    <select class="form-control rounded-md" id="loanPlatform" name="loan_platform" required>
                                        <option value="">Loading platforms...</option>
                                    </select>
                                    <small class="form-text text-muted" id="platformInfo"></small>
                                </div>
                                <div class="mb-3">
                                    <label for="loanAmount" class="form-label">Desired Loan Amount (NGN)</label>
                                    <input type="number" class="form-control rounded-md" id="loanAmount" name="loan_amount" min="10000" required placeholder="e.g., 100000">
                                </div>
                                <div class="mb-3">
                                    <label for="loanPurpose" class="form-label">Purpose of Loan</label>
                                    <textarea class="form-control rounded-md" id="loanPurpose" name="loan_purpose" rows="4" required placeholder="Briefly describe why you need the loan (e.g., purchasing seeds, equipment, expanding farm)."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="repaymentPeriod" class="form-label">Preferred Repayment Period (Months)</label>
                                    <input type="number" class="form-control rounded-md" id="repaymentPeriod" name="repayment_period" min="1" max="60" required placeholder="e.g., 12">
                                </div>

                                <hr class="my-4"> <h5 class="mb-3 text-gray-800">Bank Account Details for Disbursement</h5>

                                <div class="mb-3">
                                    <label for="bankName" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control rounded-md" id="bankName" name="bank_name" required placeholder="e.g., Access Bank">
                                </div>
                                <div class="mb-3">
                                    <label for="accountNumber" class="form-label">Account Number</label>
                                    <input type="text" class="form-control rounded-md" id="accountNumber" name="account_number" required placeholder="e.g., 0123456789" pattern="[0-9]{8,15}" title="Account number must be 8-15 digits.">
                                </div>
                                <div class="mb-3">
                                    <label for="accountName" class="form-label">Account Name</label>
                                    <input type="text" class="form-control rounded-md" id="accountName" name="account_name" required placeholder="e.g., John Doe">
                                </div>

                                <div class="mb-3">
                                    <label for="supportingDocuments" class="form-label">Supporting Documents (Optional)</label>
                                    <input type="file" class="form-control-file rounded-md" id="supportingDocuments" name="supporting_documents[]" multiple>
                                    <small class="form-text text-muted">e.g., Business plan, land title, previous financial statements. Max 5 files.</small>
                                </div>

                                <button type="submit" class="btn btn-success rounded-md w-full">Submit Loan Application</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('applyLoanForm');
    const alertMessage = document.getElementById('alertMessage');
    const loanPlatformSelect = document.getElementById('loanPlatform');
    const platformInfo = document.getElementById('platformInfo');
    
    // Store loan companies data
    let loanCompanies = [];

    // Fetch loan companies on page load
    fetchLoanCompanies();

    function fetchLoanCompanies() {
        fetch('views/get_loan_companies.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    loanCompanies = data.data;
                    populateLoanPlatforms(data.data);
                } else {
                    loanPlatformSelect.innerHTML = '<option value="">No loan platforms available</option>';
                    showAlert('No active loan platforms found. Please contact admin.', 'warning');
                }
            })
            .catch(error => {
                console.error('Error fetching loan companies:', error);
                loanPlatformSelect.innerHTML = '<option value="">Error loading platforms</option>';
                showAlert('Failed to load loan platforms. Please refresh the page.', 'danger');
            });
    }

    function populateLoanPlatforms(companies) {
        loanPlatformSelect.innerHTML = '<option value="">Select a Platform</option>';
        
        companies.forEach(company => {
            const option = document.createElement('option');
            option.value = company.company_name;
            option.textContent = company.company_name;
            option.dataset.interestRate = company.interest_rate;
            option.dataset.terms = company.terms;
            option.dataset.contact = company.contact_details;
            loanPlatformSelect.appendChild(option);
        });
    }

    // Show platform details when selected
    loanPlatformSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const interestRate = selectedOption.dataset.interestRate;
            const terms = selectedOption.dataset.terms;
            
            platformInfo.innerHTML = `
                <strong>Interest Rate:</strong> ${interestRate}% | 
                <strong>Terms:</strong> ${terms}
            `;
            platformInfo.classList.add('text-info');
        } else {
            platformInfo.innerHTML = '';
        }
    });

    function showAlert(message, type = 'danger') {
        alertMessage.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning', 'alert-info');
        alertMessage.classList.add(`alert-${type}`);
        alertMessage.textContent = message;
    }

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Clear previous alerts
        alertMessage.classList.add('d-none');
        alertMessage.textContent = '';

        const formData = new FormData(form);

        // Disable submit button to prevent double submission
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        fetch('views/process_loan_application.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Server Response:', data);

            if (data.status === 'success') {
                showAlert(data.message, 'success');
                form.reset();
                platformInfo.innerHTML = '';
                
                // Scroll to alert
                alertMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showAlert('An error occurred while submitting your loan application. Please try again later.', 'danger');
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
});
</script>
