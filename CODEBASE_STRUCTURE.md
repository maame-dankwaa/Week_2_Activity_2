# Codebase Structure Overview - Week 2 Activity 2

## Project Name
**Taste of Africa** - A product management and e-commerce platform with brand and category management

## Current Working Branch
`claude/product-display-search-filter-011CV4mPeuE3XAMFRhX7DhrG`

---

## 1. DIRECTORY STRUCTURE

```
/home/user/Week_2_Activity_2/
├── .git/                          # Git repository
├── actions/                        # Action files (API endpoints)
├── admin/                          # Admin management pages
├── classes/                        # PHP class definitions
├── controllers/                    # Business logic controllers
├── css/                            # Stylesheet files
├── db/                             # Database files & SQL scripts
├── js/                             # JavaScript files
├── login/                          # Authentication pages
├── settings/                       # Configuration & core functions
├── uploads/                        # Product image storage
├── view/                           # Frontend view pages
├── index.php                       # Home page
├── category.php                    # Category management (root level)
├── IMPLEMENTATION_SUMMARY.md       # Recent implementation summary
└── README.md                       # Project readme
```

---

## 2. DATABASE STRUCTURE

### Database: `shoppn`

#### Tables:

##### **products** (Main product table)
- `product_id` INT PRIMARY KEY AUTO_INCREMENT
- `product_cat` INT - Foreign key to categories
- `product_brand` INT - Foreign key to brands
- `product_title` VARCHAR(200)
- `product_price` DOUBLE
- `product_desc` VARCHAR(500)
- `product_image` VARCHAR(100)
- `product_keywords` VARCHAR(100)
- **Indexes**: product_cat, product_brand

##### **categories**
- `cat_id` INT PRIMARY KEY AUTO_INCREMENT
- `cat_name` VARCHAR(100)
- `user_id` INT - Foreign key to customer (added via alter_categories.sql)
- **Tracks category ownership** for multi-user support

##### **brands**
- `brand_id` INT PRIMARY KEY AUTO_INCREMENT
- `brand_name` VARCHAR(100)
- `user_id` INT - Foreign key to customer (added via alter_brands.sql)
- `category_id` INT - Foreign key to categories (added via alter_brands.sql)
- **Unique constraint**: (brand_name, category_id) combination
- **Hierarchy**: Each brand belongs to a category and a user

##### **customer** (User accounts)
- `customer_id` INT PRIMARY KEY
- `customer_name` VARCHAR(100)
- `customer_email` VARCHAR(50) UNIQUE
- `customer_pass` VARCHAR(150)
- `customer_country` VARCHAR(30)
- `customer_city` VARCHAR(30)
- `customer_contact` VARCHAR(15)
- `customer_image` VARCHAR(100)
- `user_role` INT (1=Admin, 0=Customer)

##### **cart**
- `p_id` INT - Foreign key to products
- `ip_add` VARCHAR(50)
- `c_id` INT - Foreign key to customer
- `qty` INT

##### **orders**
- `order_id` INT PRIMARY KEY
- `customer_id` INT - Foreign key to customer
- `invoice_no` INT
- `order_date` DATE
- `order_status` VARCHAR(100)

##### **orderdetails**
- `order_id` INT - Foreign key to orders
- `product_id` INT - Foreign key to products
- `qty` INT

##### **payment**
- `pay_id` INT PRIMARY KEY
- `amt` DOUBLE
- `customer_id` INT - Foreign key to customer
- `order_id` INT - Foreign key to orders
- `currency` TEXT
- `payment_date` DATE

#### Database Files:
- `/home/user/Week_2_Activity_2/db/dbforlab.sql` - Main database schema
- `/home/user/Week_2_Activity_2/db/alter_categories.sql` - Adds user_id and user tracking to categories
- `/home/user/Week_2_Activity_2/db/alter_brands.sql` - Adds user_id and category_id to brands

---

