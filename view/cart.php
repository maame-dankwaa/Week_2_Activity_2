<?php
require_once '../settings/core.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Taste of Africa</title>
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

        .cart-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .cart-item-image {
            max-width: 100px;
            border-radius: 8px;
        }

        .total-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-continue {
            background: #6c757d;
            color: white;
            border: none;
        }

        .btn-continue:hover {
            background: #5a6268;
            color: white;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-empty {
            background: #dc3545;
            color: white;
            border: none;
        }

        .btn-empty:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav-page">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="../view/all_product.php"><i class="fas fa-shopping-bag"></i> Shop</a>
                <?php if (isUserLoggedIn()): ?>
                    <a href="../login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="../login/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <?php endif; ?>
            </div>
            <h1><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>
            <p>Review your items before checkout</p>
        </div>
    </div>

    <div class="container">
        <div class="cart-container">
            <!-- Cart Items Container (populated by JavaScript) -->
            <div id="cart-items-container">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">Loading cart...</p>
                </div>
            </div>

            <!-- Total Section -->
            <div class="total-section" id="total-section">
                <div class="row">
                    <div class="col-md-8">
                        <h5>Cart Total</h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <h4 class="text-primary">
                            <span id="cart-total">$0.00</span>
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn btn-continue" onclick="window.location.href='../view/all_product.php'">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </button>
                <button class="btn btn-empty" id="empty-cart-button" onclick="emptyCart()" style="display: none;">
                    <i class="fas fa-trash-alt"></i> Empty Cart
                </button>
                <button class="btn btn-checkout" id="checkout-button" onclick="window.location.href='../view/checkout.php'" style="display: none;">
                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/cart.js"></script>
</body>
</html>
