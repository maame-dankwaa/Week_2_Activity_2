<?php

header('Content-Type: application/json');

require_once '../controllers/product_controller.php';

$response = array();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'view_all_products':
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        $products = view_all_products_ctr($limit, $offset);
        $total_count = get_total_products_count_ctr();
        
        if ($products !== false) {
            $response['status'] = 'success';
            $response['data'] = $products;
            $response['pagination'] = [
                'current_page' => $page,
                'total_pages' => ceil($total_count / $limit),
                'total_products' => $total_count,
                'limit' => $limit
            ];
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch products';
        }
        break;

    case 'search_products':
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        if (empty($query)) {
            $response['status'] = 'error';
            $response['message'] = 'Search query is required';
        } else {
            $products = search_products_ctr($query, $limit, $offset);
            $total_count = get_search_count_ctr($query);
            
            if ($products !== false) {
                $response['status'] = 'success';
                $response['data'] = $products;
                $response['pagination'] = [
                    'current_page' => $page,
                    'total_pages' => ceil($total_count / $limit),
                    'total_products' => $total_count,
                    'limit' => $limit
                ];
                $response['query'] = $query;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to search products';
            }
        }
        break;

    case 'filter_by_category':
        $cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        if ($cat_id <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid category ID';
        } else {
            $products = filter_products_by_category_ctr($cat_id, $limit, $offset);
            
            if ($products !== false) {
                $response['status'] = 'success';
                $response['data'] = $products;
                $response['pagination'] = [
                    'current_page' => $page,
                    'total_pages' => ceil(count($products) / $limit),
                    'total_products' => count($products),
                    'limit' => $limit
                ];
                $response['filter'] = ['type' => 'category', 'id' => $cat_id];
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to filter products by category';
            }
        }
        break;

    case 'filter_by_brand':
        $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        if ($brand_id <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid brand ID';
        } else {
            $products = filter_products_by_brand_ctr($brand_id, $limit, $offset);
            
            if ($products !== false) {
                $response['status'] = 'success';
                $response['data'] = $products;
                $response['pagination'] = [
                    'current_page' => $page,
                    'total_pages' => ceil(count($products) / $limit),
                    'total_products' => count($products),
                    'limit' => $limit
                ];
                $response['filter'] = ['type' => 'brand', 'id' => $brand_id];
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to filter products by brand';
            }
        }
        break;

    case 'view_single_product':
        $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        
        if ($product_id <= 0) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid product ID';
        } else {
            $product = view_single_product_ctr($product_id);
            
            if ($product !== false) {
                $response['status'] = 'success';
                $response['data'] = $product;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Product not found';
            }
        }
        break;

    case 'advanced_search':
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $cat_id = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
        $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        $products = advanced_search_ctr($query, $cat_id, $brand_id, $max_price, $limit, $offset);
        
        if ($products !== false) {
            $response['status'] = 'success';
            $response['data'] = $products;
            $response['pagination'] = [
                'current_page' => $page,
                'total_pages' => ceil(count($products) / $limit),
                'total_products' => count($products),
                'limit' => $limit
            ];
            $response['filters'] = [
                'query' => $query,
                'cat_id' => $cat_id,
                'brand_id' => $brand_id,
                'max_price' => $max_price
            ];
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to perform advanced search';
        }
        break;

    case 'get_categories':
        require_once '../controllers/category_controller.php';
        $categories = get_all_categories_ctr(); // Get all categories for dropdown

        if ($categories !== false) {
            $response['status'] = 'success';
            $response['data'] = $categories;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch categories';
        }
        break;

    case 'get_brands':
        require_once '../controllers/brand_controller.php';
        $brands = get_all_brands_ctr(); // Get all brands for dropdown

        if ($brands !== false) {
            $response['status'] = 'success';
            $response['data'] = $brands;
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch brands';
        }
        break;

    default:
        $response['status'] = 'error';
        $response['message'] = 'Invalid action specified';
        break;
}

echo json_encode($response);
