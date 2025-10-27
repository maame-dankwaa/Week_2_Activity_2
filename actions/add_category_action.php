<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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

require_once '../controllers/category_controller.php';

// Get form data
$cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
$user_id = getUserID();

// Validate input
if (empty($cat_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit();
}

// Add category
$result = add_category_ctr($cat_name, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category created successfully';
    $response['category_id'] = $result;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to create category. Category name may already exist.';
}

echo json_encode($response);
?>
