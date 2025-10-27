<?php
require_once '../settings/core.php';


// Check if user is logged in
if (!isUserLoggedIn()) {
    header("Location: ../login/login.php");
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

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

        .content {
            padding: 40px;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.4rem;
            font-weight: 500;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 12px 15px;
            border: 2px dashed #e1e5e9;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .file-upload-label:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .file-upload-label.has-file {
            border-color: #28a745;
            background: #f8fff9;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        }

        .btn-danger:hover {
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #feca57 0%, #ff9ff3 100%);
            color: #333;
        }

        .btn-warning:hover {
            box-shadow: 0 5px 15px rgba(254, 202, 87, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }

        .products-container {
            margin-top: 20px;
        }

        .category-section {
            margin-bottom: 40px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .category-title {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            font-weight: 500;
        }

        .brand-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #28a745;
        }

        .brand-title {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }

        .product-info h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .product-price {
            color: #28a745;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .product-desc {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .product-keywords {
            color: #6c757d;
            font-size: 0.8rem;
            font-style: italic;
            margin-bottom: 15px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
        }

        .product-actions button {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 6px;
        }

        .no-products {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .no-products-in-category {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .edit-form {
            display: none;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .loading {
            text-align: center;
            color: #6c757d;
            padding: 40px;
            font-style: italic;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .product-card {
                text-align: center;
            }
            
            .product-actions {
                justify-content: center;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="nav-page">
                <a href="../index.php">Home</a>
                <a href="../admin/category.php">Categories</a>
                <a href="../admin/brand.php">Brands</a>
                <a href="../admin/product.php">Add Product</a>
                <a href="../view/all_product.php">All Products</a>
                <a href="../login/logout.php">Logout</a>
            </div>
            <h1>Product Management</h1>
            <p>Manage your products organized by categories and brands</p>
        </div>
        
        <div class="content">
            <!-- Create/Edit Product Form -->
            <div class="form-section">
                <h3 id="form-title">Create New Product</h3>
                <form id="product-form" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="product_id">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product_cat">Product Category:</label>
                            <select id="product_cat" name="product_cat" required>
                                <option value="">Select a category</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product_brand">Product Brand:</label>
                            <select id="product_brand" name="product_brand" required>
                                <option value="">Select a brand</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_title">Product Title:</label>
                        <input type="text" id="product_title" name="product_title" required 
                               placeholder="Enter product title">
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product_price">Product Price:</label>
                            <input type="number" id="product_price" name="product_price" step="0.01" min="0" required 
                                   placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label for="product_keywords">Product Keywords:</label>
                            <input type="text" id="product_keywords" name="product_keywords" 
                                   placeholder="Enter keywords (comma separated)">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_desc">Product Description:</label>
                        <textarea id="product_desc" name="product_desc" 
                                  placeholder="Enter product description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="product_image">Product Image:</label>
                        <div class="file-upload">
                            <input type="file" id="product_image" name="product_image" accept="image/*">
                            <label for="product_image" class="file-upload-label" id="file-upload-label">
                                <i class="fa fa-cloud-upload-alt"></i> Choose Image or Drag & Drop
                            </label>
                        </div>
                        <img id="image-preview" class="image-preview" alt="Image preview">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn" id="submit-btn">Create Product</button>
                        <button type="button" onclick="cancelEdit()" class="btn btn-secondary" id="cancel-btn" style="display: none;">Cancel</button>
                    </div>
                </form>
            </div>
            
            <!-- Products Display -->
            <div class="form-section">
                <h3>Your Products</h3>
                <div id="products-container" class="products-container">
                    <div class="loading">Loading products...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="../js/product.js"></script>
</body>
</html>
