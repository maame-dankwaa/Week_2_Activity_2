// Product Management JavaScript

// Validation functions
function validateProductTitle(title) {
    if (!title || title.trim() === '') {
        return { valid: false, message: 'Product title is required' };
    }
    
    if (title.length < 2) {
        return { valid: false, message: 'Product title must be at least 2 characters long' };
    }
    
    if (title.length > 200) {
        return { valid: false, message: 'Product title must be less than 200 characters' };
    }
    
    return { valid: true, message: '' };
}

function validateProductPrice(price) {
    if (!price || price <= 0) {
        return { valid: false, message: 'Product price must be greater than 0' };
    }
    
    return { valid: true, message: '' };
}

function validateCategory(categoryId) {
    if (!categoryId || categoryId === '') {
        return { valid: false, message: 'Please select a category' };
    }
    
    return { valid: true, message: '' };
}

function validateBrand(brandId) {
    if (!brandId || brandId === '') {
        return { valid: false, message: 'Please select a brand' };
    }
    
    return { valid: true, message: '' };
}

// Modal functions
function showModal(title, message, type = 'info') {
    // Remove existing modal if any
    const existingModal = document.getElementById('product-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'product-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        max-width: 400px;
        width: 90%;
        text-align: center;
    `;
    
    const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
    const color = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#007bff';
    
    modalContent.innerHTML = `
        <div style="font-size: 48px; margin-bottom: 15px;">${icon}</div>
        <h3 style="margin: 0 0 15px 0; color: ${color};">${title}</h3>
        <p style="margin: 0 0 20px 0; color: #666;">${message}</p>
        <button onclick="closeModal()" style="
            background: ${color};
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        ">OK</button>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Auto-close after 3 seconds for success messages
    if (type === 'success') {
        setTimeout(closeModal, 3000);
    }
}

function closeModal() {
    const modal = document.getElementById('product-modal');
    if (modal) {
        modal.remove();
    }
}

// Fetch all products
function fetchProducts() {
    fetch('../actions/fetch_product_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayProducts(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch products', 'error');
    });
}

// Fetch categories for dropdown
function fetchCategories() {
    fetch('../actions/fetch_categories_for_product_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            populateCategoryDropdown(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch categories', 'error');
    });
}

// Fetch brands for dropdown
function fetchBrands() {
    fetch('../actions/fetch_brands_for_product_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            populateBrandDropdown(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch brands', 'error');
    });
}

// Populate category dropdown
function populateCategoryDropdown(categories) {
    const dropdown = document.getElementById('product_cat');
    if (dropdown) {
        dropdown.innerHTML = '<option value="">Select a category</option>';
        categories.forEach(cat => {
            dropdown.innerHTML += `<option value="${cat.cat_id}">${escapeHtml(cat.cat_name)}</option>`;
        });
    }
}

// Populate brand dropdown
function populateBrandDropdown(brands) {
    const dropdown = document.getElementById('product_brand');
    if (dropdown) {
        dropdown.innerHTML = '<option value="">Select a brand</option>';
        brands.forEach(brand => {
            dropdown.innerHTML += `<option value="${brand.brand_id}">${escapeHtml(brand.brand_name)}</option>`;
        });
    }
}

