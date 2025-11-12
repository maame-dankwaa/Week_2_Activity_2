<?php
/**
 * Remove from Cart Action
 * Removes an item completely from the cart
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

// Validate product ID
if ($product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

// Get user identification
$customer_id = getUserID();
$ip_address = $_SERVER['REMOTE_ADDR'];

// Remove from cart
$result = remove_from_cart_ctr($product_id, $customer_id, $ip_address);

if ($result) {
    // Get updated cart count and total
    $cart_count = get_cart_count_ctr($customer_id, $ip_address);
    $cart_total = get_cart_total_ctr($customer_id, $ip_address);

    echo json_encode([
        'status' => 'success',
        'message' => 'Item removed from cart successfully!',
        'cart_count' => $cart_count,
        'cart_total' => number_format($cart_total, 2)
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to remove item from cart. Please try again.'
    ]);
}
?>
