<?php 
require_once 'auth_check.php';
// Assuming header.php includes Bootstrap 5 CSS and Font Awesome 6
include 'header.php'; 
?>

<style>
    /* === FARM-FRESH COLOR PALETTE & ICON STYLING === */

    /* Colors - Refined for better pop and contrast */
    :root {
        --primary-green: #2E7D32;    /* Deeper Green - Primary Action/Header */
        --secondary-green: #689F38;  /* Medium Green - Accent/Buttons */
        --accent-yellow: #FFB300;    /* Richer Gold/Amber - For Ratings */
        --background-light: #F4FFF9; /* Very light, fresh background */
        --text-dark: #212121;        /* Near Black for high text contrast */
        --shadow-color: rgba(46, 139, 87, 0.15); /* Slightly stronger shadow */
        --icon-blue: #1976D2;        /* For contact/info icons */
        --icon-red: #D32F2F;         /* For error/warning states */
    }

    /* General Styling */
    .container-fluid {
        background-color: var(--background-light);
        padding-top: 20px;
        padding-bottom: 20px;
        min-height: 100vh;
    }
    .page-title {
        color: var(--primary-green);
        font-weight: 800;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
        border-bottom: 4px solid var(--secondary-green);
        display: inline-block;
        font-size: 2.5rem; /* Bigger title */
    }

    /* Filter Card */
    .filter-card {
        background-color: #fff;
        border-radius: 15px;
        border: 1px solid #C8E6C9; /* Lighter border */
        box-shadow: 0 6px 20px var(--shadow-color);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .form-label {
        color: var(--primary-green);
        font-weight: 600;
        font-size: 0.95rem; /* Slightly larger label */
        display: block; 
        margin-bottom: 0.3rem;
    }
    .form-control-sm {
        border-radius: 10px;
        border-color: #A5D6A7;
        color: var(--text-dark);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control-sm:focus {
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 0.25rem rgba(104, 159, 56, 0.3); 
    }

    /* Table Styling (Desktop) */
    .table-responsive {
        background-color: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 25px var(--shadow-color);
    }
    .table thead th {
        background-color: var(--primary-green);
        color: #fff;
        font-weight: 700;
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem; 
        letter-spacing: 0.8px;
    }
    .table tbody tr:nth-of-type(even) {
        background-color: #F9FFF5; 
    }
    .table tbody tr:nth-of-type(odd) {
        background-color: #fff; 
    }
    .table td {
        vertical-align: middle;
        color: var(--text-dark);
        font-size: 0.9rem;
    }
    
    /* Rating Stars */
    .star-rating {
        color: var(--accent-yellow);
        font-size: 0.9rem;
    }

    /* View Produce Button - More vibrant action */
    .view-produce-btn {
        background-color: var(--secondary-green);
        border-color: var(--secondary-green);
        font-weight: 600;
        border-radius: 10px; 
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        color: white; /* Important for button text */
    }
    .view-produce-btn:hover {
        background-color: #558B2F; 
        border-color: #558B2F;
        transform: translateY(-1px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    /* Pagination */
    .pagination .page-item.active .page-link {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
        color: #fff;
    }
    .pagination .page-link {
        color: var(--primary-green);
        border-radius: 8px;
        margin: 0 4px;
        transition: all 0.3s;
    }
    .pagination .page-link:hover {
        background-color: #E8F5E9;
        color: var(--text-dark);
    }

    /* === MOBILE RESPONSIVE CARDS - MORE COLORFUL === */
    .farmer-card {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px var(--shadow-color);
        margin-bottom: 15px;
        padding: 1rem;
        border-left: 5px solid var(--secondary-green); /* Added accent border */
        display: none; 
    }
    .card-header-icon {
        color: var(--secondary-green);
        font-size: 1.5rem;
    }
    .card-title-farmer {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
    }
    .card-detail-label {
        font-size: 0.75rem; 
        color: #555;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .card-detail-value {
        font-weight: 700; 
        color: var(--text-dark);
        font-size: 0.9rem;
        line-height: 1.3;
    }
    .card-rating-section {
        border-top: 1px dashed #ddd;
        padding-top: 10px;
        margin-top: 10px;
    }
    .table-container-desktop {
        display: block;
    }
    .card-container-mobile {
        display: none;
    }

    @media (max-width: 767.98px) {
        .page-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .table-container-desktop {
            display: none; 
        }
        .card-container-mobile {
            display: block; 
        }
        .farmer-card {
            display: block;
        }
        .filter-card {
            padding: 1rem;
        }
    }

    /* === MODAL STYLING - POP & STRUCTURED === */
    #farmerDetailsModal .modal-content {
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2); 
        border: 3px solid var(--secondary-green);
    }
    #farmerDetailsModal .modal-header {
        background-color: var(--primary-green);
        color: #fff;
        border-radius: 17px 17px 0 0; 
        border-bottom: none;
        padding: 1.5rem 2rem;
    }
    #farmerDetailsModal .modal-title {
        font-weight: 800;
        font-size: 1.6rem;
        letter-spacing: 0.5px;
    }
    /* Note: Bootstrap 4 close button style is assumed here */
    #farmerDetailsModal .close {
        color: #fff;
        opacity: 0.9;
        font-size: 1.8rem;
        /* Custom styling for Bootstrap 4 close button */
        text-shadow: none;
        margin-top: -0.5rem;
    }
    #farmerDetailsModal .modal-body {
        padding: 2rem;
        background-color: var(--background-light);
    }
    /* Modal Section Headings - Very distinct */
    #farmerDetailsModal .modal-body h4, #farmerDetailsModal .modal-body h5 {
        color: var(--primary-green);
        font-weight: 700;
        margin-top: 1.8rem;
        margin-bottom: 1rem;
        padding-bottom: 0.3rem;
        border-bottom: 3px solid var(--accent-yellow); 
    }
    /* Updated styling for display-only inputs */
    .form-control-plaintext {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.95rem;
        background-color: #fff; 
        border-radius: 6px;
        border: 1px solid #eee; /* Light border for separation */
        width: 100%;
    }
    /* Produce/Rating Card Styling in Modal - Use clear division */
    .modal-body .card {
        border: 1px solid #E0F2F1; 
        border-left: 6px solid var(--secondary-green); 
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s;
    }
    .modal-body .card:hover {
        transform: translateY(-2px);
    }
    .modal-body .card label {
        font-weight: 600;
        color: var(--primary-green);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 0.2rem;
    }
    /* Modal Footer */
    #farmerDetailsModal .modal-footer {
        border-top: 1px solid #eee;
        padding: 1rem 2rem;
        background-color: #fff;
        border-radius: 0 0 17px 17px;
    }
    #farmerDetailsModal .modal-footer .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        border-radius: 10px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
    }
    
    /* Ensure action buttons in the modal body are styled */
    .card-detail-value {
        font-weight: 700; 
        color: var(--text-dark);
        font-size: 0.9rem;
        line-height: 1.3;
    }

