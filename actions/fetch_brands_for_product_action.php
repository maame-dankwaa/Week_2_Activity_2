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

// Get brands for dropdown
$brands = get_brands_for_product_ctr($user_id);

if ($brands !== false) {
    $response['status'] = 'success';
    $response['data'] = $brands;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to fetch brands';
}

echo json_encode($response);
?>
