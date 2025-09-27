<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    $response['status'] = 'error';
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit();
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admin privileges required.';
    echo json_encode($response);
    exit();
}

require_once '../controllers/category_controller.php';

// Get form data
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
$cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
$user_id = $_SESSION['id'];

// Validate input
if (empty($cat_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit();
}

if ($cat_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid category ID';
    echo json_encode($response);
    exit();
}

// Update category
$result = update_category_ctr($cat_id, $cat_name, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to update category. Category name may already exist or you may not have permission to update this category.';
}

echo json_encode($response);
