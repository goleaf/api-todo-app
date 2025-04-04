document.addEventListener('DOMContentLoaded', function() {
    // Color picker sync
    const colorInput = document.getElementById('color');
    const colorHexInput = document.getElementById('color_hex');
    
    if (colorInput && colorHexInput) {
        colorInput.addEventListener('input', function() {
            colorHexInput.value = this.value;
        });
        
        colorHexInput.addEventListener('input', function() {
            colorInput.value = this.value;
        });
    }

    // Helper function to toggle visibility based on checkbox
    function setupVisibilityToggle(checkboxId, optionsId) {
        const checkbox = document.getElementById(checkboxId);
        const options = document.getElementById(optionsId);
        
        if (checkbox && options) {
            // Initial state
            options.classList.toggle('hidden', !checkbox.checked);
            
            checkbox.addEventListener('change', function() {
                options.classList.toggle('hidden', !this.checked);
            });
        }
    }

    // Setup toggles for all filter sections
    setupVisibilityToggle('filter_by_due_date', 'due_date_options');
    setupVisibilityToggle('filter_by_priority', 'priority_options');
    setupVisibilityToggle('filter_by_status', 'status_options');
    setupVisibilityToggle('filter_by_category', 'category_options');
    setupVisibilityToggle('filter_by_tags', 'tags_options');
    setupVisibilityToggle('filter_by_time_entries', 'time_entries_options');
    setupVisibilityToggle('filter_by_attachments', 'attachments_options');
    setupVisibilityToggle('filter_by_created_date', 'created_date_options');
    
    // Due date operator change logic
    const dueDateOperator = document.getElementById('due_date_operator');
    const dueDateValueContainer = document.getElementById('due_date_value_container');
    const dueDateEndContainer = document.getElementById('due_date_end_container');
    
    if (dueDateOperator && dueDateValueContainer && dueDateEndContainer) {
        const toggleDueDateFields = () => {
            const value = dueDateOperator.value;
            dueDateValueContainer.classList.toggle('hidden', ['overdue', 'today', 'next_7_days'].includes(value));
            dueDateEndContainer.classList.toggle('hidden', value !== 'between');
        }
        // Initial state
        toggleDueDateFields();
        dueDateOperator.addEventListener('change', toggleDueDateFields);
    }
    
    // Specific tags toggle logic
    const specificTagsContainer = document.getElementById('specific_tags_container');
    const tagFilterRadios = document.querySelectorAll('input[name="tags_filter_type"]');
    
    if (specificTagsContainer && tagFilterRadios.length > 0) {
        const toggleSpecificTags = () => {
            const checkedRadio = document.querySelector('input[name="tags_filter_type"]:checked');
            specificTagsContainer.classList.toggle('hidden', !checkedRadio || checkedRadio.id !== 'has_specific_tags');
        }
        // Initial state
        toggleSpecificTags();
        tagFilterRadios.forEach(radio => {
            radio.addEventListener('change', toggleSpecificTags);
        });
    }

    // Initialize TomSelect for tags if it exists
    const tagsSelect = document.getElementById('tags_ids');
    if (tagsSelect && typeof TomSelect !== 'undefined') {
        new TomSelect('#tags_ids', {
            plugins: ['remove_button'],
            create: false,
            allowEmptyOption: true
        });
    }
}); 