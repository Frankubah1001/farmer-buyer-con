<?php
// reports.php - Reports Management Module
$active = 'reports';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ==== ALL YOUR ORIGINAL STYLES (unchanged) ==== */
        :root{--farm-green:#1B5E20;--deep-green:#2E7D32;--harvest-gold:#FF8F00;--soil-brown:#4E342E;--rich-brown:#5D4037;--light-field:#F1F8E9;--cream:#FFFBEA;--shadow:rgba(27,94,32,.3);--glow:0 0 20px rgba(255,143,0,.6);--spacing-sm:.8rem;--spacing-md:1.4rem;--spacing-lg:2rem;--radius:16px;--transition:all .3s ease}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Nunito',sans-serif;background:linear-gradient(135deg,#E8F5E9 0%,#C8E6C9 100%);color:var(--soil-brown);min-height:100vh;line-height:1.6;position:relative}
        body::before{content:'';position:absolute;inset:0;background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><path fill="%23C8E6C9" opacity="0.2" d="M0 60 Q30 40,60 60 T120 60 V120 H0 Z"/></svg>') repeat;opacity:.15;z-index:-1}
        h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6{font-weight:700;color:var(--soil-brown)}
        .text-bold{font-weight:600}.text-muted{color:#6c757d!important}.text-success{color:var(--deep-green)!important}.text-warning{color:var(--harvest-gold)!important}.text-danger{color:#c62828!important}
        .admin-card{background:#fff;border-radius:var(--radius);overflow:hidden;box-shadow:0 15px 35px var(--shadow);transition:var(--transition);margin-bottom:var(--spacing-lg)}
        .admin-card:hover{transform:translateY(-10px);box-shadow:0 25px 50px rgba(27,94,32,.4)}
        .card-header{background:linear-gradient(135deg,var(--farm-green),var(--deep-green));padding:1.8rem 1.5rem;color:#fff;text-align:center;position:relative;border-bottom:4px solid var(--harvest-gold)}
        .card-header::after{content:'';position:absolute;bottom:0;left:0;right:0;height:70px;background:linear-gradient(transparent,#fff);opacity:.7}
        .logo{font-size:3rem;color:var(--harvest-gold);animation:float 3s ease-in-out infinite;text-shadow:0 2px 4px rgba(0,0,0,.3)}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        .card-header h6{font-weight:700;font-size:1.3rem;margin-top:.6rem;letter-spacing:.5px;position:relative;z-index:1}
        .card-body{padding:2rem 1.8rem}
        .stats-row{margin-top:1.5rem;margin-bottom:2rem}
        .stat-card{border-radius:var(--radius);overflow:hidden;box-shadow:0 8px 20px rgba(0,0,0,.12);transition:var(--transition);text-align:center;color:#fff;height:100%}
        .stat-card:hover{transform:translateY(-6px)}
        .stat-card .card-body{padding:1.8rem 1rem}
        .stat-card h5{font-size:2.4rem;font-weight:700;margin:0;text-shadow:0 2px 4px rgba(0,0,0,.2)}
        .stat-card small{font-size:1rem;font-weight:600;opacity:.95;margin-top:.4rem;display:block}
        .filter-card{background:var(--cream);border:2px solid #e0e0e0;border-radius:var(--radius);padding:1.5rem;margin-bottom:var(--spacing-lg)}
        .filter-group{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1rem}
        .filter-group select,.filter-group input{height:50px;padding:0 1rem;border:2px solid #d0d0d0;border-radius:12px;background:#fff;font-size:.95rem;transition:var(--transition);color:#333}
        .filter-group select:focus,.filter896-group input:focus{border-color:var(--deep-green);box-shadow:var(--glow);background:#fff;outline:none}
        .btn-filter{background:var(--deep-green);color:#fff;border:none;border-radius:12px;padding:0 1.8rem;height:50px;font-weight:600;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;justify-content:center;gap:.5rem}
        .btn-filter:hover{background:#1B5E20;transform:translateY(-2px);box-shadow:0 6px 15px rgba(27,94,32,.4)}
        .btn-clear{background:#6c757d}
        .btn-clear:hover{background:#5a6268}
        .table-container{overflow-x:auto;border-radius:var(--radius);box-shadow:0 8px 25px rgba(0,0,0,.1)}
        .table{width:100%;border-collapse:collapse;font-size:.95rem;margin:0}
        .table th{background:var(--light-field);font-weight:700;color:var(--soil-brown);padding:1.2rem 1rem;text-align:left;border-bottom:3px solid var(--deep-green);font-size:.95rem;text-transform:uppercase;letter-spacing:.5px}
        .table td{padding:1.1rem 1rem;border-bottom:1px solid #e0e0e0;vertical-align:middle;color:#333}
        .table-hover tbody tr:hover{background:#f8fff8;transform:translateY(-2px);box-shadow:0 6px 12px rgba(0,0,0,.08);transition:var(--transition)}
        .badge{padding:.5rem 1rem;font-size:.8rem;font-weight:600;border-radius:50px;text-transform:uppercase;letter-spacing:.5px}
        .badge-pending{background:#fff8e1;color:#e65100;border:1px solid #ffcc80}
        .badge-resolved{background:#e8f5e9;color:var(--deep-green);border:1px solid #81c784}
        .badge-review{background:#e3f2fd;color:#1976d2;border:1px solid #64b5f6}
        .badge-high{background:#ffebee;color:#c62828;border:1px solid #e57373}
        .badge-medium{background:#fff3e0;color:var(--harvest-gold);border:1px solid #ffb74d}
        .badge-low{background:#f1f8e9;color:var(--deep-green);border:1px solid #a5d6a7}

        /* ==== NEW ICON-ONLY ACTION BUTTONS ==== */
        .action-icons{display:flex;gap:.4rem;align-items:center}
        .action-icons button{background:none;border:none;cursor:pointer;font-size:1.1rem;padding:.35rem;transition:var(--transition)}
        .action-icons .view{color:#0D47A1}
        .action-icons .view:hover{color:#4FC3F7}
        .action-icons .resolve{color:var(--deep-green)}
        .action-icons .resolve:hover{color:#1B5E20}
        .action-icons .delete{color:#c62828}
        .action-icons .delete:hover{color:#e53935}

        /* ==== MODAL ==== */
        .modal{display:none;position:fixed;z-index:1050;inset:0;background:rgba(0,0,0,.6);animation:fadeIn .3s}
        .modal-content{background:#fff;margin:4% auto;border-radius:var(--radius);width:90%;max-width:700px;max-height:85vh;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.3)}
        .modal-header{padding:1.8rem;background:var(--light-field);border-bottom:2px solid var(--deep-green);display:flex;justify-content:space-between;align-items:center}
        .modal-header h5{margin:0;font-weight:700;color:var(--soil-brown)}
        .close{font-size:2.2rem;cursor:pointer;color:#999;transition:var(--transition)}
        .close:hover{color:#c62828}
        .modal-body{padding:2rem;font-size:1rem;line-height:1.7;color:#333}
        .modal-footer{padding:1.5rem;background:#f8f9fa;border-top:1px solid #e0e0e0;text-align:right}
        .modal-footer .btn{padding:.7rem 1.5rem;border-radius:10px;font-weight:600}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}

        /* ==== RESPONSIVE ==== */
        @media(max-width:992px){.filter-group{grid-template-columns:1fr 1fr}.stat-card h5{font-size:2rem}}
        @media(max-width:768px){.filter-group{grid-template-columns:1fr}.table th,.table td{padding:.8rem .6rem;font-size:.85rem}}
        @media(max-width:576px){.card-body{padding:1.5rem 1.2rem}.modal-content{margin:10% auto;width:95%}.stat-card h5{font-size:1.8rem}}
    </style>
</head>
<?php include 'header.php'; ?>
<body>
    <?php include 'sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Stats Row -->
            <div class="row stats-row m-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:var(--deep-green);">
                        <div class="card-body"><h5 id="totalReports">12</h5><small>Total Reports</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:#2e7d32;">
                        <div class="card-body"><h5 id="resolved">8</h5><small>Resolved</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:var(--harvest-gold);">
                        <div class="card-body"><h5 id="pending">3</h5><small>Pending</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:#c62828;">
                        <div class="card-body"><h5 id="highUrgency">1</h5><small>High Urgency</small></div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card m-4">
                <h6 class="mb-3 text-bold"><i class="fas fa-filter me-2"></i> Filter Reports</h6>
                <div class="filter-group">
                    <select id="filterStatus"><option value="">All Status</option><option value="pending">Pending</option><option value="in-review">In Review</option><option value="resolved">Resolved</option></select>
                    <select id="filterUrgency"><option value="">All Urgency</option><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option></select>
                    <select id="filterFarmer"><option value="">All Farmers</option><option value="1">Franklin Nwawuba</option><option value="2">Jacinta Ubah</option><option value="3">Steve Gbenga</option><option value="4">Victor Odogwu</option><option value="5">Goke Ibile</option></select>
                    <input type="date" id="filterDate">
                    <button class="btn-filter" onclick="applyFilters()"><i class="fas fa-search"></i> Apply</button>
                    <button class="btn-filter btn-clear" onclick="clearFilters()"><i class="fas fa-times"></i> Clear</button>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="admin-card m-4">
                <div class="card-header">
                    <i class="fas fa-list logo mt-3"></i>
                    <h6>All Buyer Reports</h6>
                </div>
                <div class="card-body p-0 m-3">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th><th>Buyer</th><th>Farmer</th><th>Order #</th><th>Issue</th><th>Description</th><th>Date</th><th>Urgency</th><th>Status</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody">
                                <tr>
                                    <td><strong>#REP001</strong></td>
                                    <td>John Doe</td>
                                    <td>Steve Gbenga</td>
                                    <td>#16</td>
                                    <td>Wrong Quantity</td>
                                    <td>Received 20kg instead of 50kg of rice...</td>
                                    <td>Nov 05</td>
                                    <td><span class="badge badge-high text-danger">High</span></td>
                                    <td><span class="badge badge-pending">Pending</span></td>
                                    <td class="action-icons">
                                        <button class="view" title="View" onclick="openModal('#REP001')"><i class="fas fa-eye"></i></button>
                                        <button class="resolve" title="Resolve" onclick="resolveReport('#REP001')"><i class="fas fa-check"></i></button>
                                        <button class="delete" title="Delete" onclick="deleteReport('#REP001')"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>#REP002</strong></td>
                                    <td>Jane Smith</td>
                                    <td>Jacinta Ubah</td>
                                    <td>#12</td>
                                    <td>Poor Quality</td>
                                    <td>Tomatoes arrived rotten and unusable...</td>
                                    <td>Nov 03</td>
                                    <td><span class="badge badge-medium text-black">Medium</span></td>
                                    <td><span class="badge badge-resolved text-success">Resolved</span></td>
                                    <td class="action-icons">
                                        <button class="view" title="View" onclick="openModal('#REP002')"><i class="fas fa-eye"></i></button>
                                    </td>
                                </tr>
                                <!-- more rows … -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- ==================== VIEW MODAL ==================== -->
    <div id="reportModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle"></h5>
                <span class="close" onclick="closeModal()">x</span>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button id="modalResolveBtn" class="btn btn-success" style="display:none;" onclick="confirmResolve()">Resolve</button>
            </div>
        </div>
    </div>

    <script>
        /* ---------- FILTERS ---------- */
        function applyFilters() { console.log('Filtering...'); }
        function clearFilters() {
            ['filterStatus','filterUrgency','filterFarmer','filterDate'].forEach(id => 
                document.getElementById(id).value = ''
            );
        }

        /* ---------- MODAL ---------- */
        function openModal(id) {
            const data = {
                '#REP001': {
                    buyer: 'John Doe', farmer: 'Steve Gbenga', order: '#16',
                    issue: 'Wrong Quantity',
                    description: 'I ordered 50kg of rice but received only 20kg. The rest was missing from the delivery. I have photos and delivery receipt as evidence.',
                    evidence: '<a href="#" class="text-success">view_image_1.jpg</a>, <a href="#" class="text-success">receipt.pdf</a>',
                    date: 'November 5, 2025', urgency: 'High', status: 'Pending'
                },
                '#REP002': {
                    buyer: 'Jane Smith', farmer: 'Jacinta Ubah', order: '#12',
                    issue: 'Poor Quality',
                    description: 'Tomatoes arrived rotten and unusable...',
                    evidence: '', date: 'November 3, 2025', urgency: 'Medium', status: 'Resolved'
                }
                // add more as needed
            };

            const rec = data[id] || {};
            document.getElementById('modalTitle').textContent = `${id} - Full Details`;
            document.getElementById('modalBody').innerHTML = `
                <p><strong>Buyer:</strong> ${rec.buyer||''}</p>
                <p><strong>Farmer:</strong> ${rec.farmer||''}</p>
                <p><strong>Order #:</strong> ${rec.order||''}</p>
                <p><strong>Issue:</strong> ${rec.issue||''}</p>
                <p><strong>Description:</strong> ${rec.description||''}</p>
                ${rec.evidence?`<p><strong>Evidence:</strong> ${rec.evidence}</p>`:''}
                <p><strong>Date Reported:</strong> ${rec.date||''}</p>
                <p><strong>Urgency:</strong> <span class="badge badge-${rec.urgency?.toLowerCase()}">${rec.urgency||''}</span></p>
                <p><strong>Status:</strong> <span class="badge badge-${rec.status?.toLowerCase().replace(' ','-')}">${rec.status||''}</span></p>
            `;

            // Show Resolve button only when status !== Resolved
            const resolveBtn = document.getElementById('modalResolveBtn');
            resolveBtn.style.display = (rec.status === 'Resolved') ? 'none' : 'inline-block';

            document.getElementById('reportModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('reportModal').style.display = 'none';
        }

        /* ---------- RESOLVE ---------- */
        function resolveReport(id) {
            Swal.fire({
                title: 'Resolve Report?',
                text: `Mark ${id} as resolved?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2E7D32',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Resolve'
            }).then(res => {
                if (res.isConfirmed) {
                    const row = document.querySelector(`button[onclick="resolveReport('${id}')"]`).closest('tr');
                    row.cells[8].innerHTML = '<span class="badge badge-resolved">Resolved</span>';
                    updateStats();
                    Swal.fire('Resolved!', `${id} has been marked as resolved.`, 'success');
                }
            });
        }

        function confirmResolve() {
            const id = document.getElementById('modalTitle').textContent.split(' ')[0];
            resolveReport(id);
            closeModal();
        }

        /* ---------- DELETE ---------- */
        function deleteReport(id) {
            Swal.fire({
                title: 'Delete Report?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete'
            }).then(res => {
                if (res.isConfirmed) {
                    document.querySelector(`button[onclick="deleteReport('${id}')"]`).closest('tr').remove();
                    updateStats();
                    Swal.fire('Deleted!', `${id} has been removed.`, 'success');
                }
            });
        }

        /* ---------- STATS ---------- */
        function updateStats() {
            const rows = document.querySelectorAll('#reportsTableBody tr');
            const total = rows.length;
            const resolved = [...rows].filter(r => r.cells[8].querySelector('.badge-resolved')).length;
            const pending = [...rows].filter(r => r.cells[8].querySelector('.badge-pending')).length;
            const high = [...rows].filter(r => r.cells[7].querySelector('.badge-high')).length;
            document.getElementById('totalReports').textContent = total;
            document.getElementById('resolved').textContent = resolved;
            document.getElementById('pending').textContent = pending;
            document.getElementById('highUrgency').textContent = high;
        }

        /* ---------- GLOBAL CLOSE ON BACKDROP ---------- */
        window.onclick = e => {
            const modal = document.getElementById('reportModal');
            if (e.target === modal) closeModal();
        };

        /* ---------- INITIALISE ---------- */
        updateStats();
    </script>
</body>
</html>