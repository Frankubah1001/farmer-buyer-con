<?php
require_once 'auth_check.php';
include 'DBcon.php';
?>
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if profile information is completed
if (isset($_SESSION['info_completed']) && $_SESSION['info_completed'] == 0) {
    header('Location: otherFarmerDetails.php');
    exit;
}

// Check if user is approved by CBN (0=pending, 1=approved, 2=rejected)
if (isset($_SESSION['cbn_approved'])) {
    if ($_SESSION['cbn_approved'] == 0) {
        header('Location: awaiting_approval.php');
        exit;
    } elseif ($_SESSION['cbn_approved'] == 2) {
        session_destroy();
        header('Location: login.php?status=rejected');
        exit;
    }
} else {
    header('Location: awaiting_approval.php');
    exit;
}
?>
<?php include 'header.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <?php include 'topbar.php'; ?>
        
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">

                <!-- Wallet Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Wallet Balance</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="walletBalance">₦0.00</div>
                                    <button class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#withdrawalModal">
                                        Withdraw
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings (Monthly) Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Earnings (Monthly)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEarningsMnth"> ₦40,000</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings (Annual) Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Earnings (Annual)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEarningsYr"> ₦215,000</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shapes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Frequently Sold Farm Produce Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Frequently Sold Farm Produce
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="frequent-produce"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-pepper-hot fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Orders Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-dark shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_order"></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-atom fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row -->
            <div class="row">
                <!-- Total Number Of Farm Products Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Number Of Farm Products </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-products"></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-seedling fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Number Of Orders (Monthly) Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Number Of Orders (Monthly)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="numOrdersMnth">10</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-poop fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Orders (Yearly) Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Total Orders (Yearly)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="numOrdersYr">15</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-atom fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Recent Transactions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Most Recent Transactions</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name Of Buyer</th>
                                            <th>Location</th>
                                            <th>Produce Purchased</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Date Of Transaction</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactionsBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawal History Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Withdrawal History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="withdrawalTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Bank</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withdrawalHistoryBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>
</div>

<!-- Withdrawal Modal -->
<div class="modal fade" id="withdrawalModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawalModalLabel">Request Withdrawal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Deduction Information -->
                <div class="alert alert-info" id="deductionInfo" style="font-size: 0.9em;">
                    <h6 class="font-weight-bold mb-3">💰 Earnings Breakdown:</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td>Total Earnings (Annual):</td>
                            <td class="text-right"><strong id="modalTotalEarnings">₦0.00</strong></td>
                        </tr>
                        <tr class="text-danger">
                            <td>&nbsp;&nbsp;- Platform Fee (0.5%):</td>
                            <td class="text-right">₦<span id="modalPlatformFee">0.00</span></td>
                        </tr>
                        <tr class="text-danger">
                            <td>&nbsp;&nbsp;- Admin Fee (1.5%):</td>
                            <td class="text-right">₦<span id="modalAdminFee">0.00</span></td>
                        </tr>
                        <tr class="text-danger border-bottom">
                            <td><strong>&nbsp;&nbsp;Total Deductions (2%):</strong></td>
                            <td class="text-right"><strong>₦<span id="modalTotalDeductions">0.00</span></strong></td>
                        </tr>
                        <tr class="text-success">
                            <td><strong>Net Earnings:</strong></td>
                            <td class="text-right"><strong id="modalNetEarnings">₦0.00</strong></td>
                        </tr>
                        <tr class="text-warning">
                            <td>&nbsp;&nbsp;- Already Withdrawn:</td>
                            <td class="text-right">₦<span id="modalWithdrawn">0.00</span></td>
                        </tr>
                        <tr class="border-top" style="background-color: #e7f3ff;">
                            <td><strong>💵 Available Balance:</strong></td>
                            <td class="text-right"><strong id="modalAvailableBalance2">₦0.00</strong></td>
                        </tr>
                    </table>
                </div>
                
                <hr>
                
                <form id="withdrawalForm">
                    <h6 class="font-weight-bold mb-3">Bank Details:</h6>
                    <div class="form-group">
                        <label for="withdrawAmount">Amount (₦) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="withdrawAmount" name="amount" required min="100" step="0.01">
                        <small class="text-muted">Available Balance: <span id="modalAvailableBalance">₦0.00</span></small>
                    </div>
                    <div class="form-group">
                        <label for="bankName">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bankName" name="bank_name" required>
                    </div>
                    <div class="form-group">
                        <label for="accountNumber">Account Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="accountNumber" name="account_number" required pattern="\d{10}" maxlength="10">
                        <small class="text-muted">Enter 10-digit account number</small>
                    </div>
                    <div class="form-group">
                        <label for="accountName">Account Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="accountName" name="account_name" required>
                    </div>
                    <div id="withdrawalMessage" class="mt-2"></div>
                    <button type="submit" class="btn btn-primary btn-block">Submit Withdrawal Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal Detail Modal -->
<div class="modal fade" id="withdrawalDetailModal" tabindex="-1" role="dialog" aria-labelledby="withdrawalDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawalDetailModalLabel">Withdrawal Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="withdrawalDetailBody">
                <!-- Details will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<?php include 'script.php'; ?>
<script src="views/dashboard_data.js"></script>