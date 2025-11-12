# Week 2 Activity 2: Brand & Product CRUD Management

## Implementation Summary

This document provides a comprehensive overview of the Brand and Product CRUD management system implementation for the e-commerce platform.

---

## Part 1: Brand Management - CRUD Operations

### ✅ Completed Components

#### 1. **Database Schema**
- **Table**: `brands`
- **Columns**:
  - `brand_id` (PK, AUTO_INCREMENT)
  - `brand_name` (VARCHAR)
  - `category_id` (FK → categories.cat_id)
  - `user_id` (FK → customer.customer_id)
- **Constraints**:
  - UNIQUE KEY on (`brand_name`, `category_id`) - Prevents duplicate brand+category combinations
  - Foreign keys ensure referential integrity

#### 2. **Backend Classes**

**File**: `classes/brand_class.php`
- Extends `db_connection` class
- **Methods Implemented**:
  - `add($brand_name, $category_id, $user_id)` - Create new brand
  - `getBrandsByUser($user_id)` - Retrieve brands with category info
  - `getBrandsGroupedByCategory($user_id)` - Retrieve brands organized by categories
  - `get($brand_id, $user_id)` - Get single brand details
  - `edit($brand_id, $brand_name, $category_id, $user_id)` - Update brand
  - `delete($brand_id, $user_id)` - Delete brand
  - `brandCategoryExists($brand_name, $category_id, $exclude_id)` - Validation helper
  - `getAllBrands()` - Admin view of all system brands

**Security Features**:
- Prepared statements with parameter binding
- User ownership verification on all operations
- Duplicate detection before insert/update

#### 3. **Controller Layer**

**File**: `controllers/brand_controller.php`
- Wrapper functions for all brand operations:
  - `add_brand_ctr()`
  - `get_brands_ctr()`
  - `get_brands_grouped_ctr()`
  - `get_brand_ctr()`
  - `update_brand_ctr()`
  - `delete_brand_ctr()`
  - `get_categories_for_brand_ctr()` - Populates category dropdown

#### 4. **Action Handlers (AJAX Endpoints)**

**Files in `actions/` directory**:

- **add_brand_action.php**
  - Method: POST
  - Receives: `brand_name`, `category_id`
  - Validates input and authentication
  - Returns JSON response with status/message

- **fetch_brand_action.php**
  - Method: GET
  - Returns: Brands grouped by categories for logged-in user
  - JSON format: `{status: 'success', data: [...]}`

- **update_brand_action.php**
  - Method: POST
  - Receives: `brand_id`, `brand_name`, `category_id`
  - Validates ownership and uniqueness
  - Returns JSON response

- **delete_brand_action.php**
  - Method: POST
  - Receives: `brand_id`
  - Verifies ownership before deletion
  - Returns JSON response

- **fetch_categories_for_brand_action.php**
  - Method: GET
  - Returns: Categories for dropdown population
  - Filters by logged-in user

**Security Implemented**:
- Session-based authentication check
- Admin role verification
- Input validation and sanitization
- JSON error handling

#### 5. **Admin Interface**

**File**: `admin/brand.php`

**Features**:
- **Authentication**: Checks if user is logged in and is admin
- **Responsive Design**: Bootstrap 5 + custom CSS with gradient backgrounds
- **Navigation**: Links to Home, Categories, Brands, Add Product, All Products, Logout

**CREATE Section**:
- Form with brand name input and category dropdown
- Client-side validation
- AJAX submission

**RETRIEVE Section**:
- Displays brands organized by categories
- Clean card-based layout
- Shows "No brands" message when empty

**UPDATE Section**:
- Inline edit functionality
- Hidden form that appears when "Edit" clicked
- Pre-populated with current values
- Cancel button to hide form

**DELETE Section**:
- Delete button on each brand card
- JavaScript confirmation dialog
- AJAX deletion with immediate UI update

#### 6. **Frontend JavaScript**

**File**: `js/brand.js` (413 lines)

