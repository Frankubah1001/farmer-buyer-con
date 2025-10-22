<?php include 'buyerheader.php'; ?>
<style>
  .pagination .page-item.active .page-link {
    background-color: #28a745;
    border-color: #28a745;
  }
  .pagination .page-link {
    color: #28a745;
  }
  .pagination .page-item.disabled .page-link {
    color: #6c757d;
  }
  .input-group button {
    border-color: #ced4da;
  }
  .input-group button:hover {
    background-color: #f8f9fa;
  }
  .star-rating i {
    color: #ffc107;
  }
  .star-rating .empty-star {
    color: #ccc;
  }
</style>

<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
    <?php include 'buyertopbar.php'; ?>
    <div class="container-fluid">
      <div class="container mt-4">
        <h2>Available Produce</h2>
        <div class="row mb-3">
          <div class="col-md-4">
            <label for="produceFilter" class="form-label">Filter by Produce:</label>
            <input type="text" class="form-control form-control-sm" id="produceFilter" placeholder="Enter produce name">
          </div>
          <div class="col-md-4">
            <label for="locationFilter" class="form-label">Filter by Location:</label>
            <div class="input-group">
              <input type="text" class="form-control form-control-sm" id="locationFilter" placeholder="Enter location (e.g., Ibadan, Lagos)">
              <button class="btn btn-outline-secondary btn-sm" type="button" id="clearLocationFilter">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4">
            <label for="farmerFilter" class="form-label">Filter by Farmer:</label>
            <input type="text" class="form-control form-control-sm" id="farmerFilter" placeholder="Enter farmer name">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Produce</th>
                <th>Quantity</th>
                <th>Quantity Remaining</th>
                <th>Quantity Ordered</th>
                <th>Price(Per Unit)</th>
                <th>Available Date</th>
                <th>Farmer</th>
                <th>Location</th>
                <th>Ratings</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <nav aria-label="Page navigation" class="mt-3">
          <ul id="pagination" class="pagination justify-content-center"></ul>
        </nav>
        <div class="modal fade" id="orderFormModal" tabindex="-1" role="dialog" aria-labelledby="orderFormModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="orderFormModalLabel">Order Produce</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="orderForm">
                  <input type="hidden" name="prod_id" id="prod_id">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="produceName">Produce Name</label>
                        <input type="text" class="form-control" id="produceName" name="produceName" readonly>
                        <small class="form-text text-muted">This will be auto-filled.</small>
                      </div>
                      <div class="form-group">
                        <label for="farmerName">Farmer Name</label>
                        <input type="text" class="form-control" id="farmerName" name="farmerName" readonly>
                        <small class="form-text text-muted">This will be auto-filled.</small>
                      </div>
                      <div class="form-group">
                        <label for="quantity">Quantity (e.g., Bags, Kg)</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
                      </div>
                      <div class="form-group">
                        <label for="pricePerUnit">Price per Unit</label>
                        <input type="text" class="form-control" id="pricePerUnit" name="pricePerUnit" readonly>
                        <small class="form-text text-muted">This will be auto-filled.</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="deliveryAddress">Delivery Address</label>
                        <textarea class="form-control" id="deliveryAddress" name="deliveryAddress" rows="3" required></textarea>
                      </div>
                      <div class="form-group">
                        <label for="deliveryDate">Preferred Delivery Date</label>
                        <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                        <small class="form-text text-muted">Please select a future date</small>
                      </div>
                      <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                      </div>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit Order</button>
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

    function loadProduce() {
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
          $('tbody').html('<tr><td colspan="10" class="text-center">Error loading produce.</td></tr>');
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
        $('tbody').html('<tr><td colspan="10" class="text-center">No produce found matching your filters.</td></tr>');
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

        $('tbody').append(`
          <tr>
            <td>${produce.produce || 'N/A'}</td>
            <td>${produce.quantity || 0}</td>
            <td>${produce.remaining_quantity || 0}</td>
            <td>${produce.quantity_ordered || 0}</td>
            <td>${produce.price || 'N/A'}</td>
            <td>${produce.available_date || 'N/A'}</td>
            <td>${produce.farmer_name || 'N/A'}</td>
            <td>${produce.location || 'N/A'}</td>
            <td class="star-rating" data-rating="${rating}">${stars}</td>
            <td>
              <button class="btn btn-sm btn-success order-btn" 
                      data-toggle="modal" 
                      data-target="#orderFormModal" 
                      data-produce="${produce.produce}" 
                      data-produce-id="${produce.prod_id}" 
                      data-farmer="${produce.farmer_name}" 
                      data-price="${produce.price}"
                      ${produce.remaining_quantity === 'Item Sold' || produce.remaining_quantity <= 0 ? 'disabled' : ''}>
                ${produce.remaining_quantity === 'Item Sold' || produce.remaining_quantity <= 0 ? 'Sold Out' : 'Order'}
              </button>
            </td>
          </tr>
        `);
      });
      renderPagination();
    }

    function renderPagination() {
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
      
      modal.find('#produceName').val(button.data('produce'));
      modal.find('#farmerName').val(button.data('farmer'));
      modal.find('#pricePerUnit').val(button.data('price'));
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
      const originalText = submitBtn.text();
      submitBtn.prop('disabled', true).text('Placing Order...');

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
          submitBtn.prop('disabled', false).text(originalText);
        }
      });
    });

    // Load initial data
    loadProduce();
  });
</script>