// Display products organized by categories and brands
function displayProducts(productsData) {
    const container = document.getElementById('products-container');
    if (!container) return;

    if (productsData.length === 0) {
        container.innerHTML = '<div class="no-products">No products found. Create your first product above.</div>';
        return;
    }

    let html = '';
    let currentCategory = null;
    let currentBrand = null;

    productsData.forEach(item => {
        // New category
        if (currentCategory !== item.cat_name) {
            if (currentCategory !== null) {
                html += '</div></div>'; // Close previous brand section and category section
            }
            html += `
                <div class="category-section">
                    <h3 class="category-title">${escapeHtml(item.cat_name)}</h3>
            `;
            currentCategory = item.cat_name;
            currentBrand = null;
        }

        // New brand within category
        if (currentBrand !== item.brand_name) {
            if (currentBrand !== null) {
                html += '</div>'; // Close previous brand section
            }
            html += `
                <div class="brand-section">
                    <h4 class="brand-title">${escapeHtml(item.brand_name)}</h4>
                    <div class="products-grid">
            `;
            currentBrand = item.brand_name;
        }

        // Products for this brand
        if (item.product_titles) {
            const titles = item.product_titles.split(',');
            const prices = item.product_prices.split(',');
            const descs = item.product_descs.split(',');
            const images = item.product_images.split(',');
            const keywords = item.product_keywords.split(',');
            const productIds = item.product_ids.split(',');

            titles.forEach((title, index) => {
                const imageSrc = images[index] && images[index] !== 'null' ? 
                    `../uploads/${images[index]}` : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg==';
                
                html += `
                    <div class="product-card">
                        <img src="${imageSrc}" alt="${escapeHtml(title)}" class="product-image" 
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                        <div class="product-info">
                            <h4>${escapeHtml(title)}</h4>
                            <div class="product-price">$${parseFloat(prices[index] || 0).toFixed(2)}</div>
                            <div class="product-desc">${escapeHtml(descs[index] || 'No description')}</div>
                            <div class="product-keywords">${escapeHtml(keywords[index] || 'No keywords')}</div>
                            <div class="product-actions">
                                <button type="button" onclick="editProduct(${productIds[index]})" class="btn btn-warning">Edit</button>
                                <button type="button" onclick="deleteProduct(${productIds[index]}, '${escapeHtml(title)}')" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += '<div class="no-products-in-category">No products in this brand yet.</div>';
        }
    });

    // Close any open sections
    if (currentBrand !== null) {
        html += '</div></div>'; // Close brand section
    }
    if (currentCategory !== null) {
        html += '</div>'; // Close category section
    }

    container.innerHTML = html;
}

// Add/Update product
function submitProduct() {
    const productId = document.getElementById('product_id').value;
    const isEdit = productId !== '';
    
    const formData = new FormData();
    
    // Add all form fields
    formData.append('product_cat', document.getElementById('product_cat').value);
    formData.append('product_brand', document.getElementById('product_brand').value);
    formData.append('product_title', document.getElementById('product_title').value);
    formData.append('product_price', document.getElementById('product_price').value);
    formData.append('product_desc', document.getElementById('product_desc').value);
    formData.append('product_keywords', document.getElementById('product_keywords').value);
    
    if (isEdit) {
        formData.append('product_id', productId);
    }
    
    // Add image if selected
    const imageFile = document.getElementById('product_image').files[0];
    if (imageFile) {
        formData.append('product_image', imageFile);
    }

    const url = isEdit ? '../actions/update_product_action.php' : '../actions/add_product_action.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            resetForm();
            fetchProducts(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to save product', 'error');
    });
}

// Delete product
function deleteProduct(productId, productTitle) {
    if (!confirm('Are you sure you want to delete the product "' + productTitle + '"?')) {
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('../actions/delete_product_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            fetchProducts(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to delete product', 'error');
    });
}

// Edit product (load data into form)
function editProduct(productId) {
    fetch(`../actions/get_product_action.php?product_id=${productId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const product = data.data;
            
            // Populate form
            document.getElementById('product_id').value = product.product_id;
            document.getElementById('product_cat').value = product.product_cat;
            document.getElementById('product_brand').value = product.product_brand;
            document.getElementById('product_title').value = product.product_title;
            document.getElementById('product_price').value = product.product_price;
            document.getElementById('product_desc').value = product.product_desc;
            document.getElementById('product_keywords').value = product.product_keywords;
            
            // Update form title and button
            document.getElementById('form-title').textContent = 'Edit Product';
            document.getElementById('submit-btn').textContent = 'Update Product';
            document.getElementById('cancel-btn').style.display = 'inline-block';
            
            // Show current image if exists
            if (product.product_image) {
                const imagePreview = document.getElementById('image-preview');
                imagePreview.src = 'uploads/' + product.product_image;
                imagePreview.style.display = 'block';
            }
            
            // Scroll to form
            document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to load product', 'error');
    });
}

// Cancel edit
function cancelEdit() {
    resetForm();
}

// Reset form
function resetForm() {
    document.getElementById('product-form').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('form-title').textContent = 'Create New Product';
    document.getElementById('submit-btn').textContent = 'Create Product';
    document.getElementById('cancel-btn').style.display = 'none';
    document.getElementById('image-preview').style.display = 'none';
    document.getElementById('file-upload-label').classList.remove('has-file');
}

// Handle file upload preview
function handleFileUpload() {
    const fileInput = document.getElementById('product_image');
    const label = document.getElementById('file-upload-label');
    const preview = document.getElementById('image-preview');
    
    if (fileInput.files && fileInput.files[0]) {
        const file = fileInput.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
        label.classList.add('has-file');
        label.innerHTML = `<i class="fa fa-check"></i> ${file.name}`;
    } else {
        preview.style.display = 'none';
        label.classList.remove('has-file');
        label.innerHTML = '<i class="fa fa-cloud-upload-alt"></i> Choose Image or Drag & Drop';
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load products, categories, and brands on page load
    fetchProducts();
    fetchCategories();
    fetchBrands();
    
    // Handle form submission
    document.getElementById('product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        const title = document.getElementById('product_title').value.trim();
        const price = parseFloat(document.getElementById('product_price').value);
        const category = document.getElementById('product_cat').value;
        const brand = document.getElementById('product_brand').value;
        
        const titleValidation = validateProductTitle(title);
        if (!titleValidation.valid) {
            showModal('Validation Error', titleValidation.message, 'error');
            return;
        }
        
        const priceValidation = validateProductPrice(price);
        if (!priceValidation.valid) {
            showModal('Validation Error', priceValidation.message, 'error');
            return;
        }
        
        const categoryValidation = validateCategory(category);
        if (!categoryValidation.valid) {
            showModal('Validation Error', categoryValidation.message, 'error');
            return;
        }
        
        const brandValidation = validateBrand(brand);
        if (!brandValidation.valid) {
            showModal('Validation Error', brandValidation.message, 'error');
            return;
        }
        
        submitProduct();
    });
    
    // Handle file upload
    document.getElementById('product_image').addEventListener('change', handleFileUpload);
});
