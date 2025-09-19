<?php

require_once '../settings/db_class.php';

/**
 * Customer Class for handling customer-related operations
 */
class Customer extends db_connection
{
    private $customer_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;
    private $country;
    private $city;
    private $image;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
            $this->phone_number = $result['customer_contact'];
            $this->country = $result['customer_country'];
            $this->city = $result['customer_city'];
            $this->image = $result['customer_image'];
        }
    }

    /**
     * Get customer by email address
     * @param string $email
     * @return array|false
     */
    public function getCustomerByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result;
    }

    /**
     * Authenticate customer login
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticateCustomer($email, $password)
    {
        $customer = $this->getCustomerByEmail($email);
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            return $customer;
        }
        return false;
    }

    /**
     * Check if email exists
     * @param string $email
     * @return boolean
     */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT 1 FROM customer WHERE customer_email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return ($stmt->num_rows > 0);
    }

    /**
     * Create a new customer
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $phone_number
     * @param int $role
     * @param string $country
     * @param string $city
     * @param string $image
     * @return int|false
     */
    public function createCustomer($name, $email, $password, $phone_number, $role, $country, $city, $image = null)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, customer_image, user_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $name, $email, $hashed_password, $country, $city, $phone_number, $image, $role);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
}
