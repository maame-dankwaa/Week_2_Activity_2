<?php
/**
 * Cart Class
 * Handles all cart-related database operations
 * Supports both logged-in users (via customer_id) and guest users (via IP address)
 */

require_once(dirname(__FILE__).'/../settings/db_class.php');

class Cart extends db_connection {

    public function __construct() {
        $this->db_connect();
    }

    /**
     * Add a product to the cart
     * Checks if product already exists and updates quantity instead of duplicating
     *
     * @param int $product_id Product ID
     * @param int|null $customer_id Customer ID (null for guests)
     * @param string $ip_address IP address
     * @param int $quantity Quantity to add
     * @return bool Success status
     */
    public function addToCart($product_id, $customer_id, $ip_address, $quantity) {
        // First check if product already exists in cart
        $existing = $this->checkProductInCart($product_id, $customer_id, $ip_address);

        if ($existing) {
            // Product exists, update quantity
            $new_quantity = $existing['qty'] + $quantity;
            return $this->updateCartQuantity($product_id, $customer_id, $ip_address, $new_quantity);
        } else {
            // Product doesn't exist, add new entry
            $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty) VALUES (?, ?, ?, ?)";
            $stmt = $this->db_conn()->prepare($sql);

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("iisi", $product_id, $customer_id, $ip_address, $quantity);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }
    }

    /**
     * Check if a product already exists in the cart
     *
     * @param int $product_id Product ID
     * @param int|null $customer_id Customer ID (null for guests)
     * @param string $ip_address IP address
     * @return array|false Cart item if exists, false otherwise
     */
    public function checkProductInCart($product_id, $customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            // Guest user
            $sql = "SELECT * FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("is", $product_id, $ip_address);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();

        return $item ? $item : false;
    }

    /**
     * Update the quantity of a product in the cart
     *
     * @param int $product_id Product ID
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @param int $quantity New quantity
     * @return bool Success status
     */
    public function updateCartQuantity($product_id, $customer_id, $ip_address, $quantity) {
        if ($customer_id) {
            // Logged-in user
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("iii", $quantity, $product_id, $customer_id);
        } else {
            // Guest user
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("iis", $quantity, $product_id, $ip_address);
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Remove a product from the cart
     *
     * @param int $product_id Product ID
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @return bool Success status
     */
    public function removeFromCart($product_id, $customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            // Guest user
            $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("is", $product_id, $ip_address);
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Retrieve all cart items for a user
     * Joins with products table to get current product details
     *
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @return array|false Array of cart items with product details
     */
    public function getUserCart($customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = ?
                    ORDER BY p.product_title ASC";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("i", $customer_id);
        } else {
            // Guest user
            $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image, p.product_desc
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    WHERE c.ip_add = ? AND c.c_id IS NULL
                    ORDER BY p.product_title ASC";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("s", $ip_address);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $items ? $items : [];
    }

    /**
     * Empty the entire cart for a user
     *
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @return bool Success status
     */
    public function emptyCart($customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "DELETE FROM cart WHERE c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("i", $customer_id);
        } else {
            // Guest user
            $sql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("s", $ip_address);
        }

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get the total number of items in cart
     *
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @return int Total item count
     */
    public function getCartCount($customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("i", $customer_id);
        } else {
            // Guest user
            $sql = "SELECT SUM(qty) as total FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("s", $ip_address);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ? (int)$row['total'] : 0;
    }

    /**
     * Get the total price of all items in cart
     *
     * @param int|null $customer_id Customer ID
     * @param string $ip_address IP address
     * @return float Total price
     */
    public function getCartTotal($customer_id, $ip_address) {
        if ($customer_id) {
            // Logged-in user
            $sql = "SELECT SUM(c.qty * p.product_price) as total
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("i", $customer_id);
        } else {
            // Guest user
            $sql = "SELECT SUM(c.qty * p.product_price) as total
                    FROM cart c
                    INNER JOIN products p ON c.p_id = p.product_id
                    WHERE c.ip_add = ? AND c.c_id IS NULL";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->bind_param("s", $ip_address);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row['total'] ? (float)$row['total'] : 0.0;
    }
}
?>
