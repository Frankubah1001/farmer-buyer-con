<?php 
require_once 'auth_check.php';
include 'header.php'; 
?>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'topbar.php'; ?>

        <div class="container-fluid">
            <div class="container mt-4">
                <h2>All Farmers</h2>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="locationFilterFarmers" class="form-label">Filter by Location:</label>
                        <input type="text" class="form-control form-control-sm" id="locationFilterFarmers" placeholder="Enter location">
                    </div>
                    <div class="col-md-6">
                        <label for="contactFilterFarmers" class="form-label">Filter by Contact:</label>
                        <input type="text" class="form-control form-control-sm" id="contactFilterFarmers" placeholder="Enter phone number">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Rating</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        </ul>
                </nav>

                <div class="modal fade" id="farmerDetailsModal" tabindex="-1" role="dialog" aria-labelledby="farmerDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="farmerDetailsModalLabel">Farmer Details</h5>
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

        // Function to fetch and display farmers
        function fetchAndDisplayFarmers() {
            let locationFilter = $('#locationFilterFarmers').val();
            let contactFilter = $('#contactFilterFarmers').val();

            $.ajax({
                url: 'views/get_farmers.php', // Backend PHP file
                type: 'GET',
                dataType: 'json',
                data: {
                    page: currentPageFarmers,
                    location: locationFilter,
                    contact: contactFilter
                },
                success: function(response) {
                    if (response.farmers) {
                        farmerData = response.farmers;
                        displayFarmers(response.farmers);
                        updatePagination(response.pagination);
                    } else {
                        $('tbody').html('<tr><td colspan="7">No farmers found.</td></tr>');
                        updatePagination({ currentPage: 1, totalPages: 1, totalRows: 0 });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching farmers:', error);
                    $('tbody').html('<tr><td colspan="7">Error fetching data.</td></tr>');
                    updatePagination({ currentPage: 1, totalPages: 1, totalRows: 0 });
                }
            });
        }

        // Function to display farmers in the table
        function displayFarmers(farmers) {
            let tableBody = $('tbody');
            tableBody.empty(); // Clear existing rows

            farmers.forEach((farmer, index) => { // Added index here
                let rowNumber = (currentPageFarmers - 1) * rowsPerPageFarmers + index + 1; // Calculate row number
                let ratingDisplay = farmer.rating !== null ? generateRatingStars(farmer.rating) : 'No ratings'; // Display stars or a message
                let row = `
                    <tr>
                        <td>${rowNumber}</td>
                        <td>${farmer.first_name}</td>
                        <td>${farmer.last_name}</td>
                        <td>${farmer.location}</td>
                        <td>${farmer.contact}</td>
                        <td>${ratingDisplay}</td>
                        <td><button class="btn btn-sm btn-success view-produce-btn" data-farmer-id="${farmer.user_id}">View Produce</button></td>
                    </tr>
                `;
                tableBody.append(row);
            });
        }

        function generateRatingStars(rating) {
            const maxRating = 5;
            const fullStar = '<i class="fas fa-star text-warning"></i>';
            const emptyStar = '<i class="far fa-star text-warning"></i>';
            const roundedRating = Math.round(rating); // Round to the nearest whole number for star display
            let stars = '';

            for (let i = 1; i <= maxRating; i++) {
                stars += i <= roundedRating ? fullStar : emptyStar;
            }
            return stars;
        }

        // Function to update pagination controls
        function updatePagination(pagination) {
            currentPageFarmers = pagination.currentPage;
            totalPagesFarmers = pagination.totalPages;
            totalRowsFarmers = pagination.totalRows;

            let paginationControls = $('.pagination');
            paginationControls.empty();

            // Previous Page
            let prevDisabled = currentPageFarmers === 1 ? 'disabled' : '';
            let prevLink = `<li class="page-item ${prevDisabled}"><a class="page-link page-link-num" href="#" data-page="${currentPageFarmers - 1}">&laquo;</a></li>`;
            paginationControls.append(prevLink);

            // Page Numbers
            for (let i = 1; i <= totalPagesFarmers; i++) {
                let activeClass = currentPageFarmers === i ? 'active' : '';
                let pageLink = `<li class="page-item ${activeClass}"><a class="page-link page-link-num" href="#" data-page="${i}">${i}</a></li>`;
                paginationControls.append(pageLink);
            }

            // Next Page
            let nextDisabled = currentPageFarmers === totalPagesFarmers ? 'disabled' : '';
            let nextLink = `<li class="page-item ${nextDisabled}"><a class="page-link page-link-num" href="#" data-page="${currentPageFarmers + 1}">&raquo;</a></li>`;
            paginationControls.append(nextLink);

            // Page number click handler
            $('.page-link-num').off('click').on('click', function(e) { // Use .off() to prevent duplicate handlers
                e.preventDefault();
                let page = parseInt($(this).data('page'));
                if (!isNaN(page) && page >= 1 && page <= totalPagesFarmers) {
                    currentPageFarmers = page;
                    fetchAndDisplayFarmers();
                }
            });
        }

        // --- Event Listeners ---
        $('#locationFilterFarmers, #contactFilterFarmers').on('input', function() {
            currentPageFarmers = 1; // Reset to first page on filter change
            fetchAndDisplayFarmers();
        });

        // --- Modal ---
        $('body').off('click', '.view-produce-btn').on('click', '.view-produce-btn', function() { // Use .off() to prevent duplicate handlers
            let farmerId = $(this).data('farmer-id');
            fetchAndDisplayFarmerDetails(farmerId);
        });

        function fetchAndDisplayFarmerDetails(farmerId) {
            $.ajax({
                url: 'views/get_farmer_details.php',
                type: 'GET',
                dataType: 'json',
                data: { farmer_id: farmerId },
                success: function(response) {
                    if (response.farmer) {
                        displayFarmerDetailsModal(response);
                        $('#farmerDetailsModal').modal('show'); // Show the modal
                    } else {
                        console.error('Error fetching farmer details:', response.error);
                        alert('Could not fetch farmer details.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching farmer details:', error);
                    alert('Error fetching farmer details.');
                }
            });
        }

        function displayFarmerDetailsModal(data) {
            let modalContent = $('#farmerDetailsModal .modal-body');
            modalContent.empty();

            let farmer = data.farmer;
            let produceListings = data.produce_listings;
            let ratings = data.ratings;

            let farmerDetailsHTML = `
                <h4>Farmer Details</h4>
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="farmerName">Name:</label>
                            <input type="text" class="form-control-plaintext text-primary" id="farmerName" value="${farmer.first_name} ${farmer.last_name}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="farmerEmail">Email:</label>
                            <input type="text" class="form-control-plaintext text-success" id="farmerEmail" value="${farmer.email}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="farmerPhone">Phone:</label>
                            <input type="text" class="form-control-plaintext text-info" id="farmerPhone" value="${farmer.phone}" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="farmerAddress">Address:</label>
                            <input type="text" class="form-control-plaintext text-danger" id="farmerAddress" value="${farmer.address}" readonly>
                        </div>
                    </div>
                </form>

                <h5>Produce Listings</h5>
                ${produceListings.length > 0
                    ? produceListings
                        .map(
                            (p) => `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Produce:</label>
                                            <input type="text" class="form-control-plaintext font-weight-bold" value="${p.produce}" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Quantity:</label>
                                            <input type="text" class="form-control-plaintext text-warning" value="${p.quantity}" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Price:</label>
                                            <input type="text" class="form-control-plaintext text-success" value="${p.price}" readonly>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Available On:</label>
                                            <input type="text" class="form-control-plaintext text-muted" value="${new Date(
                                                p.available_date
                                            ).toLocaleDateString()}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `
                        )
                        .join('')
                    : '<p>No produce listed.</p>'}

                <h5>Ratings</h5>
                ${ratings.length > 0
                    ? ratings
                        .map(
                            (r) => `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Rating:</label>
                                            ${generateRatingStars(r.rating)}
                                            <input type="text" class="form-control-plaintext d-none" value="${r.rating}" readonly>
                                        </div>
                                        <div class="form-group col-md-9">
                                            <label>Comment:</label>
                                            <textarea class="form-control-plaintext text-secondary" rows="2" readonly>${
                                                r.comment || 'No comment'
                                            }</textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Date:</label>
                                            <input type="text" class="form-control-plaintext text-muted" value="${new Date(
                                                r.created_at
                                            ).toLocaleDateString()}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `
                        )
                        .join('')
                    : '<p>No ratings yet.</p>'}
            `;

            modalContent.append(farmerDetailsHTML);
        }

        // Initial load
        fetchAndDisplayFarmers();
    });
</script>