// Category Management JavaScript

// Validation functions
function validateCategoryName(catName) {
    if (!catName || catName.trim() === '') {
        return { valid: false, message: 'Category name is required' };
    }
    
    if (catName.length < 2) {
        return { valid: false, message: 'Category name must be at least 2 characters long' };
    }
    
    if (catName.length > 100) {
        return { valid: false, message: 'Category name must be less than 100 characters' };
    }
    
    // Check for valid characters (letters, numbers, spaces, hyphens, underscores)
    if (!/^[a-zA-Z0-9\s\-_]+$/.test(catName)) {
        return { valid: false, message: 'Category name can only contain letters, numbers, spaces, hyphens, and underscores' };
    }
    
    return { valid: true, message: '' };
}

// Modal functions
function showModal(title, message, type = 'info') {
    // Remove existing modal if any
    const existingModal = document.getElementById('category-modal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal
    const modal = document.createElement('div');
    modal.id = 'category-modal';
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
    const modal = document.getElementById('category-modal');
    if (modal) {
        modal.remove();
    }
}

// Fetch all categories
function fetchCategories() {
    fetch('../actions/fetch_category_action.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    /*
    .then(response => response.text())  // Changed from .json() to .text()
    .then(text => {
        console.log('Raw response:', text);  // See what's actually returned
        const data = JSON.parse(text);  // Then parse it
    })
    */
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            displayCategories(data.data);
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to fetch categories', 'error');
    });

}

// Display categories in the table
function displayCategories(categories) {
    const tbody = document.querySelector('#categories-table tbody');
    if (!tbody) return;

    if (categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No categories found. Create your first category above.</td></tr>';
        return;
    }

    tbody.innerHTML = categories.map(cat => `
        <tr>
            <td>${cat.cat_id}</td>
            <td>${escapeHtml(cat.cat_name)}</td>
            <td class="actions">
                <button type="button" onclick="editCategory(${cat.cat_id}, '${escapeHtml(cat.cat_name)}')" class="btn-warning">Edit</button>
                <button type="button" onclick="deleteCategory(${cat.cat_id}, '${escapeHtml(cat.cat_name)}')" class="btn-danger">Delete</button>
            </td>
        </tr>
    `).join('');
}

// Add new category
function addCategory() {
    const catName = document.getElementById('cat_name').value.trim();
    
    // Validate category name
    const validation = validateCategoryName(catName);
    if (!validation.valid) {
        showModal('Validation Error', validation.message, 'error');
        return;
    }

    const formData = new FormData();
    formData.append('cat_name', catName);

    fetch('../actions/add_category_action.php', {
        method: 'POST',
        body: formData
    })
       /*
    .then(response => response.text())
    .then(text => {
     
        console.log('Full raw response:', text);
        
        // Show first 500 characters in alert
        alert('Response (first 500 chars): ' + text.substring(0, 500));
        
        // Try to parse JSON
        const data = JSON.parse(text);
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            document.getElementById('cat_name').value = '';
            fetchCategories();
        } else {
            showModal('Error', data.message, 'error');
        }
    })
        */
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            document.getElementById('cat_name').value = '';
            fetchCategories(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to add category', 'error');
    });
}

// Update category
function updateCategory() {
    const catId = document.getElementById('edit_cat_id').value;
    const catName = document.getElementById('edit_cat_name').value.trim();
    
    // Validate category name
    const validation = validateCategoryName(catName);
    if (!validation.valid) {
        showModal('Validation Error', validation.message, 'error');
        return;
    }

    const formData = new FormData();
    formData.append('cat_id', catId);
    formData.append('cat_name', catName);

    fetch('../actions/update_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            cancelEdit();
            fetchCategories(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to update category', 'error');
    });
}

// Delete category
function deleteCategory(catId, catName) {
    if (!confirm('Are you sure you want to delete the category "' + catName + '"?')) {
        return;
    }

    const formData = new FormData();
    formData.append('cat_id', catId);

    fetch('../actions/delete_category_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showModal('Success', data.message, 'success');
            fetchCategories(); // Refresh the list
        } else {
            showModal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'Failed to delete category', 'error');
    });
}

// Edit category (show edit form)
function editCategory(catId, catName) {
    document.getElementById('edit_cat_id').value = catId;
    document.getElementById('edit_cat_name').value = catName;
    document.getElementById('edit-form').style.display = 'block';
    document.getElementById('edit-form').scrollIntoView({ behavior: 'smooth' });
}

// Cancel edit
function cancelEdit() {
    document.getElementById('edit-form').style.display = 'none';
    document.getElementById('edit_cat_id').value = '';
    document.getElementById('edit_cat_name').value = '';
}

// Show message (legacy function - now uses modals)
function showMessage(type, message) {
    showModal(type === 'success' ? 'Success' : 'Error', message, type);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load categories on page load
    fetchCategories();
    
    // Handle create form submission
    document.getElementById('create-form').addEventListener('submit', function(e) {
        e.preventDefault();
        addCategory();
    });
    
    // Handle edit form submission
    document.getElementById('edit-form').addEventListener('submit', function(e) {
        e.preventDefault();
        updateCategory();
    });
});