## 3. PRODUCT-RELATED FILES

### Product Class
**File**: `/home/user/Week_2_Activity_2/classes/product_class.php`

**Key Methods**:
- `add()` - Add new product
- `get()` - Get single product by ID
- `getProductsByUser($user_id)` - Get all products for a user
- `getProductsGroupedByCategoryAndBrand($user_id)` - Get products organized hierarchically
- `edit()` - Update product
- `delete()` - Delete product with image cleanup
- `uploadImage()` - Handle product image uploads (stores in uploads/u{user_id}/p{product_id}/)
- `deleteImage()` - Delete product image
- `view_all_products()` - Public view with pagination
- `search_products()` - Search by title, description, keywords
- `filter_products_by_category()` - Filter by category
- `filter_products_by_brand()` - Filter by brand
- `view_single_product()` - Get single product for public view
- `advanced_search()` - Multiple filter search
- `get_total_products_count()` - Pagination support

### Product Controller
**File**: `/home/user/Week_2_Activity_2/controllers/product_controller.php`

**Wrapper functions** for product operations:
- `add_product_ctr()`
- `get_products_ctr()`
- `get_products_grouped_ctr()`
- `get_product_ctr()`
- `update_product_ctr()`
- `delete_product_ctr()`
- `get_categories_for_product_ctr()`
- `get_brands_for_product_ctr()`
- `upload_product_image_ctr()`
- `view_all_products_ctr()`
- `search_products_ctr()`
- `filter_products_by_category_ctr()`
- `filter_products_by_brand_ctr()`
- `view_single_product_ctr()`
- `advanced_search_ctr()`
- `get_total_products_count_ctr()`
- `get_search_count_ctr()`

---

## 4. BRAND & CATEGORY IMPLEMENTATION

### Brand Class
**File**: `/home/user/Week_2_Activity_2/classes/brand_class.php`

**Key Methods**:
- `add($brand_name, $category_id, $user_id)` - Add brand to category
- `getBrandsByUser($user_id)` - Get user's brands
- `getBrandsGroupedByCategory($user_id)` - Get brands organized by category
- `get()` - Get single brand
- `edit()` - Update brand
- `delete()` - Delete brand
- `brandCategoryExists()` - Check for duplicate brand+category combo
- `getAllBrands()` - Get all brands (admin)

**Features**:
- Brands belong to categories
- Brands are owned by users
- Unique constraint on brand_name + category_id combination
- Organized hierarchical structure

### Category Class
**File**: `/home/user/Week_2_Activity_2/classes/category_class.php`

**Key Methods**:
- `add($cat_name, $user_id)` - Add category
- `getCategoriesByUser($user_id)` - Get user's categories
- `get()` - Get single category
- `edit()` - Update category
- `delete()` - Delete category
- `categoryNameExists()` - Check for duplicate names
- `getAllCategories()` - Get all categories (admin)

**Features**:
- Categories are owned by users
- Unique category names
- Parent level in hierarchy (categories -> brands -> products)

---

## 5. VIEW FILES (Frontend)

### Admin Management Views
- `/home/user/Week_2_Activity_2/admin/product.php` - Product management interface
- `/home/user/Week_2_Activity_2/admin/brand.php` - Brand management interface
- `/home/user/Week_2_Activity_2/admin/category.php` - Category management interface

### Public Views
- `/home/user/Week_2_Activity_2/view/all_product.php` - Display all products (3,654 bytes)
- `/home/user/Week_2_Activity_2/view/single_product.php` - Single product detail view (5,763 bytes)
- `/home/user/Week_2_Activity_2/view/product_search_result.php` - Search results page (10,880 bytes)

### Root Level
- `/home/user/Week_2_Activity_2/index.php` - Home page with navigation and user profile display
- `/home/user/Week_2_Activity_2/category.php` - Category management at root level (5,725 bytes)

---

## 6. ACTION FILES (API Endpoints)

