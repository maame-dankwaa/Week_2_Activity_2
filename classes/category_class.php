<?php

require_once '../settings/db_class.php';

/**
 * Category class for managing categories
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;
    private $user_id;

    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Add a new category
     * @param string $cat_name
     * @param int $user_id
     * @return int|false
     */
    public function add($cat_name, $user_id)
    {
        // Check if category name already exists
        if ($this->categoryNameExists($cat_name)) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $cat_name, $user_id);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    /**
     * Get all categories created by a specific user
     * @param int $user_id
     * @return array|false
     */
    public function getCategoriesByUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY cat_name");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a specific category by ID
     * @param int $cat_id
     * @param int $user_id
     * @return array|false
     */
    public function get($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Edit a category
     * @param int $cat_id
     * @param string $cat_name
     * @param int $user_id
     * @return boolean
     */
    public function edit($cat_id, $cat_name, $user_id)
    {
        // Check if category name already exists (excluding current category)
        if ($this->categoryNameExists($cat_name, $cat_id)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $cat_name, $cat_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Delete a category
     * @param int $cat_id
     * @param int $user_id
     * @return boolean
     */
    public function delete($cat_id, $user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cat_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Check if category name already exists
     * @param string $cat_name
     * @param int $exclude_id (optional) - exclude this category ID from check
     * @return boolean
     */
    private function categoryNameExists($cat_name, $exclude_id = null)
    {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT 1 FROM categories WHERE cat_name = ? AND cat_id != ? LIMIT 1");
            $stmt->bind_param("si", $cat_name, $exclude_id);
        } else {
            $stmt = $this->db->prepare("SELECT 1 FROM categories WHERE cat_name = ? LIMIT 1");
            $stmt->bind_param("s", $cat_name);
        }
        
        $stmt->execute();
        $stmt->store_result();
        return ($stmt->num_rows > 0);
    }

    /**
     * Get all categories (for admin purposes)
     * @return array|false
     */
    public function getAllCategories()
    {
        $stmt = $this->db->prepare("SELECT c.*, cu.customer_name FROM categories c 
                                   JOIN customer cu ON c.user_id = cu.customer_id 
                                   ORDER BY c.cat_name");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