</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <div class="container mt-4" style="max-width: 1400px; margin: auto;">
                <h2 class="page-title"><i class="fas fa-tractor fa-fw mr-2"></i>All Farmers</h2>

                <div class="filter-card shadow-sm">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="locationFilterFarmers" class="form-label"><i class="fas fa-map-marker-alt"></i> Filter by Location:</label>
                            <input type="text" class="form-control form-control-sm" id="locationFilterFarmers" placeholder="Enter location or area">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contactFilterFarmers" class="form-label"><i class="fas fa-phone-alt"></i> Filter by Contact (Phone):</label>
                            <input type="text" class="form-control form-control-sm" id="contactFilterFarmers" placeholder="Enter phone number">
                        </div>
                    </div>
                </div>

                <div class="table-container-desktop">
                    <div class="table-responsive">
                        <table class="table table-hover" id="farmersTable"> 
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><i class="fas fa-user"></i> First Name</th>
                                    <th><i class="fas fa-user-tag"></i> Last Name</th>
                                    <th><i class="fas fa-map-marker-alt"></i> Location</th>
                                    <th><i class="fas fa-phone-alt"></i> Contact</th>
                                    <th><i class="fas fa-star"></i> Rating</th>
                                    <th><i class="fas fa-cogs"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-container-mobile" id="farmersCardContainer">
                    </div>

                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center" id="paginationControls">
                        </ul>
                </nav>

                <div class="modal fade" id="farmerDetailsModal" tabindex="-1" role="dialog" aria-labelledby="farmerDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="farmerDetailsModalLabel"><i class="fas fa-seedling mr-2"></i>Farmer Details & Produce</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
