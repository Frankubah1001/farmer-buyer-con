<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="farmersDashboard.php">
        <div class="sidebar-brand-text mx-3">F & B  <sup>Connect</sup></div>
    </a>
    <hr class="sidebar-divider my-0">
    <!-- Nav Items -->
    <li class="nav-item active">
        <a class="nav-link" href="farmersdashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <!-- ... Other sidebar items ... -->
    <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
        aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-tree"></i>
        <span>Farm Products</span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="view_product.php">View Farm Products</a>
            <a class="collapse-item" href="add_product.php">Add Farm Products</a>
            
        </div>
    </div>
</li>

<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
        aria-expanded="true" aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-poo"></i>
        <span>Orders</span>
    </a>
    <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="view_order.php">View Orders</a>
            <!-- <a class="collapse-item" href="order_history.php">Orders History</a> -->
            
        </div>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Orders"
        aria-expanded="true" aria-controls="Orders">
        <i class="fas fa-fw fa-envelope-open"></i>
        <span>Loans</span>
    </a>
    <div id="Orders" class="collapse" aria-labelledby="headingUtilities"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="apply_loan.php">Apply For Loan</a>
            <a class="collapse-item" href="loan_history.php">Loan History</a>
            
        </div>
    </div>
</li>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
        aria-expanded="true" aria-controls="collapsePages">
        <i class="fas fa-fw fa-truck"></i>
        <span>Transportation</span>
    </a>
    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="view_transport.php">View Transportation</a>
            <a class="collapse-item" href="track_transport.php">Track Transportation</a>
        </div>
    </div>
</li>

<!-- Nav Item - Charts -->
<li class="nav-item">
    <a class="nav-link" href="view_farmers.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>View Farmers</span></a>
</li>
<li class="nav-item">
    <a class="nav-link" href="farmer_report.php">
        <i class="fas fa-fw fa-flag-checkered"></i>
        <span>Report Issues</span></a>
</li>
<!-- Nav Item - Tables -->
<li class="nav-item">
    <a class="nav-link" href="profile.php">
        <i class="fas fa-fw fa-user-edit"></i>
        <span>Profile</span></a>
</li>
<li class="nav-item">
    <a class="nav-link" href="change_password.php">
        <i class="far fa-fw fa-edit"></i>
        <span>Change Password</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="logout.php" id="logoutBtn">
        <i class="fas fa-sign-out-alt"></i>
        
        <span>Logout</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>
</ul>
<!-- End of Sidebar -->
 <script>

document.getElementById('logoutBtn').addEventListener('click', function() {
    // Send logout request
    fetch('logout.php', {
        method: 'POST'
    })
    .then(response => {
        // Always redirect to login.php, even if fetch fails
        window.location.href = 'login.php';
    })
    .catch(error => {
        // Still redirect if there's an error
        window.location.href = 'login.php';
    });
});

 </script>
