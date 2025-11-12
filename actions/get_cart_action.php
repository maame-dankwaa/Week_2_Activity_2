<?php
/**
 * Get Cart Action
 * Retrieves all cart items for the current user
 * Returns JSON response with cart items and totals
 */

session_start();
require_once(dirname(__FILE__).'/../controllers/cart_controller.php');
require_once(dirname(__FILE__).'/../settings/core.php');

header('Content-Type: application/json');

// Check if request is GET or POST
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Get user identification
$customer_id = getUserID();
$ip_address = $_SERVER['REMOTE_ADDR'];

// Get cart items
$cart_items = get_user_cart_ctr($customer_id, $ip_address);
$cart_count = get_cart_count_ctr($customer_id, $ip_address);
$cart_total = get_cart_total_ctr($customer_id, $ip_address);

// Calculate subtotals for each item
foreach ($cart_items as &$item) {
    $item['subtotal'] = $item['qty'] * $item['product_price'];
}

echo json_encode([
    'status' => 'success',
    'cart_items' => $cart_items,
    'cart_count' => $cart_count,
    'cart_total' => $cart_total,
    'cart_total_formatted' => number_format($cart_total, 2)
]);
?>
