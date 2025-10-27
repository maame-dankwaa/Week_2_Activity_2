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

$user_id = getUserID();

// Get products grouped by category and brand
$products = get_products_grouped_ctr($user_id);

if ($products !== false) {
    $response['status'] = 'success';
    $response['data'] = $products;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to fetch products';
}

echo json_encode($response);
?>
