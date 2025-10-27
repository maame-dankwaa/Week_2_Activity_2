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

echo "<!-- DEBUG: uid=" . htmlspecialchars($_SESSION['user_id'] ?? 'null') .
     " role=" . htmlspecialchars($_SESSION['user_role'] ?? 'null') . " -->";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            max-width: 1400px;
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
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .nav-page a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid white;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            white-space: nowrap;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

        .brands-container {
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

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .brand-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }

        .brand-info {
            flex: 1;
        }

        .brand-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .brand-actions {
            display: flex;
            gap: 10px;
        }

        .brand-actions button {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 6px;
        }

        .no-brands {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .no-brands-in-category {
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

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .brands-grid {
                grid-template-columns: 1fr;
            }
            
            .brand-card {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .brand-actions {
                justify-content: center;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .content {
                padding: 20px;
            }
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
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

            <h1>Brand Management</h1>
            <p>Manage your brands organized by categories</p>
        </div>
        
        <div class="content">
            <!-- Create Brand Form -->
            <div class="form-section">
                <h3>Create New Brand</h3>
                <form id="create-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="brand_name">Brand Name:</label>
                            <input type="text" id="brand_name" name="brand_name" required 
                                   placeholder="Enter brand name">
                        </div>
                        <div class="form-group">
                            <label for="category_id">Category:</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn">Create Brand</button>
                </form>
            </div>
            
            <!-- Brands Display -->
            <div class="form-section">
                <h3>Your Brands</h3>
                <div id="brands-container" class="brands-container">
                    <div class="loading">Loading brands...</div>
                </div>
            </div>
            
            <!-- Edit Brand Form (Hidden by default) -->
            <div id="edit-form" class="form-section edit-form">
                <h3>Edit Brand</h3>
                <form id="edit-form-submit">
                    <input type="hidden" name="brand_id" id="edit_brand_id">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_brand_name">Brand Name:</label>
                            <input type="text" id="edit_brand_name" name="brand_name" required 
                                   placeholder="Enter brand name">
                        </div>
                        <div class="form-group">
                            <label for="edit_category_id">Category:</label>
                            <select id="edit_category_id" name="category_id" required>
                                <option value="">Select a category</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn">Update Brand</button>
                        <button type="button" onclick="cancelEdit()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/brand.js"></script>
</body>
</html>
