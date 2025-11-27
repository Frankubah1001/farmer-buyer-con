<?php 
require_once 'buyer_auth_check.php';
include 'buyerheader.php'; 
?>
<script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>

<style>
    /* ==============================================================
       FARM-THEME REDESIGN – ENHANCED & INTERACTIVE
       ============================================================== */
    :root {
        --farm-green    : #2E7D32;     /* Deep Farm Green */
        --harvest-gold  : #FF8F00;     /* Golden Harvest */
        --soil-brown    : #6D4C41;     /* Rich Soil */
        --sky-blue      : #81D4FA;     /* Clear Sky */
        --light-field   : #F1F8E9;     /* Light Green Field */
        --wheat         : #FFCA28;     /* Wheat Gold */
        --shadow        : rgba(46, 125, 50, 0.25);
        --hover-glow    : 0 0 12px rgba(255, 143, 0, 0.6);
    }

    body { 
        background: linear-gradient(to bottom, #E8F5E9, var(--light-field)); 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container-fluid { background: transparent; min-height: 100vh; }

    /* Page Title */
    .page-title { 
        color: var(--soil-brown); 
        font-weight: 800; 
        font-size: 1.8rem;
        margin-bottom: 1.8rem; 
        border-bottom: 4px solid var(--harvest-gold); 
        padding-bottom: 8px; 
        display: inline-block;
    }
    .page-title i { 
        color: var(--farm-green); 
        animation: pulse 2s infinite;
    }

    /* Filter Card */
    .filter-card {
        background:#fff; border-radius:16px; box-shadow:0 8px 20px var(--shadow);
        padding:1.8rem; margin-bottom:2rem; border-left:6px solid var(--harvest-gold);
        transition:all .3s ease;
    }
    .filter-card:hover{ transform:translateY(-4px); box-shadow:0 12px 28px var(--shadow); }

    .form-control-sm,.form-select-sm{
        border-radius:10px; border:2px solid #e0e0e0; transition:all .3s ease; font-size:.95rem;
    }
    .form-control-sm:focus,.form-select-sm:focus{
        border-color:var(--farm-green); box-shadow:0 0 0 3px rgba(46,125,50,.2);
    }

    /* Buttons */
    .btn-primary{
        background:var(--farm-green)!important; border:none!important; border-radius:12px;
        font-weight:600; padding:.6rem 1.2rem; transition:all .3s ease; position:relative; overflow:hidden;
    }
    .btn-primary::before{
        content:''; position:absolute; top:0; left:-100%; width:100%; height:100%;
        background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent);
        transition:.6s;
    }
    .btn-primary:hover{
        transform:translateY(-2px) scale(1.05);
        box-shadow:var(--hover-glow),0 6px 16px var(--shadow);
    }
    .btn-primary:hover::before{ left:100%; }

    /* Table */
    .table-responsive{
        border-radius:16px; overflow:hidden; box-shadow:0 10px 30px var(--shadow);
        transition:all .3s ease;
    }
    .table-responsive:hover{ box-shadow:0 16px 40px var(--shadow); }

    .table thead{
        background:linear-gradient(135deg,var(--farm-green),#1B5E20);
        color:#fff; text-shadow:0 1px 2px rgba(0,0,0,.3);
    }
    .table thead th{ font-weight:700; padding:1rem; font-size:1rem; }

    .table tbody tr{
        background:#fff; transition:all .3s ease; cursor:pointer;
    }
    .table tbody tr:hover{
        background:#E8F5E9; transform:translateY(-3px) scale(1.01);
        box-shadow:0 8px 20px var(--shadow); position:relative; z-index:1;
    }
    .table tbody tr:hover td{ color:var(--soil-brown); }
    .table tbody tr:hover .btn{ transform:scale(1.1); }

    /* Badges */
    .status-verified{ background:var(--farm-green); color:#fff; }
    .status-pending{ background:#6c757d; color:#fff; }
    .availability-badge{ 
        padding: 4px 10px; 
        border-radius: 8px; 
        font-size: 0.85rem;
        font-weight: 600;
    }
    .availability-immediate{ background:#4CAF50; color:#fff; }
    .availability-1-3-days{ background:#FF9800; color:#fff; }
    .availability-3-5-days{ background:#FF5722; color:#fff; }
    .availability-unavailable{ background:#9E9E9E; color:#fff; }

    /* Pagination */
    .page-link{
        color:var(--farm-green); border-radius:10px; margin:0 4px;
        transition:all .3s ease; font-weight:600;
    }
    .page-link:hover{
        background:var(--harvest-gold); color:#fff; transform:scale(1.1);
        box-shadow:var(--hover-glow);
    }
    .page-item.active .page-link{
        background:var(--farm-green); border-color:var(--farm-green); color:#fff;
        transform:scale(1.1);
    }

    /* Modal */
    #transModal .modal-content{
        border-radius:20px; border:none; overflow:hidden;
        box-shadow:0 20px 50px rgba(0,0,0,.2);
        animation:modalPop .4s ease-out;
    }
    @keyframes modalPop{
        0%{transform:scale(.8);opacity:0}
        100%{transform:scale(1);opacity:1}
    }
    #transModal .modal-header{
        background:linear-gradient(135deg,var(--farm-green),#1B5E20);
        color:#fff; padding:1.5rem; border:none;
    }
    #transModal .modal-title i{ animation:wiggle 1.5s infinite; }
    @keyframes wiggle{
        0%,100%{transform:rotate(0)}
        25%{transform:rotate(10deg)}
        75%{transform:rotate(-10deg)}
    }

    .detail-row{
        display:flex; align-items:flex-start; margin-bottom:1rem;
        padding:.6rem 0; transition:all .3s ease; border-radius:10px; padding-left:.5rem;
    }
    .detail-row:hover{
        background:rgba(46,125,50,.1); transform:translateX(4px); padding-left:1rem;
    }
    .detail-row i{
        width:36px; color:var(--harvest-gold); font-size:1.3rem; margin-top:2px;
        transition:all .3s ease;
    }
    .detail-row:hover i{ transform:scale(1.2) rotate(15deg); color:var(--wheat); }
    .detail-row span{ flex-grow:1; font-size:.98rem; color:var(--soil-brown); }

    .order-summary-box{
        background:linear-gradient(135deg,#E8F5E9,#F1F8E9);
        border:2px dashed var(--farm-green); border-radius:14px; padding:1.2rem;
        transition:all .3s ease;
    }
    .order-summary-box:hover{
        border-style:solid; box-shadow:0 0 15px rgba(46,125,50,.3);
    }

    .pay-btn{
        min-width:160px; border-radius:14px; font-weight:700; font-size:1rem;
        padding:.8rem 1.2rem; transition:all .3s ease; position:relative; overflow:hidden;
    }
    .btn-pay-now{ background:#D84315; }
    .btn-pay-now:hover{ background:#BF360C; transform:translateY(-3px) scale(1.08);
        box-shadow:0 8px 20px rgba(216,67,21,.4); }
    .btn-pay-delivery{ background:#4CAF50; }
    .btn-pay-delivery:hover{ background:#388E3C; transform:translateY(-3px) scale(1.08);
        box-shadow:0 8px 20px rgba(76,175,80,.4); }
    .pay-btn i{ transition:transform .3s ease; }
    .pay-btn:hover i{ transform:translateX(4px); }

    @keyframes pulse{
        0%{transform:scale(1)} 50%{transform:scale(1.15)} 100%{transform:scale(1)}
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include 'buyertopbar.php'; ?>

        <div class="container-fluid py-4">

            <h3 class="page-title"><i class="fas fa-tractor"></i> Available Transporters</h3>

            <div class="filter-card">
                <div class="row align-items-end">
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-search"></i> Search Transporter</label>
                        <input type="text" class="form-control form-control" id="searchTrans"
                               placeholder="Company name or contact person...">
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-map-marked-alt"></i> Filter by Location</label>
                        <select class="form-control form-select-sm" id="locationFilter">
                            <option value="">Loading Locations...</option> 
                        </select>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-shield-alt"></i> Status</label>
                        <select class="form-control form-select-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="verified">Verified Only</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-clock"></i> Availability</label>
                        <select class="form-control form-select-sm" id="availabilityFilter">
                            <option value="">All Availability</option>
                            <option value="immediate">Immediately</option>
                            <option value="1-3">Within 1-3 Days</option>
                            <option value="3-5">Within 3-5 Days</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0" id="transTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Vehicles & Capacity</th>
                            <th>Operating Areas</th>
                            <th>Availability Status</th>
                            <th>Status</th>
                            <th>Est. Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="9" class="text-center">Loading transporters...</td></tr>
                    </tbody>
                </table>
            </div>

            <nav class="mt-4">
                <ul class="pagination justify-content-center" id="transPager"></ul>
            </nav>

        </div>
    </div>
    <?php include 'buyerfooter.php'; ?>
</div>

<div class="modal fade" id="transModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-truck-loading"></i> <span id="modalCompany"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-seedling"></i> Transporter Details</h6>
                        <div id="modalDetails"></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary border-bottom pb-2"><i class="fas fa-box-open"></i> Your Paid Order</h6>
                        <div id="orderSummary" class="order-summary-box">
                            <div class="text-center p-3 text-muted">Loading your paid order details...</div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="text-success text-center mb-4"><i class="fas fa-handshake"></i> Book & Pay for Transport</h6>
                <div class="d-flex gap-3 justify-content-center">
                    <button class="btn btn-pay-now text-white pay-btn mr-5" id="payNowBtn">
                        <i class="fas fa-credit-card"></i> Pay Now
                    </button>
                    <button class="btn btn-pay-delivery text-white pay-btn" id="payDeliveryBtn">
                        <i class="fas fa-hand-holding-usd"></i> Pay on Delivery
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include 'buyerscript.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
/* ==============================================================
   GLOBAL STATE
   ============================================================== */
let currentPage = 1;
const perPage   = <?php echo $perPage ?? 5; ?>;
let totalPages  = 1;
let allTransporters = [];
let buyerOrder = null;
let availableLocations = new Set();
const LOGGED_IN_BUYER_ID = '<?php echo $_SESSION['buyer_id'] ?? 1; ?>';

/* ==============================================================
   HELPERS
   ============================================================== */
const formatNGN = n => {
    // Convert to number and handle various input types
    const num = parseFloat(n);
    
    // Return ₦0 for invalid or zero values
    if (isNaN(num) || num === 0) {
        return '₦0';
    }
    
    // Format with proper separators
    return '₦' + num.toLocaleString('en-NG', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
};

const getStatusBadge = isVerified => isVerified 
    ? '<span class="badge status-verified">Verified</span>'
    : '<span class="badge status-pending">Pending</span>';

const getAvailabilityBadge = (availability) => {
    const badges = {
        'immediate': '<span class="availability-badge availability-immediate">Immediately</span>',
        '1-3': '<span class="availability-badge availability-1-3-days">Within 1-3 Days</span>',
        '3-5': '<span class="availability-badge availability-3-5-days">Within 3-5 Days</span>',
        'unavailable': '<span class="availability-badge availability-unavailable">Unavailable</span>'
    };
    return badges[availability] || badges['immediate'];
};

/* ==============================================================
   RENDER FUNCTIONS
   ============================================================== */
function renderTable(data){
    const tbody = $('#transTable tbody').empty();
    if(data.length === 0){
        tbody.append('<tr><td colspan="9" class="text-center p-4">No available transporters found matching your criteria.</td></tr>');
        return;
    }
    
    console.log('Rendering transporters:', data); // Debug: Check raw data
    
    data.forEach((t,i) => {
        const idx = (currentPage-1)*perPage + i + 1;
        
        console.log(`Transporter ${i+1} - Fee:`, t.price, 'Type:', typeof t.price); // Debug: Check fee value
        
        tbody.append(`
            <tr>
                <td>${idx}</td>
                <td><strong>${t.company}</strong><br><small class="text-muted">${t.location}</small></td>
                <td><strong>${t.contact_person}</strong><br>
                    <small class="text-muted">${t.phone}</small><br>
                    <small class="text-muted">${t.email}</small></td>
                <td>${t.vehicles}</td>
                <td><small>${t.operating_areas}</small></td>
                <td>${getAvailabilityBadge(t.availability)}</td>
                <td>${getStatusBadge(t.is_verified)}</td>
                <td><strong>${formatNGN(t.price)}</strong></td>
                <td>
                    <button class="btn btn-sm btn-primary bookBtn" data-id="${t.id}">
                        <i class="fas fa-calendar-check"></i> Book
                    </button>
                </td>
            </tr>`);
    });
}

function renderPager(){
    const ul = $('#transPager').empty();
    if(totalPages <= 1) return;

    const prev = currentPage===1 ? 'disabled' : '';
    ul.append(`<li class="page-item ${prev}"><a class="page-link" href="#" data-page="${currentPage-1}">Previous</a></li>`);

    let start = Math.max(1, currentPage-1);
    let end   = Math.min(totalPages, currentPage+1);
    if(currentPage===1 && end<totalPages) end = Math.min(totalPages, start+2);
    if(currentPage===totalPages && start>1) start = Math.max(1, end-2);

    for(let i=start;i<=end;i++){
        const act = i===currentPage?'active':'';
        ul.append(`<li class="page-item ${act}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
    }

    const next = currentPage===totalPages?'disabled':'';
    ul.append(`<li class="page-item ${next}"><a class="page-link" href="#" data-page="${currentPage+1}">Next</a></li>`);
}

function populateLocationFilter(){
    const sel = $('#locationFilter').empty().append('<option value="">All Locations</option>');
    availableLocations.forEach(loc => loc && loc.trim() && sel.append(`<option value="${loc}">${loc}</option>`));
}

/* ==============================================================
   DATA FETCH & PAGINATION
   ============================================================== */

function fetchAllUniqueLocations(){
    const url = `views/book_transport.php?action=fetch_locations`;
    
    fetch(url)
        .then(r => r.json())
        .then(d => {
            if(d.success){
                availableLocations.clear();
                d.locations.forEach(loc => loc && loc.trim() && availableLocations.add(loc));
                populateLocationFilter();
            } else {
                console.error("Failed to fetch all locations:", d.error);
                $('#locationFilter').html('<option value="">Error Loading...</option>');
            }
        })
        .catch(() => {
            console.error("Error fetching locations.");
            $('#locationFilter').html('<option value="">Error Loading...</option>');
        });
}

function fetchTransporters(page=1){
    const search   = $('#searchTrans').val().trim();
    const location = $('#locationFilter').val();
    const status   = $('#statusFilter').val();
    const availability = $('#availabilityFilter').val();

    const url = `views/book_transport.php?page=${page}&search=${encodeURIComponent(search)}&location=${encodeURIComponent(location)}&status=${encodeURIComponent(status)}&availability=${encodeURIComponent(availability)}`;

    $('#transTable tbody').html('<tr><td colspan="9" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

    fetch(url)
        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(d => {
            if(d.success){
                allTransporters = d.data.transporters;
                currentPage = d.data.currentPage;
                totalPages  = d.data.totalPages;

                renderTable(allTransporters);
                renderPager();
            }else{
                $('#transTable tbody').html(`<tr><td colspan="9" class="text-center p-4 text-danger">Error: ${d.error}</td></tr>`);
            }
        })
        .catch(() => $('#transTable tbody').html('<tr><td colspan="9" class="text-center p-4 text-danger">Failed to load data.</td></tr>'));
}

function fetchBuyerOrder(){
    const url = `views/book_transport.php?action=fetch_order&buyer_id=${LOGGED_IN_BUYER_ID}`;
    const box = $('#orderSummary').html('<div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Fetching...</div>');

    console.log('Fetching buyer order for ID:', LOGGED_IN_BUYER_ID);

    fetch(url).then(r => r.json()).then(d => {
        console.log('Order fetch response:', d);
        
        if(d.success && d.order){
            buyerOrder = d.order;
            console.log('Buyer order set:', buyerOrder);
            
            box.html(`
                <div class="detail-row"><i class="fas fa-receipt"></i> <span><strong>Order ID:</strong> ${buyerOrder.order_id}</span></div>
                <div class="detail-row"><i class="fas fa-leaf"></i> <span><strong>Farmer:</strong> ${buyerOrder.farmerName}</span></div>
                <div class="detail-row"><i class="fas fa-box-open"></i> <span><strong>Item:</strong> ${buyerOrder.product_name||'N/A'} (${buyerOrder.quantity} units @ ${formatNGN(buyerOrder.total_amount)})</span></div>
                <div class="detail-row"><i class="fas fa-route"></i> <span><strong>Delivery City:</strong> ${buyerOrder.delivery_city}</span></div>
                <div class="detail-row"><i class="fas fa-map-marker-alt"></i> <span><strong>Delivery Address:</strong> ${buyerOrder.delivery_address||'N/A'}</span></div>
                <div class="detail-row"><i class="far fa-calendar-alt"></i> <span><strong>Target Date:</strong> ${buyerOrder.delivery_date}</span></div>
                <p class="text-success mt-3 mb-0" id="transportCostDisplay"><i class="fas fa-truck-loading"></i> <strong>Est. Transport Cost:</strong> <span id="estTransportCostModal">Select transporter</span></p>
            `);
        }else{
            buyerOrder = null;
            console.error('No paid order found:', d.error || 'Unknown error');
            
            box.html(`
                <div class="text-center p-3 text-warning">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="mb-0"><strong>No Paid Orders Found</strong></p>
                    <small>You need to complete an order payment first before booking transport.</small>
                </div>
            `);
        }
    }).catch(err => {
        buyerOrder = null;
        console.error('Failed to fetch order:', err);
        box.html(`
            <div class="text-center p-3 text-danger">
                <i class="fas fa-times-circle fa-2x mb-2"></i>
                <p class="mb-0">Failed to fetch order details.</p>
            </div>
        `);
    });
}

/* ==============================================================
   MODAL & EVENTS
   ============================================================== */
let selectedTrans = null;

$(document).on('click','.bookBtn',function(){
    const id = $(this).data('id');
    selectedTrans = allTransporters.find(t => t.id == id);
    if(!selectedTrans) return;

    $('#modalCompany').text(selectedTrans.company);
    $('#modalDetails').html(`
        <div class="detail-row"><i class="fas fa-user-circle"></i> <span><strong>Contact Person:</strong> ${selectedTrans.contact_person}</span></div>
        <div class="detail-row"><i class="fas fa-phone"></i> <span><strong>Phone:</strong> ${selectedTrans.phone}</span></div>
        <div class="detail-row"><i class="fas fa-envelope"></i> <span><strong>Email:</strong> ${selectedTrans.email}</span></div>
        <div class="detail-row"><i class="fas fa-truck-loading"></i> <span><strong>Vehicles:</strong> ${selectedTrans.vehicles}</span></div>
        <div class="detail-row"><i class="fas fa-map-marked-alt"></i> <span><strong>Location:</strong> ${selectedTrans.location}</span></div>
        <div class="detail-row"><i class="fas fa-route"></i> <span><strong>Operating Areas:</strong> ${selectedTrans.operating_areas}</span></div>
        <div class="detail-row"><i class="fas fa-clock"></i> <span><strong>Availability:</strong> ${selectedTrans.availability_text}</span></div>
        <div class="detail-row"><i class="fas fa-shield-alt"></i> <span><strong>Status:</strong> ${getStatusBadge(selectedTrans.is_verified)}</span></div>
        <div class="detail-row"><i class="fas fa-money-bill-wave"></i> <span><strong>Transport Fee:</strong> ${formatNGN(selectedTrans.price)}</span></div>
        ${selectedTrans.license_number ? `<div class="detail-row"><i class="fas fa-id-card"></i> <span><strong>License:</strong> ${selectedTrans.license_number}</span></div>` : ''}
    `);

    // Fetch buyer order FIRST, then show modal
    fetchBuyerOrder();
    
    // After a short delay to ensure order is loaded, update transport cost
    setTimeout(() => {
        // Update the transport cost with selected transporter's fee
        $('#estTransportCostModal').html(formatNGN(selectedTrans.price));
    }, 500);

    $('#payNowBtn, #payDeliveryBtn')
        .data('transporter-id', selectedTrans.id)
        .data('price', selectedTrans.price);

    // Show modal using Bootstrap 5 Modal API
    var modalEl = document.getElementById('transModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
});

// PROPER Modal close button handler
$('#transModal .btn-close, #transModal .btn-secondary').on('click', function() {
    var modalEl = document.getElementById('transModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
        modal.hide();
    }
});

// PAYSTACK PAYMENT - Pay Now
$('#payNowBtn').on('click',function(){
    const transId = $(this).data('transporter-id');
    const price   = $(this).data('price');
    
    // More robust order check
    if (!buyerOrder || !buyerOrder.order_id) {
        Swal.fire({
            icon: 'warning',
            title: 'No Paid Order Found',
            text: 'You must have a paid order before booking transport. Please complete an order payment first.',
            confirmButtonColor: '#2E8B57'
        });
        return;
    }
    
    const orderId = buyerOrder.order_id;
    const buyerEmail = '<?php echo $_SESSION['buyer_email'] ?? 'buyer@example.com'; ?>';

    const handler = PaystackPop.setup({
        key: 'pk_test_820ef67599b8a00fd2dbdde80e56e94fda5ed79f',
        email: buyerEmail,
        amount: price * 100,
        currency: 'NGN',
        ref: 'TRANS-' + orderId + '-' + Date.now(),
        callback: function(response) {
            // Show processing state
            Swal.fire({
                title: 'Processing Payment...',
                text: 'Please wait while we verify your payment',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Process payment
            $.post('views/process_transport_payment.php', {
                order_id: orderId,
                transporter_id: transId,
                reference: response.reference,
                amount: price
            }, function(res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Your transport has been booked successfully!',
                        confirmButtonColor: '#2E8B57'
                    }).then(() => {
                        // Reload the entire page to show updated data
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: res.message || 'Verification failed. Please try again.',
                        confirmButtonColor: '#d33'
                    });
                }
            }, 'json').fail(() => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Could not connect to server. Please check your internet connection.',
                    confirmButtonColor: '#d33'
                });
            });
        },
        onClose: function() {
            console.log('Payment window closed');
        }
    });
    handler.openIframe();
});

// Pay on Delivery
$('#payDeliveryBtn').on('click',function(){
    const transId = $(this).data('transporter-id');
    const price   = $(this).data('price');
    
    // More robust order check
    if (!buyerOrder || !buyerOrder.order_id) {
        Swal.fire({
            icon: 'warning',
            title: 'No Paid Order Found',
            text: 'You must have a paid order before booking transport. Please complete an order payment first.',
            confirmButtonColor: '#2E8B57'
        });
        return;
    }
    
    const orderId = buyerOrder.order_id;

    Swal.fire({
        title: 'Confirm Booking',
        text: `Book transport with payment on delivery (${formatNGN(price)})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2E8B57',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Book'
    }).then(result => {
        if (result.isConfirmed) {
            // Show processing
            Swal.fire({
                title: 'Processing Booking...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.post('views/book_transport_delivery.php', {
                order_id: orderId,
                transporter_id: transId,
                payment_method: 'delivery'
            }, function(res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Confirmed!',
                        text: 'Transport booked successfully. Pay on delivery.',
                        confirmButtonColor: '#2E8B57'
                    }).then(() => {
                        // Reload the entire page to show updated data
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Booking Failed',
                        text: res.message || 'Could not complete booking',
                        confirmButtonColor: '#d33'
                    });
                }
            }, 'json').fail(() => {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Could not connect to server',
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
});

/* ==============================================================
   FILTER & PAGINATION LISTENERS
   ============================================================== */
$('#searchTrans').on('input',   () => { currentPage=1; fetchTransporters(1); });
$('#locationFilter').on('change',() => { currentPage=1; fetchTransporters(1); });
$('#statusFilter').on('change', () => { currentPage=1; fetchTransporters(1); });
$('#availabilityFilter').on('change', () => { currentPage=1; fetchTransporters(1); });

$(document).on('click','#transPager .page-link',function(e){
    e.preventDefault();
    const p = parseInt($(this).data('page'));
    if(!$(this).parent().hasClass('disabled') && p>=1 && p<=totalPages){
        currentPage = p; fetchTransporters(p);
    }
});

$(document).ready(() => {
    fetchAllUniqueLocations(); 
    fetchTransporters(1);
});
</script>