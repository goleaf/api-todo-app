import TomSelect from 'tom-select';

document.addEventListener('DOMContentLoaded', function() {
    const tagSelect = document.getElementById('tags');
    if (tagSelect && typeof TomSelect !== 'undefined') {
        new TomSelect(tagSelect, {
            plugins: ['remove_button'],
            create: true,        // Allow creating new tags
            createOnBlur: true,  // Create tag when focus leaves input
            // Optional: Load options via fetch if the list is very large
            // load: function(query, callback) { ... }
        });
    } else if (!tagSelect) {
        // console.log('Tag select element (#tags) not found on this page.');
    } else {
        console.error('TomSelect library not found. Make sure it is installed and loaded.');
    }
}); 