**Location**: `/home/user/Week_2_Activity_2/actions/`

**Product Actions**:
- `product_actions.php` (7,683 bytes) - Main product API with routing:
  - view_all_products
  - search_products
  - filter_by_category
  - filter_by_brand
  - view_single_product
  - advanced_search
  - get_categories
  - get_brands
- `add_product_action.php` - Create product
- `update_product_action.php` - Update product
- `delete_product_action.php` - Delete product
- `get_product_action.php` - Fetch product data
- `fetch_product_action.php` - Fetch all products
- `upload_product_image_action.php` - Handle image uploads

**Brand Actions**:
- `add_brand_action.php`
- `update_brand_action.php`
- `delete_brand_action.php`
- `fetch_brand_action.php`
- `fetch_brands_for_product_action.php`

**Category Actions**:
- `add_category_action.php`
- `update_category_action.php`
- `delete_category_action.php`
- `fetch_category_action.php`
- `fetch_categories_for_product_action.php`
- `fetch_categories_for_brand_action.php`

**Authentication**:
- `login_customer_action.php` (2,131 bytes)
- `register_user_action.php` (2,209 bytes)

---

## 7. JAVASCRIPT FILES

**Location**: `/home/user/Week_2_Activity_2/js/`

| File | Size | Purpose |
|------|------|---------|
| `product_search.js` | 410 lines | Product search, filtering, pagination |
| `product.js` | 503 lines | Product CRUD operations for admin panel |
| `brand.js` | 374 lines | Brand management functionality |
| `category.js` | 307 lines | Category management functionality |
| `login.js` | 169 lines | Login form validation |
| `register.js` | 70 lines | Registration form validation |
| **Total** | **1,833 lines** | |

**Key Features in product_search.js**:
- Real-time search with debouncing (500ms)
- Filter by category, brand, price
- Pagination with prev/next buttons
- Product image fallback handling
- Add to cart placeholder
- Advanced search with multiple filters

**Key Features in product.js**:
- Product creation and editing
- Image preview and upload
- Validation (title, price, category, brand)
- Modal notifications
- Organization by category and brand
- Edit and delete operations
- Form state management

---

## 8. SETTINGS & CONFIGURATION

### Core Settings
**File**: `/home/user/Week_2_Activity_2/settings/core.php`

**Functions**:
- `isUserLoggedIn()` - Check session
- `isAdmin()` - Check admin role (user_role === 1)
- `getUserID()` - Get current user ID
- `getUserRole()` - Get current user role
- `checkRole()` - Check specific role

### Database Connection
**File**: `/home/user/Week_2_Activity_2/settings/db_class.php`

**Class**: `db_connection`

**Methods**:
- `db_connect()` - Establish connection
- `db_query()` - Execute SELECT
- `db_write_query()` - Execute INSERT/UPDATE/DELETE
- `db_fetch_one()` - Get single record
- `db_fetch_all()` - Get multiple records
- `db_count()` - Count result rows
- `last_insert_id()` - Get last insert ID

**Connection Details**: Loaded from `db_cred.php` (not shown - contains credentials)

### Credentials
**File**: `/home/user/Week_2_Activity_2/settings/db_cred.php`

Defines constants:
- `SERVER` - Database host
- `USERNAME` - Database user
- `PASSWD` - Database password
- `DATABASE` - Database name

---

## 9. IMAGE DIRECTORY STRUCTURE

### Upload Directory
**Location**: `/home/user/Week_2_Activity_2/uploads/`

**Current State**: Contains one guest image (GUEST_56246e01-0094-49b0-95c8-417fa5dcb649.avif)

**Planned Structure** (per product_class.php):
```
uploads/
├── u{user_id}/              # User-specific folder
│   └── p{product_id}/       # Product-specific folder
│       └── {uniqid}_{timestamp}.{ext}  # Image files
```

**Example Path Generated**: `u{user_id}/p{product_id}/652abc123_1668278400.jpg`

