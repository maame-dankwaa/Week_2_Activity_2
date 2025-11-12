<?php
/**
 * Empty Cart Action
 * Removes all items from the cart
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

// Get user identification
$customer_id = getUserID();
$ip_address = $_SERVER['REMOTE_ADDR'];

// Empty the cart
$result = empty_cart_ctr($customer_id, $ip_address);

if ($result !== false) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cart emptied successfully!',
        'cart_count' => 0,
        'cart_total' => '0.00'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to empty cart. Please try again.'
    ]);
}
?>
