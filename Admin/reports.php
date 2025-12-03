<?php
// reports.php - Reports Management Module
require_once 'session_check.php';
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
        .filter-group select:focus,.filter-group input:focus{border-color:var(--deep-green);box-shadow:var(--glow);background:#fff;outline:none}
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
        .action-icons{display:flex;gap:.4rem;align-items:center}
        .action-icons button{background:none;border:none;cursor:pointer;font-size:1.1rem;padding:.35rem;transition:var(--transition)}
        .action-icons .view{color:#0D47A1}
        .action-icons .view:hover{color:#4FC3F7}
        .action-icons .resolve{color:var(--deep-green)}
        .action-icons .resolve:hover{color:#1B5E20}
        .action-icons .delete{color:#c62828}
        .action-icons .delete:hover{color:#e53935}
        .modal{display:none;position:fixed;z-index:1050;inset:0;background:rgba(0,0,0,.6);animation:fadeIn .3s}
        .modal-content{background:#fff;margin:4% auto;border-radius:var(--radius);width:90%;max-width:700px;max-height:85vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,.3)}
        .modal-header{padding:1.8rem;background:var(--light-field);border-bottom:2px solid var(--deep-green);display:flex;justify-content:space-between;align-items:center}
        .modal-header h5{margin:0;font-weight:700;color:var(--soil-brown)}
        .close{font-size:2.2rem;cursor:pointer;color:#999;transition:var(--transition)}
        .close:hover{color:#c62828}
        .modal-body{padding:2rem;font-size:1rem;line-height:1.7;color:#333}
        .modal-footer{padding:1.5rem;background:#f8f9fa;border-top:1px solid #e0e0e0;text-align:right}
        .modal-footer .btn{padding:.7rem 1.5rem;border-radius:10px;font-weight:600}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @media(max-width:992px){.filter-group{grid-template-columns:1fr 1fr}.stat-card h5{font-size:2rem}}
        @media(max-width:768px){.filter-group{grid-template-columns:1fr}.table th,.table td{padding:.8rem .6rem;font-size:.85rem}}
        @media(max-width:576px){.card-body{padding:1.5rem 1.2rem}.modal-content{margin:10% auto;width:95%}.stat-card h5{font-size:1.8rem}}
        .pagination{display:flex;justify-content:center;gap:0.5rem;margin-top:1.5rem}
        .pagination button{padding:0.5rem 1rem;border:1px solid var(--deep-green);background:white;color:var(--deep-green);border-radius:8px;cursor:pointer;transition:var(--transition)}
        .pagination button:hover:not(:disabled){background:var(--deep-green);color:white}
        .pagination button.active{background:var(--deep-green);color:white}
        .pagination button:disabled{opacity:0.5;cursor:not-allowed}
        .pagination {
    display: flex;
    justify-content: center;
    gap: 0.3rem;
    margin-top: 2rem;
    padding: 1rem;
    flex-wrap: wrap;
}

.pagination button {
    padding: 0.6rem 1rem;
    border: 2px solid var(--deep-green);
    background: white;
    color: var(--deep-green);
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    font-weight: 600;
    min-width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
}

.pagination button:hover:not(:disabled) {
    background: var(--deep-green);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

.pagination button.active {
    background: var(--deep-green);
    color: white;
    font-weight: 700;
    border-color: var(--farm-green);
}

.pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f5f5f5;
    color: #999;
    border-color: #ddd;
}

.pagination span {
    padding: 0.6rem 0.5rem;
    color: #666;
    font-weight: 600;
}
    </style>
</head>
<?php include 'header.php'; ?>
<body>
    <?php include 'sidebar.php'; ?>
<main class="main-content">
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Stats Row -->
            <div class="row stats-row m-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:var(--deep-green);">
                        <div class="card-body"><h5 id="totalReports">0</h5><small>Total Reports</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:#2e7d32;">
                        <div class="card-body"><h5 id="resolved">0</h5><small>Resolved</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:var(--harvest-gold);">
                        <div class="card-body"><h5 id="pending">0</h5><small>Pending</small></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card" style="background:#c62828;">
                        <div class="card-body"><h5 id="highUrgency">0</h5><small>High Urgency</small></div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card m-4">
                <h6 class="mb-3 text-bold">Filter Reports</h6>
                <div class="filter-group">
                    <select id="filterStatus">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in-review">In Review</option>
                        <option value="resolved">Resolved</option>
                    </select>
                    <select id="filterUrgency">
                        <option value="">All Urgency</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    <select id="filterFarmer">
                        <option value="">All Farmers</option>
                    </select>
                    <input type="date" id="filterDate">
                    <input type="text" id="filterSearch" placeholder="Search...">
                    <button class="btn-filter" onclick="applyFilters()">Apply</button>
                    <button class="btn-filter btn-clear" onclick="clearFilters()">Clear</button>
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
                                <tr><td colspan="10" class="text-center">Loading reports...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination" id="paginationContainer"></div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include 'footer.php'; ?>

<!-- View Modal -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 id="modalTitle"></h5>
            <span class="close" onclick="closeModal()">×</span>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
            <button id="modalResolveBtn" class="btn btn-success" style="display:none;" onclick="showResolveForm()">
                Resolve Report
            </button>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div id="resolveModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Resolve Report</h5>
            <span class="close" onclick="closeResolveModal()">×</span>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="resolutionAction" class="form-label">Resolution Action</label>
                <select class="form-control" id="resolutionAction">
                    <option value="">Select Action</option>
                    <option value="Refund Issued">Refund Issued</option>
                    <option value="Replacement Sent">Replacement Sent</option>
                    <option value="Farmer Warned">Farmer Warned</option>
                    <option value="Farmer Suspended">Farmer Suspended</option>
                    <option value="No Action Required">No Action Required</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="resolutionNotes" class="form-label">Resolution Notes</label>
                <textarea class="form-control" id="resolutionNotes" rows="4" placeholder="Enter resolution details..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeResolveModal()">Cancel</button>
            <button class="btn btn-success" onclick="confirmResolve()">Confirm Resolve</button>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let currentReportId = null;
    const itemsPerPage = 5;

    document.addEventListener('DOMContentLoaded', function() {
        loadStats();
        loadFarmers();
        loadReports();
    });

    function loadStats() {
        fetch('api/reports_api.php?action=get_stats')
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    document.getElementById('totalReports').textContent = d.data.total;
                    document.getElementById('resolved').textContent = d.data.resolved;
                    document.getElementById('pending').textContent = d.data.pending;
                    document.getElementById('highUrgency').textContent = d.data.high_urgency;
                }
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    function loadFarmers() {
        fetch('api/reports_api.php?action=get_farmers')
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    const select = document.getElementById('filterFarmer');
                    select.innerHTML = '<option value="">All Farmers</option>';
                    d.data.forEach(f => {
                        const opt = document.createElement('option');
                        opt.value = f.cbn_user_id;
                        opt.textContent = f.farmer_name;
                        select.appendChild(opt);
                    });
                }
            })
            .catch(error => console.error('Error loading farmers:', error));
    }

    function loadReports(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({
            action: 'get_reports',
            page: page,
            limit: itemsPerPage,
            status: document.getElementById('filterStatus').value,
            urgency: document.getElementById('filterUrgency').value,
            farmer_id: document.getElementById('filterFarmer').value,
            date: document.getElementById('filterDate').value,
            search: document.getElementById('filterSearch').value
        });

        fetch(`api/reports_api.php?${params}`)
            .then(r => r.json())
            .then(d => {
                console.log('Reports data:', d); // Debug log
                if (d.success) {
                    displayReports(d.data.reports);
                    displayPagination(d.data.pagination);
                } else {
                    document.getElementById('reportsTableBody').innerHTML = 
                        `<tr><td colspan="10" class="text-center text-danger">${d.message || 'Error loading reports'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error loading reports:', error);
                document.getElementById('reportsTableBody').innerHTML = 
                    `<tr><td colspan="10" class="text-center text-danger">Error loading reports. Please check console.</td></tr>`;
            });
    }

    function displayReports(reports) {
        const tbody = document.getElementById('reportsTableBody');
        if (!reports || reports.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center">No reports found</td></tr>';
            return;
        }

        tbody.innerHTML = reports.map(r => {
            // Fix urgency class - ensure it's lowercase
            const urgencyClass = `badge-${r.urgency_level ? r.urgency_level.toLowerCase() : 'medium'}`;
            const statusClass = r.status === 'resolved' ? 'badge-resolved' : 
                              r.status === 'in-review' ? 'badge-review' : 'badge-pending';
            const desc = r.description && r.description.length > 50 ? 
                        r.description.substring(0,50) + '...' : (r.description || 'No description');
            const date = r.created_at ? new Date(r.created_at).toLocaleDateString('en-GB', {
                day:'2-digit', month:'short', year:'numeric'
            }) : 'N/A';

            return `
                <tr>
                    <td><strong>#REP${r.report_id}</strong></td>
                    <td>${r.buyer_name || 'N/A'}</td>
                    <td>${r.farmer_name || 'N/A'}</td>
                    <td>${r.order_number || 'N/A'}</td>
                    <td>${r.issue_type || 'N/A'}</td>
                    <td>${desc}</td>
                    <td>${date}</td>
                    <td><span class="badge ${urgencyClass}">${r.urgency_level || 'medium'}</span></td>
                    <td><span class="badge ${statusClass}">${r.status || 'pending'}</span></td>
                    <td class="action-icons">
                        <button class="view" title="View" onclick="openModal(${r.report_id})">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${r.status !== 'resolved' ? `
                            <button class="resolve" title="Resolve" onclick="openResolveModal(${r.report_id})">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        ` : ''}
                        <button class="delete" title="Delete" onclick="deleteReport(${r.report_id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
        }).join('');
    }

    function displayPagination(pagination) {
        const container = document.getElementById('paginationContainer');
        if (!pagination || pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';
        const maxVisible = 5;
        const current = pagination.current_page;
        const total = pagination.total_pages;
        
        // Previous button
        html += `<button onclick="loadReports(${current - 1})" ${current === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i> Previous
                 </button>`;
        
        // Page numbers
        let startPage = Math.max(1, current - Math.floor(maxVisible / 2));
        let endPage = Math.min(total, startPage + maxVisible - 1);
        
        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            html += `<button onclick="loadReports(${i})" class="${i === current ? 'active' : ''}">${i}</button>`;
        }
        
        // Next button
        html += `<button onclick="loadReports(${current + 1})" ${current === total ? 'disabled' : ''}>
                    Next <i class="fas fa-chevron-right"></i>
                 </button>`;
        
        container.innerHTML = html;
    }

    function applyFilters() { 
        loadReports(1); 
    }
    
    function clearFilters() {
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterUrgency').value = '';
        document.getElementById('filterFarmer').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterSearch').value = '';
        loadReports(1);
    }

    function openModal(reportId) {
        console.log('Opening modal for report:', reportId);
        fetch(`api/reports_api.php?action=get_report_details&report_id=${reportId}`)
            .then(r => r.json())
            .then(d => {
                console.log('Report details:', d);
                if (d.success) {
                    const r = d.data;
                    currentReportId = reportId;
                    document.getElementById('modalTitle').textContent = `Report #REP${r.report_id} - Full Details`;

                    let evidence = '';
                    if (r.evidence) {
                        const files = r.evidence.split(',').map(f => f.trim()).filter(f => f);
                        evidence = files.map(f => 
                            `<a href="../uploads/reports/${f}" target="_blank" class="text-success d-block">${f}</a>`
                        ).join('');
                    }

                    document.getElementById('modalBody').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Report ID:</strong> #REP${r.report_id}</p>
                                <p><strong>Buyer:</strong> ${r.buyer_name || 'N/A'} (${r.buyer_email || 'N/A'})</p>
                                <p><strong>Buyer Phone:</strong> ${r.buyer_phone || 'N/A'}</p>
                                <p><strong>Farmer:</strong> ${r.farmer_name || 'N/A'} (${r.farmer_email || 'N/A'})</p>
                                <p><strong>Farmer Phone:</strong> ${r.farmer_phone || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Order #:</strong> ${r.order_number || 'N/A'}</p>
                                <p><strong>Produce:</strong> ${r.produce_name || 'N/A'}</p>
                                <p><strong>Issue Type:</strong> ${r.issue_type || 'N/A'}</p>
                                <p><strong>Reason:</strong> ${r.reason || 'N/A'}</p>
                                <p><strong>Status:</strong> <span class="badge ${r.status === 'resolved' ? 'badge-resolved' : r.status === 'in-review' ? 'badge-review' : 'badge-pending'}">${r.status}</span></p>
                                <p><strong>Urgency:</strong> <span class="badge badge-${r.urgency_level || 'medium'}">${r.urgency_level || 'medium'}</span></p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>Description:</strong></p>
                                <div class="p-3 bg-light rounded">${r.description || 'No description provided'}</div>
                            </div>
                        </div>
                        ${evidence ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <p><strong>Evidence:</strong></p>
                                <div class="p-3 bg-light rounded">${evidence}</div>
                            </div>
                        </div>` : ''}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Date Reported:</strong> ${new Date(r.created_at).toLocaleString('en-GB') || 'N/A'}</p>
                                ${r.updated_at ? `<p><strong>Last Updated:</strong> ${new Date(r.updated_at).toLocaleString('en-GB')}</p>` : ''}
                            </div>
                            <div class="col-md-6">
                                ${r.resolution_action ? `<p><strong>Resolution Action:</strong> ${r.resolution_action}</p>` : ''}
                                ${r.resolution_notes ? `<p><strong>Resolution Notes:</strong> ${r.resolution_notes}</p>` : ''}
                            </div>
                        </div>
                    `;

                    // Show resolve button only if report is not already resolved
                    const resolveBtn = document.getElementById('modalResolveBtn');
                    if (r.status === 'resolved') {
                        resolveBtn.style.display = 'none';
                    } else {
                        resolveBtn.style.display = 'inline-block';
                        resolveBtn.onclick = function() {
                            showResolveForm();
                        };
                    }
                    
                    document.getElementById('reportModal').style.display = 'block';
                } else {
                    Swal.fire('Error', d.message || 'Failed to load report details', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading report details:', error);
                Swal.fire('Error', 'Failed to load report details. Please check console.', 'error');
            });
    }

    function closeModal() { 
        document.getElementById('reportModal').style.display = 'none'; 
    }

    function openResolveModal(reportId) {
        console.log('Opening resolve modal for report:', reportId);
        currentReportId = reportId;
        document.getElementById('resolutionAction').value = '';
        document.getElementById('resolutionNotes').value = '';
        document.getElementById('resolveModal').style.display = 'block';
    }

    function showResolveForm() {
        closeModal();
        if (currentReportId) {
            openResolveModal(currentReportId);
        }
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').style.display = 'none';
    }

    async function confirmResolve() {
        const action = document.getElementById('resolutionAction').value.trim();
        const notes = document.getElementById('resolutionNotes').value.trim();

        if (!action) {
            Swal.fire('Error', 'Please select a resolution action', 'warning');
            return;
        }
        if (action === 'Other' && !notes) {
            Swal.fire('Required', 'Notes are required when selecting "Other"', 'warning');
            return;
        }

        const swalResult = await Swal.fire({
            title: 'Confirm Resolution',
            text: 'Are you sure you want to mark this report as resolved?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2E7D32',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Resolve',
            cancelButtonText: 'Cancel'
        });

        if (!swalResult.isConfirmed) return;

        try {
            console.log('Resolving report:', currentReportId, 'Action:', action);
            const res = await fetch('api/reports_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'resolve_report',
                    report_id: currentReportId,
                    resolution_action: action,
                    resolution_notes: notes
                })
            });
            const data = await res.json();
            console.log('Resolve response:', data);

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Resolved!',
                    text: data.message || 'Report resolved successfully',
                    timer: 3000,
                    showConfirmButton: false
                });
                closeResolveModal();
                loadReports(currentPage);
                loadStats(); // Refresh stats
            } else {
                Swal.fire('Error', data.message || 'Failed to resolve report', 'error');
            }
        } catch (err) {
            console.error('Resolve error:', err);
            Swal.fire('Error', 'Network error occurred. Please try again.', 'error');
        }
    }

    async function deleteReport(reportId) {
        const result = await Swal.fire({
            title: 'Delete Report?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c62828',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            try {
                const res = await fetch('api/reports_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'delete_report', 
                        report_id: reportId 
                    })
                });
                const data = await res.json();
                console.log('Delete response:', data);

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Report deleted successfully',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    // Reload the reports table to reflect the deletion
                    loadReports(currentPage);
                    loadStats(); // Refresh stats
                } else {
                    Swal.fire('Error', data.message || 'Failed to delete report', 'error');
                }
            } catch (err) {
                console.error('Delete error:', err);
                Swal.fire('Error', 'Network error occurred. Please try again.', 'error');
            }
        }
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const reportModal = document.getElementById('reportModal');
        const resolveModal = document.getElementById('resolveModal');
        
        if (event.target === reportModal) {
            closeModal();
        }
        if (event.target === resolveModal) {
            closeResolveModal();
        }
    };

    // Close modals with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
            closeResolveModal();
        }
    });
</script>
</body>
</html>