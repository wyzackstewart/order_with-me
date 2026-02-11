// script.js - Main JavaScript File

// Cart Management
class CartManager {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('cart')) || [];
        this.updateCartCount();
    }
    
    addToCart(productId, name, price, image = null) {
        const existingItem = this.cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.cart.push({ 
                id: productId, 
                name: name, 
                price: price, 
                quantity: 1,
                image: image
            });
        }
        
        this.saveCart();
        this.updateCartCount();
        this.showNotification('Added to cart!');
        return this.cart;
    }
    
    removeFromCart(productId) {
        this.cart = this.cart.filter(item => item.id !== productId);
        this.saveCart();
        this.updateCartCount();
        this.showNotification('Removed from cart');
        return this.cart;
    }
    
    updateQuantity(productId, quantity) {
        const item = this.cart.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(quantity) || 1;
            this.saveCart();
            this.updateCartCount();
        }
        return this.cart;
    }
    
    clearCart() {
        this.cart = [];
        this.saveCart();
        this.updateCartCount();
    }
    
    getCart() {
        return this.cart;
    }
    
    getTotalItems() {
        return this.cart.reduce((sum, item) => sum + item.quantity, 0);
    }
    
    getTotalPrice() {
        return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }
    
    saveCart() {
        localStorage.setItem('cart', JSON.stringify(this.cart));
    }
    
    updateCartCount() {
        const countElements = document.querySelectorAll('.cart-count');
        const total = this.getTotalItems();
        countElements.forEach(el => {
            el.textContent = total;
        });
    }
    
    showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 4px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize cart manager
const cartManager = new CartManager();

// Product Search and Filter
class ProductFilter {
    constructor() {
        this.searchInput = document.getElementById('search-input');
        this.categoryFilter = document.getElementById('category-filter');
        this.priceFilter = document.getElementById('price-filter');
        this.productCards = document.querySelectorAll('.product-card');
        
        this.init();
    }
    
    init() {
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => this.filterProducts());
        }
        
        if (this.categoryFilter) {
            this.categoryFilter.addEventListener('change', () => this.filterProducts());
        }
        
        if (this.priceFilter) {
            this.priceFilter.addEventListener('change', () => this.filterProducts());
        }
    }
    
    filterProducts() {
        const searchTerm = this.searchInput ? this.searchInput.value.toLowerCase() : '';
        const selectedCategory = this.categoryFilter ? this.categoryFilter.value : '';
        const selectedPrice = this.priceFilter ? this.priceFilter.value : '';
        
        this.productCards.forEach(card => {
            const productName = card.querySelector('.product-title').textContent.toLowerCase();
            const description = card.querySelector('.product-description').textContent.toLowerCase();
            const category = card.getAttribute('data-category');
            const price = parseFloat(card.getAttribute('data-price'));
            
            let visible = true;
            
            // Search filter
            if (searchTerm && !productName.includes(searchTerm) && !description.includes(searchTerm)) {
                visible = false;
            }
            
            // Category filter
            if (selectedCategory && category !== selectedCategory) {
                visible = false;
            }
            
            // Price filter
            if (selectedPrice) {
                switch(selectedPrice) {
                    case 'under50':
                        if (price >= 50) visible = false;
                        break;
                    case '50-100':
                        if (price < 50 || price > 100) visible = false;
                        break;
                    case '100-500':
                        if (price < 100 || price > 500) visible = false;
                        break;
                    case 'over500':
                        if (price <= 500) visible = false;
                        break;
                }
            }
            
            card.style.display = visible ? 'block' : 'none';
        });
    }
}

// Form Validation
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.validateForm(e));
        
        // Add real-time validation
        const inputs = this.form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
        });
    }
    
    validateField(input) {
        const errorElement = input.nextElementSibling?.classList.contains('form-error') 
            ? input.nextElementSibling 
            : null;
        
        if (!input.value.trim()) {
            this.showError(input, 'This field is required', errorElement);
            return false;
        }
        
        if (input.type === 'email' && !this.isValidEmail(input.value)) {
            this.showError(input, 'Please enter a valid email', errorElement);
            return false;
        }
        
        if (input.type === 'password' && input.value.length < 6) {
            this.showError(input, 'Password must be at least 6 characters', errorElement);
            return false;
        }
        
        this.clearError(input, errorElement);
        return true;
    }
    
    validateForm(e) {
        let isValid = true;
        const requiredFields = this.form.querySelectorAll('input[required], textarea[required]');
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        // Special validation for password confirmation
        const password = this.form.querySelector('input[name="password"]');
        const confirmPassword = this.form.querySelector('input[name="confirm_password"]');
        
        if (password && confirmPassword && password.value !== confirmPassword.value) {
            this.showError(confirmPassword, 'Passwords do not match');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            this.showNotification('Please fix the errors in the form');
        }
    }
    
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    showError(input, message, errorElement = null) {
        input.style.borderColor = '#e74c3c';
        
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            input.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    
    clearError(input, errorElement = null) {
        input.style.borderColor = '#ddd';
        
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }
    
    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-danger';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        `;
        
        this.form.parentNode.insertBefore(notification, this.form);
        
        setTimeout(() => notification.remove(), 5000);
    }
}