<script>
    $(document).ready(function() {
        let currentPageFarmers = 1;
        let rowsPerPageFarmers = 5;
        let totalPagesFarmers = 1;
        let totalRowsFarmers = 0;
        let farmerData = [];

        // ... (Other functions: generateRatingStars, fetchAndDisplayFarmers, 
        //      displayFarmersTable, displayFarmersCards, updatePagination) ...
        
        // Function to generate rating stars (Remains the same)
        function generateRatingStars(rating) {
            const maxRating = 5;
            const fullStar = '<i class="fas fa-star"></i>';
            const halfStar = '<i class="fas fa-star-half-alt"></i>';
            const emptyStar = '<i class="far fa-star"></i>';
            
            let stars = '';
            let score = parseFloat(rating);
            
            if (isNaN(score)) return '<span class="text-muted">N/A</span>';

            for (let i = 1; i <= maxRating; i++) {
                if (score >= i) {
                    stars += fullStar;
                } else if (score > i - 1 && score < i) {
                    stars += halfStar;
                } else {
                    stars += emptyStar;
                }
            }
            return stars + (rating !== null ? ` (${score.toFixed(1)})` : '');
        }
        
        // Function to display farmers in the desktop table (Remains the same)
        function displayFarmersTable(farmers) {
             let tableBody = $('#farmersTable tbody');
            tableBody.empty(); 

            farmers.forEach((farmer, index) => {
                let rowNumber = (currentPageFarmers - 1) * rowsPerPageFarmers + index + 1;
                let ratingDisplay = farmer.rating !== null ? generateRatingStars(farmer.rating) : '<span class="text-muted">N/A</span>';
                
                let row = `
                    <tr>
                        <td>${rowNumber}</td>
                        <td><i class="fas fa-user-tie text-primary mr-1"></i> ${farmer.first_name}</td>
                        <td>${farmer.last_name}</td>
                        <td><i class="fas fa-map-pin text-info mr-1"></i> ${farmer.location}</td>
                        <td><i class="fas fa-mobile-alt text-secondary mr-1"></i> ${farmer.contact}</td>
                        <td><span class="star-rating">${ratingDisplay}</span></td>
                        <td>
                            <button class="btn btn-sm view-produce-btn" data-farmer-id="${farmer.user_id}">
                                <i class="fas fa-leaf mr-1"></i> View Produce
                            </button>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }
        
        // Function to display farmers in the mobile cards (Remains the same)
        function displayFarmersCards(farmers) {
            let cardContainer = $('#farmersCardContainer');
            cardContainer.empty();
            
            farmers.forEach((farmer, index) => {
                let ratingDisplay = farmer.rating !== null ? generateRatingStars(farmer.rating) : '<span class="text-muted">N/A</span>';
                
                let card = `
                    <div class="farmer-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <i class="card-header-icon fas fa-store mr-2"></i>
                                <span class="card-title-farmer">${farmer.first_name} ${farmer.last_name}</span>
                            </div>
                            <button class="btn btn-sm view-produce-btn" data-farmer-id="${farmer.user_id}"><i class="fas fa-eye mr-1"></i> View</button>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="card-detail-label"><i class="fas fa-map-marker-alt mr-1"></i> Location</div>
                                <div class="card-detail-value text-info">${farmer.location}</div>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="card-detail-label"><i class="fas fa-mobile-alt mr-1"></i> Contact</div>
                                <div class="card-detail-value text-secondary">${farmer.contact}</div>
                            </div>
                        </div>
                        <div class="card-rating-section">
                            <div class="card-detail-label"><i class="fas fa-star mr-1"></i> Average Rating</div>
                            <div class="card-detail-value star-rating">${ratingDisplay}</div>
                        </div>
                    </div>
                `;
                cardContainer.append(card);
            });
        }

        // Function to fetch and display farmers (Remains the same)
        function fetchAndDisplayFarmers() {
            let locationFilter = $('#locationFilterFarmers').val();
            let contactFilter = $('#contactFilterFarmers').val();

            const loadingHtml = '<tr><td colspan="7" class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2 text-primary"></i> Loading farmers...</td></tr>';
            $('#farmersTable tbody').html(loadingHtml);
            $('#farmersCardContainer').html('<div class="text-center p-4 alert alert-light border-0"><i class="fas fa-spinner fa-spin mr-2 text-primary"></i> Loading farmers...</div>');


            $.ajax({
                url: 'views/get_farmers.php', 
                type: 'GET',
                dataType: 'json',
                data: {
                    page: currentPageFarmers,
                    location: locationFilter,
                    contact: contactFilter
                },
                success: function(response) {
                    if (response.farmers && response.farmers.length > 0) {
                        farmerData = response.farmers;
                        displayFarmersTable(response.farmers);
                        displayFarmersCards(response.farmers); 
                        updatePagination(response.pagination);
                    } else {
                        const noDataHtml = '<tr><td colspan="7" class="text-center p-4"><i class="fas fa-search-minus mr-2"></i> No farmers found matching your criteria.</td></tr>';
                        $('#farmersTable tbody').html(noDataHtml);
                        $('#farmersCardContainer').html('<div class="alert alert-warning text-center"><i class="fas fa-search-minus mr-2"></i> No farmers found matching your criteria.</div>');
                        updatePagination({ currentPage: 1, totalPages: 1, totalRows: 0 });
                    }
                },
                error: function(xhr, status, error) {
                    const errorHtml = '<tr><td colspan="7" class="text-center text-danger p-4"><i class="fas fa-times-circle mr-2"></i> Error fetching data.</td></tr>';
                    $('#farmersTable tbody').html(errorHtml);
                    $('#farmersCardContainer').html('<div class="alert alert-danger text-center"><i class="fas fa-times-circle mr-2"></i> Error fetching data.</div>');
                    console.error('Error fetching farmers:', error);
                    updatePagination({ currentPage: 1, totalPages: 1, totalRows: 0 });
                }
            });
        }
        
        // Function to update pagination controls (Remains the same)
        function updatePagination(pagination) {
            currentPageFarmers = pagination.currentPage;
            totalPagesFarmers = pagination.totalPages;
            totalRowsFarmers = pagination.totalRows;

            let paginationControls = $('#paginationControls');
            paginationControls.empty();

            if (totalPagesFarmers <= 1) return;

            let prevDisabled = currentPageFarmers === 1 ? 'disabled' : '';
            let prevLink = `<li class="page-item ${prevDisabled}"><a class="page-link page-link-num" href="#" data-page="${currentPageFarmers - 1}"><i class="fas fa-angle-left"></i></a></li>`;
            paginationControls.append(prevLink);

            let maxPagesToShow = 5;
            let startPage = Math.max(1, currentPageFarmers - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPagesFarmers, startPage + maxPagesToShow - 1);
            
            if (endPage - startPage + 1 < maxPagesToShow && totalPagesFarmers >= maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                let activeClass = currentPageFarmers === i ? 'active' : '';
                let pageLink = `<li class="page-item ${activeClass}"><a class="page-link page-link-num" href="#" data-page="${i}">${i}</a></li>`;
                paginationControls.append(pageLink);
            }

            let nextDisabled = currentPageFarmers === totalPagesFarmers ? 'disabled' : '';
            let nextLink = `<li class="page-item ${nextDisabled}"><a class="page-link page-link-num" href="#" data-page="${currentPageFarmers + 1}"><i class="fas fa-angle-right"></i></a></li>`;
            paginationControls.append(nextLink);

            $('.page-link-num').off('click').on('click', function(e) { 
                e.preventDefault();
                let page = parseInt($(this).data('page'));
                if (!isNaN(page) && page >= 1 && page <= totalPagesFarmers) {
                    currentPageFarmers = page;
                    fetchAndDisplayFarmers();
                    $('html, body').animate({scrollTop: $('#farmersTable').length ? $('#farmersTable').offset().top - 100 : $('#farmersCardContainer').offset().top - 100}, 300);
                }
            });
        }


        // --- EVENT LISTENERS & MODAL FETCH ---

        // Event Listeners (Remains the same)
        $('#locationFilterFarmers, #contactFilterFarmers').on('input', function() {
            currentPageFarmers = 1; // Reset to first page on filter change
            fetchAndDisplayFarmers();
        });

        $('body').off('click', '.view-produce-btn').on('click', '.view-produce-btn', function() { 
            let farmerId = $(this).data('farmer-id');
            fetchAndDisplayFarmerDetails(farmerId);
        });

        function fetchAndDisplayFarmerDetails(farmerId) {
            let modalContent = $('#farmerDetailsModal .modal-body');
            
            // 1. Show loading spinner immediately
            modalContent.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3 font-weight-bold text-primary">Loading farmer and produce details...</p></div>');
            
            // 2. Explicitly show the modal using the Bootstrap 4/jQuery method
            $('#farmerDetailsModal').modal('show'); 

            $.ajax({
                url: 'views/get_farmer_details.php', 
                type: 'GET',
                dataType: 'json',
                data: { farmer_id: farmerId }, 
                success: function(response) {
                    if (response.farmer) {
                        displayFarmerDetailsModal(response);
                        // No need to call .modal('show') again
                    } else {
                        modalContent.html('<div class="alert alert-danger text-center"><i class="fas fa-times-circle mr-2"></i> Could not fetch farmer details.</div>');
                        console.error('Error fetching farmer details:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    modalContent.html('<div class="alert alert-danger text-center"><i class="fas fa-times-circle mr-2"></i> Error connecting to server to fetch details.</div>');
                    console.error('Error fetching farmer details:', error);
                }
            });
        }

        // --- MODAL DISPLAY FUNCTION (Remains the same) ---
        function displayFarmerDetailsModal(data) {
            let modalContent = $('#farmerDetailsModal .modal-body');
            modalContent.empty();

            let farmer = data.farmer;
            let produceListings = data.produce_listings;
            let ratings = data.ratings;

           let farmerDetailsHTML = `
            <h4 class="mb-3"><i class="fas fa-info-circle text-primary mr-2"></i> Farmer Details</h4>
            <div class="row mb-4 p-3 border rounded" style="background-color: #fff;">
                <div class="col-12 col-md-6 form-group mb-3">
                    <label for="farmerName" class="form-label"><i class="fas fa-user-tie"></i> Name:</label>
                    <input type="text" class="form-control-plaintext" id="farmerName" value="${farmer.first_name} ${farmer.last_name}" readonly>
                </div>
                <div class="col-6 col-md-3 form-group mb-3">
                    <label for="farmerEmail" class="form-label"><i class="fas fa-envelope text-success"></i> Email:</label>
                    <input type="text" class="form-control-plaintext text-success" id="farmerEmail" value="${farmer.email}" readonly>
                </div>
                <div class="col-6 col-md-3 form-group mb-3">
                    <label for="farmerPhone" class="form-label"><i class="fas fa-mobile-alt text-info"></i> Phone:</label>
                    <input type="text" class="form-control-plaintext text-info" id="farmerPhone" value="${farmer.contact}" readonly>
                </div>
                <div class="col-12 form-group mb-0">
                    <label for="farmerAddress" class="form-label"><i class="fas fa-map-marker-alt text-danger"></i> Location:</label>
                    <input type="text" class="form-control-plaintext text-danger" id="farmerAddress" value="${farmer.location}" readonly>
                </div>
            </div>

                <h5><i class="fas fa-carrot text-secondary mr-2"></i> Produce Listings</h5>
                <div class="row">
                ${produceListings.length > 0
                    ? produceListings
                        .map(
                            (p) => `
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title" style="color: var(--primary-green);"><i class="fas fa-leaf mr-2"></i> ${p.produce}</h6>
                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <div class="card-detail-label"><i class="fas fa-box-open"></i> Quantity</div>
                                                <p class="card-detail-value text-warning">${p.quantity || 'N/A'}</p> </div>
                                            <div class="col-6">
                                                <div class="card-detail-label"><i class="fas fa-tag"></i> Price/Unit</div>
                                                <p class="card-detail-value text-success">₦${p.price || 'N/A'}</p> </div>
                                            <div class="col-12 mt-2">
                                                <div class="card-detail-label"><i class="far fa-calendar-alt"></i> Available From</div>
                                                <p class="card-detail-value text-muted">${new Date(p.available_date).toLocaleDateString()}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `
                        )
                        .join('')
                    : '<div class="col-12"><p class="alert alert-info border-info"><i class="fas fa-info-circle mr-1"></i> No produce currently listed by this farmer.</p></div>'}
                </div>

                <h5 class="mt-4"><i class="fas fa-award text-primary mr-2"></i> Ratings & Reviews</h5>
                <div class="row">
                ${ratings.length > 0
                    ? ratings
                        .map(
                            (r) => `
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="star-rating">${generateRatingStars(r.rating)}</div>
                                            <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>${new Date(r.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <div class="form-group mt-2 mb-0">
                                            <label class="form-label mb-1"><i class="fas fa-comment-alt"></i> Comment:</label>
                                            <p class="form-control-plaintext bg-light" style="border: none !important;">${r.comment || 'No comment provided.'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `
                        )
                        .join('')
                    : '<div class="col-12"><p class="alert alert-secondary border-secondary"><i class="fas fa-star-half-alt mr-1"></i> No ratings or reviews yet for this farmer.</p></div>'}
                </div>
            `;

            modalContent.append(farmerDetailsHTML);
        }
        
        // Initial load
        fetchAndDisplayFarmers();
    });
</script>