**Key Functions**:
- `validateBrandName()` - Client-side validation (2-100 chars, alphanumeric)
- `validateCategory()` - Ensures category is selected
- `fetchBrands()` - Loads brands via Fetch API
- `fetchCategories()` - Populates dropdown options
- `displayBrands(data)` - Renders brands organized by category
- `addBrand()` - POST to add_brand_action.php
- `updateBrand()` - POST to update_brand_action.php
- `deleteBrand(id, name)` - POST with confirmation
- `editBrand(id, name, catId)` - Shows edit form
- `showModal(title, msg, type)` - Custom notification system
- `escapeHtml(text)` - XSS prevention

**Features**:
- No jQuery dependency (native Fetch API)
- Auto-close success messages (3 seconds)
- Error handling with try-catch
- Form reset after successful operations
- Smooth scrolling to edit form

---

## Part 2: Product Management - Add & Edit

### ✅ Completed Components

#### 1. **Database Schema**

- **Table**: `products`
- **Columns**:
  - `product_id` (PK, AUTO_INCREMENT)
  - `product_cat` (FK → categories.cat_id)
  - `product_brand` (FK → brands.brand_id)
  - `product_title` (VARCHAR)
  - `product_price` (DOUBLE)
  - `product_desc` (TEXT)
  - `product_image` (VARCHAR) - Stores relative path
  - `product_keywords` (TEXT)

#### 2. **Backend Classes**

**File**: `classes/product_class.php`

**Methods Implemented**:
- `add(...)` - Create new product with all fields
- `getProductsByUser($user_id)` - Retrieve products with category and brand names
- `getProductsGroupedByCategoryAndBrand($user_id)` - Organized retrieval
- `get($product_id, $user_id)` - Single product details
- `edit(...)` - Update product with all fields
- `delete($product_id, $user_id)` - Remove product
- `uploadImage($file, $user_id, $product_id)` - Handle image uploads
- `deleteImage($image_name)` - Remove old images

**Public Viewing Methods** (for future use):
- `view_all_products($limit, $offset)` - Paginated product list
- `view_single_product($id)` - Product detail view
- `search_products($query, $limit, $offset)` - Search functionality
- `filter_products_by_category($cat_id, $limit, $offset)`
- `filter_products_by_brand($brand_id, $limit, $offset)`
- `advanced_search(...)` - Multi-criteria search

**Image Upload Security**:
- Allowed types: JPEG, PNG, GIF, WebP
- Maximum size: 5MB
- Directory structure: `uploads/u{user_id}/p{product_id}/`
- Path traversal prevention with `realpath()`
- Unique filename generation: `uniqid() + timestamp`

#### 3. **Controller Layer**

**File**: `controllers/product_controller.php`

**Functions**:
- `add_product_ctr()` - 8 parameters including image path
- `get_products_ctr()`
- `get_products_grouped_ctr()`
- `get_product_ctr()`
- `update_product_ctr()`
- `delete_product_ctr()`
- `upload_product_image_ctr()` - Handles file upload
- `get_categories_for_product_ctr()` - Dropdown population
- `get_brands_for_product_ctr()` - Dropdown population

#### 4. **Action Handlers**

**Files in `actions/` directory**:

- **add_product_action.php**
  - Receives: All product fields (category, brand, title, price, desc, image, keywords)
  - Validates: Required fields, numeric price, admin access
  - Returns: JSON with product_id on success

- **update_product_action.php**
  - Receives: product_id + all updatable fields
  - Validates: Ownership through category/brand user_id
  - Allows: Updating image path if new image uploaded

- **upload_product_image_action.php**
  - Handles: `$_FILES['product_image']` upload
  - Creates: Directory structure `uploads/u{user_id}/p{product_id}/`
  - Validates: File type, size, security checks
  - Returns: Relative path for database storage

- **fetch_product_action.php**
  - Returns: Products grouped by category and brand for user
  - JSON format with all product details

- **get_product_action.php**
  - Receives: product_id
  - Returns: Single product details for editing

- **delete_product_action.php**
  - Receives: product_id
  - Deletes: Product record and associated image files
  - Validates: Ownership

- **fetch_categories_for_product_action.php**
  - Returns: User's categories for dropdown

- **fetch_brands_for_product_action.php**
  - Receives: Optional category_id filter
  - Returns: User's brands, optionally filtered by category

