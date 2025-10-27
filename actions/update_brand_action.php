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
$brand_name = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$user_id = getUserID();

// Validate input
if ($brand_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid brand ID';
    echo json_encode($response);
    exit();
}

if (empty($brand_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Brand name is required';
    echo json_encode($response);
    exit();
}

if ($category_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Please select a valid category';
    echo json_encode($response);
    exit();
}

// Update brand
$result = update_brand_ctr($brand_id, $brand_name, $category_id, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Brand updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to update brand. Brand name may already exist in this category.';
}

echo json_encode($response);
