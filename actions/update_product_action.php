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
$product_cat = isset($_POST['product_cat']) ? (int)$_POST['product_cat'] : 0;
$product_brand = isset($_POST['product_brand']) ? (int)$_POST['product_brand'] : 0;
$product_title = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
$product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0;
$product_desc = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
$product_keywords = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';
$user_id = $_SESSION['id'];

// Validate input
if ($product_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit();
}

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

// Get current product to check for existing image
$current_product = get_product_ctr($product_id, $user_id);
if (!$current_product) {
    $response['status'] = 'error';
    $response['message'] = 'Product not found';
    echo json_encode($response);
    exit();
}

// Handle image upload
$product_image = $current_product['product_image']; // Keep existing image by default
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $upload_result = upload_product_image_ctr($_FILES['product_image'], $user_id, $product_id);
    if ($upload_result === false) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to upload image. Please check file type and size.';
        echo json_encode($response);
        exit();
    }
    
    // Delete old image if new one is uploaded
    if ($current_product['product_image']) {
        $product = new Product();
        $product->deleteImage($current_product['product_image']);
    }
    
    $product_image = $upload_result;
}

// Update product
$result = update_product_ctr($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Product updated successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to update product';
}

echo json_encode($response);
?>