**Validation**:
- Allowed types: JPEG, PNG, GIF, WebP
- Max size: 5MB
- Path traversal protection: verified against base directory

### CSS Styling
**File**: `/home/user/Week_2_Activity_2/css/product-styles.css` (11,464 bytes)
- Product card styling
- Product display layouts
- Responsive design

---

## 10. HOME PAGE & AUTHENTICATION

### Home Page
**File**: `/home/user/Week_2_Activity_2/index.php`

**Features**:
- Bootstrap navbar with responsive menu
- Navigation menu changes based on login/admin status
- Product search bar
- User welcome message with profile info display
- Shows user: name, email, phone, location, role
- Login/Register buttons for non-authenticated users
- Admin-only links: Categories, Brands, Add Product
- Fixed menu tray in top-right corner

**Session Variables Used**:
- `$_SESSION['user_id']`
- `$_SESSION['user_name']`
- `$_SESSION['user_email']`
- `$_SESSION['user_phone']`
- `$_SESSION['user_city']`
- `$_SESSION['user_country']`
- `$_SESSION['user_role']` (0=Customer, 1=Admin)

---

## 11. AUTHENTICATION SYSTEM

### Login
**File**: `/home/user/Week_2_Activity_2/login/login.php`
- Customer login with email and password
- Session creation
- Role-based redirect

### Registration
**File**: `/home/user/Week_2_Activity_2/login/register.php`
- New user registration
- Customer table insertion
- Email uniqueness validation

### Logout
**File**: `/home/user/Week_2_Activity_2/login/logout.php`
- Session termination

---

## 12. API RESPONSE STRUCTURE

All action files follow a consistent JSON response format:

**Success Response**:
```json
{
  "status": "success",
  "data": [...],
  "message": "...",
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_products": 45,
    "limit": 10
  }
}
```

**Error Response**:
```json
{
  "status": "error",
  "message": "Error description"
}
```

---

## 13. USER ROLES & PERMISSIONS

### Admin (user_role = 1)
- Create categories
- Create brands
- Create products
- Edit/delete categories
- Edit/delete brands
- Edit/delete products
- Upload product images
- Access to `/admin/` pages

### Customer (user_role = 0)
- Browse all products
- Search products
- Filter by category/brand
- View product details
- (Cart/checkout planned)

---

## 14. CURRENT FEATURES SUMMARY

### Implemented:
1. Multi-user support with role-based access
2. Hierarchical product organization (Category > Brand > Product)
3. Product CRUD operations
4. Image upload with organized storage structure
5. Product search with multiple filters
6. Pagination support
7. Category CRUD operations
8. Brand CRUD operations with category relationship
9. Responsive UI design
10. Form validation (client-side & server-side)
11. XSS protection (HTML escaping)
12. Path traversal protection for file uploads
13. Session-based authentication

### Data Relationships:
```
Customer (user) 
├── owns Categories
│   └── contains Brands
│       └── contains Products
├── owns Cart items
└── places Orders
```

---

## 15. RECENT IMPLEMENTATION

**Latest Commit**: "Merge pull request #1 from maame-dankwaa/claude/brand-product-crud-management"
- Brand and Product CRUD Management System implemented
- Current branch: `claude/product-display-search-filter-011CV4mPeuE3XAMFRhX7DhrG`
- Focus: Product display, search, and filtering functionality

---

## 16. PROJECT STATISTICS

| Category | Count |
|----------|-------|
| PHP Files | ~30 |
| JavaScript Files | 6 |
| Database Tables | 8 |
| API Endpoints (product_actions.php) | 8 |
| CSS Files | 1 |
| Total JS Lines | 1,833 |

---

## Key Technologies

- **Backend**: PHP 8.0+ with MySQLi
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Database**: MySQL/MariaDB
- **Framework**: Custom MVC-style architecture
- **UI Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.0+
- **Build/Deploy**: Git-based workflow

