<?php
require_once 'settings/core.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    header("Location: login/login.php");
    exit();
}

// Check if user is admin
if (!isAdmin()) {
    header("Location: login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-section h3 {
            margin-top: 0;
            color: #555;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-warning:hover {
            background-color: #e0a800;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions {
            white-space: nowrap;
        }
        .actions button {
            margin-right: 5px;
            padding: 5px 10px;
            font-size: 12px;
        }
        .logout-link {
            float: right;
            margin-bottom: 20px;
        }
        .logout-link a {
            color: #dc3545;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid #dc3545;
            border-radius: 4px;
        }
        .logout-link a:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-link">
            <a href="login/logout.php">Logout</a>
        </div>
        
        <h1>Category Management</h1>
        
        <!-- Messages will be displayed here by JavaScript -->
        
        <!-- Create Category Form -->
        <div class="form-section">
            <h3>Create New Category</h3>
            <form id="create-form">
                <div class="form-group">
                    <label for="cat_name">Category Name:</label>
                    <input type="text" id="cat_name" name="cat_name" required>
                </div>
                <button type="submit">Create Category</button>
            </form>
        </div>
        
        <!-- Categories List -->
        <div class="form-section">
            <h3>Your Categories</h3>
            <table id="categories-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align: center;">Loading categories...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Edit Category Form (Hidden by default) -->
        <div id="edit-form" class="form-section" style="display: none;">
            <h3>Edit Category</h3>
            <form id="edit-form-submit">
                <input type="hidden" name="cat_id" id="edit_cat_id">
                <div class="form-group">
                    <label for="edit_cat_name">Category Name:</label>
                    <input type="text" id="edit_cat_name" name="cat_name" required>
                </div>
                <button type="submit">Update Category</button>
                <button type="button" onclick="cancelEdit()">Cancel</button>
            </form>
        </div>
    </div>

    <script src="js/category.js"></script>
</body>
</html>
