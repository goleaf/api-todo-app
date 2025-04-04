/**
 * Task form functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tag selector with TomSelect
    if (document.getElementById('tags')) {
        new TomSelect('#tags', {
            plugins: ['remove_button'],
            maxItems: null,
            placeholder: 'Select tags...'
        });
    }

    // Initialize date picker
    const dueDateInput = document.getElementById('due_date');
    if (dueDateInput) {
        // Add any specific date picker initialization here if needed
    }

    // Initialize file input
    const attachmentInput = document.getElementById('attachment');
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            if (fileName) {
                // You might want to display the file name somewhere
                console.log('Selected file:', fileName);
            }
        });
    }
}); 