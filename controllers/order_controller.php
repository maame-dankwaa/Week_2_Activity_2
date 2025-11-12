<?php
/**
 * Order Controller
 * Wrapper functions for order class methods
 * Used by action scripts to interact with order operations
 */

require_once(dirname(__FILE__).'/../classes/order_class.php');

/**
 * Create a new order
 *
 * @param int $customer_id Customer ID
 * @param int $invoice_no Invoice number
 * @param string $order_date Order date
 * @param string $order_status Order status
 * @return int|false Order ID if successful, false otherwise
 */
function create_order_ctr($customer_id, $invoice_no, $order_date, $order_status = 'pending') {
    $order = new Order();
    return $order->createOrder($customer_id, $invoice_no, $order_date, $order_status);
}

/**
 * Add order details
 *
 * @param int $order_id Order ID
 * @param int $product_id Product ID
 * @param int $quantity Quantity
 * @return bool Success status
 */
function add_order_details_ctr($order_id, $product_id, $quantity) {
    $order = new Order();
    return $order->addOrderDetails($order_id, $product_id, $quantity);
}

/**
 * Record a payment
 *
 * @param float $amount Payment amount
 * @param int $customer_id Customer ID
 * @param int $order_id Order ID
 * @param string $currency Currency code
 * @param string $payment_date Payment date
 * @return int|false Payment ID if successful, false otherwise
 */
function record_payment_ctr($amount, $customer_id, $order_id, $currency, $payment_date) {
    $order = new Order();
    return $order->recordPayment($amount, $customer_id, $order_id, $currency, $payment_date);
}

/**
 * Get all orders for a customer
 *
 * @param int $customer_id Customer ID
 * @return array Array of orders
 */
function get_customer_orders_ctr($customer_id) {
    $order = new Order();
    return $order->getCustomerOrders($customer_id);
}

/**
 * Get order by ID
 *
 * @param int $order_id Order ID
 * @return array|false Order details
 */
function get_order_by_id_ctr($order_id) {
    $order = new Order();
    return $order->getOrderById($order_id);
}

/**
 * Get all items in an order
 *
 * @param int $order_id Order ID
 * @return array Array of order items
 */
function get_order_items_ctr($order_id) {
    $order = new Order();
    return $order->getOrderItems($order_id);
}

/**
 * Update order status
 *
 * @param int $order_id Order ID
 * @param string $status New status
 * @return bool Success status
 */
function update_order_status_ctr($order_id, $status) {
    $order = new Order();
    return $order->updateOrderStatus($order_id, $status);
}

/**
 * Generate a unique invoice number
 *
 * @return int Invoice number
 */
function generate_invoice_number_ctr() {
    $order = new Order();
    return $order->generateInvoiceNumber();
}

/**
 * Generate a unique order reference
 *
 * @return string Order reference
 */
function generate_order_reference_ctr() {
    $order = new Order();
    return $order->generateOrderReference();
}

/**
 * Get all orders (Admin)
 *
 * @param int $limit Limit
 * @param int $offset Offset
 * @return array Array of all orders
 */
function get_all_orders_ctr($limit = 50, $offset = 0) {
    $order = new Order();
    return $order->getAllOrders($limit, $offset);
}

/**
 * Get total orders count
 *
 * @return int Total count
 */
function get_total_orders_count_ctr() {
    $order = new Order();
    return $order->getTotalOrdersCount();
}

/**
 * Get total revenue
 *
 * @return float Total revenue
 */
function get_total_revenue_ctr() {
    $order = new Order();
    return $order->getTotalRevenue();
}
?>
