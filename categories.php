<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'models/Product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

// Get category from URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// If no category specified, show all categories
if(empty($category)) {
    // Show category listing
    $categories = [
        [
            'id' => 'phone',
            'name' => 'Smartphones',
            'description' => 'Latest smartphones and accessories',
            'icon' => 'fa-mobile-alt',
            'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'id' => 'laptop',
            'name' => 'Laptops',
            'description' => 'Powerful laptops for work and play',
            'icon' => 'fa-laptop',
            'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'id' => 'tablet',
            'name' => 'Tablets',
            'description' => 'Portable tablets for entertainment',
            'icon' => 'fa-tablet-alt',
            'image' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ],
        [
            'id' => 'accessory',
            'name' => 'Accessories',
            'description' => 'Gadgets and accessories',
            'icon' => 'fa-headphones',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ]
    ];
} else {
    // Get products for specific category
    $products = $product->search('', $category);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo empty($category) ? 'Categories' : ucfirst($category) . ' - Order With Me'; ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .categories-page {
            padding: 2rem 0;
        }
        .page-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .page-header h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        .category-card-large {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .category-card-large:hover {
            transform: translateY(-10px);
        }
        .category-image {
            height: 200px;
            overflow: hidden;
        }
        .category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .category-card-large:hover .category-image img {
            transform: scale(1.1);
        }
        .category-content {
            padding: 1.5rem;
        }
        .category-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        .category-content h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .category-content p {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
        .back-link {
            margin-bottom: 2rem;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container categories-page">
        <?php if(!empty($category)): ?>
            <div class="back-link">
                <a href="categories.php">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        <?php endif; ?>
        
        <div class="page-header">
            <h1><?php echo empty($category) ? 'Shop by Category' : ucfirst($category) . ' Products'; ?></h1>
            <p><?php echo empty($category) ? 'Browse our wide range of electronic categories' : 'Explore our selection of ' . $category; ?></p>
        </div>
        
        <?php if(empty($category)): ?>
            <!-- Show all categories -->
            <div class="categories-grid">
                <?php foreach($categories as $cat): ?>
                    <a href="categories.php?category=<?php echo $cat['id']; ?>" class="category-card-large">
                        <div class="category-image">
                            <img src="<?php echo $cat['image']; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>" loading="lazy">
                        </div>
                        <div class="category-content">
                            <div class="category-icon">
                                <i class="fas <?php echo $cat['icon']; ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <p><?php echo htmlspecialchars($cat['description']); ?></p>
                            <span class="btn btn-primary">View Products</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Show products for specific category -->
            <?php if($products && $products->rowCount() > 0): ?>
                <div class="products-grid">
                    <?php while($row = $products->fetch(PDO::FETCH_ASSOC)): 
                        $image_url = !empty($row['image_url']) ? $row['image_url'] : 'images/default-product.jpg';
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $image_url; ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>"
                                     loading="lazy">
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?php echo ucfirst($row['category']); ?></div>
                                <h3 class="product-title">
                                    <a href="product.php?id=<?php echo $row['product_id']; ?>">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </a>
                                </h3>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>
                                    <?php if(strlen($row['description']) > 100): ?>...<?php endif; ?>
                                </p>
                                <div class="product-price">$<?php echo number_format($row['price'], 2); ?></div>
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
            <?php else: ?>
                <div class="no-products">
                    <i class="fas fa-box-open" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                    <h3>No products in this category</h3>
                    <p>We currently don't have any products in the <?php echo $category; ?> category.</p>
                    <a href="categories.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-list"></i> Browse Categories
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartManager = window.cartManager || new CartManager();
            
            // Add to cart functionality
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
                    
                    // Visual feedback
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
        });
    </script>
</body>
</html>