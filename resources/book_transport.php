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
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-search"></i> Search Transporter</label>
                        <input type="text" class="form-control form-control" id="searchTrans"
                               placeholder="Company name or contact person...">
                    </div>
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-map-marked-alt"></i> Filter by Location</label>
                        <select class="form-control form-select-sm" id="locationFilter">
                            <option value="">Loading Locations...</option> 
                        </select>
                    </div>
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <label class="form-label"><i class="fas fa-shield-alt"></i> Status</label>
                        <select class="form-control form-select-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="verified">Verified Only</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <!-- <div class="col-12 col-md-2 text-md-right">
                        <button class="btn btn-sm btn-primary w-100" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div> -->
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
                            <th>Status</th>
                            <th>Est. Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="8" class="text-center">Loading transporters...</td></tr>
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

                <h6 class="text-success text-center mb-4"><i class="fas fa-handshake"></i> Select Payment Option</h6>
                <div class="d-flex gap-3 justify-content-center">
                    <button class="btn btn-pay-now text-white pay-btn" id="payNowBtn">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
const formatNGN = n => (typeof n === 'number' && !isNaN(n)) ? '₦' + n.toLocaleString('en-US') : '₦0.00';

const getStatusBadge = isVerified => isVerified 
    ? '<span class="badge status-verified">Verified</span>'
    : '<span class="badge status-pending">Pending</span>';

/* ==============================================================
   RENDER FUNCTIONS
   ============================================================== */
