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
$cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
$user_id = $_SESSION['id'];

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