// Order Processing
class OrderProcessor {
    constructor() {
        this.checkoutForm = document.getElementById('checkout-form');
        if (this.checkoutForm) {
            this.init();
        }
    }
    
    init() {
        this.checkoutForm.addEventListener('submit', (e) => this.processOrder(e));
        this.updateOrderSummary();
    }
    
    updateOrderSummary() {
        const cart = cartManager.getCart();
        const summaryElement = document.getElementById('order-summary');
        const totalElement = document.getElementById('order-total');
        
        if (summaryElement) {
            let html = '<h3>Order Summary</h3>';
            
            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                html += `
                    <div class="order-summary-item">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>$${subtotal.toFixed(2)}</span>
                    </div>
                `;
            });
            
            html += `<hr><div class="order-summary-total">
                <strong>Total:</strong>
                <strong>$${cartManager.getTotalPrice().toFixed(2)}</strong>
            </div>`;
            
            summaryElement.innerHTML = html;
        }
        
        if (totalElement) {
            totalElement.textContent = cartManager.getTotalPrice().toFixed(2);
        }
    }
    
    processOrder(e) {
        e.preventDefault();
        
        const cart = cartManager.getCart();
        if (cart.length === 0) {
            alert('Your cart is empty');
            return;
        }
        
        // Validate form
        const requiredFields = this.checkoutForm.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#e74c3c';
                isValid = false;
            }
        });
        
        if (!isValid) {
            alert('Please fill all required fields');
            return;
        }
        
        // Collect form data
        const formData = new FormData(this.checkoutForm);
        const orderData = {
            cart: cart,
            shipping: Object.fromEntries(formData)
        };
        
        // Submit order via AJAX (you'll need to implement the PHP endpoint)
        this.submitOrder(orderData);
    }
    
    async submitOrder(orderData) {
        try {
            const response = await fetch('api/place_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                cartManager.clearCart();
                window.location.href = 'order_success.php?id=' + result.order_id;
            } else {
                alert('Order failed: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart manager
    window.cartManager = cartManager;
    
    // Initialize product filter if on products page
    if (document.querySelector('.product-card')) {
        new ProductFilter();
    }
    
    // Initialize form validators
    if (document.getElementById('register-form')) {
        new FormValidator('register-form');
    }
    
    if (document.getElementById('login-form')) {
        new FormValidator('login-form');
    }
    
    if (document.getElementById('checkout-form')) {
        new OrderProcessor();
    }
    
    // Add to cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-to-cart') || e.target.closest('.add-to-cart')) {
            const button = e.target.classList.contains('add-to-cart') ? e.target : e.target.closest('.add-to-cart');
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const productPrice = parseFloat(button.dataset.productPrice);
            const productImage = button.dataset.productImage || null;
            
            cartManager.addToCart(productId, productName, productPrice, productImage);
        }
    });
    
    // Quantity controls in cart
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const productId = e.target.dataset.productId;
            const quantity = e.target.value;
            cartManager.updateQuantity(productId, quantity);
            
            // Update cart display if on cart page
            if (window.location.pathname.includes('cart.php')) {
                location.reload(); // Or use AJAX to update without reload
            }
        }
    });
    
    // Remove from cart buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-from-cart') || e.target.closest('.remove-from-cart')) {
            const button = e.target.classList.contains('remove-from-cart') ? e.target : e.target.closest('.remove-from-cart');
            const productId = button.dataset.productId;
            
            if (confirm('Remove this item from cart?')) {
                cartManager.removeFromCart(productId);
                
                // Update cart display if on cart page
                if (window.location.pathname.includes('cart.php')) {
                    location.reload(); // Or use AJAX to update without reload
                }
            }
        }
    });
    
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', function() {
            navLinks.classList.toggle('show');
        });
    }
});

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @media (max-width: 768px) {
        .nav-links {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #2c3e50;
            padding: 1rem;
        }
        
        .nav-links.show {
            display: flex;
        }
        
        #mobile-menu-btn {
            display: block;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
    }
`;
document.head.appendChild(style);