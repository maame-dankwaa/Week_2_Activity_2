<?php

require_once '../classes/product_class.php';
require_once '../classes/category_class.php';
require_once '../classes/brand_class.php';

/**
 * Product Controller
 * Handles business logic for product operations
 */

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
function add_product_ctr($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id)
{
    $product = new Product();
    return $product->add($product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);
}

/**
 * Get all products for a user
 * @param int $user_id
 * @return array|false
 */
function get_products_ctr($user_id)
{
    $product = new Product();
    return $product->getProductsByUser($user_id);
}

/**
 * Get products grouped by category and brand for a user
 * @param int $user_id
 * @return array|false
 */
function get_products_grouped_ctr($user_id)
{
    $product = new Product();
    return $product->getProductsGroupedByCategoryAndBrand($user_id);
}

/**
 * Get a specific product
 * @param int $product_id
 * @param int $user_id
 * @return array|false
 */
function get_product_ctr($product_id, $user_id)
{
    $product = new Product();
    return $product->get($product_id, $user_id);
}

/**
 * Update a product
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
function update_product_ctr($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id)
{
    $product = new Product();
    return $product->edit($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $product_image, $product_keywords, $user_id);
}

/**
 * Delete a product
 * @param int $product_id
 * @param int $user_id
 * @return boolean
 */
function delete_product_ctr($product_id, $user_id)
{
    $product = new Product();
    return $product->delete($product_id, $user_id);
}

/**
 * Get all categories for a user (for dropdown)
 * @param int $user_id
 * @return array|false
 */
function get_categories_for_product_ctr($user_id)
{
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

/**
 * Get all brands for a user (for dropdown)
 * @param int $user_id
 * @return array|false
 */
function get_brands_for_product_ctr($user_id)
{
    $brand = new Brand();
    return $brand->getBrandsByUser($user_id);
}

/**
 * Upload product image
 * @param array $file
 * @param int $user_id
 * @param int $product_id
 * @return string|false
 */
function upload_product_image_ctr($file, $user_id, $product_id = null)
{
    $product = new Product();
    return $product->uploadImage($file, $user_id, $product_id);
}

/**
 * View all products (public access)
 * @param int $limit
 * @param int $offset
 * @return array|false
 */
function view_all_products_ctr($limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->view_all_products($limit, $offset);
}

/**
 * Search products
 * @param string $query
 * @param int $limit
 * @param int $offset
 * @return array|false
 */
function search_products_ctr($query, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->search_products($query, $limit, $offset);
}

/**
 * Filter products by category
 * @param int $cat_id
 * @param int $limit
 * @param int $offset
 * @return array|false
 */
function filter_products_by_category_ctr($cat_id, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->filter_products_by_category($cat_id, $limit, $offset);
}

/**
 * Filter products by brand
 * @param int $brand_id
 * @param int $limit
 * @param int $offset
 * @return array|false
 */
function filter_products_by_brand_ctr($brand_id, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->filter_products_by_brand($brand_id, $limit, $offset);
}

/**
 * View single product (public access)
 * @param int $id
 * @return array|false
 */
function view_single_product_ctr($id)
{
    $product = new Product();
    return $product->view_single_product($id);
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
function advanced_search_ctr($query = '', $cat_id = 0, $brand_id = 0, $max_price = 0, $limit = 10, $offset = 0)
{
    $product = new Product();
    return $product->advanced_search($query, $cat_id, $brand_id, $max_price, $limit, $offset);
}

/**
 * Get total products count
 * @return int
 */
function get_total_products_count_ctr()
{
    $product = new Product();
    return $product->get_total_products_count();
}

/**
 * Get search results count
 * @param string $query
 * @return int
 */
function get_search_count_ctr($query)
{
    $product = new Product();
    return $product->get_search_count($query);
}
