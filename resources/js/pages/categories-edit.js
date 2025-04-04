document.addEventListener('DOMContentLoaded', function() {
    // Logic for categories/edit.blade.php
    // Example: Color picker sync (similar to smart-tags-form.js)
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
}); 