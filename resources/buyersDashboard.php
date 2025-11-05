<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php';
?>

<div id="content-wrapper" class="d-flex flex-column">
  <div id="content">
    <?php include 'buyertopbar.php'; ?>

    <div class="container-fluid" style="padding: 2rem 1.5rem; max-width: 1400px; margin: auto;">

      <!-- WELCOME TITLE -->
      <h1 class="h3 mb-4" style="
        font-size: 2.2rem; 
        font-weight: 800; 
        background: linear-gradient(90deg, #2E8B57, #4CAF50);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: fadeInDown 0.8s ease-out;
      ">
        Welcome, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!
      </h1>

      <div class="row">

        <!-- RECENT ORDERS -->
        <div class="col-xl-6 col-md-6 mb-4">
          <div class="card shadow h-100 py-2" id="recent-orders-card" style="
            border-radius: 18px; 
            overflow: hidden; 
            border: 1px solid #E8F5E9;
            transition: all 0.4s ease;
          " onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 18px 35px rgba(46,139,87,0.22)'"
              onmouseout="this.style.transform=''; this.style.boxShadow=''">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="
                    color: #2E8B57; 
                    font-size: 1.05rem; 
                    display: flex; 
                    align-items: center; 
                    gap: 8px;
                  ">
                    <i class="fas fa-shopping-cart"></i> Recent Orders
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered" id="recent-orders-table" width="100%" cellspacing="0" style="font-size: 0.92rem;">
                      <thead style="background: #E8F5E9; color: #1B5E20;">
                        <tr>
                          <th>Order #</th>
                          <th>Date</th>
                          <th>Produce</th>
                          <th>Quantity</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                  <div class="mt-3">
                    <a href="order_history.php" class="btn btn-sm" style="
                      background: #2E8B57; 
                      color: #fff; 
                      padding: 0.55rem 1.2rem; 
                      border-radius: 12px; 
                      font-weight: 600;
                      display: inline-flex;
                      align-items: center;
                      gap: 6px;
                      text-decoration: none;
                      transition: all 0.3s ease;
                    " onmouseover="this.style.background='#276945'; this.style.transform='translateY(-2px)'" 
                      onmouseout="this.style.background='#2E8B57'; this.style.transform=''">
                      <i class="fas fa-list"></i> View All Orders
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MOST ORDERED PRODUCE -->
      <div class="col-xl-6 col-md-6 mb-4">
  <div class="card shadow h-100 py-2" id="most-produce-card" style="
    border-radius: 18px; 
    overflow: hidden; 
    border: 1px solid #E8F5E9;
    transition: all 0.4s ease;
  " onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 18px 35px rgba(46,139,87,0.22)'"
      onmouseout="this.style.transform=''; this.style.boxShadow=''">
    <div class="card-body">
      <div class="row no-gutters align-items-center">
        <div class="col mr-2">
          <div class="text-xs font-weight-bold text-uppercase mb-3" style="
            color: #4CAF50; 
            font-size: 1.05rem; 
            display: flex; 
            align-items: center; 
            gap: 8px;
          ">
            <i class="fas fa-chart-bar"></i> Top 5 Farm Produce Ordered (Quantity)
          </div>
          <div class="chart-area" style="height: 250px;">
            <canvas id="mostProduceChart"></canvas>
          </div>
          <div class="mt-3 text-center">
            <a href="buyer_view_product.php" class="btn btn-sm" style="
              background: #4CAF50; 
              color: #fff; 
              padding: 0.55rem 1.2rem; 
              border-radius: 12px; 
              font-weight: 600;
              display: inline-flex;
              align-items: center;
              gap: 6px;
              text-decoration: none;
            " onmouseover="this.style.background='#388E3C'; this.style.transform='translateY(-2px)'" 
              onmouseout="this.style.background='#4CAF50'; this.style.transform=''">
              <i class="fas fa-seedling"></i> View All Produce
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
      </div>


      <div class="row">

        <!-- FARMERS -->
        <div class="col-xl-6 col-md-6 mb-4">
          <div class="card shadow h-100 py-2" id="farmers-card" style="
            border-radius: 18px; 
            overflow: hidden; 
            border: 1px solid #E8F5E9;
            transition: all 0.4s ease;
          " onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 18px 35px rgba(46,139,87,0.22)'"
              onmouseout="this.style.transform=''; this.style.boxShadow=''">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="
                    color: #17A2B8; 
                    font-size: 1.05rem; 
                    display: flex; 
                    align-items: center; 
                    gap: 8px;
                  ">
                    <i class="fas fa-user-friends"></i> Farmers
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered" id="farmers-table" width="100%" cellspacing="0" style="font-size: 0.92rem;">
                      <thead style="background: #E8F5E9; color: #1B5E20;">
                        <tr>
                          <th>Name</th>
                          <th>Location</th>
                          <th>Rating</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                  <div class="mt-3">
                    <a href="buyer_view_farmer.php" class="btn btn-sm" style="
                      background: #17A2B8; 
                      color: #fff; 
                      padding: 0.55rem 1.2rem; 
                      border-radius: 12px; 
                      font-weight: 600;
                      display: inline-flex;
                      align-items: center;
                      gap: 6px;
                      text-decoration: none;
                    " onmouseover="this.style.background='#138496'" 
                      onmouseout="this.style.background='#17A2B8'">
                      <i class="fas fa-users"></i> View All
                    </a>
                    <button class="btn btn-sm ml-2" id="rateFarmerBtn" data-toggle="modal" data-target="#rateFarmerModal" style="
                      background: #FFB300; 
                      color: #1B5E20; 
                      padding: 0.55rem 1.2rem; 
                      border-radius: 12px; 
                      font-weight: 600;
                      display: inline-flex;
                      align-items: center;
                      gap: 6px;
                    " onmouseover="this.style.background='#e0a800'" 
                      onmouseout="this.style.background='#FFB300'">
                      <i class="fas fa-star"></i> Rate Farmer
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ORDER STATUS -->
        <div class="col-xl-6 col-md-6 mb-4">
          <div class="card shadow h-100 py-2" id="order-status-card" style="
            border-radius: 18px; 
            overflow: hidden; 
            border: 1px solid #E8F5E9;
            transition: all 0.4s ease;
          " onmouseover="this.style.transform='translateY(-8px) scale(1.02)'; this.style.boxShadow='0 18px 35px rgba(46,139,87,0.22)'"
              onmouseout="this.style.transform=''; this.style.boxShadow=''">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="
                    color: #FFB300; 
                    font-size: 1.05rem; 
                    display: flex; 
                    align-items: center; 
                    gap: 8px;
                  ">
                    <i class="fas fa-truck"></i> Order Status
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0" style="font-size: 0.92rem;">
                      <thead style="background: #E8F5E9; color: #1B5E20;">
                        <tr>
                          <th>Produce</th>
                          <th>Status</th>
                          <th>Update</th>
                          <th>Comment</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RATE FARMER MODAL -->
      <div class="modal fade" id="rateFarmerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #2E8B57; color: #fff; border-radius: 16px 16px 0 0;">
              <h5 class="modal-title"><i class="fas fa-star"></i> Rate a Farmer</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <form id="ratingForm">
                <div class="form-group">
                  <label>Select Farmer:</label>
                  <select class="form-control" id="farmerToRate" name="farmer_id" required style="border-radius: 12px; border: 1px solid #A5D6A7; background: #E8F5E9;"></select>
                </div>
                <div class="form-group">
                  <label>Rating:</label>
                  <select class="form-control" id="rating" name="rating" required style="border-radius: 12px; border: 1px solid #A5D6A7; background: #E8F5E9;">
                    <option value="">-- Select Rating --</option>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Comment (Optional):</label>
                  <textarea class="form-control" id="comment" name="comment" rows="3" style="border-radius: 12px; border: 1px solid #A5D6A7; background: #E8F5E9;"></textarea>
                </div>
                <button type="submit" class="btn btn-block" style="
                  background: #2E8B57; 
                  color: #fff; 
                  padding: 0.7rem; 
                  border-radius: 12px; 
                  font-weight: 600;
                ">
                  <i class="fas fa-paper-plane"></i> Submit Rating
                </button>
              </form>
              <div id="ratingMessage" class="mt-3"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- COMMENT MODAL -->
      <div class="modal fade" id="commentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: #4CAF50; color: #fff; border-radius: 16px 16px 0 0;">
              <h5 class="modal-title"><i class="fas fa-comment"></i> Send Message to Farmer</h5>
              <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              <form id="commentForm">
                <input type="hidden" id="commentOrderId" name="order_id">
                <input type="hidden" id="commentFarmerId" name="farmer_id">
                <div class="form-group">
                  <label>Message:</label>
                  <textarea class="form-control" id="commentMessage" name="message" rows="5" required style="border-radius: 12px; border: 1px solid #A5D6A7; background: #E8F5E9;"></textarea>
                </div>
                <button type="submit" class="btn btn-block" style="
                  background: #4CAF50; 
                  color: #fff; 
                  padding: 0.7rem; 
                  border-radius: 12px; 
                  font-weight: 600;
                ">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </form>
              <div id="commentMessageStatus" class="mt-3"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php include 'buyerfooter.php'; ?>
</div>

<?php include 'buyerscript.php'; ?>

<style>
  @keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
  }
  .table th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
  .table td { vertical-align: middle; }
  .star-rating i { color: #FFB300; }
  .star-rating .far { color: #C8E6C9; }
  .status-select {
    padding: 0.4rem 0.8rem;
    border-radius: 10px;
    border: 1px solid #A5D6A7;
    background: #E8F5E9;
    font-size: 0.88rem;
  }
</style>

<script>
  async function loadDashboardData() {
    try {
      const response = await fetch('views/buyerdashboard.php');
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      const data = await response.json();
      if (data.error) { console.error('Error:', data.error); return; }

      updateRecentOrderCard(data.recent_orders);
      // CALL NEW CHART FUNCTION AND PASS NEW DATA KEY
      renderMostProduceChart(data.top_purchased_produce); 
      updateFarmersCard(data.farmers);
      populateFarmerDropdown(data.all_farmers);
      updateOrderStatusCard(data.order_status);
    } catch (error) {
      console.error('Error:', error);
    }
  }

  function updateRecentOrderCard(recentOrders) {
    const tableBody = document.querySelector("#recent-orders-table tbody");
    tableBody.innerHTML = '';
    if (recentOrders && Array.isArray(recentOrders) && recentOrders.length > 0) {
      recentOrders.forEach(order => {
        const row = tableBody.insertRow();
        row.insertCell().textContent = order.order_id.replace('ORD-', '');
        row.insertCell().textContent = order.order_date.split(' ')[0];
        row.insertCell().textContent = order.produce;
        row.insertCell().textContent = order.quantity;
        row.insertCell().textContent = order.total_amount;
      });
    } else {
      const row = tableBody.insertRow();
      const cell = row.insertCell();
      cell.setAttribute('colspan', 5);
      cell.textContent = "No recent orders";
      cell.style.textAlign = "center";
    }
  }

// NEW FUNCTION TO RENDER BAR CHART
function renderMostProduceChart(topProduceData) {
    // Destroy previous chart instance if it exists
    if (window.mostProduceChartInstance) {
        window.mostProduceChartInstance.destroy();
    }

    const ctx = document.getElementById('mostProduceChart');
    if (!ctx) return;

    if (!topProduceData || topProduceData.length === 0) {
        // Fallback message when no data is available
        const chartArea = document.querySelector('#most-produce-card .chart-area');
        if (chartArea) {
            chartArea.innerHTML = '<div class="text-center p-5"><i class="fas fa-leaf fa-3x" style="color: #ccc;"></i><p class="mt-3">No orders placed yet to generate the chart.</p></div>';
        }
        return;
    }
    
    // Extract labels (Produce names) and data (Quantities)
    const labels = topProduceData.map(item => item.produce);
    const quantities = topProduceData.map(item => item.quantity);
    
    // Define a palette of distinct colors for the bars
    const backgroundColors = [
        'rgba(76, 175, 80, 0.7)',  // Green
        'rgba(255, 159, 64, 0.7)', // Orange
        'rgba(54, 162, 235, 0.7)', // Blue
        'rgba(255, 99, 132, 0.7)', // Red
        'rgba(153, 102, 255, 0.7)',// Purple
        'rgba(255, 205, 86, 0.7)', // Yellow
        'rgba(75, 192, 192, 0.7)', // Teal
        'rgba(201, 203, 207, 0.7)' // Gray
    ];

    const borderColors = [
        'rgba(46, 139, 87, 1)',   // Darker Green
        'rgba(255, 140, 0, 1)',   // Darker Orange
        'rgba(25, 118, 210, 1)',  // Darker Blue
        'rgba(220, 53, 69, 1)',   // Darker Red
        'rgba(123, 31, 162, 1)',  // Darker Purple
        'rgba(255, 193, 7, 1)',   // Darker Yellow
        'rgba(0, 121, 107, 1)',   // Darker Teal
        'rgba(108, 117, 125, 1)'  // Darker Gray
    ];
    
    // Create the Chart.js Bar Chart
    window.mostProduceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Quantity Ordered',
                data: quantities,
                backgroundColor: backgroundColors.slice(0, labels.length), // Use subset of colors based on data length
                borderColor: borderColors.slice(0, labels.length),       // Use subset of colors based on data length
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity (Units)'
                    },
                    // Ensure quantities are displayed as integers
                    ticks: {
                       callback: function(value) {
                          if (value % 1 === 0) { return value; }
                       }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false // Still hide the legend as colors directly map to bars
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y + ' units';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
}

  function updateFarmersCard(farmers) {
    const tableBody = document.querySelector("#farmers-table tbody");
    tableBody.innerHTML = '';
    if (farmers && Array.isArray(farmers) && farmers.length > 0) {
      farmers.forEach(farmer => {
        const row = tableBody.insertRow();
        row.insertCell().textContent = `${farmer.first_name} ${farmer.last_name}`;
        row.insertCell().textContent = farmer.location;
        const ratingCell = row.insertCell();
        let stars = '';
        for (let i = 1; i <= 5; i++) {
          stars += i <= farmer.rating ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star"></i>';
        }
        ratingCell.innerHTML = stars;
      });
    } else {
      const row = tableBody.insertRow();
      const cell = row.insertCell();
      cell.setAttribute('colspan', 3);
      cell.textContent = "No patronised farmers found.";
      cell.style.textAlign = "center";
    }
  }

  function populateFarmerDropdown(farmers) {
    const farmerSelect = document.getElementById('farmerToRate');
    farmerSelect.innerHTML = '<option value="">-- Select a Farmer --</option>';
    if (farmers && Array.isArray(farmers)) {
      farmers.forEach(farmer => {
        const option = document.createElement('option');
        option.value = farmer.user_id;
        option.textContent = `${farmer.first_name} ${farmer.last_name}`;
        farmerSelect.appendChild(option);
      });
    }
  }

  function updateOrderStatusCard(orderStatus) {
    const tableBody = document.querySelector("#order-status-card tbody");
    tableBody.innerHTML = '';
    if (orderStatus) {
      const row = tableBody.insertRow();
      row.insertCell().textContent = orderStatus.produce;
      const statusCell = row.insertCell();
      statusCell.textContent = orderStatus.status || 'Pending';
      const updateCell = row.insertCell();
      const select = document.createElement('select');
      select.className = 'form-control status-select';
      select.dataset.orderId = orderStatus.order_id;
      select.dataset.previousValue = orderStatus.status || 'Pending';
      const statusOptions = ['Delivery Confirmed', 'Delivery Not Received', 'Still Awaiting Delivery', 'Delivery Cancelled'];
      statusOptions.forEach(option => {
        const optElement = document.createElement('option');
        optElement.value = option;
        optElement.textContent = option;
        if (option === (orderStatus.status || 'Pending')) optElement.selected = true;
        select.appendChild(optElement);
      });
      updateCell.appendChild(select);
      const commentCell = row.insertCell();
      const commentBtn = document.createElement('button');
      commentBtn.className = 'btn btn-primary btn-sm comment-btn';
      commentBtn.textContent = 'Comment';
      commentBtn.dataset.toggle = 'modal';
      commentBtn.dataset.target = '#commentModal';
      commentBtn.dataset.orderId = orderStatus.order_id;
      commentBtn.dataset.farmerId = orderStatus.farmer_id;
      commentCell.appendChild(commentBtn);
    } else {
      const row = tableBody.insertRow();
      const cell = row.insertCell();
      cell.setAttribute('colspan', 4);
      cell.textContent = "No recent orders with status available";
      cell.style.textAlign = "center";
    }
  }

  $(document).on('change', '.status-select', function() {
    const selectElement = this;
    const orderId = $(this).data('order-id');
    const newStatus = $(this).val();
    $(this).prop('disabled', true).after('<span class="spinner-border spinner-border-sm ml-2"></span>');
    $.ajax({
      url: 'views/update_order_status.php',
      type: 'POST',
      dataType: 'json',
      data: { order_id: orderId.replace('ORD-', ''), new_status: newStatus },
      success: function(response) {
        $(selectElement).prop('disabled', false).next('.spinner-border').remove();
        if (response.status === 'success') {
          alert('Order status updated successfully');
          location.reload();
        } else {
          toastr.error('Failed: ' + response.message);
          $(selectElement).val($(selectElement).data('previous-value'));
        }
      },
      error: function() {
        $(selectElement).prop('disabled', false).next('.spinner-border').remove();
        toastr.error('Error updating status');
        $(selectElement).val($(selectElement).data('previous-value'));
      }
    });
  });

  $(document).on('click', '.comment-btn', function() {
    $('#commentOrderId').val($(this).data('order-id').replace('ORD-', ''));
    $('#commentFarmerId').val($(this).data('farmer-id'));
    $('#commentMessage').val('');
    $('#commentMessageStatus').removeClass().text('');
  });

  $('#commentForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
      url: 'views/submit_order_comment.php',
      type: 'POST',
      dataType: 'json',
      data: $(this).serialize(),
      success: function(response) {
        const status = $('#commentMessageStatus');
        status.removeClass();
        if (response.status === 'success') {
          status.addClass('alert-success').text(response.message);
          setTimeout(() => $('#commentModal').modal('hide'), 1500);
        } else {
          status.addClass('alert-danger').text(response.message);
        }
      }
    });
  });

  $(document).ready(function() {
    loadDashboardData();
    $('#ratingForm').submit(function(e) {
      e.preventDefault();
      $.ajax({
        url: 'views/submit_rating.php',
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        success: function(response) {
          const msg = $('#ratingMessage');
          msg.removeClass();
          if (response.status === 'success') {
            msg.addClass('alert-success').text(response.message);
            $('#ratingForm')[0].reset();
            $('#rateFarmerModal').modal('hide');
            loadDashboardData();
          } else {
            msg.addClass('alert-danger').text(response.message);
          }
        }
      });
    });
  });
</script>