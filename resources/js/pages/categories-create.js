document.addEventListener('DOMContentLoaded', function() {
    // Logic for categories/create.blade.php
    // Example: Color picker sync (similar to smart-tags-form.js)
    const colorInput = document.getElementById('color');
    const colorHexInput = document.getElementById('color_hex');
    
    if (colorInput && colorHexInput) {
        // Set initial hex value if color has default
        colorHexInput.value = colorInput.value;

        colorInput.addEventListener('input', function() {
            colorHexInput.value = this.value;
        });
        
        colorHexInput.addEventListener('input', function() {
            // Basic validation for hex color
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                 colorInput.value = this.value;
            }
        });
    }
}); 