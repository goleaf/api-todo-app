// Functions for the delete confirmation modal
window.confirmDelete = function(tagId, tagName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const message = document.getElementById('deleteModalMessage');
    const modalTitle = document.getElementById('deleteModalTitle'); // Assuming a title element exists

    if (!modal || !form || !message) {
        console.error('Delete modal elements not found.');
        return;
    }

    // Construct the URL safely (ensure route base is correct if needed)
    let url = window.smartTagsAdminIndexUrl || '/admin/smart-tags'; // Fallback URL
    form.action = `${url.replace(/\/?$/, '')}/${tagId}`;
    
    if (modalTitle) {
        modalTitle.textContent = `Delete Smart Tag "${tagName}"`;
    }
    message.textContent = `Are you sure you want to delete this smart tag? This action cannot be undone.`;
    
    modal.classList.remove('hidden');
    // Optional: focus the confirm button
    modal.querySelector('[data-modal-confirm]')?.focus();
}

window.cancelDelete = function() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('deleteModal');
    
    // Close modal on outside click
    if (modal) {
        modal.addEventListener('click', function(event) {
            // Check if the click is directly on the modal background (data-modal-bg attribute)
            if (event.target.hasAttribute('data-modal-bg')) {
                window.cancelDelete();
            }
        });
        
        // Close modal on Escape key
        modal.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.cancelDelete();
            }
        });
        
        // Add event listeners to cancel buttons if they exist
        modal.querySelectorAll('[data-modal-cancel]').forEach(button => {
            button.addEventListener('click', window.cancelDelete);
        });
    }
    
    // Handle delete button clicks using event delegation if buttons are added dynamically
    document.body.addEventListener('click', function(event) {
        const deleteButton = event.target.closest('[data-delete-smart-tag-id]');
        if (deleteButton) {
            const tagId = deleteButton.getAttribute('data-delete-smart-tag-id');
            const tagName = deleteButton.getAttribute('data-delete-smart-tag-name') || 'this smart tag';
            // Pass the base URL for the form action
            window.smartTagsAdminIndexUrl = deleteButton.getAttribute('data-delete-smart-tag-url-base');
            window.confirmDelete(tagId, tagName);
        }
    });
}); 