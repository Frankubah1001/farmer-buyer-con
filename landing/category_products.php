<?php
session_start();
include 'DBcon.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'All';

// Keyword Mapping for Categories
$category_map = [
    'Vegetables' => ['Tomato', 'Pepper', 'Okra', 'Leaf', 'Spinach', 'Vegetable', 'Onion', 'Carrot', 'Cabbage', 'Lettuce', 'Cucumber', 'Garden Egg', 'Efo', 'Ugwu'],
    'Fruits' => ['Fruit', 'Mango', 'Orange', 'Banana', 'Pineapple', 'Apple', 'Watermelon', 'Pawpaw', 'Guava', 'Lemon', 'Lime', 'Cashew', 'Berry'],
    'Grains' => ['Grain', 'Rice', 'Maize', 'Corn', 'Bean', 'Millet', 'Sorghum', 'Wheat', 'Soybean', 'Acha'],
    'Livestock' => ['Chicken', 'Goat', 'Cow', 'Sheep', 'Meat', 'Egg', 'Poultry', 'Fish', 'Catfish', 'Tilapia', 'Pork', 'Pig', 'Ram', 'Turkey'],
    'Tubers' => ['Yam', 'Cassava', 'Potato', 'Cocoyam', 'Sweet Potato'],
    'Spices' => ['Ginger', 'Turmeric', 'Garlic', 'Spice', 'Curry', 'Thyme', 'Nutmeg']
];

$keywords = isset($category_map[$category]) ? $category_map[$category] : [$category];

// Build SQL Query
$sql = "SELECT
            pl.prod_id,
            pl.produce,
            pl.image_path,
            pl.address,
            pl.available_date,
            pl.quantity AS uploaded_quantity,
            pl.units,
            pl.price,
            COALESCE(o.remaining_quantity, 'No Order Yet') AS remaining_quantity,
            IFNULL(AVG(r.rating), 0) AS rating
        FROM
            produce_listings pl
        LEFT JOIN (
            SELECT produce_id, remaining_quantity
            FROM orders
            WHERE (order_date, order_id) IN (
                SELECT MAX(order_date), MAX(order_id)
                FROM orders
                GROUP BY produce_id
            )
        ) o ON pl.prod_id = o.produce_id
        JOIN users u ON pl.user_id = u.user_id
        LEFT JOIN ratings r ON u.user_id = r.user_id
        WHERE
            u.cbn_approved = 1
            AND pl.is_deleted = 0
            AND pl.available_date <= CURDATE()
            AND (o.remaining_quantity IS NULL OR o.remaining_quantity > 0)";

// Add Category Filter
if (!empty($keywords)) {
    $like_parts = [];
    foreach ($keywords as $word) {
        $safe_word = mysqli_real_escape_string($conn, $word);
        $like_parts[] = "pl.produce LIKE '%$safe_word%'";
    }
    $sql .= " AND (" . implode(" OR ", $like_parts) . ")";
}

$sql .= " GROUP BY pl.prod_id ORDER BY pl.created_at DESC";

$result = mysqli_query($conn, $sql);
$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($category); ?> – FarmConnect</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    }
    .container { max-width: 1280px; margin: auto; padding: 0 1.5rem; }
    a { text-decoration: none; color: inherit; transition: all 0.3s ease; }
    
    /* Header */
    .page-header {
        background: var(--primary);
        color: var(--white);
        padding: 3rem 0;
        text-align: center;
        border-radius: 0 0 30px 30px;
        margin-bottom: 3rem;
    }
    .page-header h1 { font-size: 2.5rem; font-weight: 700; }
    .page-header p { opacity: 0.9; font-size: 1.1rem; }
    .back-link { display: inline-block; margin-top: 1rem; color: var(--secondary); font-weight: 600; }
    .back-link:hover { color: var(--white); text-decoration: underline; }

    /* Grid */
    .produce-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
      padding-bottom: 4rem;
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
    
    /* Footer */
    footer {
      background: #111;
      color: #aaa;
      padding: 2rem 0;
      text-align: center;
      margin-top: auto;
    }
    
    /* Modal */
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
  </style>
