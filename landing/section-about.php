<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    .produce-listing {
        padding: 80px 0;
        background: linear-gradient(135deg, #e6f3e6 0%, #f8f9fa 100%);
    }

    .produce-item {
        background: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .produce-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        background: #f9fff9;
    }

    .produce-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 10px;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .produce-item:hover .produce-img {
        transform: scale(1.08);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .produce-item h4 {
        margin: 20px 0 12px;
        color: #2d2d2d;
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .location, .quantity {
        color: #555;
        font-size: 1rem;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .location i, .quantity i {
        color: #28a745;
        font-size: 1.2rem;
    }

    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 25px;
        background: linear-gradient(90deg, #28a745, #34c759);
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        margin-top: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-view:hover {
        background: linear-gradient(90deg, #218838, #2db84c);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .btn-view i {
        font-size: 1.1rem;
    }

    /* Star Rating Styles */
    .star-rating {
        display: flex;
        justify-content: center;
        gap: 4px;
        margin: 12px 0;
        font-size: 1.3rem;
    }

    .star-rating i {
        color: #f7c948;
        transition: transform 0.2s ease;
    }

    .star-rating .far {
        color: #e0e0e0;
    }

    .star-rating .fas, .star-rating .fa-star-half-alt {
        color: #f7c948;
    }

    .star-rating i:hover {
        transform: scale(1.2);
    }

    /* Filter Styles */
    .filter-container {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    .filter-group label {
        display: block;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
    }

    .filter-group input, .filter-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .filter-group input:focus, .filter-group select:focus {
        border-color: #28a745;
        outline: none;
    }

    #produce-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    #produce-suggestions ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    #produce-suggestions li {
        padding: 12px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    #produce-suggestions li:hover {
        background: #f0f0f0;
    }

    .btn-filter {
        padding: 12px 25px;
        background: linear-gradient(90deg, #28a745, #34c759);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-filter:hover {
        background: linear-gradient(90deg, #218838, #2db84c);
        transform: translateY(-2px);
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        margin: auto;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        width: 80%;
        max-width: 500px;
        text-align: center;
    }

    .modal-content p {
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .modal-btn {
        padding: 12px 25px;
        background: linear-gradient(90deg, #28a745, #34c759);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .modal-btn:hover {
        background: linear-gradient(90deg, #218838, #2db84c);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .produce-item {
            padding: 20px;
        }
        .produce-img {
            height: 180px;
        }
        .produce-item h4 {
            font-size: 1.3rem;
        }
        .btn-view {
            padding: 10px 20px;
        }
        .filter-container {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-filter {
            width: 100%;
        }
    }
</style>

<!-- Produce Listing Section -->
<section id="produce-listing" class="produce-listing section">
    <div class="container section-title" data-aos="fade-up">
        <h2>Featured Farm Produce</h2>
        <p>Discover fresh, high-quality produce from top-rated farmers.</p>
    </div>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <!-- Filter Section -->
        <div class="filter-container">
            <div class="filter-group">
                <label for="produce-search">Produce Name</label>
                <input type="text" id="produce-search" placeholder="Search for produce...">
                <div id="produce-suggestions">
                    <ul></ul>
                </div>
            </div>
            <div class="filter-group">
                <label for="location-select">Location</label>
                <select id="location-select">
                    <option value="">All Locations</option>
                    <!-- Options will be populated via JS -->
                </select>
            </div>
            <button class="btn-filter" id="apply-filter">Apply Filter</button>
        </div>
        <div id="produce-grid" class="row gy-4"></div>
    </div>
</section>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <p>You will be redirected to login before you can place an order to any produce listed. You will also see other farmers and their produce.</p>
        <button class="modal-btn" onclick="redirectToLogin()">OK</button>
    </div>
</div>

<!-- AJAX and Custom JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        loadLocations();
        loadProduceListing();

        // Load locations for dropdown
        function loadLocations() {
            $.ajax({
                url: 'get_produce_listing.php',
                type: 'GET',
                data: { action: 'locations' },
                dataType: 'json',
                success: function(locations) {
                    let options = '<option value="">All Locations</option>';
                    locations.forEach(location => {
                        options += `<option value="${location}">${location}</option>`;
                    });
                    $('#location-select').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading locations:', status, error);
                }
            });
        }

        // Produce search suggestions with debounce
        let debounceTimeout;
        $('#produce-search').on('keyup', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: 'get_produce_listing.php',
                        type: 'GET',
                        data: { action: 'suggestions', query: query },
                        dataType: 'json',
                        success: function(response) {
                            console.log('Suggestions response:', response); // Debug log
                            let html = '';
                            if (Array.isArray(response)) {
                                response.forEach(sug => {
                                    if (sug && sug !== 'null') {
                                        html += `<li>${sug}</li>`;
                                    }
                                });
                            } else {
                                console.warn('Unexpected response format:', response);
                            }
                            $('#produce-suggestions ul').html(html);
                            $('#produce-suggestions').toggle(html.length > 0);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading suggestions:', status, error);
                            $('#produce-suggestions').hide();
                        }
                    });
                } else {
                    $('#produce-suggestions').hide();
                }
            }, 300);
        });

        // Select suggestion
        $('#produce-suggestions').on('click', 'li', function() {
            $('#produce-search').val($(this).text());
            $('#produce-suggestions').hide();
            loadProduceListing();
        });

        // Apply filter button
        $('#apply-filter').on('click', function() {
            loadProduceListing();
        });

        // Load on enter in search
        $('#produce-search').on('keypress', function(e) {
            if (e.which == 13) {
                loadProduceListing();
            }
        });

        // Load on location change
        $('#location-select').on('change', function() {
            loadProduceListing();
        });

        function loadProduceListing() {
            let search = $('#produce-search').val();
            let location = $('#location-select').val();
            $.ajax({
                url: 'get_produce_listing.php',
                type: 'GET',
                data: { action: 'listings', search: search, location: location },
                dataType: 'json',
                success: function(response) {
                    if (response.error || response.length === 0) {
                        $('#produce-grid').html('<div class="col-12"><p class="text-center">No produce available.</p></div>');
                        return;
                    }
                    let html = '';
                    response.forEach(produce => {
                        const rating = parseFloat(produce.average_rating) || 0;
                        const fullStars = Math.floor(rating);
                        const hasHalfStar = rating % 1 >= 0.25 && rating % 1 < 0.75;
                        const emptyStars = 5 - Math.ceil(rating);

                        let starsHtml = '';
                        for (let i = 0; i < fullStars; i++) starsHtml += '<i class="fas fa-star"></i>';
                        if (hasHalfStar) starsHtml += '<i class="fas fa-star-half-alt"></i>';
                        for (let i = 0; i < emptyStars; i++) starsHtml += '<i class="far fa-star"></i>';

                        html += `
                            <div class="col-lg-4 col-md-6">
                                <div class="produce-item">
                                    <img src="${produce.image_path}" alt="${produce.produce}" class="produce-img">
                                    <h4>${produce.produce}</h4>
                                    <p class="location"><i class="fas fa-map-marker-alt"></i> ${produce.address}</p>
                                    <p class="quantity"><i class="fas fa-box-open"></i> Uploaded: ${produce.uploaded_quantity} units</p>
                                    <p class="quantity"><i class="fas fa-shopping-cart"></i> Ordered: ${produce.quantity_ordered} units</p>
                                    <p class="quantity"><i class="fas fa-warehouse"></i> Remaining: ${produce.remaining_quantity} units</p>
                                    <div class="star-rating">${starsHtml}</div>
                                    <a href="#" class="btn-view" onclick="checkLogin('${produce.prod_id}')"><i class="fas fa-cart-plus"></i> Place Order</a>
                                </div>
                            </div>
                        `;
                    });
                    $('#produce-grid').html(html);
                },
                error: function(xhr, status, error) {
                    $('#produce-grid').html('<div class="col-12"><p class="text-center">Error loading produce.</p></div>');
                    console.error('AJAX Error:', status, error);
                }
            });
        }

        window.checkLogin = function(prodId) {
            if (typeof isLoggedIn === 'undefined' || !isLoggedIn) {
                $('#loginModal').css('display', 'flex');
                window.currentProdId = prodId;
            } else {
                window.location.href = '../resources/buyer_view_product.php?prod_id=' + prodId;
            }
        };

        window.redirectToLogin = function() {
            $('#loginModal').hide();
            window.location.href = '../resources/Buyer_login.php';
        };
    });

    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>