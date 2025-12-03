<?php
session_start();
include 'DBcon.php';

// --- FETCH BANNER IMAGES ---
$banner_images = [];
$sql_banner = "SELECT image_path FROM produce_listings WHERE image_path IS NOT NULL AND image_path != '' ORDER BY RAND() LIMIT 5";
$result_banner = mysqli_query($conn, $sql_banner);
if ($result_banner) {
    while ($row = mysqli_fetch_assoc($result_banner)) {
        $banner_images[] = $row['image_path'];
    }
}
// Fallback if no images found
if (empty($banner_images)) {
    $banner_images[] = 'https://images.unsplash.com/photo-1495107334309-fcf20504a5ab?auto=format&fit=crop&w=1920&q=80';
}

// --- FETCH STATS ---
// 1. Active Farmers (those with listings)
$count_farmers = 0;
$sql_farmers = "SELECT COUNT(DISTINCT user_id) as count FROM produce_listings";
$res_farmers = mysqli_query($conn, $sql_farmers);
if ($res_farmers) $count_farmers = mysqli_fetch_assoc($res_farmers)['count'];

// 2. Fresh Products
$count_products = 0;
$sql_products = "SELECT COUNT(*) as count FROM produce_listings WHERE is_deleted = 0";
$res_products = mysqli_query($conn, $sql_products);
if ($res_products) $count_products = mysqli_fetch_assoc($res_products)['count'];

// 3. Happy Customers (Buyers)
$count_customers = 0;
$sql_customers = "SELECT COUNT(*) as count FROM buyers";
$res_customers = mysqli_query($conn, $sql_customers);
if ($res_customers) $count_customers = mysqli_fetch_assoc($res_customers)['count'];

