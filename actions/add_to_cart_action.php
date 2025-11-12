<?php
/**
 * Add to Cart Action
 * Processes Add to Cart requests from the frontend
 * Returns JSON response
 */

session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');
require_once(dirname(__FILE__).'/../settings/core.php');

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

// Get product ID from request
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Validate product ID
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

// Validate quantity
if ($quantity <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Quantity must be greater than zero.'
    ]);
    exit;
}

// Get user identification (logged-in user or guest IP)
$customer_id = getUserID(); // Returns null if not logged in
$ip_address = $_SERVER['REMOTE_ADDR'];

// Add to cart
$result = add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity);

if ($result) {
    // Get updated cart count
    $cart_count = get_cart_count_ctr($customer_id, $ip_address);

    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to cart successfully!',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to cart. Please try again.'
    ]);
}
?>
