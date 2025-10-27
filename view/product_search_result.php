<?php
require_once '../settings/core.php';

// Get search parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$max_price = isset($_GET['price']) ? (float)$_GET['price'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if (empty($query) && $cat_id <= 0 && $brand_id <= 0 && $max_price <= 0) {
    header("Location: all_product.php");
    exit();
}

require_once '../controllers/product_controller.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

// Get search results
$limit = 10;
$offset = ($page - 1) * $limit;

if (!empty($query)) {
    $products = search_products_ctr($query, $limit, $offset);
    $total_count = get_search_count_ctr($query);
} else {
    $products = advanced_search_ctr($query, $cat_id, $brand_id, $max_price, $limit, $offset);
    $total_count = count($products);
}

// Get categories and brands for filters
$categories = get_categories_for_brand_ctr(1);
$brands = get_brands_for_product_ctr(1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/product-styles.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container">
            <a href="all_product.php" class="back-link">
                <i class="fa fa-arrow-left me-2"></i>Back to All Products
            </a>
            
            <h1><i class="fa fa-search me-2"></i>Search Results</h1>
            <p>Find exactly what you're looking for</p>
        </div>
    </div>

    <div class="container">
        <!-- Search Info -->
        <div class="search-info">
            <div class="search-query">
                <?php if (!empty($query)): ?>
                    Search results for: <strong>"<?php echo htmlspecialchars($query); ?>"</strong>
                <?php else: ?>
                    Filtered results
                <?php endif; ?>
            </div>
            <div class="results-count">
                Found <?php echo $total_count; ?> product<?php echo $total_count != 1 ? 's' : ''; ?>
                <?php if ($total_count > 0): ?>
                    (Page <?php echo $page; ?> of <?php echo ceil($total_count / $limit); ?>)
                <?php endif; ?>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <form method="GET" action="product_search_result.php">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($query); ?>">
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="cat">Filter by Category:</label>
                        <select name="cat" id="cat">
                            <option value="">All Categories</option>
                            <?php if ($categories): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['cat_id']; ?>" 
                                            <?php echo $cat_id == $category['cat_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="brand">Filter by Brand:</label>
                        <select name="brand" id="brand">
                            <option value="">All Brands</option>
                            <?php if ($brands): ?>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['brand_id']; ?>" 
                                            <?php echo $brand_id == $brand['brand_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="price">Max Price:</label>
                        <input type="number" name="price" id="price" 
                               value="<?php echo $max_price > 0 ? $max_price : ''; ?>" 
                               placeholder="Enter max price" min="0" step="0.01">
                    </div>
                    
                    <button type="submit" class="btn-filter">
                        <i class="fa fa-filter me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <?php if ($products && count($products) > 0): ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" onclick="viewProduct(<?php echo $product['product_id']; ?>)">
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                <i class="fa fa-image fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['product_title']); ?></h3>
                            <div class="product-price">$<?php echo number_format($product['product_price'], 2); ?></div>
                            
                            <div class="product-meta">
                                <span class="product-category"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                                <span class="product-brand"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                            </div>
                            
                            <div class="product-desc">
                                <?php echo htmlspecialchars($product['product_desc'] ?: 'No description available.'); ?>
                            </div>
                            
                            <div class="product-actions">
                                <a href="single_product.php?id=<?php echo $product['product_id']; ?>" class="btn-view">
                                    <i class="fa fa-eye me-1"></i>View Details
                                </a>
                                <button class="btn-cart" onclick="event.stopPropagation(); addToCart(<?php echo $product['product_id']; ?>)">
                                    <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_count > $limit): ?>
                <div class="pagination">
                    <?php
                    $total_pages = ceil($total_count / $limit);
                    $current_page = $page;
                    
                    // Previous page
                    if ($current_page > 1):
                        $prev_params = http_build_query(array_merge($_GET, ['page' => $current_page - 1]));
                    ?>
                        <a href="?<?php echo $prev_params; ?>">
                            <i class="fa fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <!-- Page numbers -->
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    for ($i = $start_page; $i <= $end_page; $i++):
                        $page_params = http_build_query(array_merge($_GET, ['page' => $i]));
                        $is_current = $i == $current_page;
                    ?>
                        <a href="?<?php echo $page_params; ?>" 
                           class="<?php echo $is_current ? 'current-page' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <!-- Next page -->
                    <?php if ($current_page < $total_pages):
                        $next_params = http_build_query(array_merge($_GET, ['page' => $current_page + 1]));
                    ?>
                        <a href="?<?php echo $next_params; ?>">
                            Next <i class="fa fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-results">
                <i class="fa fa-search"></i>
                <h3>No products found</h3>
                <p>Try adjusting your search criteria or browse all products.</p>
                <a href="all_product.php" class="btn btn-primary">
                    <i class="fa fa-box me-2"></i>Browse All Products
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewProduct(productId) {
            window.location.href = 'single_product.php?id=' + productId;
        }

        function addToCart(productId) {
            // Placeholder for add to cart functionality
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }
    </script>
</body>
</html>