// 4. Average Rating
$avg_rating = 0;
$sql_rating = "SELECT AVG(rating) as avg_rating FROM ratings";
$res_rating = mysqli_query($conn, $sql_rating);
if ($res_rating) {
    $row = mysqli_fetch_assoc($res_rating);
    $avg_rating = $row['avg_rating'] ? round($row['avg_rating'], 1) : 4.8; // Default to 4.8 if no ratings
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FarmConnect – Fresh from Farmers</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>

  <style>
    :root {
      --primary: #2E7D32;
      --primary-dark: #1B5E20;
      --secondary: #81C784;
      --accent: #FFB300;
      --bg-light: #F1F8E9;
      --text-dark: #1A1A1A;
      --text-light: #555;
      --white: #ffffff;
      --shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    *, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }
    
    body {
      font-family: 'Outfit', sans-serif;
      background: var(--bg-light);
      color: var(--text-dark);
      line-height: 1.6;
      overflow-x: hidden;
    }

    .container { max-width: 1280px; margin: auto; padding: 0 1.5rem; }
    
    a { text-decoration: none; color: inherit; transition: all 0.3s ease; }
    ul { list-style: none; }

    /* === HERO SECTION === */
    .hero {
      position: relative;
      height: 60vh; /* Reduced height */
      min-height: 450px;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      align-items: center;
      color: var(--white);
      margin-bottom: 3rem;
      border-radius: 0 0 50px 50px;
      overflow: hidden;
      box-shadow: 0 10px 40px rgba(46,125,50,0.2);
      transition: background-image 1s ease-in-out; /* Smooth transition */
    }
    
    /* Overlay */
    .hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6));
        z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 800px;
      padding: 2rem;
      animation: fadeInUp 1s ease-out;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      text-shadow: 0 4px 15px rgba(0,0,0,0.3);
    }

    .hero h1 span { color: var(--secondary); }

    .hero p {
      font-size: 1.2rem;
      margin-bottom: 2.5rem;
      opacity: 0.9;
      font-weight: 400;
      max-width: 600px;
    }

    .btn-hero {
      display: inline-block;
      background: var(--primary);
      color: var(--white);
      padding: 1rem 2.5rem;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 600;
      box-shadow: 0 10px 20px rgba(46,125,50,0.3);
      border: 2px solid transparent;
    }

    .btn-hero:hover {
      background: transparent;
      border-color: var(--white);
      transform: translateY(-3px);
    }

    /* === CATEGORIES === */
    .section-title {
      text-align: center;
      margin-bottom: 3rem;
    }
    .section-title h2 {
      font-size: 2.5rem;
      color: var(--primary-dark);
      margin-bottom: 0.5rem;
    }
    .section-title p { color: var(--text-light); }

    .categories { padding: 4rem 0; }
    .cat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 2rem;
      text-align: center;
    }
    .cat-item {
      background: var(--white);
      padding: 2rem 1rem;
      border-radius: 20px;
      box-shadow: var(--shadow);
      transition: transform 0.3s ease;
      cursor: pointer;
      display: block; /* Make anchor tag block */
    }
    .cat-item:hover { transform: translateY(-10px); }
    .cat-icon {
      width: 80px; height: 80px;
      background: #E8F5E9;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1rem;
      font-size: 2rem;
      color: var(--primary);
    }
    .cat-item h3 { font-size: 1.1rem; color: var(--text-dark); }

    /* === FEATURED PRODUCE (Existing Grid) === */
    .main-content { padding: 4rem 0; background: var(--white); }
    
    .filter-container {
      background: var(--bg-light);
      border-radius: 20px;
      padding: 1.5rem;
      margin-bottom: 3rem;
      display: flex;
      gap: 1rem;
      align-items: center;
      flex-wrap: wrap;
    }
    
    .filter-group { flex: 1; min-width: 250px; position: relative; }
    .filter-group label {
      font-weight: 600; color: var(--primary-dark); margin-bottom: 0.5rem; display: block;
    }
    .filter-group input, .filter-group select {
      width: 100%; padding: 0.8rem 1.2rem;
      border: 2px solid transparent;
      border-radius: 12px;
      background: var(--white);
      font-family: inherit;
      font-size: 1rem;
      transition: all 0.3s;
    }
    .filter-group input:focus, .filter-group select:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 4px rgba(46,125,50,0.1);
    }

    #produce-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
    }

    .produce-item {
      background: var(--white);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      border: 1px solid #eee;
      position: relative;
    }
    .produce-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    
    .produce-img-wrapper {
      height: 200px;
      overflow: hidden;
      position: relative;
    }
    .produce-img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform 0.5s ease;
    }
    .produce-item:hover .produce-img { transform: scale(1.1); }

    .badge-remaining {
      position: absolute; top: 15px; right: 15px;
      background: rgba(255,255,255,0.9);
      color: var(--primary);
      font-weight: 700;
      padding: 0.4rem 0.8rem;
      border-radius: 30px;
      font-size: 0.8rem;
      backdrop-filter: blur(5px);
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .produce-body { padding: 1.5rem; }
    .produce-body h4 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--text-dark);
    }
    
    .meta-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: var(--text-light);
    }
    .meta-info i { color: var(--primary); margin-right: 5px; }

    .price-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #eee;
    }

    .star-rating { color: var(--accent); font-size: 0.9rem; }
    
    .btn-view {
      background: var(--primary);
      color: var(--white);
      padding: 0.6rem 1.2rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.9rem;
    }
    .btn-view:hover { background: var(--primary-dark); }

    /* === HOW IT WORKS === */
    .how-it-works { padding: 5rem 0; background: #E8F5E9; }
    .steps-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 3rem;
      text-align: center;
      margin-top: 3rem;
    }
    .step-card {
      position: relative;
    }
    .step-icon {
      width: 100px; height: 100px;
      background: var(--white);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.5rem;
      font-size: 2.5rem;
      color: var(--primary);
      box-shadow: 0 10px 20px rgba(46,125,50,0.1);
    }
    .step-number {
      position: absolute;
      top: 0; right: 50%;
      transform: translateX(50px);
      background: var(--accent);
      color: var(--text-dark);
      width: 30px; height: 30px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700;
    }
    .step-card h3 { margin-bottom: 1rem; color: var(--primary-dark); }

    /* === STATS === */
    .stats {
      padding: 4rem 0;
      background: var(--primary-dark);
      color: var(--white);
      text-align: center;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
    }
    .stat-item h2 { font-size: 3rem; font-weight: 800; color: var(--secondary); margin-bottom: 0.5rem; }
    .stat-item p { font-size: 1.1rem; opacity: 0.8; }

    /* === NEWSLETTER === */
    .newsletter {
      padding: 5rem 0;
      text-align: center;
      background: url('https://www.transparenttextures.com/patterns/cubes.png'), linear-gradient(135deg, #2E7D32, #1B5E20);
      color: var(--white);
      margin-top: 4rem;
      border-radius: 30px;
      margin: 4rem 1.5rem;
    }
    .newsletter-form {
      max-width: 500px;
      margin: 2rem auto 0;
      display: flex;
      gap: 1rem;
    }
    .newsletter-form input {
      flex: 1;
      padding: 1rem;
      border-radius: 50px;
      border: none;
      outline: none;
    }
    .newsletter-form button {
      background: var(--accent);
      color: var(--text-dark);
      border: none;
      padding: 0 2rem;
      border-radius: 50px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .newsletter-form button:hover { transform: scale(1.05); }

    /* === FOOTER === */
    footer {
      background: #111;
      color: #aaa;
      padding: 4rem 0 2rem;
    }
    .footer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 3rem;
      margin-bottom: 3rem;
    }
    .footer-col h4 { color: var(--white); margin-bottom: 1.5rem; font-size: 1.2rem; }
    .footer-col ul li { margin-bottom: 0.8rem; }
    .footer-col ul li a:hover { color: var(--primary); }
    .social-links { display: flex; gap: 1rem; }
    .social-links a {
      width: 40px; height: 40px;
      background: rgba(255,255,255,0.1);
      display: flex; align-items: center; justify-content: center;
      border-radius: 50%;
      transition: all 0.3s;
    }
    .social-links a:hover { background: var(--primary); color: var(--white); }
    .copyright {
      text-align: center;
      padding-top: 2rem;
      border-top: 1px solid #333;
    }

    /* === MODAL === */
    .modal {
      display: none; position: fixed; z-index: 1000; inset: 0;
      background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
      justify-content: center; align-items: center; padding: 1rem;
    }
    .modal-content {
      background: var(--white); padding: 2.5rem; border-radius: 20px;
      max-width: 400px; text-align: center;
      animation: zoomIn 0.3s ease;
    }
    @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Suggestions */
    #produce-suggestions {
      position: absolute; top: 100%; left: 0; width: 100%; z-index: 10;
      background: var(--white); border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto;
      display: none; margin-top: 0.5rem;
    }
    #produce-suggestions li {
      padding: 0.8rem 1.2rem; cursor: pointer;
      border-bottom: 1px solid #f5f5f5;
    }
    #produce-suggestions li:hover { background: var(--bg-light); color: var(--primary); }

    @media (max-width: 768px) {
      .hero h1 { font-size: 2.5rem; }
      .newsletter-form { flex-direction: column; }
      .newsletter-form button { padding: 1rem; }
    }
  </style>
