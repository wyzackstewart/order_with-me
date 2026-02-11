<?php
require_once 'config/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Order With Me</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cart-page {
            padding: 2rem 0;
            min-height: 60vh;
        }
        .page-header {
            margin-bottom: 2rem;
        }
        .page-header h1 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .cart-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .cart-items {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 1.5rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .cart-item-image {
            width: 100px;
            height: 100px;
            overflow: hidden;
            border-radius: 8px;
        }
        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cart-item-details h3 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .cart-item-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
            padding: 0.3rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .cart-item-remove {
            color: #e74c3c;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .cart-summary {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .summary-row.total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #e74c3c;
            border-bottom: none;
        }
        .cart-empty {
            text-align: center;
            padding: 4rem 2rem;
        }
        .cart-empty i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        @media (max-width: 992px) {
            .cart-container {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 576px) {
            .cart-item {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .cart-item-image {
                margin: 0 auto;
            }
            .cart-item-quantity {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container cart-page">
        <div class="page-header">
            <h1>Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>
        
        <div id="cart-content">
            <!-- Cart content will be loaded by JavaScript -->
            <div class="loading" style="text-align: center; padding: 3rem;">
                <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
                <p>Loading cart...</p>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    
    <script>
        // Cart Manager Class
        class CartManager {
            constructor() {
                this.cart = JSON.parse(localStorage.getItem('cart')) || [];
            }
            
            getCart() {
                return this.cart;
            }
            
            updateQuantity(productId, quantity) {
                const item = this.cart.find(item => item.id == productId);
                if (item) {
                    item.quantity = parseInt(quantity) || 1;
                    if (item.quantity < 1) item.quantity = 1;
                    this.saveCart();
                }
                return this.cart;
            }
            
            removeFromCart(productId) {
                this.cart = this.cart.filter(item => item.id != productId);
                this.saveCart();
                return this.cart;
            }
            
            clearCart() {
                this.cart = [];
                this.saveCart();
            }
            
            getTotalItems() {
                return this.cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
            }
            
            getTotalPrice() {
                return this.cart.reduce((sum, item) => sum + (item.price * (item.quantity || 0)), 0);
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
                const notification = document.createElement('div');
                notification.className = `cart-notification`;
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
            
            // Load cart content
            loadCartContent();
            
            // Update cart count
            cartManager.updateCartCount();
        });
        
        function loadCartContent() {
            const cartManager = window.cartManager;
            const cart = cartManager.getCart();
            const container = document.getElementById('cart-content');
            
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some products to your cart and they will appear here.</p>
                        <a href="products.php" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-shopping-bag"></i> Start Shopping
                        </a>
                    </div>
                `;
                return;
            }
            
            let itemsHTML = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                itemsHTML += `
                    <div class="cart-item" data-product-id="${item.id}">
                        <div class="cart-item-image">
                            <img src="${item.image || 'images/default-product.jpg'}" alt="${item.name}">
                        </div>
                        <div class="cart-item-details">
                            <h3>${item.name}</h3>
                            <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                            <div class="cart-item-quantity">
                                <button class="quantity-btn decrease-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="quantity-input" 
                                       value="${item.quantity}" min="1" 
                                       onchange="updateQuantity(${item.id}, this.value)">
                                <button class="quantity-btn increase-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <div style="font-weight: bold; margin-bottom: 0.5rem;">$${itemTotal.toFixed(2)}</div>
                            <button class="cart-item-remove" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            const shipping = subtotal > 0 ? 5.99 : 0;
            const tax = subtotal * 0.08; // 8% tax
            const total = subtotal + shipping + tax;
            
            container.innerHTML = `
                <div class="cart-container">
                    <div class="cart-items">
                        ${itemsHTML}
                    </div>
                    <div class="cart-summary">
                        <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$${subtotal.toFixed(2)}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>$${shipping.toFixed(2)}</span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (8%)</span>
                            <span>$${tax.toFixed(2)}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>$${total.toFixed(2)}</span>
                        </div>
                        <div style="margin-top: 2rem;">
                            <a href="checkout.php" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                                <i class="fas fa-lock"></i> Proceed to Checkout
                            </a>
                            <a href="products.php" class="btn btn-secondary" style="width: 100%;">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        </div>
                        <div style="margin-top: 2rem; text-align: center;">
                            <button onclick="clearCart()" class="btn btn-danger" style="width: 100%;">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        function updateQuantity(productId, quantity) {
            const cartManager = window.cartManager;
            cartManager.updateQuantity(productId, quantity);
            cartManager.showNotification('Quantity updated', 'info');
            loadCartContent();
        }
        
        function removeFromCart(productId) {
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                const cartManager = window.cartManager;
                cartManager.removeFromCart(productId);
                cartManager.showNotification('Item removed from cart', 'info');
                loadCartContent();
            }
        }
        
        function clearCart() {
            if (confirm('Are you sure you want to clear your entire cart?')) {
                const cartManager = window.cartManager;
                cartManager.clearCart();
                cartManager.showNotification('Cart cleared', 'info');
                loadCartContent();
            }
        }
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
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