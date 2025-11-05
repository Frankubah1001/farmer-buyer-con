<?php include 'buyerheader.php'; ?>
<style>
  /* === FARM-INSPIRED STYLES (Retained) === */

  /* General Page Colors */
  .container-fluid {
    background-color: #F9FFF5; /* Very light, clean green/cream background */
  }
  .container h2 {
    color: #2E8B57; /* Sea Green - main farm color */
    font-weight: 700;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #8BC34A; /* Light Green accent border */
    display: inline-block;
  }

  /* Filter Section Styling */
  .form-label {
    color: #4CAF50; /* Green */
    font-weight: 600;
    font-size: 0.9rem;
  }
  .form-control-sm {
    border-radius: 10px;
    border-color: #A5D6A7; /* Light Green border */
    transition: border-color 0.3s;
  }
  .form-control-sm:focus {
    border-color: #2E8B57;
    box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
  }
  #clearLocationFilter {
    color: #795548; /* Brown */
    border-color: #A5D6A7;
  }
  #clearLocationFilter:hover {
    background-color: #E8F5E9; /* Very light green hover */
  }

  /* Table Styling */
  .table {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08); /* Soft shadow */
    border: 1px solid #E8F5E9;
    /* Ensure table text is smaller on mobile for fit */
    font-size: 0.9rem; 
  }
  .table thead th {
    background-color: #4CAF50; /* Primary Green header */
    color: #fff;
    font-weight: 700;
    border: none;
    text-transform: uppercase;
    font-size: 0.8rem; /* Smaller header font */
    letter-spacing: 0.5px;
  }
  .table tbody tr {
    transition: all 0.3s ease;
  }
  /* Stunning Hover Effect */
  .table tbody tr:hover {
    background-color: #E8F5E9; /* Light green row hover */
    transform: scale(1.005);
    box-shadow: 0 4px 10px rgba(46, 139, 87, 0.1);
  }
  .table-striped tbody tr:nth-of-type(odd) {
    background-color: #F8FDF5; /* Slight variance */
  }
  .table td {
    vertical-align: middle;
    color: #384F38; /* Darker green-brown text */
  }

  /* Action Button (Order) */
  .order-btn {
    background-color: #FFB300; /* Yellow/Gold for action */
    border-color: #FFB300;
    color: #384F38; /* Dark text on button */
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem; /* Smaller button text for tables */
  }
  .order-btn:hover:not(:disabled) {
    background-color: #e0a800;
    border-color: #e0a800;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(255, 179, 0, 0.4);
  }
  .order-btn:disabled {
    background-color: #BDBDBD; /* Gray for sold out */
    border-color: #BDBDBD;
    color: #fff;
    cursor: not-allowed;
    opacity: 0.8;
  }

  /* Rating Stars */
  .star-rating i {
    color: #FFC107; /* Gold/Yellow star color */
  }
  .star-rating .empty-star {
    color: #C8E6C9; /* Very light green for empty stars */
  }

  /* Pagination */
  .pagination .page-item.active .page-link {
    background-color: #2E8B57; /* Darker green active button */
    border-color: #2E8B57;
    box-shadow: 0 2px 5px rgba(46, 139, 87, 0.3);
  }
  .pagination .page-link {
    color: #4CAF50; /* Green link text */
    border-radius: 8px;
    margin: 0 3px;
    transition: background-color 0.3s, color 0.3s;
    padding: 0.5rem 0.75rem; /* Better tap area on mobile */
  }
  .pagination .page-link:hover {
    background-color: #E8F5E9;
    color: #1B5E20;
  }
  .pagination .page-item.disabled .page-link {
    color: #BDBDBD;
  }
  
  /* Modal Styling (Retained) */
  .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
  }
  .modal-header {
    background: linear-gradient(90deg, #2E8B57, #4CAF50);
    color: #fff;
    border-radius: 20px 20px 0 0;
    border-bottom: none;
  }
  .modal-title {
    font-weight: 700;
  }
  .modal-header .close {
    color: #fff;
    opacity: 1;
  }
  .modal-body label {
    font-weight: 600;
    color: #384F38;
  }
  .modal-body .form-control {
    border-radius: 10px;
    border-color: #A5D6A7;
    background-color: #F8FDF5;
  }
  .modal-body .btn-primary {
    background-color: #2E8B57;
    border-color: #2E8B57;
    font-weight: 600;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    transition: background-color 0.3s;
  }
  .modal-body .btn-primary:hover {
    background-color: #1B5E20;
    border-color: #1B5E20;
  }
  
  /* === MOBILE-SPECIFIC ADJUSTMENTS (Media Query) === */
  @media (max-width: 767.98px) {
      /* Filter fields stack vertically */
      .card.shadow .row > div {
          margin-bottom: 1rem;
      }
      .card.shadow .row > div:last-child {
          margin-bottom: 0;
      }

      /* Reduce table padding and size */
      .table td, .table th {
          padding: 0.5rem 0.4rem;
          font-size: 0.75rem;
      }
      .table thead th {
          font-size: 0.7rem;
      }
      .order-btn {
          padding: 0.3rem 0.6rem;
          font-size: 0.7rem;
      }
      
      /* Optional: Hide less critical columns on small screens to reduce horizontal clutter */
      /* Note: Use with caution, table-responsive is the primary solution */
      /* .table td:nth-child(2), .table th:nth-child(2), /* Total Qty */
      /* .table td:nth-child(4), .table th:nth-child(4)  /* Qty Ordered */
      /* {
          display: none;
      } */
  }
</style>

<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
    <?php include 'buyertopbar.php'; ?>
    <div class="container-fluid">
      <div class="container mt-4" style="max-width: 1400px; margin: auto; padding: 1.5rem;">
        <h2><i class="fas fa-leaf fa-fw mr-2"></i>Available Farm Produce</h2>
        
        <div class="card shadow p-4 mb-4" style="border-radius: 18px; border: 1px solid #E8F5E9;">
            <div class="row">
              <div class="col-12 col-md-4">
                <label for="produceFilter" class="form-label"><i class="fas fa-carrot mr-1"></i> Filter by Produce:</label>
                <input type="text" class="form-control form-control-sm" id="produceFilter" placeholder="e.g., Yam, Tomato">
              </div>
              <div class="col-12 col-md-4">
                <label for="locationFilter" class="form-label"><i class="fas fa-map-marker-alt mr-1"></i> Filter by Location:</label>
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" id="locationFilter" placeholder="e.g., Ibadan, Lagos">
                  <button class="btn btn-outline-secondary btn-sm" type="button" id="clearLocationFilter">
                    <i class="fas fa-times"></i> Clear
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <label for="farmerFilter" class="form-label"><i class="fas fa-user-tag mr-1"></i> Filter by Farmer:</label>
                <input type="text" class="form-control form-control-sm" id="farmerFilter" placeholder="Enter farmer name">
              </div>
            </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th><i class="fas fa-seedling"></i> Produce</th>
                <th><i class="fas fa-calculator"></i> Total Qty</th>
                <th><i class="fas fa-check-circle"></i> Remaining Qty</th>
                <th><i class="fas fa-shopping-basket"></i> Qty Ordered</th>
                <th><i class="fas fa-money-bill-wave"></i> Price (Per Unit)</th>
                <th><i class="fas fa-calendar-alt"></i> Available Date</th>
                <th><i class="fas fa-user-tie"></i> Farmer</th>
                <th><i class="fas fa-map-pin"></i> Location</th>
                <th><i class="fas fa-star"></i> Ratings</th>
                <th><i class="fas fa-cart-plus"></i> Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <nav aria-label="Page navigation" class="mt-4">
          <ul id="pagination" class="pagination justify-content-center"></ul>
        </nav>
        
        <div class="modal fade" id="orderFormModal" tabindex="-1" role="dialog" aria-labelledby="orderFormModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="orderFormModalLabel"><i class="fas fa-box-open mr-2"></i>Place Your Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="orderForm">
                  <input type="hidden" name="prod_id" id="prod_id">
                  <div class="row">
                    <div class="col-12 col-md-6"> <div class="form-group">
                        <label for="produceName"><i class="fas fa-tag"></i> Produce Name</label>
                        <input type="text" class="form-control" id="produceName" name="produceName" readonly>
                      </div>
                      <div class="form-group">
                        <label for="farmerName"><i class="fas fa-hands-helping"></i> Farmer Name</label>
                        <input type="text" class="form-control" id="farmerName" name="farmerName" readonly>
                      </div>
                      <div class="form-group">
                        <label for="quantity"><i class="fas fa-balance-scale"></i> Quantity (e.g., Bags, Kg)</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                      </div>
                      <div class="form-group">
                        <label for="pricePerUnit"><i class="fas fa-naira-sign"></i> Price per Unit</label>
                        <input type="text" class="form-control" id="pricePerUnit" name="pricePerUnit" readonly>
                      </div>
                    </div>
                    <div class="col-12 col-md-6"> <div class="form-group">
                        <label for="deliveryAddress"><i class="fas fa-road"></i> Delivery Address</label>
                        <textarea class="form-control" id="deliveryAddress" name="deliveryAddress" rows="3" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="deliveryDate"><i class="fas fa-truck-moving"></i> Preferred Delivery Date</label>
                        <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                        <small class="form-text text-muted">Please select a future date</small>
                      </div>
                      <div class="form-group">
                        <label for="notes"><i class="fas fa-pencil-alt"></i> Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-paper-plane mr-2"></i>Submit Order</button>
                </form>
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
  <?php include 'buyerfooter.php'; ?>
</div>
<?php include 'buyerscript.php'; ?>
<script>
  $(document).ready(function() {
    let currentPage = 1;
    const itemsPerPage = 5;
    let allProduce = [];
    let filteredProduce = [];

    // --- NEW HELPER FUNCTIONS FOR FORMATTING ---

    /**
     * Formats a number with comma separators (e.g., 8239 -> 8,239)
     * @param {number|string} num The number to format
     * @returns {string} The formatted number string
     */
    function formatNumber(num) {
        if (num === null || num === undefined) return 'N/A';
        // Convert to string and handle if it's 'Item Sold'
        const number = parseFloat(num);
        if (isNaN(number)) return num; // Return original if not a number (e.g., 'Item Sold')
        return number.toLocaleString('en-US');
    }

    /**
     * Formats an amount with the Naira symbol and comma separators (e.g., 345909 -> ₦345,909.00)
     * @param {number|string} amount The amount to format
     * @returns {string} The formatted currency string
     */
    function formatCurrency(amount) {
        const number = parseFloat(amount);
        if (isNaN(number)) return 'N/A';
        // Use toLocaleString for currency format, specifying the Naira symbol (Nigerian currency code: NGN)
        return number.toLocaleString('en-NG', {
            style: 'currency',
            currency: 'NGN',
            minimumFractionDigits: 0, // Set to 0 to remove the .00 if you prefer whole Naira values
            maximumFractionDigits: 2
        }).replace('NGN', '₦'); // Replace the standard NGN prefix with the desired symbol ₦
    }


    function loadProduce() {
      // Add loading state
      $('tbody').html('<tr><td colspan="10" class="text-center p-4"><i class="fas fa-spinner fa-spin mr-2"></i> Loading available produce...</td></tr>');
      
      $.ajax({
        url: 'views/fetch_produce.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
          allProduce = data;
          filteredProduce = [...allProduce];
          renderTable();
        },
        error: function(xhr, status, error) {
          console.error("Error fetching produce:", error);
          $('tbody').html('<tr><td colspan="10" class="text-center text-danger p-4"><i class="fas fa-times-circle mr-2"></i> Error loading produce. Please try again.</td></tr>');
        }
      });
    }

    function renderTable() {
      $('tbody').empty();
      const start = (currentPage - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      const pageProduce = filteredProduce.slice(start, end);

      if (pageProduce.length === 0 && currentPage > 1) {
        currentPage--;
        renderTable();
        return;
      }

      if (pageProduce.length === 0) {
        $('tbody').html('<tr><td colspan="10" class="text-center p-4"><i class="fas fa-search-minus mr-2"></i> No produce found matching your filters.</td></tr>');
        $('#pagination').empty();
        return;
      }

      $.each(pageProduce, function(index, produce) {
        const rating = parseFloat(produce.rating) || 0;
        let stars = '';
        for (let i = 1; i <= 5; i++) {
          if (i <= Math.floor(rating)) stars += '<i class="fas fa-star"></i>';
          else if (i === Math.ceil(rating) && rating % 1 !== 0) stars += '<i class="fas fa-star-half-alt"></i>';
          else stars += '<i class="far fa-star empty-star"></i>';
        }
        
        const isSoldOut = produce.remaining_quantity === 'Item Sold' || produce.remaining_quantity <= 0;
        const buttonText = isSoldOut ? '<i class="fas fa-ban"></i> Sold Out' : '<i class="fas fa-hand-holding-usd"></i> Order';
        
        // --- APPLY FORMATTING HERE ---
        const formattedTotalQuantity = formatNumber(produce.quantity);
        const formattedRemainingQuantity = formatNumber(produce.remaining_quantity);
        const formattedQuantityOrdered = formatNumber(produce.quantity_ordered);
        const formattedPrice = formatCurrency(produce.price);
        
        // Add colorful icons to table cells for visual aid
        $('tbody').append(`
          <tr>
            <td><i class="fas fa-apple-alt text-success mr-1"></i>${produce.produce || 'N/A'}</td>
            <td>${formattedTotalQuantity}</td>
            <td><span class="font-weight-bold ${isSoldOut ? 'text-danger' : 'text-primary'}">${formattedRemainingQuantity}</span></td>
            <td>${formattedQuantityOrdered}</td>
            <td class="font-weight-bold text-success">${formattedPrice}</td>
            <td><i class="far fa-clock text-info mr-1"></i>${produce.available_date || 'N/A'}</td>
            <td><i class="fas fa-tractor text-warning mr-1"></i>${produce.farmer_name || 'N/A'}</td>
            <td><i class="fas fa-location-arrow text-danger mr-1"></i>${produce.location || 'N/A'}</td>
            <td class="star-rating" data-rating="${rating}">${stars}</td>
            <td>
              <button class="btn btn-sm order-btn" 
                      data-toggle="modal" 
                      data-target="#orderFormModal" 
                      data-produce="${produce.produce}" 
                      data-produce-id="${produce.prod_id}" 
                      data-farmer="${produce.farmer_name}" 
                      data-price="${produce.price}"
                      ${isSoldOut ? 'disabled' : ''}>
                ${buttonText}
              </button>
            </td>
          </tr>
        `);
      });
      renderPagination();
    }

    // --- Pagination, Filtering, and Modal/Form Submission functions remain here ---
    
    function renderPagination() {
      // (Pagination logic)
      const totalPages = Math.ceil(filteredProduce.length / itemsPerPage);
      const $pagination = $('#pagination');
      $pagination.empty();

      if (totalPages <= 1) return;

      $pagination.append(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" aria-label="Previous" data-page="${currentPage - 1}">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
      `);

      const maxVisiblePages = 5;
      let startPage, endPage;
      if (totalPages <= maxVisiblePages) {
        startPage = 1;
        endPage = totalPages;
      } else {
        const maxPagesBeforeCurrent = Math.floor(maxVisiblePages / 2);
        const maxPagesAfterCurrent = Math.ceil(maxVisiblePages / 2) - 1;
        if (currentPage <= maxPagesBeforeCurrent) {
          startPage = 1;
          endPage = maxVisiblePages;
        } else if (currentPage + maxPagesAfterCurrent >= totalPages) {
          startPage = totalPages - maxVisiblePages + 1;
          endPage = totalPages;
        } else {
          startPage = currentPage - maxPagesBeforeCurrent;
          endPage = currentPage + maxPagesAfterCurrent;
        }
      }

      if (startPage > 1) {
        $pagination.append(`
          <li class="page-item ${currentPage === 1 ? 'active' : ''}">
            <a class="page-link" href="#" data-page="1">1</a>
          </li>
        `);
        if (startPage > 2) $pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
      }

      for (let i = startPage; i <= endPage; i++) {
        $pagination.append(`
          <li class="page-item ${currentPage === i ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
          </li>
        `);
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) $pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
        $pagination.append(`
          <li class="page-item ${currentPage === totalPages ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
          </li>
        `);
      }

      $pagination.append(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
          <a class="page-link" href="#" aria-label="Next" data-page="${currentPage + 1}">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      `);
    }

    $(document).on('click', '#pagination .page-link', function(e) {
      e.preventDefault();
      const page = parseInt($(this).data('page'));
      if (!isNaN(page) && page !== currentPage && page >= 1 && page <= Math.ceil(filteredProduce.length / itemsPerPage)) {
        currentPage = page;
        renderTable();
        $('html, body').animate({ scrollTop: $('.table-responsive').offset().top - 20 }, 200);
      }
    });

    let filterTimeout;
    function applyFilters() {
      const produceTerm = $('#produceFilter').val().toLowerCase();
      const locationTerm = $('#locationFilter').val().toLowerCase();
      const farmerTerm = $('#farmerFilter').val().toLowerCase();

      filteredProduce = allProduce.filter(produce => {
        const matchesProduce = produce.produce.toLowerCase().includes(produceTerm);
        const matchesLocation = locationTerm === '' || (produce.location && produce.location.toLowerCase().includes(locationTerm));
        const matchesFarmer = produce.farmer_name.toLowerCase().includes(farmerTerm);
        return matchesProduce && matchesLocation && matchesFarmer;
      });

      currentPage = 1;
      renderTable();
    }

    $('#produceFilter, #locationFilter, #farmerFilter').on('keyup', function() {
      clearTimeout(filterTimeout);
      filterTimeout = setTimeout(applyFilters, 300);
    });

    $('#clearLocationFilter').click(function() {
      $('#locationFilter').val('');
      applyFilters();
    });

    // Order modal handling
    $('#orderFormModal').on('show.bs.modal', function(event) {
      const button = $(event.relatedTarget);
      const modal = $(this);
      
      // Pass the unformatted price/quantity data to the form fields
      modal.find('#produceName').val(button.data('produce'));
      modal.find('#farmerName').val(button.data('farmer'));
      modal.find('#pricePerUnit').val(button.data('price')); // Pass unformatted price
      modal.find('#prod_id').val(button.data('produce-id'));
      
      // Reset form
      modal.find('#quantity').val('');
      modal.find('#deliveryAddress').val('');
      modal.find('#deliveryDate').val('');
      modal.find('#notes').val('');
    });

    // Form submission with proper date handling
    $('#orderForm').submit(function(event) {
      event.preventDefault();
      
      // Get and validate delivery date
      const deliveryDateInput = $('#deliveryDate');
      const deliveryDateValue = deliveryDateInput.val();
      
      console.log('Delivery Date Value:', deliveryDateValue);
      
      // Validate date
      if (!deliveryDateValue) {
        alert('Please select a delivery date');
        deliveryDateInput.focus();
        return;
      }
      
      // Validate date format (YYYY-MM-DD)
      const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
      if (!dateRegex.test(deliveryDateValue)) {
        alert('Invalid date format. Please select a valid date.');
        deliveryDateInput.focus();
        return;
      }
      
      // Validate date is not in the past
      const selectedDate = new Date(deliveryDateValue);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (selectedDate < today) {
        alert('Please select a future date for delivery.');
        deliveryDateInput.focus();
        return;
      }
      
      // Validate quantity
      const quantity = $('#quantity').val();
      if (!quantity || quantity < 1) {
        alert('Please enter a valid quantity');
        $('#quantity').focus();
        return;
      }
      
      // Validate delivery address
      const deliveryAddress = $('#deliveryAddress').val().trim();
      if (!deliveryAddress) {
        alert('Please enter a delivery address');
        $('#deliveryAddress').focus();
        return;
      }

      // Show loading state
      const submitBtn = $(this).find('button[type="submit"]');
      const originalText = submitBtn.html();
      submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Placing Order...');

      $.ajax({
        url: 'prod_process.php',
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        success: function(data) {
          console.log('Server Response:', data);
          
          if (data.status === 'success') {
            alert(data.message);
            $('#orderFormModal').modal('hide');
            $('#orderForm')[0].reset();
            loadProduce(); // Refresh the produce list
          } else {
            // Handle specific error cases
            if (data.message.includes('exceeds total available quantity') || 
                data.message.includes('exceeds remaining quantity')) {
              alert('Quantity Error: ' + data.message);
            } else if (data.message.includes('must be logged in')) {
              alert('Please log in to place an order.');
            } else {
              alert('Error: ' + data.message);
            }
          }
        },
        error: function(xhr, status, error) {
          console.error("Error submitting order:", error);
          console.error("Response text:", xhr.responseText);
          alert('An error occurred while submitting the order. Please try again.');
        },
        complete: function() {
          // Restore button state
          submitBtn.prop('disabled', false).html(originalText);
        }
      });
    });

    // Load initial data
    loadProduce();
  });
</script>