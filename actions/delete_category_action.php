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
$user_id = $_SESSION['id'];

// Validate input
if ($cat_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid category ID';
    echo json_encode($response);
    exit();
}

// Delete category
$result = delete_category_ctr($cat_id, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category deleted successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to delete category. You may not have permission to delete this category.';
}

echo json_encode($response);
