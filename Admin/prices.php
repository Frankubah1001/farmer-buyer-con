<?php
// Include session timeout check
require_once 'session_check.php';

// prices.php
$active = 'prices';
?>
<!DOCTYPE html>
<html lang="en">
    <?php include 'header.php'; ?>
<body>
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

    <main class="main-content">
        <h2 class="mb-4">Produce Prices Management</h2>
        
        <div class="data-table-container">
            <div class="table-header">
                <h4>Price Ranges</h4>
                <div class="table-actions">
                    <button class="btn btn-agri" data-bs-toggle="modal" data-bs-target="#setPriceRangeModal">
                        <i class="fas fa-plus"></i> Set New Price Range
                    </button>
                    <button class="btn btn-agri-blue" onclick="exportPriceRanges()">
                        <i class="fas fa-download"></i> Export
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search by produce type..." id="searchProduce">
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="filterStatus">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-agri" onclick="filterTable()">Apply Filters</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover" id="priceTable">
                    <thead>
                        <tr>
                            <th>Produce Type</th>
                            <th>Min Price (₦)</th>
                            <th>Max Price (₦)</th>
                            <th>Added Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="priceTableBody">
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Prices pagination">
                <ul class="pagination justify-content-end" id="paginationControls">
                    <!-- Pagination controls will be generated dynamically -->
                </ul>
            </nav>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <!-- Set Price Range Modal (Add New) -->
    <div class="modal fade" id="setPriceRangeModal" tabindex="-1" aria-labelledby="setPriceRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setPriceRangeModalLabel">Set New Price Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPriceForm">
                        <div class="mb-3">
                            <label for="addProduceType" class="form-label">Produce Type</label>
                            <input type="text" class="form-control" id="addProduceType" placeholder="e.g., Tomatoes" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addMinPrice" class="form-label">Min Price (₦)</label>
                                    <input type="number" class="form-control" id="addMinPrice" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="addMaxPrice" class="form-label">Max Price (₦)</label>
                                    <input type="number" class="form-control" id="addMaxPrice" min="0" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="saveNewPrice">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Price Range Modal -->
    <div class="modal fade" id="editPriceRangeModal" tabindex="-1" aria-labelledby="editPriceRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceRangeModalLabel">Edit Price Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editPriceForm">
                        <div class="mb-3">
                            <label for="editProduceType" class="form-label">Produce Type</label>
                            <input type="text" class="form-control" id="editProduceType" readonly required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editMinPrice" class="form-label">Min Price (₦)</label>
                                    <input type="number" class="form-control" id="editMinPrice" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editMaxPrice" class="form-label">Max Price (₦)</label>
                                    <input type="number" class="form-control" id="editMaxPrice" min="0" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-agri" id="updatePrice">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Disable Price Range Modal -->
    <div class="modal fade" id="disablePriceRangeModal" tabindex="-1" aria-labelledby="disablePriceRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disablePriceRangeModalLabel">Disable Price Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="disableConfirmText">Are you sure you want to disable this price range?</p>
                    <div class="mb-3">
                        <label for="disablePriceReason" class="form-label">Reason</label>
                        <textarea class="form-control" id="disablePriceReason" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-disable" id="confirmDisablePrice">Disable</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - Price module script starting');

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
                }
            });
        }

        // Price module-specific functions
        let currentEditPriceId = '';
        let currentEditProduce = '';
        let currentPage = 1;
        let itemsPerPage = 10;

        // Load price ranges on page load
        loadPriceRanges();

        // Load price ranges from API with pagination
        function loadPriceRanges(page = 1) {
            currentPage = page;
            const search = document.getElementById('searchProduce').value.toLowerCase();
            const statusFilter = document.getElementById('filterStatus').value;
            
            fetch(`api/prices_api.php?page=${page}&limit=${itemsPerPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.getElementById('priceTableBody');
                        tbody.innerHTML = '';
                        
                        data.data.prices.forEach(price => {
                            // Apply client-side filtering
                            const produce = price.produce_type.toLowerCase();
                            const status = price.status;
                            let show = true;
                            
                            if (search && !produce.includes(search)) show = false;
                            if (statusFilter && status !== statusFilter) show = false;
                            
                            if (show) {
                                const row = document.createElement('tr');
                                row.setAttribute('data-produce', price.produce_type.toLowerCase());
                                row.setAttribute('data-status', price.status);
                                row.innerHTML = `
                                    <td>${price.produce_type}</td>
                                    <td>₦${parseFloat(price.min_price).toLocaleString()}</td>
                                    <td>₦${parseFloat(price.max_price).toLocaleString()}</td>
                                    <td>${new Date(price.created_at).toLocaleDateString('en-GB')}</td>
                                    <td><span class="badge ${price.status === 'active' ? 'badge-approved' : 'badge-disabled'}">${price.status.charAt(0).toUpperCase() + price.status.slice(1)}</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit edit-price-btn" data-price-id="${price.price_id}" data-produce="${price.produce_type}" data-min="${price.min_price}" data-max="${price.max_price}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            ${price.status === 'active' ? 
                                                `<button class="action-btn btn-disable disable-price-btn" data-price-id="${price.price_id}" data-produce="${price.produce_type}">
                                                    <i class="fas fa-ban"></i>
                                                </button>` :
                                                `<button class="action-btn btn-approve enable-price-btn" data-price-id="${price.price_id}" data-produce="${price.produce_type}">
                                                    <i class="fas fa-check"></i>
                                                </button>`
                                            }
                                        </div>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            }
                        });

                        // Generate pagination controls
                        generatePaginationControls(data.data.pagination);
                    }
                })
                .catch(error => console.error('Error loading price ranges:', error));
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
                    loadPriceRanges(page);
                }
            }
        });

        // Event delegation for ACTION buttons ONLY (edit/disable/enable)
        document.addEventListener('click', function(e) {
            if (e.target.closest('a[href]') && !e.target.closest('.action-buttons')) {
                return;
            }

            const target = e.target.closest('.edit-price-btn, .disable-price-btn, .enable-price-btn');

            if (!target) return;

            e.preventDefault();

            const priceId = target.getAttribute('data-price-id');
            const produceType = target.getAttribute('data-produce');
            const minPrice = target.getAttribute('data-min') || 0;
            const maxPrice = target.getAttribute('data-max') || 0;

            console.log('Action button clicked:', { priceId, produceType, minPrice, maxPrice });

            try {
                if (target.classList.contains('edit-price-btn')) {
                    currentEditPriceId = priceId;
                    currentEditProduce = produceType;
                    document.getElementById('editProduceType').value = produceType;
                    document.getElementById('editMinPrice').value = minPrice;
                    document.getElementById('editMaxPrice').value = maxPrice;
                    const editModal = new bootstrap.Modal(document.getElementById('editPriceRangeModal'));
                    editModal.show();
                    console.log('Edit modal opened for:', produceType);
                } else if (target.classList.contains('disable-price-btn')) {
                    document.getElementById('disableConfirmText').textContent = `Are you sure you want to disable the price range for ${produceType}? This will make it unavailable for farmer uploads.`;
                    currentEditPriceId = priceId;
                    currentEditProduce = produceType;
                    const disableModal = new bootstrap.Modal(document.getElementById('disablePriceRangeModal'));
                    disableModal.show();
                    console.log('Disable modal opened for:', produceType);
                } else if (target.classList.contains('enable-price-btn')) {
                    enablePriceRange(priceId, produceType);
                }
            } catch (error) {
                console.error('Error handling button click:', error);
                alert('Error: ' + error.message);
            }
        });

        // Save new price range
        const saveNewPriceBtn = document.getElementById('saveNewPrice');
        if (saveNewPriceBtn) {
            saveNewPriceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const produceType = document.getElementById('addProduceType').value.trim();
                const minPrice = parseFloat(document.getElementById('addMinPrice').value);
                const maxPrice = parseFloat(document.getElementById('addMaxPrice').value);

                if (!produceType) {
                    alert('Produce type is required.');
                    return;
                }
                if (isNaN(minPrice) || isNaN(maxPrice) || minPrice < 0 || maxPrice < 0) {
                    alert('Valid prices are required (non-negative numbers).');
                    return;
                }
                if (minPrice >= maxPrice) {
                    alert('Minimum price must be less than maximum price.');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('produce_type', produceType);
                formData.append('min_price', minPrice);
                formData.append('max_price', maxPrice);

                fetch('api/prices_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('setPriceRangeModal'));
                        if (modal) modal.hide();
                        document.getElementById('addPriceForm').reset();
                        loadPriceRanges(currentPage);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error adding price range:', error);
                    alert('Error adding price range');
                });
            });
        }

        // Update price range
        const updatePriceBtn = document.getElementById('updatePrice');
        if (updatePriceBtn) {
            updatePriceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const minPrice = parseFloat(document.getElementById('editMinPrice').value);
                const maxPrice = parseFloat(document.getElementById('editMaxPrice').value);

                if (isNaN(minPrice) || isNaN(maxPrice) || minPrice < 0 || maxPrice < 0) {
                    alert('Valid prices are required (non-negative numbers).');
                    return;
                }
                if (minPrice >= maxPrice) {
                    alert('Minimum price must be less than maximum price.');
                    return;
                }

                const formData = new FormData();
                formData.append('action', 'update');
                formData.append('price_id', currentEditPriceId);
                formData.append('min_price', minPrice);
                formData.append('max_price', maxPrice);

                fetch('api/prices_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editPriceRangeModal'));
                        if (modal) modal.hide();
                        loadPriceRanges(currentPage);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating price range:', error);
                    alert('Error updating price range');
                });
            });
        }

        // Confirm disable
        const confirmDisableBtn = document.getElementById('confirmDisablePrice');
        if (confirmDisableBtn) {
            confirmDisableBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const reason = document.getElementById('disablePriceReason').value.trim();
                
                const formData = new FormData();
                formData.append('action', 'toggle_status');
                formData.append('price_id', currentEditPriceId);
                formData.append('current_status', 'active');
                formData.append('reason', reason);

                fetch('api/prices_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('disablePriceRangeModal'));
                        if (modal) modal.hide();
                        document.getElementById('disablePriceReason').value = '';
                        loadPriceRanges(currentPage);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error disabling price range:', error);
                    alert('Error disabling price range');
                });
            });
        }

        // Enable price range
        function enablePriceRange(priceId, produceType) {
            if (confirm(`Enable price range for ${produceType}?`)) {
                const formData = new FormData();
                formData.append('action', 'toggle_status');
                formData.append('price_id', priceId);
                formData.append('current_status', 'disabled');
                formData.append('reason', '');

                fetch('api/prices_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadPriceRanges(currentPage);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error enabling price range:', error);
                    alert('Error enabling price range');
                });
            }
        }

        // Filter table
        window.filterTable = function() {
            loadPriceRanges(1); // Reset to page 1 when filtering
        }

        // Search on input
        const searchInput = document.getElementById('searchProduce');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                loadPriceRanges(1); // Reset to page 1 when searching
            });
        }

        // Export functionality
        const exportBtn = document.querySelector('.btn-agri-blue');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                alert('Preparing price ranges export...');
                window.open('api/prices_api.php?action=export', '_blank');
            });
        }

        console.log('Price module script fully initialized');
    });
</script>
</body>
</html>