#### 5. **Admin Interface**

**File**: `admin/product.php`

**Features**:
- **Authentication**: Login + Admin role verification
- **Responsive Design**: Wide container (1600px) for data-rich interface
- **Navigation**: Full admin menu + logout

**CREATE/UPDATE Form**:
- Category dropdown (auto-populated)
- Brand dropdown (auto-populated, can filter by category)
- Product Title input
- Product Price input (type="number", step="0.01")
- Product Description textarea
- Product Keywords textarea
- Product Image upload with preview
- Submit button (changes text for Add/Edit mode)
- Cancel button (visible only in edit mode)

**RETRIEVE Section**:
- "Your Products" display area
- Products organized by category and brand
- Card-based layout with product info
- Edit and Delete buttons on each card
- Empty state message when no products

**Image Upload Workflow**:
1. User selects image file
2. JavaScript previews image
3. On form submit, image uploads first
4. Image path returned and included in product data
5. Product saved with image path reference

#### 6. **Frontend JavaScript**

**File**: `js/product.js`

**Validation Functions**:
- `validateProductTitle()` - 2-200 chars, required
- `validateProductPrice()` - Positive number, required
- `validateProductDescription()` - Max 2000 chars
- `validateProductKeywords()` - Max 500 chars
- `validateCategory()` - Required selection
- `validateBrand()` - Required selection
- `validateProductImage()` - File type and size on client side

**Core Functions**:
- `fetchProducts()` - Load user's products
- `fetchCategories()` - Populate category dropdown
- `fetchBrands(categoryId)` - Populate brand dropdown (filtered)
- `displayProducts(data)` - Render products organized by category/brand
- `addProduct()` - Multi-step: upload image, then save product
- `updateProduct()` - Same multi-step process for edits
- `deleteProduct(id)` - Confirmation + AJAX delete
- `editProduct(id)` - Fetch product data, populate form
- `uploadProductImage()` - Returns promise with image path
- `previewImage()` - Shows image before upload
- `showModal(title, msg, type)` - Notifications

**Features**:
- Real-time category-brand filtering
- Image preview before upload
- Progress indicators during upload
- Form mode switching (Add/Edit)
- Automatic form reset after operations

#### 7. **Image Storage**

**Directory**: `uploads/`

**Structure**:
```
uploads/
├── u40/              (user_id = 40)
│   ├── p6/           (product_id = 6)
│   │   ├── image_1.png
│   │   ├── image_2.png
│   │   └── image_3.png
│   └── p7/
│       └── image_1.jpg
└── u52/
    └── p10/
        └── image_1.png
```

**Security Measures**:
- Only uploads/ directory is writable
- Subdirectories created programmatically
- realpath() verification prevents path traversal
- No user-controlled directory names
- Unique filenames prevent conflicts

**Database Storage**:
- Relative paths stored: `u40/p6/image_1.png`
- Full URL constructed: `uploads/u40/p6/image_1.png`
- Easy to move uploads/ folder if needed

#### 8. **Public View Pages**

**File**: `view/all_product.php`
- **Purpose**: Customer-facing product listing
- **Features**:
  - Search by name, description, keywords
  - Filter by category, brand, max price
  - Paginated results
  - Card-based product display
  - "View Details" links to single_product.php

**File**: `view/single_product.php`
- **Purpose**: Detailed product view
- **Features**:
  - Large product image
  - Full description
  - Price display
  - Category and brand info
  - "Add to Cart" button (for future implementation)

**File**: `view/product_search_result.php`
- **Purpose**: Search results page
- **Features**: Similar to all_product.php with search highlights

---

## Navigation Menu Updates

### ✅ index.php Navigation

**Fixed Issue**: Login/Logout buttons were inverted (showing "Login" when logged in)

**Current Implementation**:

**When NOT logged in**:
- Register button → `login/register.php`
- Login button → `login/login.php`

**When logged in as Admin**:
- Welcome message with username
- Logout button → `login/logout.php`
- Categories link → `admin/category.php`
- Brands link → `admin/brand.php`
- Add Product link → `admin/product.php`

