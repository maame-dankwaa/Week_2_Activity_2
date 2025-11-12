<?php

require_once '../classes/brand_class.php';
require_once '../classes/category_class.php';

/**
 * Brand Controller
 * Handles business logic for brand operations
 */

/**
 * Add a new brand
 * @param string $brand_name
 * @param int $category_id
 * @param int $user_id
 * @return int|false
 */
function add_brand_ctr($brand_name, $category_id, $user_id)
{
    $brand = new Brand();
    return $brand->add($brand_name, $category_id, $user_id);
}

/**
 * Get all brands for a user
 * @param int $user_id
 * @return array|false
 */
function get_brands_ctr($user_id)
{
    $brand = new Brand();
    return $brand->getBrandsByUser($user_id);
}

/**
 * Get brands grouped by category for a user
 * @param int $user_id
 * @return array|false
 */
function get_brands_grouped_ctr($user_id)
{
    $brand = new Brand();
    return $brand->getBrandsGroupedByCategory($user_id);
}

/**
 * Get a specific brand
 * @param int $brand_id
 * @param int $user_id
 * @return array|false
 */
function get_brand_ctr($brand_id, $user_id)
{
    $brand = new Brand();
    return $brand->get($brand_id, $user_id);
}

/**
 * Update a brand
 * @param int $brand_id
 * @param string $brand_name
 * @param int $category_id
 * @param int $user_id
 * @return boolean
 */
function update_brand_ctr($brand_id, $brand_name, $category_id, $user_id)
{
    $brand = new Brand();
    return $brand->edit($brand_id, $brand_name, $category_id, $user_id);
}

/**
 * Delete a brand
 * @param int $brand_id
 * @param int $user_id
 * @return boolean
 */
function delete_brand_ctr($brand_id, $user_id)
{
    $brand = new Brand();
    return $brand->delete($brand_id, $user_id);
}

/**
 * Get all categories for a user (for dropdown)
 * @param int $user_id
 * @return array|false
 */
function get_categories_for_brand_ctr($user_id)
{
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

/**
 * Get all brands (for public view)
 * @return array|false
 */
function get_all_brands_ctr()
{
    $brand = new Brand();
    return $brand->getAllBrands();
}
