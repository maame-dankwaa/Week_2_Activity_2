// Product Search and Filtering JavaScript

let currentPage = 1;
let currentFilters = {
    query: '',
    cat_id: '',
    brand_id: '',
    max_price: ''
};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    loadCategories();
    loadBrands();
    
    // Set up event listeners
    setupEventListeners();
});

// Set up event listeners
function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
        
        // Real-time search with debouncing
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 2 || this.value.length === 0) {
                    performSearch();
                }
            }, 500);
        });
    }
    
    // Filter dropdowns
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
    
    if (brandFilter) {
        brandFilter.addEventListener('change', applyFilters);
    }
    
    if (priceFilter) {
        priceFilter.addEventListener('input', function() {
            // Debounce price filter
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
    }
}

// Load products
function loadProducts(page = 1) {
    currentPage = page;
    
    const container = document.getElementById('products-container');
    if (!container) return;
    
    // Show loading
    container.innerHTML = `
        <div class="loading">
            <i class="fa fa-spinner"></i>
            <p>Loading products...</p>
        </div>
    `;
    
    // Build URL with current filters
    const params = new URLSearchParams({
        action: 'view_all_products',
        page: page,
        limit: 10
    });
    
    // Add search query if present
    if (currentFilters.query) {
        params.set('action', 'search_products');
        params.set('query', currentFilters.query);
    }
    
    // Add filters if present
    if (currentFilters.cat_id) {
        params.set('action', 'advanced_search');
        params.set('cat_id', currentFilters.cat_id);
    }
    
    if (currentFilters.brand_id) {
        params.set('action', 'advanced_search');
        params.set('brand_id', currentFilters.brand_id);
    }
    
    if (currentFilters.max_price) {
        params.set('action', 'advanced_search');
        params.set('max_price', currentFilters.max_price);
    }
    
    fetch(`../actions/product_actions.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayProducts(data.data);
                displayPagination(data.pagination);
                updateResultsInfo(data.pagination);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to load products');
        });
}

// Display products
function displayProducts(products) {
    const container = document.getElementById('products-container');
    if (!container) return;
    
    if (!products || products.length === 0) {
        container.innerHTML = `
            <div class="no-products">
                <i class="fa fa-box-open"></i>
                <h3>No products found</h3>
                <p>Try adjusting your search criteria or browse all products.</p>
            </div>
        `;
        return;
    }
    
    const productsHtml = products.map(product => `
        <div class="product-card" onclick="viewProduct(${product.product_id})">
            ${getProductImageHtml(product)}
            <div class="product-info">
                <h3 class="product-title">${escapeHtml(product.product_title)}</h3>
                <div class="product-price">$${parseFloat(product.product_price).toFixed(2)}</div>
                
                <div class="product-meta">
                    <span class="product-category">${escapeHtml(product.cat_name)}</span>
                    <span class="product-brand">${escapeHtml(product.brand_name)}</span>
                </div>
                
                <div class="product-desc">
                    ${escapeHtml(product.product_desc || 'No description available.')}
                </div>
                
                <div class="product-actions">
                    <button class="btn-view" onclick="event.stopPropagation(); viewProduct(${product.product_id})">
                        <i class="fa fa-eye me-1"></i>View Details
                    </button>
                    <button class="btn-cart" onclick="event.stopPropagation(); addToCart(${product.product_id})">
                        <i class="fa fa-shopping-cart me-1"></i>Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = `<div class="products-grid">${productsHtml}</div>`;
}

// Get product image HTML
function getProductImageHtml(product) {
    if (product.product_image) {
        return `
            <img src="../uploads/${escapeHtml(product.product_image)}" 
                 alt="${escapeHtml(product.product_title)}" 
                 class="product-image"
                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjI1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
        `;
    } else {
        return `
            <div class="no-image" style="width: 100%; height: 250px;">
                <i class="fa fa-image fa-3x"></i>
            </div>
        `;
    }
}

// Display pagination
function displayPagination(pagination) {
    const container = document.getElementById('pagination');
    if (!container || !pagination) return;
    
    const { current_page, total_pages, total_products } = pagination;
    
    if (total_pages <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    
    let paginationHtml = '';
    
    // Previous button
    if (current_page > 1) {
        paginationHtml += `
            <button onclick="loadProducts(${current_page - 1})">
                <i class="fa fa-chevron-left"></i> Previous
            </button>
        `;
    }
    
    // Page numbers
    const startPage = Math.max(1, current_page - 2);
    const endPage = Math.min(total_pages, current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const isCurrent = i === current_page;
        paginationHtml += `
            <button onclick="loadProducts(${i})" 
                    class="${isCurrent ? 'current-page' : ''}">
                ${i}
            </button>
        `;
    }
    
    // Next button
    if (current_page < total_pages) {
        paginationHtml += `
            <button onclick="loadProducts(${current_page + 1})">
                Next <i class="fa fa-chevron-right"></i>
            </button>
        `;
    }
    
    container.innerHTML = paginationHtml;
}

// Update results info
function updateResultsInfo(pagination) {
    const container = document.getElementById('results-info');
    if (!container || !pagination) return;
    
    const { current_page, total_pages, total_products } = pagination;
    
    let infoText = `Showing ${total_products} product${total_products !== 1 ? 's' : ''}`;
    if (total_pages > 1) {
        infoText += ` (Page ${current_page} of ${total_pages})`;
    }
    
    if (currentFilters.query) {
        infoText = `Search results for "${currentFilters.query}" - ${infoText}`;
    }
    
    document.getElementById('results-text').textContent = infoText;
    container.style.display = 'block';
}

// Perform search
function performSearch() {
    const searchInput = document.getElementById('search-input');
    if (!searchInput) return;
    
    currentFilters.query = searchInput.value.trim();
    currentPage = 1;
    loadProducts();
}

// Apply filters
function applyFilters() {
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    currentFilters.cat_id = categoryFilter ? categoryFilter.value : '';
    currentFilters.brand_id = brandFilter ? brandFilter.value : '';
    currentFilters.max_price = priceFilter ? priceFilter.value : '';
    
    currentPage = 1;
    loadProducts();
}

// Load categories for filter dropdown
function loadCategories() {
    fetch('../actions/product_actions.php?action=get_categories')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateCategoryDropdown(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

// Load brands for filter dropdown
function loadBrands() {
    fetch('../actions/product_actions.php?action=get_brands')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateBrandDropdown(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading brands:', error);
        });
}

// Populate category dropdown
function populateCategoryDropdown(categories) {
    const dropdown = document.getElementById('category-filter');
    if (!dropdown) return;
    
    dropdown.innerHTML = '<option value="">All Categories</option>';
    categories.forEach(cat => {
        dropdown.innerHTML += `<option value="${cat.cat_id}">${escapeHtml(cat.cat_name)}</option>`;
    });
}

// Populate brand dropdown
function populateBrandDropdown(brands) {
    const dropdown = document.getElementById('brand-filter');
    if (!dropdown) return;
    
    dropdown.innerHTML = '<option value="">All Brands</option>';
    brands.forEach(brand => {
        dropdown.innerHTML += `<option value="${brand.brand_id}">${escapeHtml(brand.brand_name)}</option>`;
    });
}

// View product
function viewProduct(productId) {
    window.location.href = `../view/single_product.php?id=${productId}`;
}

// Add to cart
function addToCart(productId) {
    // Send AJAX request to add to cart
    fetch('../actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            showCartMessage(data.message, 'success');
            // Update cart count if function exists
            if (typeof updateCartCount === 'function') {
                updateCartCount(data.cart_count);
            }
        } else {
            showCartMessage(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCartMessage('Failed to add to cart. Please try again.', 'error');
    });
}

// Show cart message
function showCartMessage(message, type = 'info') {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Add to body
    document.body.appendChild(alertDiv);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 3000);
}

// Show error message
function showError(message) {
    const container = document.getElementById('products-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="no-results">
            <i class="fa fa-exclamation-triangle"></i>
            <h3>Error</h3>
            <p>${escapeHtml(message)}</p>
        </div>
    `;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Advanced search with multiple filters
function performAdvancedSearch() {
    const query = document.getElementById('search-input')?.value.trim() || '';
    const catId = document.getElementById('category-filter')?.value || '';
    const brandId = document.getElementById('brand-filter')?.value || '';
    const maxPrice = document.getElementById('price-filter')?.value || '';
    
    // Build search URL
    const params = new URLSearchParams();
    if (query) params.set('q', query);
    if (catId) params.set('cat', catId);
    if (brandId) params.set('brand', brandId);
    if (maxPrice) params.set('price', maxPrice);
    
    // Redirect to search results page
    window.location.href = `../view/product_search_result.php?${params}`;
}

// Clear all filters
function clearFilters() {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const brandFilter = document.getElementById('brand-filter');
    const priceFilter = document.getElementById('price-filter');
    
    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (brandFilter) brandFilter.value = '';
    if (priceFilter) priceFilter.value = '';
    
    currentFilters = {
        query: '',
        cat_id: '',
        brand_id: '',
        max_price: ''
    };
    
    currentPage = 1;
    loadProducts();
}
