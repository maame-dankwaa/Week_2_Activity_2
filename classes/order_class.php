<?php
/**
 * Order Class
 * Handles all order-related database operations
 * Manages orders, order details, and payment records
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Order extends db_connection {

    public function __construct() {
        $this->db_connect();
    }

    /**
     * Create a new order in the orders table
     *
     * @param int $customer_id Customer ID
     * @param int $invoice_no Invoice number
     * @param string $order_date Order date (Y-m-d format)
     * @param string $order_status Order status (default: 'pending')
     * @return int|false Order ID if successful, false otherwise
     */
    public function createOrder($customer_id, $invoice_no, $order_date, $order_status = 'pending') {
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db_conn()->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iiss", $customer_id, $invoice_no, $order_date, $order_status);
        $result = $stmt->execute();

        if ($result) {
            $order_id = $stmt->insert_id;
            $stmt->close();
            return $order_id;
        }

        $stmt->close();
        return false;
    }

    /**
     * Add order details (product information) to orderdetails table
     *
     * @param int $order_id Order ID
     * @param int $product_id Product ID
     * @param int $quantity Quantity ordered
     * @return bool Success status
     */
    public function addOrderDetails($order_id, $product_id, $quantity) {
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty)
                VALUES (?, ?, ?)";
        $stmt = $this->db_conn()->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iii", $order_id, $product_id, $quantity);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Record a payment in the payment table
     *
     * @param float $amount Payment amount
     * @param int $customer_id Customer ID
     * @param int $order_id Order ID
     * @param string $currency Currency code (e.g., 'USD', 'GHS')
     * @param string $payment_date Payment date (Y-m-d format)
     * @return int|false Payment ID if successful, false otherwise
     */
    public function recordPayment($amount, $customer_id, $order_id, $currency, $payment_date) {
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db_conn()->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("diiss", $amount, $customer_id, $order_id, $currency, $payment_date);
        $result = $stmt->execute();

        if ($result) {
            $payment_id = $stmt->insert_id;
            $stmt->close();
            return $payment_id;
        }

        $stmt->close();
        return false;
    }

    /**
     * Retrieve all orders for a specific customer
     *
     * @param int $customer_id Customer ID
     * @return array Array of orders
     */
    public function getCustomerOrders($customer_id) {
        $sql = "SELECT o.*, p.amt as payment_amount, p.currency
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC, o.order_id DESC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $orders ? $orders : [];
    }

    /**
     * Get details of a specific order
     *
     * @param int $order_id Order ID
     * @return array|false Order details if found, false otherwise
     */
    public function getOrderById($order_id) {
        $sql = "SELECT o.*, p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.order_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();

        return $order ? $order : false;
    }

    /**
     * Get all items in a specific order
     *
     * @param int $order_id Order ID
     * @return array Array of order items with product details
     */
    public function getOrderItems($order_id) {
        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image
                FROM orderdetails od
                INNER JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = ?
                ORDER BY p.product_title ASC";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items ? $items : [];
    }

    /**
     * Update order status
     *
     * @param int $order_id Order ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateOrderStatus($order_id, $status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("si", $status, $order_id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Generate a unique invoice number
     *
     * @return int Unique invoice number
     */
    public function generateInvoiceNumber() {
        // Generate invoice number using timestamp and random number
        return (int)(time() . rand(100, 999));
    }

    /**
     * Generate a unique order reference
     *
     * @return string Unique order reference
     */
    public function generateOrderReference() {
        // Generate format: ORD-YYYYMMDD-XXXXXX (where X is random)
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    }

    /**
     * Get all orders (Admin function)
     *
     * @param int $limit Limit number of results
     * @param int $offset Offset for pagination
     * @return array Array of all orders
     */
    public function getAllOrders($limit = 50, $offset = 0) {
        $sql = "SELECT o.*, c.customer_name, c.customer_email, p.amt as payment_amount
                FROM orders o
                INNER JOIN customer c ON o.customer_id = c.customer_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                ORDER BY o.order_date DESC, o.order_id DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db_conn()->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $orders ? $orders : [];
    }

    /**
     * Get total number of orders
     *
     * @return int Total order count
     */
    public function getTotalOrdersCount() {
        $sql = "SELECT COUNT(*) as total FROM orders";
        $result = $this->db_conn()->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ? (int)$row['total'] : 0;
    }

    /**
     * Get total revenue
     *
     * @return float Total revenue
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(amt) as total FROM payment";
        $result = $this->db_conn()->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ? (float)$row['total'] : 0.0;
    }
}
?>