</head>
<body>

  <header class="page-header">
    <div class="container">
      <h1><?php echo htmlspecialchars($category); ?></h1>
      <p>Fresh <?php echo strtolower(htmlspecialchars($category)); ?> directly from the farm.</p>
      <a href="landingPage.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
  </header>

  <div class="container">
    <?php if (empty($products)): ?>
        <div style="text-align:center; padding:4rem; color:#666; background:#fff; border-radius:20px; box-shadow: var(--shadow);">
            <i class="fas fa-leaf fa-4x" style="color:#ccc; margin-bottom:1.5rem;"></i>
            <h3>No produce found in this category.</h3>
            <p>Check back later or browse other categories.</p>
            <a href="landingPage.php" class="btn-view" style="display:inline-block; margin-top:1rem;">Browse All</a>
        </div>
    <?php else: ?>
        <div class="produce-grid">
            <?php foreach ($products as $p): 
                $rating = floatval($p['rating']);
                $full = floor($rating);
                $half = $rating - $full >= 0.25 && $rating - $full < 0.75;
                $empty = 5 - ceil($rating);
                
                $uploaded_quantity = intval($p['uploaded_quantity']);
                $remaining_quantity = ($p['remaining_quantity'] === 'No Order Yet') ? $uploaded_quantity : intval($p['remaining_quantity']);
                $unit = (!empty($p['units'])) ? ' ' . $p['units'] : '';
                $badgeText = $remaining_quantity . $unit . ' left';
                $isSoldOut = $remaining_quantity <= 0;
            ?>
            <div class="produce-item">
                <div class="produce-img-wrapper">
                    <?php if ($isSoldOut): ?>
                        <div class="badge-remaining" style="background:#e74c3c; color:#fff;">Sold Out</div>
                    <?php else: ?>
                        <div class="badge-remaining"><i class="fas fa-clock me-1"></i> <?php echo $badgeText; ?></div>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['produce']); ?>" class="produce-img">
                </div>
                <div class="produce-body">
                    <h4><?php echo htmlspecialchars($p['produce']); ?></h4>
                    <div class="meta-info">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($p['address']); ?></span>
                    </div>
                    <div class="meta-info">
                        <span><i class="fas fa-box"></i> Stock: <?php echo $uploaded_quantity . $unit; ?></span>
                    </div>
                    
                    <div class="price-row">
                        <div class="star-rating">
                            <?php 
                            for($i=0; $i<$full; $i++) echo '<i class="fas fa-star"></i>';
                            if($half) echo '<i class="fas fa-star-half-alt"></i>';
                            for($i=0; $i<$empty; $i++) echo '<i class="far fa-star"></i>';
                            ?>
                        </div>
                        <a href="#" class="btn-view" onclick="checkLogin('<?php echo $p['prod_id']; ?>')">
                            Order Now <i class="fas fa-arrow-right ms-2" style="font-size:0.8em"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
  </div>

  <footer>
    <div class="container">
      <p>&copy; 2025 FarmConnect. All rights reserved.</p>
    </div>
  </footer>

  <!-- Login Modal -->
  <div id="loginModal" class="modal">
    <div class="modal-content">
      <div style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;">
        <i class="fas fa-lock"></i>
      </div>
      <h3 style="margin-bottom: 0.5rem;">Login Required</h3>
      <p style="color: #666; margin-bottom: 1.5rem;">You need to log in to place an order.</p>
      <button class="modal-btn" onclick="redirectToLogin()" style="background:var(--primary); color:#fff; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-weight:600; cursor:pointer;">Log In Now</button>
      <button class="modal-btn" onclick="document.getElementById('loginModal').style.display='none'" style="background: #ccc; margin-left: 0.5rem; border:none; padding:0.8rem 1.5rem; border-radius:10px; font-weight:600; cursor:pointer;">Cancel</button>
    </div>
  </div>

  <script>
    var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    
    function checkLogin(prodId) {
      if (!isLoggedIn) {
        document.getElementById('loginModal').style.display = 'flex';
      } else {
        window.location.href = '../resources/buyer_view_product.php?prod_id=' + prodId;
      }
    }

    function redirectToLogin() {
      window.location.href = '../resources/Buyer_login.php';
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target == document.getElementById('loginModal')) {
        document.getElementById('loginModal').style.display = "none";
      }
    }
  </script>
</body>
</html>
