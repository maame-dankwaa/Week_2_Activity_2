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

require_once '../controllers/category_controller.php';

$user_id = getUserID();

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
?>