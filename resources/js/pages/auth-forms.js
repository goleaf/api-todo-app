document.addEventListener('DOMContentLoaded', function() {
    // Logic common to auth forms (if any)
    // Example: Password visibility toggle
    const togglePasswordVisibility = (button) => {
        const targetId = button.getAttribute('data-target');
        const targetInput = document.getElementById(targetId);
        const icon = button.querySelector('svg'); // Assuming button contains an SVG icon
        if (!targetInput || !icon) return;

        if (targetInput.type === 'password') {
            targetInput.type = 'text';
            // Change icon to 'eye-off' or similar
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0 1 12 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 0 1 1.563-3.029m5.858.908a3 3 0 1 1 4.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-1.293-.775M6.75 12a5.25 5.25 0 0 0 .75 2.535M12 12a5.25 5.25 0 0 0-3-10.035M12 12h.008" />`;
        } else {
            targetInput.type = 'password';
            // Change icon back to 'eye'
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />`;
        }
    }

    document.querySelectorAll('[data-toggle-password]').forEach(button => {
        button.addEventListener('click', () => togglePasswordVisibility(button));
    });
}); 