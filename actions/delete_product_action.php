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

// Get form data
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$user_id = getUserID();

// Validate input
if ($product_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit();
}

// Delete product
$result = delete_product_ctr($product_id, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Product deleted successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to delete product';
}

echo json_encode($response);
?>
