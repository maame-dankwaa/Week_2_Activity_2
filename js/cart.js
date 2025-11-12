/**
 * Cart JavaScript
 * Handles all dynamic cart interactions
 * - Add to cart
 * - Update quantity
 * - Remove items
 * - Empty cart
 * - Update cart UI
 */

// Add product to cart
function addToCart(productId, quantity = 1) {
    // Validate inputs
    if (!productId || productId <= 0) {
        showMessage('Invalid product ID', 'error');
        return;
    }

    // Show loading state
    const addButton = event.target;
    const originalText = addButton.innerHTML;
    addButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    addButton.disabled = true;

    // Send AJAX request
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            updateCartCount(data.cart_count);
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        addButton.innerHTML = originalText;
        addButton.disabled = false;
    });
}

// Update cart item quantity
function updateQuantity(productId, quantity) {
    // Validate inputs
    if (!productId || productId <= 0) {
        showMessage('Invalid product ID', 'error');
        return;
    }

    if (quantity < 0) {
        showMessage('Invalid quantity', 'error');
        return;
    }

    // Send AJAX request
    fetch('../actions/update_quantity_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            updateCartCount(data.cart_count);

            // Reload cart view if on cart page
            if (typeof loadCartItems === 'function') {
                loadCartItems();
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

// Remove item from cart
function removeFromCart(productId) {
    // Confirm removal
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    // Send AJAX request
    fetch('../actions/remove_from_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            updateCartCount(data.cart_count);

            // Reload cart view if on cart page
            if (typeof loadCartItems === 'function') {
                loadCartItems();
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

// Empty entire cart
function emptyCart() {
    // Confirm action
    if (!confirm('Are you sure you want to empty your entire cart? This action cannot be undone.')) {
        return;
    }

    // Send AJAX request
    fetch('../actions/empty_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showMessage(data.message, 'success');
            updateCartCount(0);

            // Reload cart view if on cart page
            if (typeof loadCartItems === 'function') {
                loadCartItems();
            }
        } else {
            showMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
    });
}

// Load cart items (for cart.php page)
function loadCartItems() {
    const cartContainer = document.getElementById('cart-items-container');
    const cartTotal = document.getElementById('cart-total');
    const checkoutButton = document.getElementById('checkout-button');
    const emptyCartButton = document.getElementById('empty-cart-button');

    if (!cartContainer) return;

    // Show loading state
    cartContainer.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';

    // Fetch cart items
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.cart_items.length === 0) {
                    // Empty cart
                    cartContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                            <h4>Your cart is empty</h4>
                            <p class="text-muted">Add some products to get started!</p>
                            <a href="../view/all_product.php" class="btn btn-primary mt-3">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        </div>
                    `;
                    if (checkoutButton) checkoutButton.style.display = 'none';
                    if (emptyCartButton) emptyCartButton.style.display = 'none';
                    if (cartTotal) cartTotal.innerHTML = '$0.00';
                } else {
                    // Display cart items
                    let html = '';
                    data.cart_items.forEach(item => {
                        const subtotal = (item.product_price * item.qty).toFixed(2);
                        const imagePath = item.product_image ? `../uploads/${item.product_image}` : '../uploads/default-product.png';

                        html += `
                            <div class="card mb-3" id="cart-item-${item.p_id}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <img src="${imagePath}" class="img-fluid rounded" alt="${item.product_title}">
                                        </div>
                                        <div class="col-md-3">
                                            <h5 class="mb-0">${item.product_title}</h5>
                                            <small class="text-muted">${item.product_desc ? item.product_desc.substring(0, 60) + '...' : ''}</small>
                                        </div>
                                        <div class="col-md-2">
                                            <p class="mb-0"><strong>$${parseFloat(item.product_price).toFixed(2)}</strong></p>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    onclick="updateQuantity(${item.p_id}, ${item.qty - 1})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control form-control-sm text-center"
                                                    value="${item.qty}" min="1"
                                                    onchange="updateQuantity(${item.p_id}, this.value)"
                                                    style="max-width: 60px;">
                                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                                    onclick="updateQuantity(${item.p_id}, ${item.qty + 1})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <p class="mb-0"><strong>$${subtotal}</strong></p>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.p_id})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    cartContainer.innerHTML = html;
                    if (cartTotal) cartTotal.innerHTML = '$' + data.cart_total_formatted;
                    if (checkoutButton) checkoutButton.style.display = 'inline-block';
                    if (emptyCartButton) emptyCartButton.style.display = 'inline-block';
                }

                updateCartCount(data.cart_count);
            } else {
                cartContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            cartContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Failed to load cart items. Please refresh the page.
                </div>
            `;
        });
}

// Update cart count in navbar
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count;
        if (count > 0) {
            element.style.display = 'inline-block';
        } else {
            element.style.display = 'none';
        }
    });
}

// Show message to user
function showMessage(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to body
    document.body.appendChild(alertDiv);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 3000);
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load cart count
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                updateCartCount(data.cart_count);
            }
        })
        .catch(error => console.error('Error loading cart count:', error));

    // Load cart items if on cart page
    if (document.getElementById('cart-items-container')) {
        loadCartItems();
    }
});
