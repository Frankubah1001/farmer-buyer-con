<?php
// orders.php - Updated: View-only mode with enhanced, structured modal and AJAX integration
$active = 'orders';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'header.php'; ?>
<body>
    <!-- Header -->
    <header class="header">
        <button class="toggle-btn" id="headerToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="user-info">
            <div class="user-avatar">AD</div>
            <span>Admin User</span>
        </div>
    </header>

    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="mb-4">Orders Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>All Orders</h4>
                <div class="table-actions">
                    <button class="btn btn-agri-blue" onclick="exportOrders()">
                        <i class="fas fa-download"></i> Export CSV
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by Order ID, Buyer or Farmer..." id="searchOrders">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="Order Sent">Pending</option>
                        <option value="Produce On the Way">Shipped</option>
                        <option value="Produce Delivered Confirmed">Completed</option>
                        <option value="Sold">Sold</option>
                        <option value="Make Payment">Awaiting Payment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterFromDate" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterToDate" placeholder="To Date">
                    <button class="btn btn-agri mt-2" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Buyer</th>
                            <th>Farmer</th>
                            <th>Produce Items</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Data will be loaded dynamically via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Orders pagination">
                <ul class="pagination justify-content-end" id="paginationControls">
                    <!-- Pagination controls will be generated dynamically -->
                </ul>
            </nav>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- View Order Details Modal (Enhanced Structure) -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewOrderModalLabel"><i class="fas fa-file-invoice-dollar me-2"></i>Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetailsContent">
                        <!-- Dynamically populated with structured cards -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded - Orders module script starting');

            // Common scripts (sidebar toggle, logout, etc.)
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('collapsed');
                });
            }

            const headerToggle = document.getElementById('headerToggle');
            if (headerToggle) {
                headerToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('active');
                });
            }

            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to sign out?')) {
                        alert('You have been signed out successfully.');
                        // Redirect to logout endpoint if needed
                    }
                });
            }

            // Orders module-specific variables
            let currentOrderId = '';
            let currentPage = 1;
            const itemsPerPage = 10;

            // Status mapping for display
            const statusDisplayMap = {
                'Order Sent': 'Pending',
                'Produce On the Way': 'Shipped',
                'Produce Delivered Confirmed': 'Completed',
                'Sold': 'Sold',
                'Make Payment': 'Awaiting Payment'
            };

            // Status badge classes
            const statusBadgeMap = {
                'Order Sent': 'badge-pending',
                'Produce On the Way': 'badge-approved',
                'Produce Delivered Confirmed': 'badge-approved',
                'Sold': 'badge-success',
                'Make Payment': 'badge-blue'
            };

            // Load orders on page load
            loadOrders();

            // Load orders from API with pagination and filters
            function loadOrders(page = 1) {
                currentPage = page;
                const search = document.getElementById('searchOrders').value;
                const status = document.getElementById('filterStatus').value;
                const fromDate = document.getElementById('filterFromDate').value;
                const toDate = document.getElementById('filterToDate').value;

                const query = new URLSearchParams({
                    page,
                    limit: itemsPerPage,
                    search,
                    status,
                    fromDate,
                    toDate
                }).toString();

                fetch(`api/order_api.php?${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const tbody = document.getElementById('ordersTableBody');
                            tbody.innerHTML = '';
                            data.data.orders.forEach(order => {
                                const row = document.createElement('tr');
                                row.setAttribute('data-orderid', order.order_id);
                                row.setAttribute('data-status', order.order_status.toLowerCase());
                                row.setAttribute('data-buyer', order.buyer_name.toLowerCase());
                                row.setAttribute('data-farmer', order.farmer_name.toLowerCase());
                                row.setAttribute('data-date', order.order_date);
                                row.innerHTML = `
                                    <td>#${order.order_id}</td>
                                    <td>${order.buyer_name}</td>
                                    <td>${order.farmer_name}</td>
                                    <td>${order.produce} (${order.quantity}kg)</td>
                                    <td>${order.quantity}kg</td>
                                    <td>₦${parseFloat(order.total_amount).toLocaleString()}</td>
                                    <td><span class="badge ${statusBadgeMap[order.order_status] || 'badge-primary'}">${statusDisplayMap[order.order_status] || order.order_status}</span></td>
                                    <td>${new Date(order.order_date).toLocaleDateString('en-GB')}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit view-order-btn" data-orderid="${order.order_id}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });

                            generatePaginationControls(data.data.pagination);
                        } else {
                            alert(data.message || 'Error loading orders');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading orders:', error);
                        alert('Error loading orders');
                    });
            }

            // Generate pagination controls
            function generatePaginationControls(pagination) {
                const paginationControls = document.getElementById('paginationControls');
                paginationControls.innerHTML = '';

                const { current_page, total_pages } = pagination;

                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${current_page === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page - 1}">Previous</a>`;
                paginationControls.appendChild(prevLi);

                // Page numbers
                const maxPagesToShow = 5;
                let startPage = Math.max(1, current_page - Math.floor(maxPagesToShow / 2));
                let endPage = Math.min(total_pages, startPage + maxPagesToShow - 1);

                if (endPage - startPage + 1 < maxPagesToShow) {
                    startPage = Math.max(1, endPage - maxPagesToShow + 1);
                }

                if (startPage > 1) {
                    const firstPageLi = document.createElement('li');
                    firstPageLi.className = 'page-item';
                    firstPageLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
                    paginationControls.appendChild(firstPageLi);
                    
                    if (startPage > 2) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dotsLi);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === current_page ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    paginationControls.appendChild(pageLi);
                }

                if (endPage < total_pages) {
                    if (endPage < total_pages - 1) {
                        const dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<span class="page-link">...</span>`;
                        paginationControls.appendChild(dotsLi);
                    }
                    
                    const lastPageLi = document.createElement('li');
                    lastPageLi.className = 'page-item';
                    lastPageLi.innerHTML = `<a class="page-link" href="#" data-page="${total_pages}">${total_pages}</a>`;
                    paginationControls.appendChild(lastPageLi);
                }

                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${current_page === total_pages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${current_page + 1}">Next</a>`;
                paginationControls.appendChild(nextLi);
            }

            // Handle pagination clicks
            document.getElementById('paginationControls').addEventListener('click', function(e) {
                e.preventDefault();
                const target = e.target.closest('.page-link');
                if (target && !target.parentElement.classList.contains('disabled')) {
                    const page = parseInt(target.getAttribute('data-page'));
                    if (page) {
                        loadOrders(page);
                    }
                }
            });

            // View order details with AJAX
            function viewOrderDetails(orderId) {
                fetch(`api/order_api.php?page=1&limit=1&search=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.orders.length > 0) {
                            const order = data.data.orders[0];
                            document.getElementById('orderDetailsContent').innerHTML = `
                                <div class="row g-3">
                                    <!-- Order Summary Card -->
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>Order Summary</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3"><strong>Order ID:</strong> #${order.order_id}</div>
                                                    <div class="col-md-3"><strong>Status:</strong> <span class="badge ${statusBadgeMap[order.order_status] || 'badge-pending'}">${statusDisplayMap[order.order_status] || order.order_status}</span></div>
                                                    <div class="col-md-3"><strong>Total:</strong> <i class="fas fa-naira-sign text-success me-1"></i>₦${parseFloat(order.total_amount).toLocaleString()}</div>
                                                    <div class="col-md-3"><strong>Date:</strong> <i class="fas fa-calendar-alt me-1"></i>${new Date(order.order_date).toLocaleDateString('en-GB')}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buyer Info Card -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0"><i class="fas fa-user-circle text-white me-2"></i>Buyer Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-user text-primary me-2"></i><strong>${order.buyer_name}</strong></p>
                                                <p><i class="fas fa-envelope text-muted me-2"></i>${order.buyer_email}</p>
                                                <p><i class="fas fa-phone text-muted me-2"></i>${order.buyer_phone}</p>
                                                <p><i class="fas fa-map-marker-alt text-muted me-2"></i>${order.buyer_address}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Farmer Info Card -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="fas fa-user-farmer text-white me-2"></i>Farmer Information</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-user text-primary me-2"></i><strong>${order.farmer_name}</strong></p>
                                                <p><i class="fas fa-envelope text-muted me-2"></i>${order.farmer_email}</p>
                                                <p><i class="fas fa-phone text-muted me-2"></i>${order.farmer_phone}</p>
                                                <p><i class="fas fa-map-marker-alt text-muted me-2"></i>${order.farmer_address}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Items Card -->
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0"><i class="fas fa-shopping-cart text-dark me-2"></i>Order Items</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Item</th>
                                                                <th>Quantity</th>
                                                                <th>Unit Price</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><i class="fas fa-seedling text-success me-2"></i>${order.produce}</td>
                                                                <td>${order.quantity}kg</td>
                                                                <td><i class="fas fa-naira-sign me-1"></i>₦${parseFloat(order.price_per_unit).toLocaleString()}</td>
                                                                <td><i class="fas fa-naira-sign text-success me-1"></i>₦${parseFloat(order.total_amount).toLocaleString()}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Timestamps & Shipping Card -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-secondary text-white">
                                                <h6 class="mb-0"><i class="fas fa-clock text-white me-2"></i>Timestamps & Shipping</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-calendar-check text-info me-2"></i><strong>Placed:</strong> ${new Date(order.created_at).toLocaleString('en-GB')}</p>
                                                <p><i class="fas fa-truck text-warning me-2"></i><strong>Shipped:</strong> ${order.order_status === 'Produce On the Way' || order.order_status === 'Produce Delivered Confirmed' ? new Date(order.updated_at).toLocaleString('en-GB') : 'N/A'}</p>
                                                <p><i class="fas fa-home text-success me-2"></i><strong>Delivered:</strong> ${order.order_status === 'Produce Delivered Confirmed' ? new Date(order.updated_at).toLocaleString('en-GB') : 'N/A'}</p>
                                                <p><i class="fas fa-route text-muted me-2"></i><strong>Method:</strong> N/A</p>
                                                <p><i class="fas fa-barcode text-muted me-2"></i><strong>Tracking:</strong> ${order.paystack_reference || 'N/A'}</p>
                                                <p><i class="fas fa-map text-muted me-2"></i><strong>Route:</strong> ${order.farmer_address} to ${order.delivery_address}</p>
                                                <p><i class="fas fa-clock text-muted me-2"></i><strong>ETA:</strong> ${order.delivery_date}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment & Notes Card -->
                                    <div class="col-md-6">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-danger text-white">
                                                <h6 class="mb-0"><i class="fas fa-credit-card text-white me-2"></i>Payment & Notes</h6>
                                            </div>
                                            <div class="card-body">
                                                <p><i class="fas fa-wallet text-success me-2"></i><strong>Method:</strong> ${order.paystack_reference ? 'Paystack' : 'N/A'}</p>
                                                <p><i class="fas fa-money-check-alt text-primary me-2"></i><strong>Amount:</strong> <i class="fas fa-naira-sign me-1"></i>₦${parseFloat(order.total_amount).toLocaleString()}</p>
                                                <p><i class="fas fa-check-circle text-success me-2"></i><strong>Status:</strong> ${order.payment_status}</p>
                                                <p><i class="fas fa-receipt text-muted me-2"></i><strong>Transaction ID:</strong> ${order.paystack_reference || 'N/A'}</p>
                                                <hr>
                                                <p><i class="fas fa-sticky-note text-secondary me-2"></i><strong>Notes:</strong></p>
                                                <div class="alert alert-info" role="alert">${order.notes || 'No additional notes.'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            new bootstrap.Modal(document.getElementById('viewOrderModal')).show();
                        } else {
                            alert('Order details not found');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching order details:', error);
                        alert('Error fetching order details');
                    });
            }

            // Event delegation for View buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('a[href]') && !e.target.closest('.action-buttons')) {
                    return; // Allow normal navigation
                }

                const target = e.target.closest('.view-order-btn');
                if (!target) return;

                e.preventDefault();
                currentOrderId = target.getAttribute('data-orderid');
                console.log('View button clicked:', { orderId: currentOrderId });

                try {
                    viewOrderDetails(currentOrderId);
                } catch (error) {
                    console.error('Error handling view click:', error);
                    alert('Error: ' + error.message);
                }
            });

            // Apply filters
            window.applyFilters = function() {
                loadOrders(1); // Reset to page 1 when filtering
            };

            // Real-time search
            document.getElementById('searchOrders').addEventListener('keyup', function() {
                loadOrders(1); // Reset to page 1 when searching
            });

            // Real-time filter status change
            document.getElementById('filterStatus').addEventListener('change', function() {
                loadOrders(1); // Reset to page 1 when status changes
            });

            // Real-time date filter change
            document.getElementById('filterFromDate').addEventListener('change', function() {
                loadOrders(1); // Reset to page 1 when date changes
            });
            document.getElementById('filterToDate').addEventListener('change', function() {
                loadOrders(1); // Reset to page 1 when date changes
            });

            // Export orders
            window.exportOrders = function() {
                alert('Preparing orders export...');
                window.open('api/order_api.php?action=export', '_blank');
            };

            console.log('Orders module script fully initialized');
        });
    </script>
</body>
</html>