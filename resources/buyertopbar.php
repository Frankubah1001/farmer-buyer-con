<?php
//session_start();
include 'DBcon.php';

// Initialize profile picture variable
$profilePicture = 'resources/asset/img/undraw_profile_1.svg'; // Default image path

// Check if the user is logged in (you might need to adjust this based on your session management)
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];

    // Prepare and execute the SQL query to fetch the profile picture
    $stmt = mysqli_prepare($conn, "SELECT profile_picture FROM buyers WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $userEmail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // If a profile picture path exists in the database, update the variable
        if (!empty($user['profile_picture'])) {
            $profilePicture = $user['profile_picture'];
        }
    }

    mysqli_stmt_close($stmt);
}

// No need to close $conn here as it might be used in included files
?>
<style>
    .sidebar .nav-item .nav-link .badge-counter, .topbar .nav-item .nav-link .badge-counter {
    right: -0.6rem;
    margin-top: -.25rem;
}
</style>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow d-sm-none">
                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-search fa-fw"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                    aria-labelledby="searchDropdown">
                    <form class="form-inline mr-auto w-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Search for..." aria-label="Search"
                                aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>

            <li class="nav-item dropdown no-arrow mx-1">
                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell fa-fw"></i>
                    <span class="badge badge-danger badge-counter">3+</span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Alerts Center
                    </h6>
                    
                </div>
            </li>


            <div class="topbar-divider d-none d-sm-block"></div>

            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><h6>
                    <?php
                    if (isset($_SESSION['firstname'])) {
                        echo strtoupper(htmlspecialchars($_SESSION['firstname']) . ' ' . htmlspecialchars($_SESSION['lastname']));
                    } else {
                        echo "GUEST"; // Or strtoupper("Guest") if you prefer
                    }
                ?>
</h6>
                    </span>
                    <?php echo ""; ?>
                    <img class="img-profile rounded-circle"
                        src="<?php echo htmlspecialchars($profilePicture); ?>">
                </a>
                </li>
    </ul>
</nav>

<script>
    function fetchOrderCountAndDetails() {
    fetch('buyer_topbar_not.php')
        .then(response => response.json())
        .then(data => {
            console.log('Response from PHP:', data);

            const orderCountBadge = document.querySelector('.badge-counter');
            if (data.orderCount !== undefined && orderCountBadge) {
                orderCountBadge.textContent = data.orderCount > 0 ? data.orderCount + '+' : '0';
            }

            const alertsDropdownList = document.querySelector('#alertsDropdown + .dropdown-menu .dropdown-header').parentNode;
            const showAllLink = alertsDropdownList.querySelector('.dropdown-item.text-center.small.text-gray-500');

            // Clear old order items (but leave showAllLink if exists)
            alertsDropdownList.querySelectorAll('.order-notification-item').forEach(item => item.remove());

            if (data.recentOrders && Array.isArray(data.recentOrders) && data.recentOrders.length > 0) {
                data.recentOrders.forEach((order, index) => {
                    const orderItem = document.createElement('a');
                    orderItem.classList.add('dropdown-item', 'text-center', 'order-notification-item');
                    orderItem.href = `order_history.php?order_id=${order.order_id}`;
                    orderItem.textContent = `${index + 1}. You have a new order, click to open order`;
                    orderItem.dataset.orderId = order.order_id;

                    orderItem.addEventListener('click', function (event) {
                        event.preventDefault();

                        fetch('buyer_mark_order_read.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `order_id=${order.order_id}`
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                this.remove();
                                fetchOrderCountAndDetails();
                                window.location.href = this.href; // redirect to view_order
                            }
                        });
                    });

                    alertsDropdownList.insertBefore(orderItem, showAllLink);
                });

                if (showAllLink) {
                    showAllLink.style.display = 'block';
                    showAllLink.href = 'order_history.php';
                }

            } else {
                if (showAllLink) {
                    showAllLink.style.display = 'none';
                }

                const noOrdersItem = document.createElement('a');
                noOrdersItem.classList.add('dropdown-item', 'text-center', 'small', 'text-gray-500');
                noOrdersItem.href = '#';
                noOrdersItem.textContent = 'No new orders';
                alertsDropdownList.appendChild(noOrdersItem);
            }

            // Save to localStorage for cross-tab sync
            localStorage.setItem('notificationCount', data.orderCount);

            // Debug logs if any
            if (data.error) console.error('Error:', data.error);
            if (data.debug) data.debug.forEach(msg => console.debug('PHP Debug:', msg));
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}

// On page load
document.addEventListener('DOMContentLoaded', fetchOrderCountAndDetails);

// On dropdown open
document.querySelector('#alertsDropdown').addEventListener('click', function () {
    fetchOrderCountAndDetails();
});

// Listen for cross-tab notification count sync
window.addEventListener('storage', function (event) {
    if (event.key === 'notificationCount') {
        const count = parseInt(event.newValue, 10);
        const badge = document.querySelector('.badge-counter');
        badge.textContent = count > 0 ? count + '+' : '0';
    }
});

</script>