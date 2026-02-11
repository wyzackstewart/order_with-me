<?php
// index.php - Homepage with COMPLETE IMAGE FIX
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get all products
$products = $product->readAll();

// Handle search if submitted
$search_keywords = '';
$selected_category = '';
$search_results = null;

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search_keywords = trim($_GET['search']);
    $selected_category = isset($_GET['category']) ? $_GET['category'] : '';
    $search_results = $product->search($search_keywords, $selected_category);
}

// Array of fallback images (working Unsplash URLs)
$fallback_images = [
    'phone' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'laptop' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'tablet' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'accessory' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
    'default' => 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order With Me - Premium Electronics Store, Tanzania</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            color: #e74c3c;
        }
        
        .logo span {
            background: rgba(255,255,255,0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-left: 5px;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 1.5rem;
            align-items: center;
        }
        
        .nav-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-menu a:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .nav-menu a.active {
            background: rgba(255,255,255,0.2);
        }
        
        .cart-link {
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
            display: none;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #2c3e50;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #1a252f;
        }
        
        .btn-success {
            background: #2ecc71;
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('<?php echo $fallback_images['default']; ?>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 5rem 1rem;
            margin-bottom: 3rem;
        }
        
        .hero-section h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero-section p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero-section .tz-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        
        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 3rem 0;
        }
        
        .feature-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        /* Search Section */
        .search-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 2rem 0;
        }
        
        .search-form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        
        .search-box {
            flex: 1;
            min-width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .filter-select {
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            min-width: 150px;
        }
        
        /* Section Titles */
        .section-title {
            text-align: center;
            margin: 3rem 0;
        }
        
        .section-title h2 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #e74c3c;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .product-image {
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-category {
            color: #3498db;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .product-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        
        .product-title a {
            color: inherit;
            text-decoration: none;
        }
        
        .product-title a:hover {
            color: #3498db;
        }
        
        .product-description {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            height: 60px;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 1.5rem;
            color: #e74c3c;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .product-stock {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .in-stock {
            color: #2ecc71;
        }
        
        .out-of-stock {
            color: #e74c3c;
        }
        
        .product-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .product-actions .btn {
            flex: 1;
            padding: 0.6rem;
            font-size: 0.9rem;
        }
        
        /* Categories Grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        
        .category-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
        }
        
        .category-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(rgba(44, 62, 80, 0.95), rgba(44, 62, 80, 0.95)), 
                        url('<?php echo $fallback_images['default']; ?>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 4rem 1rem;
            margin: 4rem 0;
            border-radius: 10px;
        }
        
        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .cta-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        /* Footer - Updated with Tanzania info */
        .main-footer {
            background: #2c3e50;
            color: white;
            padding: 4rem 0 2rem;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            color: #3498db;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 0.8rem;
        }
        
        .footer-section ul li a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section ul li a:hover {
            color: #3498db;
        }
        
        .tz-flag {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
            font-size: 0.9rem;
        }
        
        /* No Products */
        .no-products {
            text-align: center;
            padding: 4rem 1rem;
            color: #7f8c8d;
        }
        
        .no-products i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.5rem;
            }
            
            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: #2c3e50;
                padding: 1rem;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }
            
            .nav-menu.show {
                display: flex;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .search-box {
                min-width: 100%;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .filter-select {
                width: 100%;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 480px) {
            .hero-section {
                padding: 3rem 1rem;
            }
            
            .hero-section h1 {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>OrderWithMe</span>
                    <span style="font-size: 0.7rem; background: #e74c3c;">Tanzania</span>
                </a>
                
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li><a href="categories.php"><i class="fas fa-list"></i> Categories</a></li>
                    <li><a href="cart.php" class="cart-link">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="cart-count">0</span>
                    </a></li>
                    
                    <?php if(isLoggedIn()): ?>
                        <li><a href="orders.php"><i class="fas fa-history"></i> Orders</a></li>
                        <?php if(isAdmin()): ?>
                            <li><a href="admin/"><i class="fas fa-cog"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section - Updated for Tanzania -->
    <section class="hero-section">
        <div class="container">
            <h1>Welcome to Order With Me Tanzania</h1>
            <p>Your trusted destination for premium electronics in Dar es Salaam and across Tanzania.</p>
            <div class="tz-badge">
                <i class="fas fa-map-marker-alt"></i> Dar es Salaam, Tanzania
            </div>
            <div style="margin-top: 2rem;">
                <a href="#featured" class="btn btn-primary btn-large"><i class="fas fa-shopping-bag"></i> Shop Now</a>
                <a href="products.php" class="btn btn-secondary btn-large"><i class="fas fa-star"></i> View Products</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="container">
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Free Shipping</h3>
                <p>Free delivery in Dar es Salaam on orders over 200,000 TZS</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Payment</h3>
                <p>100% secure payments via Tigo Pesa, M-Pesa, Airtel Money</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <h3>7-Day Returns</h3>
                <p>Easy returns within 7 days of delivery</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Call us anytime at 0688 213 043</p>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="container">
        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <div class="search-box">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Search for phones, laptops, accessories..."
                           value="<?php echo htmlspecialchars($search_keywords); ?>"
                           autocomplete="off">
                </div>
                <select name="category" class="filter-select">
                    <option value="">All Categories</option>
                    <option value="phone" <?php echo ($selected_category == 'phone') ? 'selected' : ''; ?>>Smartphones</option>
                    <option value="laptop" <?php echo ($selected_category == 'laptop') ? 'selected' : ''; ?>>Laptops</option>
                    <option value="tablet" <?php echo ($selected_category == 'tablet') ? 'selected' : ''; ?>>Tablets</option>
                    <option value="accessory" <?php echo ($selected_category == 'accessory') ? 'selected' : ''; ?>>Accessories</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if($search_keywords || $selected_category): ?>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="container">
        <div class="section-title">
            <h2>Featured Products</h2>
            <p>Discover our handpicked selection of premium electronics</p>
        </div>
        
        <?php 
        // Determine which products to display
        $display_products = $search_results ? $search_results : $products;
        $product_count = 0;
        $has_products = false;
        
        // Check if we have products
        if($display_products) {
            if(method_exists($display_products, 'rowCount')) {
                $has_products = $display_products->rowCount() > 0;
            }
        }
        
        if($has_products): 
        ?>
            <div class="products-grid">
                <?php 
                // Reset and fetch products
                if($search_results) {
                    $search_results->execute();
                    $rows = $search_results->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $rows = $products->fetchAll(PDO::FETCH_ASSOC);
                }
                
                foreach($rows as $row): 
                    $product_count++;
                    if($product_count > 8) break;
                    
                    // Determine badge
                    $badge = '';
                    if($row['stock_quantity'] < 5 && $row['stock_quantity'] > 0) {
                        $badge = '<span class="product-badge">Low Stock</span>';
                    } elseif($row['price'] < 100) {
                        $badge = '<span class="product-badge" style="background: #f39c12;">Great Deal</span>';
                    } elseif($row['stock_quantity'] == 0) {
                        $badge = '<span class="product-badge" style="background: #95a5a6;">Out of Stock</span>';
                    }
                    
                    // Get category display name
                    $category_names = [
                        'phone' => 'Smartphone',
                        'laptop' => 'Laptop',
                        'tablet' => 'Tablet',
                        'accessory' => 'Accessory'
                    ];
                    $category_display = $category_names[$row['category']] ?? ucfirst($row['category']);
                    
                    // --- COMPLETE IMAGE FIX ---
                    if(!empty($row['image_url'])) {
                        if(filter_var($row['image_url'], FILTER_VALIDATE_URL)) {
                            $image_url = $row['image_url'];
                        } else {
                            $local_path = __DIR__ . '/' . $row['image_url'];
                            if(file_exists($local_path)) {
                                $image_url = $row['image_url'];
                            } else {
                                $image_url = $fallback_images[$row['category']] ?? $fallback_images['default'];
                            }
                        }
                    } else {
                        $image_url = $fallback_images[$row['category']] ?? $fallback_images['default'];
                    }
                ?>
                    <div class="product-card">
                        <?php echo $badge; ?>
                        
                        <div class="product-image">
                            <img src="<?php echo $image_url; ?>" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='<?php echo $fallback_images['default']; ?>';">
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category"><?php echo $category_display; ?></div>
                            
                            <h3 class="product-title">
                                <a href="product.php?id=<?php echo $row['product_id']; ?>">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h3>
                            
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>
                                <?php if(strlen($row['description']) > 80): ?>...<?php endif; ?>
                            </p>
                            
                            <div class="product-price">
                                TZS <?php echo number_format($row['price'] * 2500, 0); ?> /-
                            </div>
                            
                            <div class="product-stock">
                                <?php if($row['stock_quantity'] > 0): ?>
                                    <span class="in-stock">
                                        <i class="fas fa-check-circle"></i> 
                                        <?php echo $row['stock_quantity']; ?> in stock
                                    </span>
                                <?php else: ?>
                                    <span class="out-of-stock">
                                        <i class="fas fa-times-circle"></i> Out of stock
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-actions">
                                <?php if($row['stock_quantity'] > 0): ?>
                                    <button class="btn btn-primary add-to-cart-btn"
                                            data-product-id="<?php echo $row['product_id']; ?>"
                                            data-product-name="<?php echo htmlspecialchars($row['name']); ?>"
                                            data-product-price="<?php echo $row['price'] * 2500; ?>"
                                            data-product-image="<?php echo $image_url; ?>">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="fas fa-cart-plus"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
                                
                                <a href="product.php?id=<?php echo $row['product_id']; ?>" 
                                   class="btn btn-success">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if($product_count > 0): ?>
                <div style="text-align: center; margin-top: 3rem;">
                    <a href="products.php" class="btn btn-primary btn-large">
                        <i class="fas fa-arrow-right"></i> View All Products
                    </a>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="no-products">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p><?php echo $search_keywords ? 'No products match your search criteria.' : 'No products available at the moment.'; ?></p>
                <?php if($search_keywords): ?>
                    <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-home"></i> Clear Search
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Categories Section -->
    <section class="container">
        <div class="section-title">
            <h2>Shop by Category</h2>
            <p>Browse our wide range of electronic categories</p>
        </div>
        
        <div class="categories-grid">
            <a href="categories.php?category=phone" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Smartphones</h3>
                <p>Latest models from Apple, Samsung, Tecno, Infinix</p>
            </a>
            
            <a href="categories.php?category=laptop" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-laptop"></i>
                </div>
                <h3>Laptops</h3>
                <p>Dell, HP, Lenovo, Apple MacBook</p>
            </a>
            
            <a href="categories.php?category=tablet" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-tablet-alt"></i>
                </div>
                <h3>Tablets</h3>
                <p>iPad, Samsung Tab, Huawei</p>
            </a>
            
            <a href="categories.php?category=accessory" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-headphones"></i>
                </div>
                <h3>Accessories</h3>
                <p>Headphones, chargers, cases, cables</p>
            </a>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Karibu Tanzania!</h2>
                <p>Join thousands of satisfied customers across Dar es Salaam, Arusha, Mwanza, and all regions of Tanzania. Get 10% off your first order!</p>
                
                <?php if(!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-primary btn-large">
                        <i class="fas fa-gift"></i> Jisajili Sasa
                    </a>
                <?php else: ?>
                    <a href="products.php" class="btn btn-primary btn-large">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer - Updated with Tanzania location and phone -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Order With Me Tanzania</h3>
                    <p>Your trusted electronics store in Dar es Salaam, offering premium products with exceptional customer service since 2024.</p>
                    <div class="tz-flag">
                        <i class="fas fa-map-marker-alt"></i> Dar es Salaam, Tanzania
                    </div>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" style="color: white; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="categories.php">Categories</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="shipping.php">Shipping Policy</a></li>
                        <li><a href="returns.php">Returns & Refunds</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-map-marker-alt" style="color: #3498db; margin-right: 0.8rem; width: 20px;"></i>
                            <span>Samora Avenue, Dar es Salaam, Tanzania</span>
                        </li>
                        <li style="margin-bottom: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-phone" style="color: #3498db; margin-right: 0.8rem; width: 20px;"></i>
                            <span>0688 213 043</span>
                        </li>
                        <li style="margin-bottom: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-mobile-alt" style="color: #3498db; margin-right: 0.8rem; width: 20px;"></i>
                            <span>0788 213 043 (WhatsApp)</span>
                        </li>
                        <li style="margin-bottom: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-envelope" style="color: #3498db; margin-right: 0.8rem; width: 20px;"></i>
                            <span>info@orderwithme.co.tz</span>
                        </li>
                        <li style="margin-bottom: 0.8rem; display: flex; align-items: center;">
                            <i class="fas fa-clock" style="color: #3498db; margin-right: 0.8rem; width: 20px;"></i>
                            <span>Mon-Sat: 9AM - 7PM, Sun: 10AM - 4PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Order With Me Tanzania. All rights reserved. | Designed for Academic Purposes</p>
                <p style="margin-top: 0.5rem; font-size: 0.8rem;">
                    <i class="fas fa-map-pin"></i> Dar es Salaam | <i class="fas fa-phone"></i> 0688 213 043 | <i class="fas fa-wifi"></i> Serving All Tanzania
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Cart Manager Class
        class CartManager {
            constructor() {
                this.cart = JSON.parse(localStorage.getItem('cart')) || [];
                this.updateCartCount();
            }
            
            addToCart(productId, name, price, image = null) {
                const existingItem = this.cart.find(item => item.id == productId);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    this.cart.push({
                        id: productId,
                        name: name,
                        price: parseFloat(price),
                        quantity: 1,
                        image: image
                    });
                }
                
                this.saveCart();
                this.updateCartCount();
                this.showNotification('Imeongezwa kwenye cart!', 'success');
                return this.cart;
            }
            
            removeFromCart(productId) {
                const item = this.cart.find(item => item.id == productId);
                this.cart = this.cart.filter(item => item.id != productId);
                this.saveCart();
                this.updateCartCount();
                this.showNotification('Imeondolewa kwenye cart', 'info');
                return this.cart;
            }
            
            updateQuantity(productId, quantity) {
                const item = this.cart.find(item => item.id == productId);
                if (item) {
                    item.quantity = parseInt(quantity) || 1;
                    if (item.quantity < 1) item.quantity = 1;
                    this.saveCart();
                    this.updateCartCount();
                }
                return this.cart;
            }
            
            getCart() {
                return this.cart;
            }
            
            getTotalItems() {
                return this.cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            }
            
            getTotalPrice() {
                return this.cart.reduce((sum, item) => sum + (item.price * (item.quantity || 0)), 0);
            }
            
            clearCart() {
                this.cart = [];
                this.saveCart();
                this.updateCartCount();
                this.showNotification('Cart imefutwa', 'info');
            }
            
            saveCart() {
                localStorage.setItem('cart', JSON.stringify(this.cart));
                this.updateCartCount();
            }
            
            updateCartCount() {
                const total = this.getTotalItems();
                const cartCountElements = document.querySelectorAll('.cart-count');
                cartCountElements.forEach(el => {
                    el.textContent = total;
                    el.style.display = total > 0 ? 'flex' : 'none';
                });
            }
            
            showNotification(message, type = 'success') {
                const existing = document.querySelector('.cart-notification');
                if (existing) existing.remove();
                
                const notification = document.createElement('div');
                notification.className = 'cart-notification';
                notification.innerHTML = `
                    <div class="notification-content">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#2ecc71' : '#3498db'};
                    color: white;
                    padding: 1rem 1.5rem;
                    border-radius: 8px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                    z-index: 9999;
                    animation: slideInRight 0.3s ease;
                    max-width: 300px;
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const cartManager = new CartManager();
            window.cartManager = cartManager;
            
            // Mobile menu toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const navMenu = document.getElementById('navMenu');
            
            if (mobileMenuBtn && navMenu) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    navMenu.classList.toggle('show');
                    this.innerHTML = navMenu.classList.contains('show') 
                        ? '<i class="fas fa-times"></i>' 
                        : '<i class="fas fa-bars"></i>';
                });
                
                document.addEventListener('click', function(e) {
                    if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        navMenu.classList.remove('show');
                        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                });
            }
            
            // Add to cart buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-to-cart-btn') || 
                    e.target.closest('.add-to-cart-btn')) {
                    const button = e.target.classList.contains('add-to-cart-btn') 
                        ? e.target 
                        : e.target.closest('.add-to-cart-btn');
                    
                    const productId = button.dataset.productId;
                    const productName = button.dataset.productName;
                    const productPrice = button.dataset.productPrice;
                    const productImage = button.dataset.productImage;
                    
                    cartManager.addToCart(productId, productName, productPrice, productImage);
                    
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i> Imeongezwa!';
                    button.style.background = '#2ecc71';
                    button.disabled = true;
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.style.background = '';
                        button.disabled = false;
                    }, 2000);
                }
            });
            
            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
