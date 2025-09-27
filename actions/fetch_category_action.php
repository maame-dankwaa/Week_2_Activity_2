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

$user_id = $_SESSION['id'];

// Fetch categories for the logged-in user
$categories = fetch_categories_ctr($user_id);

if ($categories !== false) {
    $response['status'] = 'success';
    $response['message'] = 'Categories fetched successfully';
    $response['data'] = $categories;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to fetch categories';
    $response['data'] = array();
}

echo json_encode($response);
