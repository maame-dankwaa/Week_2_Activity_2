<?php
require_once '../settings/core.php';

// Redirect to login if not logged in
if (!isUserLoggedIn()) {
    header('Location: ../login/login.php?redirect=checkout');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 0;
            margin-bottom: 30px;
            color: white;
            text-align: center;
            position: relative;
        }

        .nav-page {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .nav-page a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid white;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            margin-left: 10px;
        }

        .nav-page a:hover {
            background: white;
            color: #667eea;
        }

        .checkout-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .checkout-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .checkout-step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .checkout-step .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .checkout-step.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .total-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .btn-payment {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            width: 100%;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .list-group-item img {
            max-width: 80px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav-page">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="../view/all_product.php"><i class="fas fa-shopping-bag"></i> Shop</a>
                <a href="../view/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            <h1><i class="fas fa-credit-card me-2"></i>Checkout</h1>
            <p>Complete your purchase</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-container">
                    <div class="checkout-steps">
                        <div class="checkout-step active">
                            <div class="step-number">1</div>
                            <small>Cart Review</small>
                        </div>
                        <div class="checkout-step active">
                            <div class="step-number">2</div>
                            <small>Payment</small>
                        </div>
                        <div class="checkout-step">
                            <div class="step-number">3</div>
                            <small>Confirmation</small>
                        </div>
                    </div>

                    <h4 class="mb-3"><i class="fas fa-list-alt"></i> Order Summary</h4>

                    <!-- Order Summary Container (populated by JavaScript) -->
                    <div id="checkout-summary">
                        <div class="text-center py-5">
                            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                            <p class="mt-3">Loading order summary...</p>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-secondary" onclick="window.location.href='../view/cart.php'">
                            <i class="fas fa-arrow-left"></i> Back to Cart
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="checkout-container">
                    <h5 class="mb-3"><i class="fas fa-receipt"></i> Order Total</h5>

                    <div id="checkout-total">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>$0.00</span>
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
                            <strong class="text-primary">$0.00</strong>
                        </div>
                    </div>

                    <hr>

                    <div class="total-box text-center">
                        <h3 class="mb-0" id="final-total">$0.00</h3>
                        <small>Total Amount</small>
                    </div>

                    <button class="btn btn-payment" id="simulate-payment-btn" onclick="showPaymentModal()" disabled>
                        <i class="fas fa-lock"></i> Simulate Payment
                    </button>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <small>This is a simulated payment. No actual transaction will occur.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-credit-card"></i> Payment Confirmation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-4">
                        <i class="fas fa-credit-card fa-5x text-primary mb-3"></i>
                        <h5>Simulated Payment</h5>
                        <p class="text-muted">This is a demo payment. No actual transaction will occur.</p>
                        <p>Click "Confirm Payment" to simulate a successful payment.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancel-payment-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-payment-btn" onclick="processCheckout('USD')">
                        <i class="fas fa-check"></i> Confirm Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/cart.js"></script>
    <script src="../js/checkout.js"></script>
</body>
</html>
