<?php

header('Content-Type: application/json');

session_start();

$response = array();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

// Get form data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate required fields
if (empty($email) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Prepare data for controller
$login_data = [
    'email' => $email,
    'password' => $password
];

// Call the controller function
$result = login_customer_ctr($login_data);

if ($result['status'] === 'success') {
    // Set session variables
    $customer_data = $result['customer_data'];
    
    $_SESSION['user_id'] = $customer_data['customer_id'];
    $_SESSION['user_name'] = $customer_data['customer_name'];
    $_SESSION['user_email'] = $customer_data['customer_email'];
    $_SESSION['user_role'] = $customer_data['user_role'];
    $_SESSION['user_phone'] = $customer_data['customer_contact'];
    $_SESSION['user_country'] = $customer_data['customer_country'];
    $_SESSION['user_city'] = $customer_data['customer_city'];
    $_SESSION['user_image'] = $customer_data['customer_image'];
    $_SESSION['login_time'] = time();
    
    // Set session timeout (24 hours)
    $_SESSION['session_timeout'] = time() + (24 * 60 * 60);
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    $response['redirect_url'] = '../index.php';
} else {
    $response['status'] = 'error';
    $response['message'] = $result['message'];
}

echo json_encode($response);
