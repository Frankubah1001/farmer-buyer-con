<?php
require_once 'auth_check.php';
include 'DBcon.php';
?>
<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit;
}

// Check if profile information is completed
if (isset($_SESSION['info_completed']) && $_SESSION['info_completed'] == 0) {
    header('Location: otherFarmerDetails.php'); // Redirect to complete profile if not completed
    exit;
}

// Check if user is approved by CBN (0=pending, 1=approved, 2=rejected)
if (isset($_SESSION['cbn_approved'])) {
    if ($_SESSION['cbn_approved'] == 0) { // Pending approval
        header('Location: awaiting_approval.php');
        exit;
    } elseif ($_SESSION['cbn_approved'] == 2) { // Rejected
        // If they managed to get here while rejected, destroy session and redirect to login
        session_destroy();
        header('Location: login.php?status=rejected');
        exit;
    }
} else {
    // This case should ideally not happen if login.view.php is setting cbn_approved in session.
    // As a fallback, redirect to awaiting approval or re-login.
    header('Location: awaiting_approval.php');
    exit;
}

// If all checks pass and cbn_approved == 1, the user can access the dashboard.
?>
<?php include 'header.php'; ?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <?php include 'topbar.php'; ?>
        
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Your main content here (cards, charts, tables) -->
                 <!-- Content Row -->
<div class="row">
<!-- Earnings (Monthly) Card Example -->
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

<!-- Earnings (Monthly) Card Example -->
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

<!-- Earnings (Monthly) Card Example -->
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

<!-- Pending Requests Card Example -->
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

<!-- Earnings (Monthly) Card Example -->
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

<!-- Earnings (Monthly) Card Example -->
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


<!-- Content Row -->

<div class="row">




<div class="card-body">
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
</div>
        </div>
    

    <?php include 'footer.php'; ?>
</div>

<?php include 'script.php'; ?>
<script src="views/dashboard_data.js">

</script>