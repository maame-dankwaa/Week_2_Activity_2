<?php
require_once '../settings/core.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/product-styles.css" rel="stylesheet">
    <style>
        .nav-page {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .nav-page a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid white;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-page a:hover {
            background: white;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav-page">
                <a href="../index.php">Home</a>
                <a href="../view/all_product.php">All Products</a>
                <a href="../login/logout.php">Logout</a>
            </div>
            <h1><i class="fa fa-box me-2"></i>All Products</h1>
            <p>Discover our amazing collection of products</p>
        </div>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <div class="filters-section">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Search products by name, description, or keywords...">
                <button onclick="performSearch()">
                    <i class="fa fa-search"></i>
                </button>
            </div>
            
            <div class="filter-row">
                <div class="filter-group">
                    <label for="category-filter">Filter by Category:</label>
                    <select id="category-filter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="brand-filter">Filter by Brand:</label>
                    <select id="brand-filter">
                        <option value="">All Brands</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price-filter">Max Price:</label>
                    <input type="number" id="price-filter" placeholder="Enter max price" min="0" step="0.01">
                </div>
                
                <button class="btn-filter" onclick="applyFilters()">
                    <i class="fa fa-filter me-1"></i>Apply Filters
                </button>
            </div>
        </div>

        <!-- Results Info -->
        <div id="results-info" class="results-info" style="display: none;">
            <span id="results-text"></span>
        </div>

        <!-- Products Grid -->
        <div id="products-container">
            <div class="loading">
                <i class="fa fa-spinner"></i>
                <p>Loading products...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="pagination" style="display: none;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/product_search.js"></script>
</body>
</html>
