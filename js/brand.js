// Brand Management JavaScript

const FETCH_BRANDS_URL = "../actions/fetch_brand_action.php";
const ADD_BRAND_URL    = "../actions/add_brand_action.php";
const UPDATE_BRAND_URL = "../actions/update_brand_action.php";
const DELETE_BRAND_URL = "../ actions/delete_brand_action.php";

// Validation functions
function validateBrandName(brandName) {
    if (!brandName || brandName.trim() === '') {
        return { valid: false, message: 'Brand name is required' };
    }
    
    if (brandName.length < 2) {
        return { valid: false, message: 'Brand name must be at least 2 characters long' };
    }
    
    if (brandName.length > 100) {
        return { valid: false, message: 'Brand name must be less than 100 characters' };
    }
    
    // Check for valid characters (letters, numbers, spaces, hyphens, underscores)
    if (!/^[a-zA-Z0-9\s\-_]+$/.test(brandName)) {
        return { valid: false, message: 'Brand name can only contain letters, numbers, spaces, hyphens, and underscores' };
    }
    
    return { valid: true, message: '' };
}

function validateCategory(categoryId) {
    if (!categoryId || categoryId === '') {
        return { valid: false, message: 'Please select a category' };
    }
    
    return { valid: true, message: '' };
}

// Modal functions
function showModal(title, message, type = 'info') {
    // Remove existing modal if any
    const existingModal = document.getElementById('brand-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'brand-modal';
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
    const modal = document.getElementById('brand-modal');
    if (modal) {
        modal.remove();
    }
}

// Fetch all brands
function fetchBrands() {
    fetch('../actions/fetch_brand_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayBrands(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch brands', 'error');
    });
}

// Fetch categories for dropdown
function fetchCategories() {
    fetch('../actions/fetch_categories_for_brand_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            populateCategoryDropdowns(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch categories', 'error');
    });
}

// Populate category dropdowns
function populateCategoryDropdowns(categories) {
    const createDropdown = document.getElementById('category_id');
    const editDropdown = document.getElementById('edit_category_id');
    
    if (createDropdown) {
        createDropdown.innerHTML = '<option value="">Select a category</option>';
        categories.forEach(cat => {
            createDropdown.innerHTML += `<option value="${cat.cat_id}">${escapeHtml(cat.cat_name)}</option>`;
        });
    }
    
    if (editDropdown) {
        editDropdown.innerHTML = '<option value="">Select a category</option>';
        categories.forEach(cat => {
            editDropdown.innerHTML += `<option value="${cat.cat_id}">${escapeHtml(cat.cat_name)}</option>`;
        });
    }
}

// Display brands in the table organized by categories
function displayBrands(brandsData) {
    const container = document.getElementById('brands-container');
    if (!container) return;

    if (brandsData.length === 0) {
        container.innerHTML = '<div class="no-brands">No brands found. Create your first brand above.</div>';
        return;
    }

    let html = '';
    brandsData.forEach(category => {
        if (category.brand_names) {
            const brandNames = category.brand_names.split(',');
            const brandIds = category.brand_ids.split(',');
            
            html += `
                <div class="category-section">
                    <h3 class="category-title">${escapeHtml(category.cat_name)}</h3>
                    <div class="brands-grid">
                        ${brandNames.map((name, index) => `
                            <div class="brand-card">
                                <div class="brand-info">
                                    <span class="brand-name">${escapeHtml(name)}</span>
                                </div>
                                <div class="brand-actions">
                                    <button type="button" onclick="editBrand(${brandIds[index]}, '${escapeHtml(name)}', ${category.cat_id})" class="btn-warning">Edit</button>
                                    <button type="button" onclick="deleteBrand(${brandIds[index]}, '${escapeHtml(name)}')" class="btn-danger">Delete</button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        } else {
            html += `
                <div class="category-section">
                    <h3 class="category-title">${escapeHtml(category.cat_name)}</h3>
                    <div class="no-brands-in-category">No brands in this category yet.</div>
                </div>
            `;
        }
    });

    container.innerHTML = html;
}

// Add new brand
function addBrand() {
    const brandName = document.getElementById('brand_name').value.trim();
    const categoryId = document.getElementById('category_id').value;
    
    // Validate inputs
    const brandValidation = validateBrandName(brandName);
    if (!brandValidation.valid) {
        showModal('Validation Error', brandValidation.message, 'error');
        return;
    }
    
    const categoryValidation = validateCategory(categoryId);
    if (!categoryValidation.valid) {
        showModal('Validation Error', categoryValidation.message, 'error');
        return;
    }

    const formData = new FormData();
    formData.append('brand_name', brandName);
    formData.append('category_id', categoryId);

    fetch('../actions/add_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            document.getElementById('brand_name').value = '';
            document.getElementById('category_id').value = '';
            fetchBrands(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to add brand', 'error');
    });
}

// Update brand
function updateBrand() {
    const brandId = document.getElementById('edit_brand_id').value;
    const brandName = document.getElementById('edit_brand_name').value.trim();
    const categoryId = document.getElementById('edit_category_id').value;
    
    // Validate inputs
    const brandValidation = validateBrandName(brandName);
    if (!brandValidation.valid) {
        showModal('Validation Error', brandValidation.message, 'error');
        return;
    }
    
    const categoryValidation = validateCategory(categoryId);
    if (!categoryValidation.valid) {
        showModal('Validation Error', categoryValidation.message, 'error');
        return;
    }

    const formData = new FormData();
    formData.append('brand_id', brandId);
    formData.append('brand_name', brandName);
    formData.append('category_id', categoryId);

    fetch('../actions/update_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            cancelEdit();
            fetchBrands(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to update brand', 'error');
    });
}

// Delete brand
function deleteBrand(brandId, brandName) {
    if (!confirm('Are you sure you want to delete the brand "' + brandName + '"?')) {
        return;
    }

    const formData = new FormData();
    formData.append('brand_id', brandId);

    fetch('../actions/delete_brand_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            fetchBrands(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to delete brand', 'error');
    });
}

// Edit brand (show edit form)
function editBrand(brandId, brandName, categoryId) {
    document.getElementById('edit_brand_id').value = brandId;
    document.getElementById('edit_brand_name').value = brandName;
    document.getElementById('edit_category_id').value = categoryId;
    document.getElementById('edit-form').style.display = 'block';
    document.getElementById('edit-form').scrollIntoView({ behavior: 'smooth' });
}

// Cancel edit
function cancelEdit() {
    document.getElementById('edit-form').style.display = 'none';
    document.getElementById('edit_brand_id').value = '';
    document.getElementById('edit_brand_name').value = '';
    document.getElementById('edit_category_id').value = '';
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load brands and categories on page load
    fetchBrands();
    fetchCategories();
    
    // Handle create form submission
    document.getElementById('create-form').addEventListener('submit', function(e) {
        e.preventDefault();
        addBrand();
    });
    
    // Handle edit form submission
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateBrand();
    });
});
