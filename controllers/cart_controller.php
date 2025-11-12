<?php
/**
 * Cart Controller
 * Wrapper functions for cart class methods
 * Used by action scripts to interact with cart operations
 */

require_once(dirname(__FILE__).'/../classes/cart_class.php');

/**
 * Add a product to the cart
 *
 * @param int $product_id Product ID
 * @param int|null $customer_id Customer ID (null for guests)
 * @param string $ip_address IP address
 * @param int $quantity Quantity to add
 * @return bool Success status
 */
function add_to_cart_ctr($product_id, $customer_id, $ip_address, $quantity) {
    $cart = new Cart();
    return $cart->addToCart($product_id, $customer_id, $ip_address, $quantity);
}

/**
 * Update cart item quantity
 *
 * @param int $product_id Product ID
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @param int $quantity New quantity
 * @return bool Success status
 */
function update_cart_item_ctr($product_id, $customer_id, $ip_address, $quantity) {
    $cart = new Cart();
    return $cart->updateCartQuantity($product_id, $customer_id, $ip_address, $quantity);
}

/**
 * Remove a product from the cart
 *
 * @param int $product_id Product ID
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return bool Success status
 */
function remove_from_cart_ctr($product_id, $customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->removeFromCart($product_id, $customer_id, $ip_address);
}

/**
 * Get all cart items for a user
 *
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return array Cart items with product details
 */
function get_user_cart_ctr($customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->getUserCart($customer_id, $ip_address);
}

/**
 * Empty the entire cart for a user
 *
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return bool Success status
 */
function empty_cart_ctr($customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->emptyCart($customer_id, $ip_address);
}

/**
 * Get the total number of items in cart
 *
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return int Total item count
 */
function get_cart_count_ctr($customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->getCartCount($customer_id, $ip_address);
}

/**
 * Get the total price of all items in cart
 *
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return float Total price
 */
function get_cart_total_ctr($customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->getCartTotal($customer_id, $ip_address);
}

/**
 * Check if a product exists in the cart
 *
 * @param int $product_id Product ID
 * @param int|null $customer_id Customer ID
 * @param string $ip_address IP address
 * @return array|false Cart item if exists, false otherwise
 */
function check_product_in_cart_ctr($product_id, $customer_id, $ip_address) {
    $cart = new Cart();
    return $cart->checkProductInCart($product_id, $customer_id, $ip_address);
}
?>
