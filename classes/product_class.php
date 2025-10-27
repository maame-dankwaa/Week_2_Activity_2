<?php

require_once '../settings/db_class.php';

/**
 * Product class for managing products
 */
class Product extends db_connection
{
    private $product_id;
    private $product_cat;
    private $product_brand;
    private $product_title;
    private $product_price;
    private $product_desc;
    private $product_image;
    private $product_keywords;
    private $user_id;

    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Add a new product
     * @param int $product_cat
     * @param int $product_brand
     * @param string $product_title
     * @param float $product_price
     * @param string $product_desc
     * @param string $product_image
     * @param string $product_keywords
     * @param int $user_id
     * @return int|false
     */
    public function add($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdsis", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get all products created by a specific user, organized by categories and brands
     * @param int $user_id
     * @return array|false
     */
    public function getProductsByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE c.user_id = ? AND b.user_id = ?
            ORDER BY c.cat_name, b.brand_name, p.product_title
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get products grouped by category and brand for a specific user
     * @param int $user_id
     * @return array|false
     */
    public function getProductsGroupedByCategoryAndBrand($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT c.cat_id, c.cat_name, b.brand_id, b.brand_name,
                   GROUP_CONCAT(p.product_id ORDER BY p.product_title) as product_ids,
                   GROUP_CONCAT(p.product_title ORDER BY p.product_title) as product_titles,
                   GROUP_CONCAT(p.product_price ORDER BY p.product_title) as product_prices,
                   GROUP_CONCAT(p.product_desc ORDER BY p.product_title) as product_descs,
                   GROUP_CONCAT(p.product_image ORDER BY p.product_title) as product_images,
                   GROUP_CONCAT(p.product_keywords ORDER BY p.product_title) as product_keywords
            FROM categories c
            LEFT JOIN brands b ON c.cat_id = b.category_id AND b.user_id = ?
            LEFT JOIN products p ON c.cat_id = p.product_cat AND b.brand_id = p.product_brand
            WHERE c.user_id = ?
            GROUP BY c.cat_id, c.cat_name, b.brand_id, b.brand_name
            ORDER BY c.cat_name, b.brand_name
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a specific product by ID
     * @param int $product_id
     * @param int $user_id
     * @return array|false
     */
    public function get($product_id, $user_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_id = ? AND c.user_id = ? AND b.user_id = ?
        ");
        $stmt->bind_param("iii", $product_id, $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Edit a product
     * @param int $product_id
     * @param int $product_cat
     * @param int $product_brand
     * @param string $product_title
     * @param float $product_price
     * @param string $product_desc
     * @param string $product_image
     * @param string $product_keywords
     * @param int $user_id
     * @return boolean
     */
    public function edit($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id)
    {
        $stmt = $this->db->prepare("
            UPDATE products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            SET p.product_cat = ?, p.product_brand = ?, p.product_title = ?, 
                p.product_price = ?, p.product_desc = ?, p.product_image = ?, p.product_keywords = ?
            WHERE p.product_id = ? AND c.user_id = ? AND b.user_id = ?
        ");
        $stmt->bind_param("iisdsisiii", $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $product_id, $user_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Delete a product
     * @param int $product_id
     * @param int $user_id
     * @return boolean
     */
    public function delete($product_id, $user_id)
    {
        // First get the product to delete the image file
        $product = $this->get($product_id, $user_id);
        if ($product && $product['product_image']) {
            $image_path = '../images/product/' . $product['product_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $stmt = $this->db->prepare("
            DELETE p FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_id = ? AND c.user_id = ? AND b.user_id = ?
        ");
        $stmt->bind_param("iii", $product_id, $user_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Get all products (for admin purposes)
     * @return array|false
     */
    public function getAllProducts()
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name, cu.customer_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            JOIN customer cu ON c.user_id = cu.customer_id 
            ORDER BY c.cat_name, b.brand_name, p.product_title
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Upload product image
     * @param array $file
     * @param int $user_id
     * @param int $product_id
     * @return string|false
     */
    public function uploadImage($file, $user_id, $product_id = null)
    {
        // Use uploads/ folder as specified
        $base_upload_dir = '../uploads/';
        
        // Verify uploads directory exists
        if (!file_exists($base_upload_dir)) {
            return false;
        }

        // Create user directory structure: uploads/u{user_id}/p{product_id}/
        $user_dir = $base_upload_dir . 'u' . $user_id . '/';
        if (!file_exists($user_dir)) {
            mkdir($user_dir, 0777, true);
        }

        $product_dir = $user_dir . 'p' . $product_id . '/';
        if (!file_exists($product_dir)) {
            mkdir($product_dir, 0777, true);
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        if ($file['size'] > $max_size) {
            return false;
        }

        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $upload_path = $product_dir . $new_filename;

        // Verify the upload path is within the uploads directory
        $real_upload_path = realpath($upload_path);
        $real_base_path = realpath($base_upload_dir);
        
        if ($real_upload_path === false || strpos($real_upload_path, $real_base_path) !== 0) {
            return false;
        }

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Return relative path from uploads directory
            return 'u' . $user_id . '/p' . $product_id . '/' . $new_filename;
        }

        return false;
    }

    /**
     * Delete product image
     * @param string $image_name
     * @return boolean
     */
    public function deleteImage($image_name)
    {
        $image_path = '../uploads/' . $image_name;
        if (file_exists($image_path)) {
            return unlink($image_path);
        }
        return true;
    }

    /**
     * View all products (public access)
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function view_all_products($limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search products by title, description, or keywords
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function search_products($query, $limit = 10, $offset = 0)
    {
        $search_term = '%' . $query . '%';
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_title LIKE ? 
               OR p.product_desc LIKE ? 
               OR p.product_keywords LIKE ?
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("sssii", $search_term, $search_term, $search_term, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Filter products by category
     * @param int $cat_id
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function filter_products_by_category($cat_id, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_cat = ?
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $cat_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Filter products by brand
     * @param int $brand_id
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function filter_products_by_brand($brand_id, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_brand = ?
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $brand_id, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * View single product (public access)
     * @param int $id
     * @return array|false
     */
    public function view_single_product($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            WHERE p.product_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Advanced search with multiple filters
     * @param string $query
     * @param int $cat_id
     * @param int $brand_id
     * @param float $max_price
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function advanced_search($query = '', $cat_id = 0, $brand_id = 0, $max_price = 0, $limit = 10, $offset = 0)
    {
        $where_conditions = [];
        $params = [];
        $types = '';

        if (!empty($query)) {
            $where_conditions[] = "(p.product_title LIKE ? OR p.product_desc LIKE ? OR p.product_keywords LIKE ?)";
            $search_term = '%' . $query . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
            $types .= 'sss';
        }

        if ($cat_id > 0) {
            $where_conditions[] = "p.product_cat = ?";
            $params[] = $cat_id;
            $types .= 'i';
        }

        if ($brand_id > 0) {
            $where_conditions[] = "p.product_brand = ?";
            $params[] = $brand_id;
            $types .= 'i';
        }

        if ($max_price > 0) {
            $where_conditions[] = "p.product_price <= ?";
            $params[] = $max_price;
            $types .= 'd';
        }

        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $sql = "
            SELECT p.*, c.cat_name, b.brand_name 
            FROM products p 
            JOIN categories c ON p.product_cat = c.cat_id 
            JOIN brands b ON p.product_brand = b.brand_id 
            {$where_clause}
            ORDER BY p.product_id DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get total count of products
     * @return int
     */
    public function get_total_products_count()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    /**
     * Get total count of search results
     * @param string $query
     * @return int
     */
    public function get_search_count($query)
    {
        $search_term = '%' . $query . '%';
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM products p 
            WHERE p.product_title LIKE ? 
               OR p.product_desc LIKE ? 
               OR p.product_keywords LIKE ?
        ");
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }
}
