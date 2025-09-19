<?php

require_once '../classes/customer_class.php';

/**
 * Customer Controller for handling customer-related operations
 */

/**
 * Login customer controller function
 * @param array $kwargs - array containing email and password
 * @return array
 */
function login_customer_ctr($kwargs)
{
    $email = isset($kwargs['email']) ? trim($kwargs['email']) : '';
    $password = isset($kwargs['password']) ? $kwargs['password'] : '';

    // Validate input
    if (empty($email) || empty($password)) {
        return [
            'status' => 'error',
            'message' => 'Email and password are required'
        ];
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => 'Invalid email format'
        ];
    }

    try {
        // Create customer instance
        $customer = new Customer();
        
        // Authenticate customer
        $customer_data = $customer->authenticateCustomer($email, $password);
        
        if ($customer_data) {
            return [
                'status' => 'success',
                'message' => 'Login successful',
                'customer_data' => $customer_data
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid email or password'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'An error occurred during login. Please try again.'
        ];
    }
}

/**
 * Get customer by email controller function
 * @param string $email
 * @return array
 */
function get_customer_by_email_ctr($email)
{
    try {
        $customer = new Customer();
        $customer_data = $customer->getCustomerByEmail($email);
        
        if ($customer_data) {
            return [
                'status' => 'success',
                'customer_data' => $customer_data
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Customer not found'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'An error occurred while retrieving customer data'
        ];
    }
}

/**
 * Create customer controller function
 * @param array $kwargs - array containing customer data
 * @return array
 */
function create_customer_ctr($kwargs)
{
    $name = isset($kwargs['name']) ? trim($kwargs['name']) : '';
    $email = isset($kwargs['email']) ? trim($kwargs['email']) : '';
    $password = isset($kwargs['password']) ? $kwargs['password'] : '';
    $phone_number = isset($kwargs['phone_number']) ? trim($kwargs['phone_number']) : '';
    $role = isset($kwargs['role']) ? (int)$kwargs['role'] : 2;
    $country = isset($kwargs['country']) ? trim($kwargs['country']) : '';
    $city = isset($kwargs['city']) ? trim($kwargs['city']) : '';
    $image = isset($kwargs['image']) ? trim($kwargs['image']) : null;

    // Validate required fields
    if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($country) || empty($city)) {
        return [
            'status' => 'error',
            'message' => 'All required fields must be filled'
        ];
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => 'Invalid email format'
        ];
    }

    try {
        $customer = new Customer();
        
        // Check if email already exists
        if ($customer->emailExists($email)) {
            return [
                'status' => 'error',
                'message' => 'Email already exists'
            ];
        }
        
        // Create customer
        $customer_id = $customer->createCustomer($name, $email, $password, $phone_number, $role, $country, $city, $image);
        
        if ($customer_id) {
            return [
                'status' => 'success',
                'message' => 'Customer created successfully',
                'customer_id' => $customer_id
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create customer'
            ];
        }
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'An error occurred while creating customer'
        ];
    }
}
