<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FarmConnect – Fresh from Farmers</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"/>

  <style>
    /* ==== Reset & Base ==== */
    *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Inter', sans-serif;
      background:#F1F8E9;
      color:#1B5E20;
      line-height:1.6;
    }
    a { text-decoration:none; color:inherit; }

    .container { max-width:1200px; margin:auto; padding:0 1rem; }

    /* ==== HEADER – FULLY MOBILE FIXED ==== */
    .header {
      position: sticky; top: 0; z-index: 100;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(46, 139, 87, 0.1);
    }

    .header-backdrop {
      position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(76, 175, 80, 0.03), rgba(46, 139, 87, 0.02));
      pointer-events: none;
    }

    .header-inner {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      padding: 0.75rem 0;
      gap: 0.75rem;
      position: relative;
    }

    .logo {
      font-weight: 800;
      font-size: 1.5rem;
      color: #2E8B57;
      display: flex;
      flex-direction: column;
      line-height: 1.2;
      flex-shrink: 0;
    }
    .logo small {
      font-size: 0.65rem;
      opacity: 0.8;
      font-weight: 500;
      white-space: nowrap;
    }

    .search-container {
      flex: 1;
      min-width: 200px;
      max-width: 100%;
    }

    .search-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(46, 139, 87, 0.25);
      border-radius: 50px;
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
      min-height: 44px; /* Touch-friendly */
    }

    .search-wrapper:focus-within {
      border-color: #2E8B57;
      box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.15);
      background: #fff;
    }

    .search-icon {
      color: #2E8B57;
      width: 18px;
      height: 18px;
      margin-right: 0.75rem;
      flex-shrink: 0;
    }

    .search-wrapper input {
      flex: 1;
      border: none;
      outline: none;
      font-size: 0.95rem;
      background: transparent;
      color: #1B5E20;
      min-width: 0; /* Allow shrinking */
    }

    .mic-icon {
      color: #2E8B57;
      width: 16px;
      height: 16px;
      margin-left: 0.5rem;
      opacity: 0.7;
      cursor: pointer;
      flex-shrink: 0;
    }
    .mic-icon:hover { opacity: 1; }

    .header-actions {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }

    .icon-btn {
      position: relative;
      background: none;
      border: none;
      color: #2E8B57;
      cursor: pointer;
      padding: 0.6rem;
      border-radius: 50%;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .icon-btn:hover {
      background: rgba(46, 139, 87, 0.12);
      transform: scale(1.08);
    }

    .icon-btn svg {
      width: 20px;
      height: 20px;
    }

    .cart-badge {
      position: absolute;
      top: 0.1rem;
      right: 0.1rem;
      background: #E8F5E8;
      color: #2E8B57;
      border: 1.5px solid #2E8B57;
      font-size: 0.65rem;
      font-weight: 700;
      min-width: 16px;
      height: 16px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .tooltip {
      position: absolute;
      bottom: -28px;
      left: 50%;
      transform: translateX(-50%);
      background: #1B5E20;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
      z-index: 10;
    }

    .icon-btn:hover .tooltip { opacity: 1; }

    /* ==== Hero ==== */
    .hero {
      background:linear-gradient(135deg, #4CAF50, #2E7D32);
      color:#fff;
      text-align:center;
      padding:3rem 1rem;
      border-radius:0 0 1.5rem 1.5rem;
      margin-bottom: 2rem;
    }
    .hero h1 { font-size:2rem; margin-bottom:0.5rem; }
    .hero p { font-size:1rem; opacity:0.92; }

    /* ==== Products Grid ==== */
    .products { padding:1.5rem 0; }
    .grid {
      display:grid;
      gap:1.5rem;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }

    /* ==== Product Card ==== */
    .card {
      background:#fff;
      border-radius:0.9rem;
      overflow:hidden;
      box-shadow:0 3px 12px rgba(46,139,87,.08);
      transition:transform .3s ease, box-shadow .3s ease;
      border:1px solid #E8F5E9;
      display: flex;
      flex-direction: column;
    }
    .card:hover {
      transform:translateY(-6px);
      box-shadow:0 14px 28px rgba(46,139,87,.15);
    }

    .card-img {
      height:180px;
      overflow:hidden;
      background:#E8F5E9;
    }
    .card-img img {
      width:100%;
      height:100%;
      object-fit: cover;
      transition:transform .35s ease;
    }
    .card:hover .card-img img { transform:scale(1.1); }

    .card-body {
      padding:1rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex-grow: 1;
      min-height: 140px;
    }

    .card-content { flex-grow: 1; }

    .card-title {
      font-size:1rem;
      font-weight:600;
      margin-bottom:0.35rem;
      display:-webkit-box;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      overflow:hidden;
      color:#1B5E20;
    }
    .price {
      font-size:1.15rem;
      font-weight:700;
      color:#2E8B57;
      margin-bottom:0.35rem;
    }
    .rating {
      display:flex;
      align-items:center;
      gap:0.25rem;
      font-size:0.85rem;
      color:#FFB300;
      margin-bottom:0.7rem;
    }
    .rating span { color:#7CB342; font-weight:500; }

    .btn {
      background:#2E8B57;
      color:#fff;
      border:none;
      padding:0.6rem 1rem;
      border-radius:0.6rem;
      font-weight:600;
      cursor:pointer;
      transition:background .3s ease, transform .2s ease;
      width:100%;
      text-align:center;
      font-size:0.9rem;
      margin-top: auto;
    }
    .btn:hover {
      background:#276945;
      transform:translateY(-2px);
    }

    /* ==== Footer ==== */
    footer {
      background:#1B5E20;
      color:#C8E6C9;
      text-align:center;
      padding:2rem 1rem;
      margin-top:2rem;
    }
    footer a { color:#A5D6A7; text-decoration:underline; }

    /* ==== MOBILE RESPONSIVE FIXES ==== */
    @media (max-width: 768px) {
      .container { padding: 0 0.75rem; }

      .header-inner {
        padding: 0.6rem 0;
        gap: 0.5rem;
      }

      .logo {
        font-size: 1.35rem;
      }
      .logo small {
        font-size: 0.6rem;
      }

      .search-container {
        order: 3;
        flex-basis: 100%;
        margin: 0.5rem 0 0;
      }

      .search-wrapper {
        padding: 0.7rem 0.9rem;
        min-height: 42px;
      }

      .search-wrapper input {
        font-size: 0.9rem;
      }

      .hero {
        padding: 2.5rem 1rem;
        border-radius: 0 0 1.2rem 1.2rem;
      }
      .hero h1 { font-size: 1.75rem; }
      .hero p { font-size: 0.95rem; }

      .products { padding: 1rem 0; }
      .grid { gap: 1.25rem; }
    }

    @media (max-width: 480px) {
      .logo { font-size: 1.25rem; }
      .logo small { font-size: 0.55rem; }
      .search-wrapper { padding: 0.65rem 0.8rem; }
      .icon-btn svg { width: 18px; height: 18px; }
      .cart-badge { font-size: 0.6rem; min-width: 15px; height: 15px; }
    }
  </style>
</head>

<body>

  <!-- FIXED MOBILE HEADER -->
  <header class="header">
    <div class="header-backdrop"></div>
    <div class="container header-inner">
      <div class="logo">
        <div>FarmConnect</div>
        <small>Fresh from Farmers</small>
      </div>
      
      <div class="search-container">
        <div class="search-wrapper">
          <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="search" placeholder="Search maize, tomatoes, yam..." />
          <svg class="mic-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3s-3 1.34-3 3v6c0 1.66 1.34 3 3 3z"/>
            <path d="M19 11c0 3.87-3.13 7-7 7s-7-3.13-7-7"/>
            <path d="M12 19v3"/>
          </svg>
        </div>
      </div>
      
      <div class="header-actions">
        <button class="icon-btn">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
          <span class="tooltip">Account</span>
        </button>
        <button class="icon-btn cart-btn">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
          </svg>
          <div class="cart-badge">3</div>
          <span class="tooltip">Cart</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <h1>Fresh from Farm to You</h1>
      <p>Buy directly from local farmers • No middlemen • Best prices</p>
    </div>
  </section>

  <!-- Products -->
  <section class="products">
    <div class="container">
      <h2 style="margin-bottom:1rem; font-size:1.5rem; color:#1B5E20;">Today’s Fresh Harvest</h2>
      <div class="grid" id="productGrid"></div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p>© 2025 FarmConnect. <a href="#">Privacy</a> • <a href="#">Terms</a> • <a href="#">Support Farmers</a></p>
    </div>
  </footer>

  <!-- JS: Safe DOM Load -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const products = [
        {img:"https://images.unsplash.com/photo-1500595046743-cd271d694e9d?auto=format&fit=crop&w=400&q=80", title:"Fresh Organic Tomatoes (1 Crate)", price:"₦8,500", rating:4.8},
        {img:"https://images.unsplash.com/photo-1567206562510-48f2e6f2a2b4?auto=format&fit=crop&w=400&q=80", title:"Yellow Maize (50kg Bag – Premium)", price:"₦22,000", rating:4.7},
        {img:"https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=400&q=80", title:"Farm-Fresh Eggs (30 Pieces)", price:"₦2,100", rating:4.9},
        {img:"https://images.unsplash.com/photo-1592911112001-2c2e3b5b3c9b?auto=format&fit=crop&w=400&q=80", title:"Cassava Tubers (10kg Bundle)", price:"₦4,200", rating:4.6},
        {img:"https://images.unsplash.com/photo-1600563438938-a9a1c4e5d7d3?auto=format&fit=crop&w=400&q=80", title:"Groundnut Oil (5L Pure)", price:"₦15,000", rating:4.8},
        {img:"https://images.unsplash.com/photo-1586201375761-8e2a1e4c6492?auto=format&fit=crop&w=400&q=80", title:"Plantain Bunch (Medium)", price:"₦3,800", rating:4.5}
      ];

      const grid = document.getElementById('productGrid');
      if (!grid) return;

      products.forEach(p => {
        const fullStars = '★'.repeat(Math.floor(p.rating));
        const halfStar = p.rating % 1 >= 0.5 ? '½' : '';
        const cardHTML = `
          <article class="card">
            <div class="card-img">
              <img src="${p.img}" alt="${p.title}" loading="lazy">
            </div>
            <div class="card-body">
              <div class="card-content">
                <h3 class="card-title">${p.title}</h3>
                <div class="price">${p.price}</div>
                <div class="rating">${fullStars}${halfStar}<span> (${p.rating})</span></div>
              </div>
              <button class="btn" onclick="alert('Added: ${p.title}')">
                Add to Cart
              </button>
            </div>
          </article>`;
        grid.insertAdjacentHTML('beforeend', cardHTML);
      });
    });
  </script>
</body>
</html>