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

$user_id = getUserID();
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    $response['status'] = 'error';
    $response['message'] = 'No file uploaded or upload error occurred';
    echo json_encode($response);
    exit();
}

// Verify uploads directory exists and is accessible
$uploads_dir = '../uploads/';
if (!file_exists($uploads_dir) || !is_writable($uploads_dir)) {
    $response['status'] = 'error';
    $response['message'] = 'Upload directory not accessible';
    echo json_encode($response);
    exit();
}

// Upload the image
$upload_result = upload_product_image_ctr($_FILES['product_image'], $user_id, $product_id);

if ($upload_result !== false) {
    $response['status'] = 'success';
    $response['message'] = 'Image uploaded successfully';
    $response['image_path'] = $upload_result;
    $response['full_url'] = 'uploads/' . $upload_result;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to upload image. Please check file type and size.';
}

echo json_encode($response);
?>