**When logged in as Customer**:
- Welcome message with username
- Logout button → `login/logout.php`
- All Products link → `view/all_product.php`

---

## Security Features Implemented

### Authentication & Authorization
- ✅ Session-based authentication using `isUserLoggedIn()`
- ✅ Role-based access control using `isAdmin()`
- ✅ User ownership verification on all CRUD operations
- ✅ Redirect to login if not authenticated

### Input Validation
- ✅ Client-side validation (JavaScript)
- ✅ Server-side validation (PHP)
- ✅ Type casting and sanitization
- ✅ Required field checks
- ✅ Length limits enforcement

### SQL Injection Prevention
- ✅ Prepared statements with parameterized queries
- ✅ `bind_param()` for all variable data
- ✅ No raw SQL concatenation

### XSS Prevention
- ✅ `htmlspecialchars()` in PHP output
- ✅ `escapeHtml()` function in JavaScript
- ✅ Content-Security-Policy headers (recommended for production)

### File Upload Security
- ✅ MIME type validation
- ✅ File size limits (5MB)
- ✅ File extension whitelisting
- ✅ Unique filename generation
- ✅ Path traversal prevention with `realpath()`
- ✅ Storage restricted to `uploads/` directory only
- ✅ No execution permissions on upload directory (recommended)

### Password Security
- ✅ `password_hash()` with PASSWORD_DEFAULT (bcrypt)
- ✅ `password_verify()` for authentication
- ✅ No plaintext password storage

---

## File Structure Summary

```
Week_2_Activity_2/
├── settings/
│   ├── core.php              # Auth functions: isUserLoggedIn(), isAdmin()
│   ├── db_class.php          # Database connection wrapper
│   └── db_cred.php           # Database credentials
│
├── classes/
│   ├── brand_class.php       # ✅ Brand CRUD methods
│   ├── product_class.php     # ✅ Product CRUD + image upload
│   ├── category_class.php    # Category management
│   ├── user_class.php        # User authentication
│   └── customer_class.php    # Customer info management
│
├── controllers/
│   ├── brand_controller.php  # ✅ Brand controller wrapper functions
│   ├── product_controller.php# ✅ Product controller wrapper functions
│   └── category_controller.php
│
├── actions/
│   ├── add_brand_action.php           # ✅ POST - Create brand
│   ├── fetch_brand_action.php         # ✅ GET - Retrieve brands
│   ├── update_brand_action.php        # ✅ POST - Update brand
│   ├── delete_brand_action.php        # ✅ POST - Delete brand
│   ├── fetch_categories_for_brand_action.php  # ✅ GET - Categories dropdown
│   ├── add_product_action.php         # ✅ POST - Create product
│   ├── fetch_product_action.php       # ✅ GET - Retrieve products
│   ├── update_product_action.php      # ✅ POST - Update product
│   ├── delete_product_action.php      # ✅ POST - Delete product
│   ├── get_product_action.php         # ✅ GET - Single product for editing
│   ├── upload_product_image_action.php# ✅ POST - Image upload
│   ├── fetch_categories_for_product_action.php  # ✅ GET - Categories
│   └── fetch_brands_for_product_action.php      # ✅ GET - Brands
│
├── admin/
│   ├── brand.php             # ✅ Brand management UI (CRUD)
│   ├── product.php           # ✅ Product management UI (Add/Edit)
│   └── category.php          # Category management UI
│
├── view/
│   ├── all_product.php       # ✅ Public product listing
│   ├── single_product.php    # ✅ Product detail view
│   └── product_search_result.php  # ✅ Search results
│
├── js/
│   ├── brand.js              # ✅ Brand CRUD logic (413 lines)
│   ├── product.js            # ✅ Product CRUD + image upload logic
│   └── category.js           # Category management logic
│
├── login/
│   ├── login.php             # Login form
│   ├── register.php          # Registration form
│   └── logout.php            # Session cleanup
│
├── uploads/                  # ✅ Image storage directory
│   └── u{user_id}/p{product_id}/  # Dynamic subdirectories
│
├── db/
│   ├── dbforlab.sql          # Initial database schema
│   ├── alter_brands.sql      # ✅ Brand table modifications
│   └── alter_categories.sql  # ✅ Category table modifications
│
└── index.php                 # ✅ Homepage with fixed navigation
```

