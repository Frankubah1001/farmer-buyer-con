<?php
// sidebar.php - Common sidebar with dynamic active class (assumes $active is set in including page)
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h3 class="sidebar-text">AgriAdmin</h3>
       
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php if(isset($active) && $active == 'dashboard') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i> <span class="sidebar-text">Dashboard</span></a></li>
        <li><a href="farmers.php" class="<?php if(isset($active) && $active == 'farmers') echo 'active'; ?>"><i class="fas fa-user-tie"></i> <span class="sidebar-text">Manage Farmers</span></a></li>
        <li><a href="buyers.php" class="<?php if(isset($active) && $active == 'buyers') echo 'active'; ?>"><i class="fas fa-users"></i> <span class="sidebar-text">Manage Buyers</span></a></li>
        <li class="has-submenu">
            <a href="#" class="<?php if(isset($active) && in_array($active, ['incentives', 'loan_applications', 'farm_tools_applications', 'grant_applications'])) echo 'active'; ?>">
                <i class="fas fa-gift"></i> <span class="sidebar-text">Manage Incentives</span>
                <i class="fas fa-chevron-down submenu-icon"></i>
            </a>
            <ul class="submenu">
                <li><a href="incentives.php" class="<?php if(isset($active) && $active == 'incentives') echo 'active'; ?>"><i class="fas fa-building"></i> Loan Companies</a></li>
                <li><a href="loan_applications.php" class="<?php if(isset($active) && $active == 'loan_applications') echo 'active'; ?>"><i class="fas fa-file-invoice-dollar"></i> Loan Applications</a></li>
                <li><a href="farm_tools_applications.php" class="<?php if(isset($active) && $active == 'farm_tools_applications') echo 'active'; ?>"><i class="fas fa-tools"></i> Farm Tools Applications</a></li>
                <li><a href="grant_applications.php" class="<?php if(isset($active) && $active == 'grant_applications') echo 'active'; ?>"><i class="fas fa-hand-holding-usd"></i> Grant Applications</a></li>
            </ul>
        </li>
        <li><a href="prices.php" class="<?php if(isset($active) && $active == 'prices') echo 'active'; ?>"><i class="fas fa-tags"></i> <span class="sidebar-text">Manage Produce Prices</span></a></li>
        <li><a href="orders.php" class="<?php if(isset($active) && $active == 'orders') echo 'active'; ?>"><i class="fas fa-shopping-cart"></i> <span class="sidebar-text">Manage Orders</span></a></li>
        <li><a href="transport.php" class="<?php if(isset($active) && $active == 'transport') echo 'active'; ?>"><i class="fas fa-truck"></i> <span class="sidebar-text">Manage Transport</span></a></li>
        <li><a href="reports.php" class="<?php if(isset($active) && $active == 'reports') echo 'active'; ?>"><i class="fas fa-flag"></i> <span class="sidebar-text">Manage Reports</span></a></li>
        <li><a href="profile.php" class="<?php if(isset($active) && $active == 'profile') echo 'active'; ?>"><i class="fas fa-user-cog"></i> <span class="sidebar-text">Manage Users</span></a></li>
        <li><a href="settings.php" class="<?php if(isset($active) && $active == 'settings') echo 'active'; ?>"><i class="fas fa-gear"></i> <span class="sidebar-text">Settings</span></a></li>
        <li><a href="signout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Sign Out</span></a></li>
    </ul>
</aside>