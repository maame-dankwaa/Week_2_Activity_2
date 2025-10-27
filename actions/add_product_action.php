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
$product_cat = isset($_POST['product_cat']) ? (int)$_POST['product_cat'] : 0;
$product_brand = isset($_POST['product_brand']) ? (int)$_POST['product_brand'] : 0;
$product_title = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
$product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
$product_desc = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
$product_keywords = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';
$user_id = getUserID();

// Validate input
if ($product_cat <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Please select a valid category';
    echo json_encode($response);
    exit();
}

if ($product_brand <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Please select a valid brand';
    echo json_encode($response);
    exit();
}

if (empty($product_title)) {
    $response['status'] = 'error';
    $response['message'] = 'Product title is required';
    echo json_encode($response);
    exit();
}

if ($product_price <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Product price must be greater than 0';
    echo json_encode($response);
    exit();
}

// Add product first to get product_id
$result = add_product_ctr($product_cat, $product_brand, $product_title, $product_price, $product_desc, '', $product_keywords, $user_id);

if (!$result) {
    $response['status'] = 'error';
    $response['message'] = 'Failed to create product';
    echo json_encode($response);
    exit();
}

$product_id = $result;

// Handle image upload after product creation
$product_image = '';
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $upload_result = upload_product_image_ctr($_FILES['product_image'], $user_id, $product_id);
    if ($upload_result !== false) {
        $product_image = $upload_result;
        
        // Update product with image path
        $product = new Product();
        $product->edit($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);
    }
}

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Product created successfully';
    $response['product_id'] = $result;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to create product';
}

echo json_encode($response);
?>