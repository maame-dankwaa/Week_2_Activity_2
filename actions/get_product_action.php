<?php
require_once '../settings/core.php';

header('Content-Type: application/json');


$response = array();

// Check if user is logged in
if (!isUserLoggedIn()) {
    $response['status'] = 'error';
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admin privileges required.';
    echo json_encode($response);
    exit();
}

require_once '../controllers/product_controller.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$user_id = getUserID();

// Validate input
if ($product_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit();
}

// Get product
$product = get_product_ctr($product_id, $user_id);

if ($product !== false) {
    $response['status'] = 'success';
    $response['data'] = $product;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Product not found';
}

echo json_encode($response);
?>
