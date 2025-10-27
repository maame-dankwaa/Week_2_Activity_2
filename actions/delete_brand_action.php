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

require_once '../controllers/brand_controller.php';

// Get form data
$brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$user_id = getUserID();

// Validate input
if ($brand_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid brand ID';
    echo json_encode($response);
    exit();
}

// Delete brand
$result = delete_brand_ctr($brand_id, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Brand deleted successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to delete brand';
}

echo json_encode($response);
?>