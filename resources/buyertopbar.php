<?php
//session_start();
include 'DBcon.php';

// Initialize profile picture variable
$profilePicture = 'asset/img/undraw_profile_1.svg'; // Default image path

// Check if the user is logged in
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
            // Check if path starts with 'uploads/' or is absolute
            if (strpos($user['profile_picture'], 'uploads/') === 0) {
                $profilePicture = $user['profile_picture'];
            } else if (file_exists($user['profile_picture'])) {
                $profilePicture = $user['profile_picture'];
            }
        }
    }

    mysqli_stmt_close($stmt);
}
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
                    <span class="badge badge-danger badge-counter" style="display:none;"></span>
                </a>
                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="alertsDropdown">
                    <h6 class="dropdown-header">
                        Notifications
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
                        echo "GUEST";
                    }
                ?>
</h6>
                    </span>
                    <img class="img-profile rounded-circle"
                        src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile">
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
                // Show exact count if less than 10, otherwise show 9+
                if (data.orderCount === 0) {
                    orderCountBadge.textContent = '';
                    orderCountBadge.style.display = 'none';
                } else {
                    orderCountBadge.style.display = 'inline-block';
                    orderCountBadge.textContent = data.orderCount > 9 ? '9+' : data.orderCount;
                }
            }

            const alertsDropdownList = document.querySelector('#alertsDropdown + .dropdown-menu .dropdown-header').parentNode;
            const showAllLink = alertsDropdownList.querySelector('.dropdown-item.text-center.small.text-gray-500');

            // Clear old order items (but leave showAllLink if exists)
            alertsDropdownList.querySelectorAll('.order-notification-item').forEach(item => item.remove());

            if (data.recentOrders && Array.isArray(data.recentOrders) && data.recentOrders.length > 0) {
                data.recentOrders.forEach((order, index) => {
                    const orderItem = document.createElement('a');
                    orderItem.classList.add('dropdown-item', 'd-flex', 'align-items-center', 'order-notification-item');
                    orderItem.href = `order_history.php?order_id=${order.order_id}`;
                    orderItem.dataset.orderId = order.order_id;
                    orderItem.style.padding = '0.75rem 1.5rem';
                    orderItem.style.borderBottom = '1px solid #e3e6f0';
                    
                    // Create meaningful notification title
                    const produceName = order.produce || 'Unknown Produce';
                    const quantity = order.quantity || '';
                    const status = order.order_status || 'New Order';
                    
                    let notificationTitle = '';
                    if (status === 'Order Sent') {
                        notificationTitle = `New Order: ${produceName} (${quantity} units)`;
                    } else if (status === 'Make Payment') {
                        notificationTitle = `Payment Required: ${produceName}`;
                    } else if (status === 'Processing Produce For Delivery') {
                        notificationTitle = `Processing: ${produceName}`;
                    } else if (status === 'Produce On The Way') {
                        notificationTitle = `On The Way: ${produceName}`;
                    } else if (status === 'Produce Delivered & Confirmed') {
                        notificationTitle = `Delivered: ${produceName}`;
                    } else if (status === 'Cancel Order') {
                        notificationTitle = `Cancelled: ${produceName}`;
                    } else {
                        notificationTitle = `${status}: ${produceName}`;
                    }
                    
                    // Create icon based on status
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'mr-3';
                    let iconClass = 'fa-shopping-cart';
                    let iconColor = '#4e73df';
                    
                    if (status === 'Make Payment') {
                        iconClass = 'fa-money-bill-wave';
                        iconColor = '#f6c23e';
                    } else if (status === 'Produce On The Way') {
                        iconClass = 'fa-truck';
                        iconColor = '#36b9cc';
                    } else if (status === 'Produce Delivered & Confirmed') {
                        iconClass = 'fa-check-circle';
                        iconColor = '#1cc88a';
                    } else if (status === 'Cancel Order') {
                        iconClass = 'fa-times-circle';
                        iconColor = '#e74a3b';
                    }
                    
                    iconDiv.innerHTML = `<div class="icon-circle" style="background-color: ${iconColor}20; width: 2.5rem; height: 2.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas ${iconClass}" style="color: ${iconColor};"></i>
                    </div>`;
                    
                    const textDiv = document.createElement('div');
                    textDiv.innerHTML = `
                        <div class="small text-gray-500">${new Date(order.order_date).toLocaleDateString()}</div>
                        <span class="font-weight-bold">${notificationTitle}</span>
                    `;
                    
                    orderItem.appendChild(iconDiv);
                    orderItem.appendChild(textDiv);

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
                                // Remove this notification from the tray
                                this.remove();
                                
                                // Update counter immediately
                                const currentCount = parseInt(orderCountBadge.textContent) || 0;
                                const newCount = Math.max(0, currentCount - 1);
                                
                                if (newCount === 0) {
                                    orderCountBadge.textContent = '';
                                    orderCountBadge.style.display = 'none';
                                } else {
                                    orderCountBadge.textContent = newCount > 9 ? '9+' : newCount;
                                }
                                
                                // Update localStorage
                                localStorage.setItem('notificationCount', newCount);
                                
                                // Redirect to order page
                                window.location.href = this.href;
                            }
                        })
                        .catch(error => {
                            console.error('Error marking order as read:', error);
                            // Still redirect even if marking fails
                            window.location.href = this.href;
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
                noOrdersItem.classList.add('dropdown-item', 'text-center', 'small', 'text-gray-500', 'order-notification-item');
                noOrdersItem.href = '#';
                noOrdersItem.textContent = 'No new notifications';
                noOrdersItem.style.padding = '1rem';
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
        if (count === 0) {
            badge.textContent = '';
            badge.style.display = 'none';
        } else {
            badge.style.display = 'inline-block';
            badge.textContent = count > 9 ? '9+' : count;
        }
    }
});

</script>