</head>
<body>

  <!-- HERO SECTION -->
  <header class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Fresh from the Farm,<br><span>Direct to Your Table</span></h1>
        <p>Connect with local farmers and get the highest quality organic produce delivered to your doorstep. No middlemen, just fresh goodness.</p>
        <a href="#shop" class="btn-hero"><i class="fas fa-shopping-basket me-2"></i> Shop Now</a>
      </div>
    </div>
  </header>

  <!-- CATEGORIES SECTION -->
  <section class="categories container">
    <div class="section-title">
      <h2>Browse Categories</h2>
      <p>Explore our wide range of fresh farm produce</p>
    </div>
    <div class="cat-grid">
      <a href="category_products.php?category=Vegetables" class="cat-item">
        <div class="cat-icon"><i class="fas fa-carrot"></i></div>
        <h3>Vegetables</h3>
      </a>
      <a href="category_products.php?category=Fruits" class="cat-item">
        <div class="cat-icon"><i class="fas fa-apple-alt"></i></div>
        <h3>Fruits</h3>
      </a>
      <a href="category_products.php?category=Grains" class="cat-item">
        <div class="cat-icon"><i class="fas fa-wheat"></i></div>
        <h3>Grains</h3>
      </a>
      <a href="category_products.php?category=Livestock" class="cat-item">
        <div class="cat-icon"><i class="fas fa-egg"></i></div>
        <h3>Livestock</h3>
      </a>
      <a href="category_products.php?category=Tubers" class="cat-item">
        <div class="cat-icon"><i class="fas fa-leaf"></i></div>
        <h3>Tubers</h3>
      </a>
      <a href="category_products.php?category=Spices" class="cat-item">
        <div class="cat-icon"><i class="fas fa-pepper-hot"></i></div>
        <h3>Spices</h3>
      </a>
    </div>
  </section>

  <!-- MAIN SHOP SECTION -->
  <section id="shop" class="main-content">
    <div class="container">
      <div class="section-title">
        <h2>Featured Produce</h2>
        <p>Hand-picked fresh items just for you</p>
      </div>

      <!-- Filter -->
      <div class="filter-container">
        <div class="filter-group">
          <label for="produce-search"><i class="fas fa-search"></i> Search Produce</label>
          <input type="text" id="produce-search" placeholder="What are you looking for?" autocomplete="off">
          <div id="produce-suggestions"><ul></ul></div>
        </div>
        <div class="filter-group">
          <label for="location-select"><i class="fas fa-map-marker-alt"></i> Location</label>
          <select id="location-select">
            <option value="">All Locations</option>
          </select>
        </div>
      </div>

      <!-- Grid -->
      <div id="produce-grid">
        <!-- JS will populate this -->
        <div style="grid-column: 1/-1; text-align:center; padding:3rem;">
          <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--primary)"></i>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="how-it-works">
    <div class="container">
      <div class="section-title">
        <h2>How It Works</h2>
        <p>Simple steps to get fresh food</p>
      </div>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-number">1</div>
          <div class="step-icon"><i class="fas fa-search"></i></div>
          <h3>Browse Produce</h3>
          <p>Explore thousands of fresh products from local farmers near you.</p>
        </div>
        <div class="step-card">
          <div class="step-number">2</div>
          <div class="step-icon"><i class="fas fa-handshake"></i></div>
          <h3>Connect & Order</h3>
          <p>Place your order directly. Secure payments and transparent pricing.</p>
        </div>
        <div class="step-card">
          <div class="step-number">3</div>
          <div class="step-icon"><i class="fas fa-truck-fast"></i></div>
          <h3>Fast Delivery</h3>
          <p>Get your fresh produce delivered to your doorstep in record time.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS -->
  <section class="stats">
    <div class="container">
      <div class="stats-grid">
        <div class="stat-item">
          <h2><?php echo number_format($count_farmers); ?>+</h2>
          <p>Local Farmers</p>
        </div>
        <div class="stat-item">
          <h2><?php echo number_format($count_products); ?>+</h2>
          <p>Fresh Products</p>
        </div>
        <div class="stat-item">
          <h2><?php echo number_format($count_customers); ?>+</h2>
          <p>Happy Customers</p>
        </div>
        <div class="stat-item">
          <h2><?php echo $avg_rating; ?></h2>
          <p>Average Rating</p>
        </div>
      </div>
    </div>
  </section>

  <!-- NEWSLETTER -->
  <section class="container">
    <div class="newsletter">
      <h2>Join Our Community</h2>
      <p>Subscribe to get updates on seasonal produce and exclusive offers.</p>
      <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Subscribed successfully!');">
        <input type="email" placeholder="Enter your email address" required>
        <button type="submit">Subscribe</button>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h4>FarmConnect</h4>
          <p>Bridging the gap between farmers and consumers. Fresh, local, and sustainable.</p>
          <div class="social-links" style="margin-top: 1.5rem;">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#shop">Shop Produce</a></li>
            <li><a href="#">Our Farmers</a></li>
            <li><a href="#">About Us</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Support</h4>
          <ul>
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Contact Us</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>Contact</h4>
          <ul>
            <li><i class="fas fa-map-marker-alt me-2"></i> 123 Farm Road, Lagos, NG</li>
            <li><i class="fas fa-phone me-2"></i> +234 800 123 4567</li>
            <li><i class="fas fa-envelope me-2"></i> hello@farmconnect.com</li>
          </ul>
        </div>
      </div>
      <div class="copyright">
        <p>&copy; 2025 FarmConnect. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Login Modal -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <div style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;">
        <i class="fas fa-lock"></i>
      </div>
      <h3 style="margin-bottom: 0.5rem;">Login Required</h3>
      <p style="color: #666; margin-bottom: 1.5rem;">You need to log in to place an order and connect with farmers.</p>
      <button class="modal-btn" onclick="redirectToLogin()" style="background:var(--primary); color:#fff; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-weight:600; cursor:pointer;">Log In Now</button>
      <button class="modal-btn" onclick="document.getElementById('loginModal').style.display='none'" style="background: #ccc; margin-left: 0.5rem; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-weight:600; cursor:pointer;">Cancel</button>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    loadLocations();
    loadProduceListing();

    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
      e.preventDefault();
      $('html, body').animate({
        scrollTop: $($(this).attr('href')).offset().top
      }, 500);
    });

    // --- BANNER SLIDER LOGIC ---
    const bannerImages = <?php echo json_encode($banner_images); ?>;
    let currentSlide = 0;
    const hero = document.querySelector('.hero');
    
    // Initialize first image
    if (bannerImages.length > 0) {
        hero.style.backgroundImage = `url('${bannerImages[0]}')`;
    }

    function changeSlide() {
        if (bannerImages.length <= 1) return;
        currentSlide = (currentSlide + 1) % bannerImages.length;
        hero.style.backgroundImage = `url('${bannerImages[currentSlide]}')`;
    }

    let slideInterval = setInterval(changeSlide, 3000);

    // Pause on hover
    hero.addEventListener('mouseenter', () => clearInterval(slideInterval));
    hero.addEventListener('mouseleave', () => slideInterval = setInterval(changeSlide, 3000));


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
          $('#produce-grid').html('<div style="grid-column: 1/-1; text-align:center; padding:3rem; color:#666; background:#f9f9f9; border-radius:15px;"><i class="fas fa-carrot fa-3x" style="color:#ccc; margin-bottom:1rem;"></i><br>No produce found matching your criteria.</div>');
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
          const uploadedQuantity = parseInt(p.uploaded_quantity);
          const unit = (p.units && p.units.trim() !== '') ? ` ${p.units}` : '';
          
          const badgeText = `${remaining}${unit} left`;
          const badge = remaining > 0 
            ? `<div class="badge-remaining"><i class="fas fa-clock me-1"></i> ${badgeText}</div>` 
            : `<div class="badge-remaining" style="background:#e74c3c; color:#fff;">Sold Out</div>`;

          const displayQuantity = `${uploadedQuantity}${unit}`;

          html += `
            <div class="produce-item">
              <div class="produce-img-wrapper">
                ${badge}
                <img src="${p.image_path}" alt="${p.produce}" class="produce-img">
              </div>
              <div class="produce-body">
                <h4>${p.produce}</h4>
                <div class="meta-info">
                  <span><i class="fas fa-map-marker-alt"></i> ${p.address}</span>
                </div>
                <div class="meta-info">
                   <span><i class="fas fa-box"></i> Stock: ${displayQuantity}</span>
                </div>
                
                <div class="price-row">
                  <div class="star-rating">${stars}</div>
                  <a href="#" class="btn-view" onclick="checkLogin('${p.prod_id}')">
                    Order Now <i class="fas fa-arrow-right ms-2" style="font-size:0.8em"></i>
                  </a>
                </div>
              </div>
            </div>`;
        });
        $('#produce-grid').html(html);
      }, 'json').fail(() => {
        $('#produce-grid').html('<div style="grid-column: 1/-1; text-align:center; padding:2rem; color:#e74c3c;">Error loading data. Please try again.</div>');
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
    
    // Close modal when clicking outside
    $(window).on('click', function(e) {
      if ($(e.target).is('#loginModal')) {
        $('#loginModal').hide();
      }
    });
  });

  var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
</body>
</html>