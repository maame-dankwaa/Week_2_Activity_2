<?php

header('Content-Type: application/json');

session_start();

$response = array();

// TODO: Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

// pull required fields (per DB schema/instructions)
$name         = isset($_POST['name']) ? trim($_POST['name']) : '';
$email        = isset($_POST['email']) ? trim($_POST['email']) : '';
$password     = isset($_POST['password']) ? $_POST['password'] : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$role         = isset($_POST['role']) ? (int)$_POST['role'] : 2;

// NEW (schema-required fields)
$country      = isset($_POST['country']) ? trim($_POST['country']) : '';
$city         = isset($_POST['city']) ? trim($_POST['city']) : '';

// optional (nullable in schema)
$image        = isset($_POST['image']) ? trim($_POST['image']) : null;

/*
 * Minimal validation to avoid empty inserts.
 * (Your register.js should also validate; controller will do uniqueness and hashing.)
 */
if ($name === '' || $email === '' || $password === '' || $phone_number === '' || $country === '' || $city === '') {
    echo json_encode([
        'status'  => 'error',
        'message' => 'All fields are required.'
    ]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Invalid email address.'
    ]);
    exit();
}

// keep your original call style; controller should expose this function
// adjust signature to include country, city, (and optional image)
$user_id = register_user_ctr($name, $email, $password, $phone_number, $role, $country, $city);

if ($user_id) {
    $response['status']  = 'success';
    $response['message'] = 'Registered successfully';
    $response['user_id'] = (int)$user_id;
} else {
    $response['status']  = 'error';
    $response['message'] = 'Failed to register';
}

echo json_encode($response);
