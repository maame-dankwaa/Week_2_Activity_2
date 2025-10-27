<?php

require_once '../settings/db_class.php';

/**
 * Brand class for managing brands
 */
class Brand extends db_connection
{
    private $brand_id;
    private $brand_name;
    private $user_id;
    private $category_id;

    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Add a new brand
     * @param string $brand_name
     * @param int $category_id
     * @param int $user_id
     * @return int|false
     */
    public function add($brand_name, $category_id, $user_id)
    {
        // Check if brand name + category combination already exists
        if ($this->brandCategoryExists($brand_name, $category_id)) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, category_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $brand_name, $category_id, $user_id);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get all brands created by a specific user, organized by categories
     * @param int $user_id
     * @return array|false
     */
    public function getBrandsByUser($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            JOIN categories c ON b.category_id = c.cat_id 
            WHERE b.user_id = ? 
            ORDER BY c.cat_name, b.brand_name
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get brands grouped by category for a specific user
     * @param int $user_id
     * @return array|false
     */
    public function getBrandsGroupedByCategory($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT c.cat_id, c.cat_name, 
                   GROUP_CONCAT(b.brand_id ORDER BY b.brand_name) as brand_ids,
                   GROUP_CONCAT(b.brand_name ORDER BY b.brand_name) as brand_names
            FROM categories c
            LEFT JOIN brands b ON c.cat_id = b.category_id AND b.user_id = ?
            WHERE c.user_id = ?
            GROUP BY c.cat_id, c.cat_name
            ORDER BY c.cat_name
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a specific brand by ID
     * @param int $brand_id
     * @param int $user_id
     * @return array|false
     */
    public function get($brand_id, $user_id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name 
            FROM brands b 
            JOIN categories c ON b.category_id = c.cat_id 
            WHERE b.brand_id = ? AND b.user_id = ?
        ");
        $stmt->bind_param("ii", $brand_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Edit a brand
     * @param int $brand_id
     * @param string $brand_name
     * @param int $category_id
     * @param int $user_id
     * @return boolean
     */
    public function edit($brand_id, $brand_name, $category_id, $user_id)
    {
        // Check if brand name + category combination already exists (excluding current brand)
        if ($this->brandCategoryExists($brand_name, $category_id, $brand_id)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ?, category_id = ? WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("siii", $brand_name, $category_id, $brand_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Delete a brand
     * @param int $brand_id
     * @param int $user_id
     * @return boolean
     */
    public function delete($brand_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $brand_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Check if brand name + category combination already exists
     * @param string $brand_name
     * @param int $category_id
     * @param int $exclude_id (optional) - exclude this brand ID from check
     * @return boolean
     */
    private function brandCategoryExists($brand_name, $category_id, $exclude_id = null)
    {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT 1 FROM brands WHERE brand_name = ? AND category_id = ? AND brand_id != ? LIMIT 1");
            $stmt->bind_param("sii", $brand_name, $category_id, $exclude_id);
        } else {
            $stmt = $this->db->prepare("SELECT 1 FROM brands WHERE brand_name = ? AND category_id = ? LIMIT 1");
            $stmt->bind_param("si", $brand_name, $category_id);
        }
        
        $stmt->execute();
        $stmt->store_result();
        return ($stmt->num_rows > 0);
    }

    /**
     * Get all brands (for admin purposes)
     * @return array|false
     */
    public function getAllBrands()
    {
        $stmt = $this->db->prepare("
            SELECT b.*, c.cat_name, cu.customer_name 
            FROM brands b 
            JOIN categories c ON b.category_id = c.cat_id 
            JOIN customer cu ON b.user_id = cu.customer_id 
            ORDER BY c.cat_name, b.brand_name
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
