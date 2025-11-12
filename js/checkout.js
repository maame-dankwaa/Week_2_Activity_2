/**
 * Checkout JavaScript
 * Handles checkout workflow and simulated payment
 */

// Load checkout summary
function loadCheckoutSummary() {
    const summaryContainer = document.getElementById('checkout-summary');
    const totalElement = document.getElementById('checkout-total');
    const paymentButton = document.getElementById('simulate-payment-btn');

    if (!summaryContainer) return;

    // Show loading state
    summaryContainer.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';

    // Fetch cart items
    fetch('../actions/get_cart_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.cart_items.length === 0) {
                    // Empty cart - redirect to cart page
                    window.location.href = '../view/cart.php';
                    return;
                }

                // Display checkout summary
                let html = '<div class="list-group mb-3">';
                data.cart_items.forEach(item => {
                    const subtotal = (item.product_price * item.qty).toFixed(2);
                    const imagePath = item.product_image ? `../uploads/${item.product_image}` : '../uploads/default-product.png';

                    html += `
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-2">
                                    <img src="${imagePath}" class="img-fluid rounded" alt="${item.product_title}">
                                </div>
                                <div class="col-5">
                                    <h6 class="mb-0">${item.product_title}</h6>
                                    <small class="text-muted">Quantity: ${item.qty}</small>
                                </div>
                                <div class="col-3 text-end">
                                    <span class="text-muted">$${parseFloat(item.product_price).toFixed(2)} × ${item.qty}</span>
                                </div>
                                <div class="col-2 text-end">
                                    <strong>$${subtotal}</strong>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';

                summaryContainer.innerHTML = html;

                // Update total
                if (totalElement) {
                    totalElement.innerHTML = `
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$${data.cart_total_formatted}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (0%):</span>
                            <span>$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-primary">$${data.cart_total_formatted}</strong>
                        </div>
                    `;
                }

                // Enable payment button
                if (paymentButton) {
                    paymentButton.disabled = false;
                }
            } else {
                summaryContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            summaryContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Failed to load checkout summary. Please try again.
                </div>
            `;
        });
}

// Show payment modal
function showPaymentModal() {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

// Process checkout (after payment confirmation)
function processCheckout(currency = 'USD') {
    // Get modal and button references
    const confirmButton = document.getElementById('confirm-payment-btn');
    const cancelButton = document.getElementById('cancel-payment-btn');
    const modalBody = document.querySelector('#paymentModal .modal-body');

    // Disable buttons and show processing state
    confirmButton.disabled = true;
    cancelButton.disabled = true;
    const originalText = confirmButton.innerHTML;
    confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    // Send checkout request
    fetch('../actions/process_checkout_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `currency=${currency}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success in modal
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                    <h4>Payment Successful!</h4>
                    <p class="text-muted mb-3">Your order has been placed successfully.</p>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-2"><strong>Order Reference:</strong> ${data.data.order_reference}</p>
                            <p class="mb-2"><strong>Invoice Number:</strong> ${data.data.invoice_no}</p>
                            <p class="mb-2"><strong>Total Amount:</strong> ${data.data.currency} $${data.data.total_amount}</p>
                            <p class="mb-0"><strong>Items:</strong> ${data.data.items_count}</p>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-3" onclick="window.location.href='../view/all_product.php'">
                        Continue Shopping
                    </button>
                </div>
            `;

            // Update cart count
            updateCartCount(0);

            // Hide modal footer buttons
            document.querySelector('#paymentModal .modal-footer').style.display = 'none';

        } else {
            // Show error in modal
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-times-circle fa-5x text-danger mb-3"></i>
                    <h4>Payment Failed</h4>
                    <p class="text-danger">${data.message}</p>
                    <button class="btn btn-secondary mt-3" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            `;

            // Hide modal footer buttons
            document.querySelector('#paymentModal .modal-footer').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);

        // Show error in modal
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-3"></i>
                <h4>Connection Error</h4>
                <p class="text-muted">Unable to process payment. Please try again.</p>
                <button class="btn btn-secondary mt-3" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        `;

        // Hide modal footer buttons
        document.querySelector('#paymentModal .modal-footer').style.display = 'none';
    });
}

// Handle modal reset when closed
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        paymentModal.addEventListener('hidden.bs.modal', function() {
            // Reset modal content
            const modalBody = paymentModal.querySelector('.modal-body');
            const modalFooter = paymentModal.querySelector('.modal-footer');
            const confirmButton = document.getElementById('confirm-payment-btn');

            // Restore original modal content
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-credit-card fa-5x text-primary mb-3"></i>
                    <h5>Simulated Payment</h5>
                    <p class="text-muted">This is a demo payment. No actual transaction will occur.</p>
                    <p>Click "Confirm Payment" to simulate a successful payment.</p>
                </div>
            `;

            // Restore footer
            modalFooter.style.display = 'flex';
            if (confirmButton) {
                confirmButton.disabled = false;
                confirmButton.innerHTML = '<i class="fas fa-check"></i> Confirm Payment';
            }
        });
    }

    // Load checkout summary if on checkout page
    if (document.getElementById('checkout-summary')) {
        loadCheckoutSummary();
    }
});

// Update cart count in navbar (shared with cart.js)
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
