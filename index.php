<?php
// index.php - Homepage
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order With Me - Premium Electronics Store</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    
    <style>
        /* Main Styles */
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --border: #ddd;
        }
        
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
        
        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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
            color: var(--accent);
        }
        
        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .logo-main {
            font-size: 1.5rem;
        }
        
        .logo-sub {
            font-size: 0.8rem;
            opacity: 0.8;
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
            background: var(--accent);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .user-menu {
            position: relative;
        }
        
        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }
        
        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        .user-dropdown a {
            color: #333;
            padding: 0.8rem 1rem;
            display: block;
            border-bottom: 1px solid #eee;
        }
        
        .user-dropdown a:hover {
            background: #f8f9fa;
            color: var(--primary);
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 6rem 1rem;
            margin-bottom: 3rem;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-accent {
            background: var(--accent);
            color: white;
        }
        
        .btn-accent:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .btn-large {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
        }
        
        /* Features Section */
        .features-section {
            padding: 4rem 0;
            background: var(--light);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .feature-card h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        /* Search Section */
        .search-section {
            padding: 2rem 0;
            background: white;
            margin: 2rem 0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--border);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .filter-select {
            padding: 0.8rem 1rem;
            border: 2px solid var(--border);
            border-radius: 4px;
            background: white;
            font-size: 1rem;
            min-width: 150px;
        }
        
        /* Products Section */
        .products-section {
            padding: 4rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: var(--accent);
        }
        
        .section-title p {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
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
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--accent);
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
            color: var(--primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }
        
        .product-title {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .product-title a {
            color: inherit;
            text-decoration: none;
        }
        
        .product-title a:hover {
            color: var(--primary);
        }
        
        .product-description {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            height: 60px;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 1.5rem;
            color: var(--accent);
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .product-stock {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .in-stock {
            color: var(--success);
        }
        
        .out-of-stock {
            color: var(--danger);
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
        
        /* Categories Section */
        .categories-section {
            padding: 4rem 0;
            background: var(--light);
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .category-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .category-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .category-card h3 {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .category-card p {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(rgba(44, 62, 80, 0.95), rgba(44, 62, 80, 0.95)), 
                        url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 5rem 1rem;
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
        
        .cta-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        /* Footer */
        .main-footer {
            background: var(--secondary);
            color: white;
            padding: 4rem 0 2rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            color: var(--primary);
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
            color: var(--primary);
        }
        
        .contact-info li {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .contact-info i {
            color: var(--primary);
            margin-top: 3px;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: #aaa;
            font-size: 0.9rem;
        }
        
        /* No Products Message */
        .no-products {
            text-align: center;
            padding: 4rem 1rem;
            color: var(--gray);
        }
        
        .no-products i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--secondary);
                padding: 1rem;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            }
            
            .nav-menu.show {
                display: flex;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-large {
                width: 100%;
                max-width: 300px;
            }
            
            .search-box {
                min-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .hero-section {
                padding: 4rem 1rem;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-actions {
                flex-direction: column;
            }
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        .slide-in-up {
            animation: slideInUp 0.5s ease;
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
                    <div class="logo-text">
                        <span class="logo-main">OrderWithMe</span>
                        <span class="logo-sub">Electronics Store</span>
                    </div>
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
                        <li class="user-menu">
                            <a href="#" id="userDropdownBtn">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['first_name']); ?>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                                <a href="orders.php"><i class="fas fa-history"></i> My Orders</a>
                                <?php if(isAdmin()): ?>
                                    <a href="admin/"><i class="fas fa-cog"></i> Admin Panel</a>
                                <?php endif; ?>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="register.php" class="btn btn-accent"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Welcome to Order With Me</h1>
                <p class="hero-subtitle">Your trusted destination for premium electronics, gadgets, and tech accessories at unbeatable prices.</p>
                <div class="hero-buttons">
                    <a href="#featured" class="btn btn-primary btn-large"><i class="fas fa-shopping-bag"></i> Shop Now</a>
                    <a href="products.php" class="btn btn-secondary btn-large"><i class="fas fa-star"></i> View Products</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title">
                <h2>Why Choose Us</h2>
                <p>We provide the best shopping experience for electronics</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Free Shipping</h3>
                    <p>Free delivery on all orders over $50</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Payment</h3>
                    <p>100% secure and encrypted payments</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h3>30-Day Returns</h3>
                    <p>Easy returns within 30 days</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Dedicated customer support team</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <form method="GET" action="index.php" class="search-form">
                <div class="search-box">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Search for products, brands, categories..."
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
    <section id="featured" class="products-section">
        <div class="container">
            <div class="section-title">
                <h2>Featured Products</h2>
                <p>Discover our handpicked selection of premium electronics</p>
            </div>
            
            <?php 
            // Determine which products to display
            $display_products = $search_results ? $search_results : $products;
            $product_count = 0;
            
            if($display_products && $display_products->rowCount() > 0): 
            ?>
                <div class="products-grid">
                    <?php 
                    // Reset pointer if it's a search result
                    if($search_results) {
                        $display_products->execute(); // Re-execute if it's a PDOStatement
                    }
                    
                    while($row = $display_products->fetch(PDO::FETCH_ASSOC)): 
                        $product_count++;
                        // Limit to 8 products on homepage
                        if($product_count > 8) break;
                        
                        // Determine badge based on conditions
                        $badge = '';
                        if($row['stock_quantity'] < 5 && $row['stock_quantity'] > 0) {
                            $badge = '<span class="product-badge">Low Stock</span>';
                        } elseif($row['price'] < 100) {
                            $badge = '<span class="product-badge">Great Deal</span>';
                        } elseif($row['stock_quantity'] == 0) {
                            $badge = '<span class="product-badge" style="background: var(--gray);">Out of Stock</span>';
                        }
                        
                        // Get category display name
                        $category_names = [
                            'phone' => 'Smartphone',
                            'laptop' => 'Laptop',
                            'tablet' => 'Tablet',
                            'accessory' => 'Accessory',
                            'other' => 'Other'
                        ];
                        $category_display = isset($category_names[$row['category']]) ? $category_names[$row['category']] : ucfirst($row['category']);
                        
                        // Use default image if none provided
                        $image_url = !empty($row['image_url']) ? $row['image_url'] : 'https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80';
                    ?>
                        <div class="product-card" 
                             data-category="<?php echo $row['category']; ?>"
                             data-price="<?php echo $row['price']; ?>"
                             data-name="<?php echo htmlspecialchars(strtolower($row['name'])); ?>">
                            
                            <?php echo $badge; ?>
                            
                            <div class="product-image">
                                <img src="<?php echo $image_url; ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                                     loading="lazy">
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
                                    $<?php echo number_format($row['price'], 2); ?>
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
                                                data-product-price="<?php echo $row['price']; ?>"
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
                    <?php endwhile; ?>
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
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-title">
                <h2>Shop by Category</h2>
                <p>Browse our wide range of electronic categories</p>
            </div>
            
            <div class="categories-grid">
                <a href="products.php?category=phone" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Smartphones</h3>
                    <p>Latest models & accessories</p>
                </a>
                
                <a href="products.php?category=laptop" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3>Laptops</h3>
                    <p>Powerful computing devices</p>
                </a>
                
                <a href="products.php?category=tablet" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tablet-alt"></i>
                    </div>
                    <h3>Tablets</h3>
                    <p>Portable entertainment</p>
                </a>
                
                <a href="products.php?category=accessory" class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <h3>Accessories</h3>
                    <p>Gadgets & accessories</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Upgrade Your Tech?</h2>
                <p>Join thousands of satisfied customers who trust us for their electronic needs. Sign up today and get 10% off your first order!</p>
                
                <?php if(!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-accent btn-large">
                        <i class="fas fa-gift"></i> Get 10% Off Now
                    </a>
                <?php else: ?>
                    <a href="products.php" class="btn btn-accent btn-large">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Order With Me</h3>
                    <p>Your trusted electronics store offering premium products with exceptional customer service since 2024.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="faq.php">FAQ</a></li>
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
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Tech Street, Digital City</span>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <span>(555) 123-4567</span>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <span>support@orderwithme.com</span>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Fri: 9AM-6PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Order With Me. All rights reserved. | Designed for Academic Purposes</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Cart Management System
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
                this.showNotification('Added to cart!', 'success');
                return this.cart;
            }
            
            removeFromCart(productId) {
                this.cart = this.cart.filter(item => item.id != productId);
                this.saveCart();
                this.updateCartCount();
                this.showNotification('Removed from cart', 'info');
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
            }
            
            saveCart() {
                localStorage.setItem('cart', JSON.stringify(this.cart));
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
                // Remove existing notifications
                const existing = document.querySelector('.cart-notification');
                if (existing) existing.remove();
                
                // Create notification
                const notification = document.createElement('div');
                notification.className = `cart-notification cart-notification-${type}`;
                notification.innerHTML = `
                    <div class="notification-content">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `;
                
                // Style notification
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
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOutRight 0.3s ease';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart manager
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
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        navMenu.classList.remove('show');
                        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                });
            }
            
            // User dropdown
            const userDropdownBtn = document.getElementById('userDropdownBtn');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userDropdownBtn && userDropdown) {
                userDropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    userDropdown.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    if (userDropdown && userDropdown.classList.contains('show')) {
                        userDropdown.classList.remove('show');
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
                    
                    // Add to cart
                    cartManager.addToCart(productId, productName, productPrice, productImage);
                    
                    // Visual feedback on button
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i> Added!';
                    button.style.background = '#2ecc71';
                    button.disabled = true;
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.style.background = '';
                        button.disabled = false;
                    }, 2000);
                }
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    
                    if (this.value.trim().length >= 2) {
                        searchTimeout = setTimeout(() => {
                            filterProducts(this.value.trim());
                        }, 500);
                    } else if (this.value.trim() === '') {
                        filterProducts('');
                    }
                });
            }
            
            // Category filter
            const categoryFilter = document.querySelector('select[name="category"]');
            if (categoryFilter) {
                categoryFilter.addEventListener('change', function() {
                    filterProductsByCategory(this.value);
                });
            }
            
            // Smooth scrolling for anchor links
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
            
            // Initialize animations
            initAnimations();
        });
        
        // Filter products by search term
        function filterProducts(searchTerm) {
            const productCards = document.querySelectorAll('.product-card');
            const searchTermLower = searchTerm.toLowerCase();
            
            productCards.forEach(card => {
                const productName = card.querySelector('.product-title').textContent.toLowerCase();
                const productDesc = card.querySelector('.product-description').textContent.toLowerCase();
                const productCategory = card.dataset.category;
                
                const matchesSearch = searchTerm === '' || 
                    productName.includes(searchTermLower) || 
                    productDesc.includes(searchTermLower);
                
                card.style.display = matchesSearch ? 'block' : 'none';
                
                // Add highlight effect for matches
                if (matchesSearch && searchTerm !== '') {
                    card.classList.add('search-match');
                    card.style.animation = 'pulse 1s';
                    setTimeout(() => {
                        card.style.animation = '';
                    }, 1000);
                } else {
                    card.classList.remove('search-match');
                }
            });
        }
        
        // Filter products by category
        function filterProductsByCategory(category) {
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productCategory = card.dataset.category;
                
                if (category === '' || productCategory === category) {
                    card.style.display = 'block';
                    card.classList.add('category-match');
                } else {
                    card.style.display = 'none';
                    card.classList.remove('category-match');
                }
            });
        }
        
        // Initialize animations
        function initAnimations() {
            // Add animation styles
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
                
                @keyframes pulse {
                    0% {
                        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.7);
                    }
                    70% {
                        box-shadow: 0 0 0 10px rgba(52, 152, 219, 0);
                    }
                    100% {
                        box-shadow: 0 0 0 0 rgba(52, 152, 219, 0);
                    }
                }
                
                .search-match {
                    border: 2px solid #3498db;
                }
                
                .category-match {
                    border: 2px solid #2ecc71;
                }
                
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                
                .notification-content i {
                    font-size: 1.2rem;
                }
            `;
            document.head.appendChild(style);
            
            // Add fade-in animation to elements
            const elementsToAnimate = document.querySelectorAll('.feature-card, .product-card, .category-card');
            elementsToAnimate.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
                el.classList.add('fade-in');
            });
        }
        
        // Handle page refresh to maintain cart
        window.addEventListener('beforeunload', function() {
            // Save cart state
            if (window.cartManager) {
                window.cartManager.saveCart();
            }
        });
    </script>
</body>
</html>