function renderTable(data){
    const tbody = $('#transTable tbody').empty();
    if(data.length === 0){
        tbody.append('<tr><td colspan="8" class="text-center p-4">No transporters found matching your criteria.</td></tr>');
        return;
    }
    data.forEach((t,i) => {
        const idx = (currentPage-1)*perPage + i + 1;
        tbody.append(`
            <tr>
                <td>${idx}</td>
                <td><strong>${t.company}</strong><br><small class="text-muted">${t.location}</small></td>
                <td><strong>${t.contact_person}</strong><br>
                    <small class="text-muted">${t.phone}</small><br>
                    <small class="text-muted">${t.email}</small></td>
                <td>${t.vehicles}</td>
                <td><small>${t.operating_areas}</small></td>
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
   DATA FETCH & PAGINATION (FIXED LOGIC)
   ============================================================== */

/**
 * Fetches all unique locations from the backend for the filter dropdown.
 */
function fetchAllUniqueLocations(){
    const url = `views/book_transport.php?action=fetch_locations`;
    
    fetch(url)
        .then(r => r.json())
        .then(d => {
            if(d.success){
                availableLocations.clear();
                // Add fetched locations to the Set
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

    const url = `views/book_transport.php?page=${page}&search=${encodeURIComponent(search)}&location=${encodeURIComponent(location)}&status=${encodeURIComponent(status)}`;

    $('#transTable tbody').html('<tr><td colspan="8" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

    fetch(url)
        .then(r => { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(d => {
            if(d.success){
                allTransporters = d.data.transporters;
                currentPage = d.data.currentPage;
                totalPages  = d.data.totalPages;
                
                // IMPORTANT: The location filter is populated once by fetchAllUniqueLocations(), 
                // so we no longer update availableLocations here.

                renderTable(allTransporters);
                renderPager();
            }else{
                $('#transTable tbody').html(`<tr><td colspan="8" class="text-center p-4 text-danger">Error: ${d.error}</td></tr>`);
            }
        })
        .catch(() => $('#transTable tbody').html('<tr><td colspan="8" class="text-center p-4 text-danger">Failed to load data.</td></tr>'));
}

function fetchBuyerOrder(){
    const url = `views/book_transport.php?action=fetch_order&buyer_id=${LOGGED_IN_BUYER_ID}`;
    const box = $('#orderSummary').html('<div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Fetching...</div>');

    fetch(url).then(r => r.json()).then(d => {
        if(d.success && d.order){
            buyerOrder = d.order;
            box.html(`
                <div class="detail-row"><i class="fas fa-receipt"></i> <span><strong>Order ID:</strong> ${buyerOrder.order_id}</span></div>
                <div class="detail-row"><i class="fas fa-leaf"></i> <span><strong>Farmer:</strong> ${buyerOrder.farmerName}</span></div>
                <div class="detail-row"><i class="fas fa-box-open"></i> <span><strong>Item:</strong> ${buyerOrder.product_name||'N/A'} (${buyerOrder.quantity} units @ ${formatNGN(buyerOrder.price_per_unit)})</span></div>
                <div class="detail-row"><i class="fas fa-route"></i> <span><strong>Delivery City:</strong> ${buyerOrder.delivery_city}</span></div>
                <div class="detail-row"><i class="fas fa-map-marker-alt"></i> <span><strong>Delivery Address:</strong> ${buyerOrder.delivery_address||'N/A'}</span></div>
                <div class="detail-row"><i class="far fa-calendar-alt"></i> <span><strong>Target Date:</strong> ${buyerOrder.delivery_date}</span></div>
                <p class="text-success mt-3 mb-0"><i class="fas fa-truck-loading"></i> <strong>Est. Transport Cost:</strong> <span id="estTransportCostModal">${formatNGN(buyerOrder.transport_cost)}</span></p>
            `);
        }else{
            box.html('<div class="text-center p-3 text-warning"><i class="fas fa-exclamation-triangle"></i> No paid orders found.</div>');
        }
    }).catch(() => box.html('<div class="text-center p-3 text-danger">Failed to fetch order.</div>'));
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
        <div class="detail-row"><i class="fas fa-shield-alt"></i> <span><strong>Status:</strong> ${getStatusBadge(selectedTrans.is_verified)}</span></div>
        <div class="detail-row"><i class="fas fa-money-bill-wave"></i> <span><strong>Base Est. Price:</strong> ${formatNGN(selectedTrans.price)}</span></div>
        ${selectedTrans.license_number ? `<div class="detail-row"><i class="fas fa-id-card"></i> <span><strong>License:</strong> ${selectedTrans.license_number}</span></div>` : ''}
    `);

    fetchBuyerOrder();

    $('#payNowBtn, #payDeliveryBtn')
        .data('transporter-id', selectedTrans.id)
        .data('price', selectedTrans.price);

    $('#transModal').modal('show');
});

$('#payNowBtn').on('click',function(){
    const transId = $(this).data('transporter-id');
    const price   = $(this).data('price');
    const orderId = buyerOrder ? buyerOrder.order_id : 'N/A';
    alert(`Pay Now – Order ${orderId}, Transporter ${transId}, Amount ${formatNGN(price)}`);
});
$('#payDeliveryBtn').on('click',function(){
    const transId = $(this).data('transporter-id');
    const price   = $(this).data('price');
    const orderId = buyerOrder ? buyerOrder.order_id : 'N/A';
    alert(`Pay on Delivery – Order ${orderId}, Transporter ${transId}, Amount ${formatNGN(price)}`);
});

/* ==============================================================
   FILTER & PAGINATION LISTENERS
   ============================================================== */
$('#searchTrans').on('input',   () => { currentPage=1; fetchTransporters(1); });
$('#locationFilter').on('change',() => { currentPage=1; fetchTransporters(1); });
$('#statusFilter').on('change', () => { currentPage=1; fetchTransporters(1); });
$('#refreshBtn').on('click', e => { e.preventDefault(); currentPage=1; fetchTransporters(1); });

$(document).on('click','#transPager .page-link',function(e){
    e.preventDefault();
    const p = parseInt($(this).data('page'));
    if(!$(this).parent().hasClass('disabled') && p>=1 && p<=totalPages){
        currentPage = p; fetchTransporters(p);
    }
});

// **FIXED INITIAL CALL:** Fetch locations first, then fetch transporters.
$(document).ready(() => {
    fetchAllUniqueLocations(); 
    fetchTransporters(1);
});
</script>