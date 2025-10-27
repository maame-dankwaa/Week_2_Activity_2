<?php
require_once '../settings/core.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: all_product.php");
    exit();
}

require_once '../controllers/product_controller.php';

// Get product details
$product = view_single_product_ctr($product_id);

if (!$product) {
    header("Location: all_product.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_title']); ?> - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/product-styles.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container-narrow">
            <a href="all_product.php" class="back-link">
                <i class="fa fa-arrow-left me-2"></i>Back to All Products
            </a>
            
            <div class="breadcrumb">
                <a href="../index.php">Home</a>
                <span class="separator">></span>
                <a href="all_product.php">Products</a>
                <span class="separator">></span>
                <span><?php echo htmlspecialchars($product['product_title']); ?></span>
            </div>
        </div>
    </div>

    <div class="container-narrow">
        <div class="product-container">
            <div class="product-content">
                <div class="product-image-section">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($product['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                             class="product-image-large">
                    <?php else: ?>
                        <div class="no-image">
                            <i class="fa fa-image fa-3x mb-3"></i>
                            <div>No Image Available</div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-details">
                    <div class="product-id">Product ID: #<?php echo htmlspecialchars($product['product_id']); ?></div>
                    
                    <h1 class="product-title-large"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                    
                    <div class="product-price-large">$<?php echo number_format($product['product_price'], 2); ?></div>
                    
                    <div class="product-meta-large">
                        <div class="meta-item">
                            <span class="meta-label">Category</span>
                            <span class="meta-value"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Brand</span>
                            <span class="meta-value"><?php echo htmlspecialchars($product['brand_name']); ?></span>
                        </div>
                    </div>
                    
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['product_desc'] ?: 'No description available.')); ?>
                    </div>
                    
                    <?php if (!empty($product['product_keywords'])): ?>
                    <div class="product-keywords">
                        <div class="keywords-label">Keywords:</div>
                        <div class="keywords-tags">
                            <?php 
                            $keywords = explode(',', $product['product_keywords']);
                            foreach ($keywords as $keyword): 
                                $keyword = trim($keyword);
                                if (!empty($keyword)):
                            ?>
                                <span class="keyword-tag"><?php echo htmlspecialchars($keyword); ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-actions-large">
                        <button class="btn btn-primary" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                            <i class="fa fa-shopping-cart me-2"></i>Add to Cart
                        </button>
                        <button class="btn btn-success" onclick="buyNow(<?php echo $product['product_id']; ?>)">
                            <i class="fa fa-bolt me-2"></i>Buy Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            // Placeholder for add to cart functionality
            alert('Add to Cart functionality will be implemented in future labs. Product ID: ' + productId);
        }

        function buyNow(productId) {
            // Placeholder for buy now functionality
            alert('Buy Now functionality will be implemented in future labs. Product ID: ' + productId);
        }
    </script>
</body>
</html>
