<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FarmConnect – Fresh from Farmers</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Inter', sans-serif;
      background: #F1F8E9;
      color: #1B5E20;
      line-height: 1.5;
      margin: 0;
    }
    .container { max-width: 1400px; margin: auto; padding: 0 0.75rem; }

    /* === TOPBAR – FARM PRODUCE IMAGE + GLASS EFFECT === */
    .topbar {
      position: relative;
      height: 200px;
      background: linear-gradient(135deg, rgba(46,139,87,0.92), rgba(76,175,80,0.88)),
                  url('https://images.unsplash.com/photo-1500595046743-cd271d694e9d?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
      border-bottom: 4px solid #2E8B57;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: #fff;
      box-shadow: 0 8px 25px rgba(0,0,0,0.18);
    }
    .topbar::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(45deg, rgba(0,0,0,0.1), transparent 50%);
      backdrop-filter: blur(4px);
      z-index: 1;
    }
    .topbar-content {
      position: relative;
      z-index: 2;
      max-width: 900px;
      padding: 1.5rem;
    }
    .topbar h1 {
      font-size: 2.8rem;
      font-weight: 800;
      margin-bottom: 0.6rem;
      text-shadow: 0 3px 10px rgba(0,0,0,0.4);
      background: linear-gradient(90deg, #fff, #E8F5E8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: fadeInDown 1.2s ease-out;
    }
    .topbar p {
      font-size: 1.2rem;
      font-weight: 500;
      opacity: 0.98;
      text-shadow: 0 1px 4px rgba(0,0,0,0.3);
      animation: fadeInUp 1.2s ease-out 0.4s both;
    }
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-40px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* === MAIN CONTENT === */
    .main-content {
      padding: 2rem 0;
    }

    /* === FILTER BAR === */
    .filter-container {
      background: #fff; border-radius: 16px; padding: 1rem;
      margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(46,139,87,0.1);
      display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;
      border: 1px solid #E8F5E9;
    }
    .filter-group {
      flex: 1; min-width: 180px; position: relative;
    }
    .filter-group label {
      font-weight: 600; color: #1B5E20; font-size: 0.9rem; margin-bottom: 0.35rem; display: block;
    }
    .filter-group input, .filter-group select {
      width: 100%; padding: 0.6rem 0.9rem; border: 1px solid #A5D6A7;
      border-radius: 12px; font-size: 0.9rem; background: #E8F5E9;
      transition: all 0.3s ease;
    }
    .filter-group input:focus, .filter-group select:focus {
      border-color: #2E8B57; box-shadow: 0 0 0 3px rgba(46,139,87,0.12);
    }
    #produce-suggestions {
      position: absolute; top: 100%; left: 0; width: 100%; z-index: 10;
      background: #fff; border: 1px solid #ddd; border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1); max-height: 180px; overflow-y: auto;
      display: none; margin-top: 0.25rem;
    }
    #produce-suggestions li {
      padding: 0.6rem 0.9rem; cursor: pointer; font-size: 0.9rem;
      transition: background 0.2s;
    }
    #produce-suggestions li:hover { background: #E8F5E9; color: #2E8B57; }

    /* === GRID === */
    #produce-grid {
      display: grid;
      gap: 1rem;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      margin-top: 1rem;
    }

    /* === CARD === */
    .produce-item {
      background: #fff; border-radius: 14px; overflow: hidden;
      box-shadow: 0 3px 10px rgba(46,139,87,0.08);
      border: 1px solid #E8F5E9;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex; flex-direction: column;
      position: relative;
    }
    .produce-item:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 12px 25px rgba(46,139,87,0.18);
      border-color: #2E8B57;
      z-index: 5;
    }
    .produce-img {
      height: 140px; width: 100%; object-fit: cover;
      transition: transform 0.5s ease;
    }
    .produce-item:hover .produce-img { transform: scale(1.1); }

    .produce-body {
      padding: 0.9rem; display: flex; flex-direction: column;
      justify-content: space-between; flex-grow: 1; min-height: 130px;
    }
    .produce-item h4 {
      font-size: 1.05rem; font-weight: 700; color: #1B5E20;
      margin-bottom: 0.4rem; line-height: 1.3;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .location, .quantity {
      font-size: 0.82rem; color: #4E6B4E; margin: 0.25rem 0;
      display: flex; align-items: center; gap: 0.4rem;
    }
    .location i, .quantity i { color: #2E8B57; font-size: 0.95rem; }
    .star-rating {
      display: flex; gap: 0.2rem; font-size: 0.95rem; margin: 0.4rem 0;
    }
    .star-rating i { color: #E0E0E0; }
    .star-rating .fas, .star-rating .fa-star-half-alt { color: #FFB300; }

    .btn-view {
      background: linear-gradient(135deg, #2E8B57, #4CAF50);
      color: #fff; padding: 0.65rem 0.9rem;
      border-radius: 10px; font-weight: 600; font-size: 0.9rem;
      text-align: center; transition: all 0.3s ease;
      margin-top: auto; display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    }
    .btn-view:hover {
      background: linear-gradient(135deg, #276945, #388E3C);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(46,139,87,0.3);
    }

    .badge-remaining {
      position: absolute; top: 8px; right: 8px;
      background: #4CAF50; color: #fff; font-size: 0.7rem; font-weight: 700;
      padding: 0.25rem 0.5rem; border-radius: 8px; z-index: 2;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    /* === FOOTER – CLEAN & SOCIAL ONLY === */
    footer {
      background: #1B5E20;
      color: #C8E6C9;
      padding: 2rem 0;
      margin-top: 3rem;
      font-size: 0.95rem;
      text-align: center;
    }
    .footer-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
    }
    .social-links {
      display: flex; gap: 1.2rem;
    }
    .social-links a {
      width: 44px; height: 44px;
      background: rgba(255,255,255,0.12);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      color: #C8E6C9;
      font-size: 1.15rem;
      transition: all 0.3s ease;
      backdrop-filter: blur(5px);
    }
    .social-links a:hover {
      background: #4CAF50;
      color: #fff;
      transform: translateY(-4px) scale(1.1);
      box-shadow: 0 6px 15px rgba(76,175,80,0.3);
    }
    .footer-bottom {
      font-size: 0.9rem;
      color: #A5D6A7;
      margin-top: 0.5rem;
    }
    .footer-bottom a {
      color: #4CAF50;
      text-decoration: none;
      font-weight: 500;
    }
    .footer-bottom a:hover {
      text-decoration: underline;
    }

    /* === MODAL === */
    .modal {
      display: none; position: fixed; z-index: 1000; inset: 0;
      background: rgba(0,0,0,0.6); justify-content: center; align-items: center; padding: 1rem;
    }
    .modal-content {
      background: #fff; padding: 1.8rem; border-radius: 16px;
      max-width: 90%; text-align: center; box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }
    .modal-btn {
      margin-top: 1rem; padding: 0.7rem 2rem; background: #2E8B57;
      color: #fff; border: none; border-radius: 12px; cursor: pointer; font-weight: 600;
    }
    .modal-btn:hover { background: #276945; }

    /* === RESPONSIVE === */
    @media (max-width: 768px) {
      .topbar { height: 160px; }
      .topbar h1 { font-size: 2.2rem; }
      .topbar p { font-size: 1.05rem; }
      #produce-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.9rem; }
      .produce-img { height: 120px; }
      .filter-container { flex-direction: column; padding: 1rem; }
      .social-links a { width: 40px; height: 40px; font-size: 1rem; }
    }
    @media (max-width: 480px) {
      .topbar { height: 140px; }
      .topbar h1 { font-size: 1.9rem; }
      .topbar p { font-size: 0.95rem; }
      #produce-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
      .produce-img { height: 110px; }
    }
  </style>
</head>
<body>

  <!-- TOPBAR: FARM PRODUCE IMAGE -->
  <div class="topbar">
    <div class="topbar-content">
      <h1>FarmConnect</h1>
      <p>Fresh from Farmers – Direct to Your Table</p>
    </div>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="container">
      <!-- Filter -->
      <div class="filter-container">
        <div class="filter-group">
          <label for="produce-search">Search Produce</label>
          <input type="text" id="produce-search" placeholder="e.g. Tomatoes, Maize..." autocomplete="off">
          <div id="produce-suggestions"><ul></ul></div>
        </div>
        <div class="filter-group">
          <label for="location-select">Location</label>
          <select id="location-select">
            <option value="">All Locations</option>
          </select>
        </div>
      </div>

      <!-- Grid -->
      <div id="produce-grid"></div>
    </div>
  </div>

  <!-- FOOTER: SOCIAL + COPYRIGHT ONLY -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
        <div class="footer-bottom">
          © 2025 FarmConnect. All rights reserved. | 
          <a href="#">Privacy Policy</a> • <a href="#">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Login Modal -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <p>You need to log in to place an order.</p>
      <button class="modal-btn" onclick="redirectToLogin()">OK</button>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      loadLocations();
      loadProduceListing();

      function loadLocations() {
        $.get('get_produce_listing.php', { action: 'locations' }, function(data) {
          let opts = '<option value="">All Locations</option>';
          data.forEach(loc => opts += `<option value="${loc}">${loc}</option>`);
          $('#location-select').html(opts);
        }, 'json');
      }

      let debounce;
      $('#produce-search').on('keyup', function() {
        clearTimeout(debounce);
        const query = $(this).val().trim();

        if (query.length > 1) {
          debounce = setTimeout(() => {
            $.get('get_produce_listing.php', { action: 'suggestions', query: query }, function(data) {
              let html = '';
              data.forEach(s => { if (s) html += `<li>${s}</li>`; });
              $('#produce-suggestions ul').html(html);
              $('#produce-suggestions').toggle(!!html);
            }, 'json');
          }, 300);
        } else {
          $('#produce-suggestions').hide();
        }

        clearTimeout(debounce);
        debounce = setTimeout(loadProduceListing, 350);
      });

      $('#produce-suggestions').on('click', 'li', function() {
        $('#produce-search').val($(this).text());
        $('#produce-suggestions').hide();
        loadProduceListing();
      });

      $('#location-select').on('change', loadProduceListing);

      function loadProduceListing() {
        const search = $('#produce-search').val().trim();
        const location = $('#location-select').val();

        $.get('get_produce_listing.php', {
          action: 'listings',
          search: search,
          location: location
        }, function(response) {
          if (!response || response.error || response.length === 0) {
            $('#produce-grid').html('<div style="grid-column: 1/-1; text-align:center; padding:2rem; color:#666;">No produce found.</div>');
            return;
          }

          let html = '';
          response.forEach(p => {
            const rating = parseFloat(p.average_rating) || 0;
            const full = Math.floor(rating);
            const half = rating % 1 >= 0.25 && rating % 1 < 0.75;
            const empty = 5 - Math.ceil(rating);

            let stars = '<i class="fas fa-star"></i>'.repeat(full);
            if (half) stars += '<i class="fas fa-star-half-alt"></i>';
            stars += '<i class="far fa-star"></i>'.repeat(empty);

            const remaining = parseInt(p.remaining_quantity);
            const badge = remaining > 0 
              ? `<div class="badge-remaining">${remaining} left</div>` 
              : `<div class="badge-remaining" style="background:#e74c3c;">Sold Out</div>`;

            html += `
              <div class="produce-item">
                ${badge}
                <img src="${p.image_path}" alt="${p.produce}" class="produce-img">
                <div class="produce-body">
                  <div>
                    <h4>${p.produce}</h4>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> ${p.address}</p>
                    <p class="quantity"><i class="fas fa-box-open"></i> ${p.uploaded_quantity} units</p>
                    <div class="star-rating">${stars}</div>
                  </div>
                  <a href="#" class="btn-view" onclick="checkLogin('${p.prod_id}')">
                    <i class="fas fa-cart-plus"></i> Order
                  </a>
                </div>
              </div>`;
          });
          $('#produce-grid').html(html);
        }, 'json').fail(() => {
          $('#produce-grid').html('<div style="grid-column: 1/-1; text-align:center; padding:2rem; color:#e74c3c;">Error loading data.</div>');
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
</body>
</html>