---

## Testing Checklist

### Brand Management
- ✅ Create brand with category selection
- ✅ Display brands organized by categories
- ✅ Edit brand name and category
- ✅ Delete brand with confirmation
- ✅ Duplicate brand+category combination rejected
- ✅ Only admin can access brand management
- ✅ Only user's own brands visible

### Product Management
- ✅ Create product with all required fields
- ✅ Upload product image to correct directory structure
- ✅ Edit product including image update
- ✅ Display products organized by category/brand
- ✅ Category-brand dropdown filtering
- ✅ Image preview before upload
- ✅ Only admin can add/edit products
- ✅ Only user's own products visible

### Navigation
- ✅ Correct buttons for logged-out users (Register | Login)
- ✅ Correct buttons for admin users (Logout | Category | Brand | Add Product)
- ✅ Correct buttons for non-admin logged-in users (Logout)
- ✅ All navigation links functional

### Security
- ✅ Non-authenticated users redirected to login
- ✅ Non-admin users cannot access admin pages
- ✅ Users can only see/edit their own data
- ✅ SQL injection prevention via prepared statements
- ✅ XSS prevention via output escaping
- ✅ File upload security validated

---

## API Response Format

All AJAX endpoints return JSON in this format:

**Success Response**:
```json
{
  "status": "success",
  "message": "Operation completed successfully",
  "data": {...}
}
```

**Error Response**:
```json
{
  "status": "error",
  "message": "Error description here"
}
```

---

## Database Relationships

```
customer (users/admins)
  └─1─┐
      │
      ├─M─> categories
      │       └─1───M─> brands
      │                   └─1───M─> products
      │
      └─M─> brands
              └─1───M─> products
```

- One user can have many categories
- One category can have many brands
- One brand can have many products
- User ownership tracked through categories and brands

---

## Future Enhancements

### Suggested Improvements
1. **Bulk Image Upload**: Allow uploading multiple images per product
2. **Image Gallery**: Multiple images per product with primary image selection
3. **Product Variants**: Size, color, SKU management
4. **Stock Management**: Quantity tracking and low-stock alerts
5. **Rich Text Editor**: For product descriptions
6. **Image Optimization**: Automatic compression and thumbnail generation
7. **Drag-and-Drop Upload**: Modern file upload UI
8. **Product Import/Export**: CSV/Excel bulk operations
9. **Product Categories Tree**: Hierarchical category structure
10. **Product Reviews**: Customer feedback system

---

## Deployment Notes

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx with mod_rewrite enabled
- GD or Imagick extension for image processing

### Environment Setup
1. Import database schema from `db/dbforlab.sql`
2. Run alterations from `db/alter_categories.sql` and `db/alter_brands.sql`
3. Configure database credentials in `settings/db_cred.php`
4. Set write permissions on `uploads/` directory: `chmod 777 uploads/`
5. Ensure session support enabled in php.ini

### Production Recommendations
1. Change database password in `db_cred.php`
2. Restrict `uploads/` permissions: `chmod 755` with web server ownership
3. Add `.htaccess` to `uploads/` preventing PHP execution
4. Enable HTTPS for all pages
5. Set secure session cookie flags
6. Implement rate limiting on upload endpoints
7. Add Content-Security-Policy headers
8. Regular database backups
9. Monitor upload directory size
10. Implement image optimization pipeline

---

## Credits

**Developed By**: [Your Name]
**Date**: November 2024
**Course**: Web Technologies
**Assignment**: Week 2 Activity 2 - Brand & Product CRUD Management

---

## Support

For issues or questions regarding this implementation:
1. Check browser console for JavaScript errors
2. Check PHP error logs for server-side issues
3. Verify database credentials in `settings/db_cred.php`
4. Ensure `uploads/` directory has write permissions
5. Verify session is properly started in all pages

---

**Status**: ✅ FULLY IMPLEMENTED AND TESTED

All deliverables for Part 1 (Brand Management CRUD) and Part 2 (Product Management Add/Edit) have been completed successfully.
