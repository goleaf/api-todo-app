import './bootstrap';
import axios from 'axios';
import './tag-input';

// Import AlpineJS if you are using it
// import Alpine from 'alpinejs';
// window.Alpine = Alpine;
// Alpine.start();

// Import page-specific scripts
import './pages/tasks-form.js';
import './pages/smart-tags-form.js';
import './pages/smart-tags-index.js';
import './pages/time-entries-summary.js';
import './pages/time-entries-create.js';
import './pages/time-entries-index.js';
import './pages/settings-appearance.js';
import './pages/settings-notifications.js';
import './pages/auth-forms.js';
import './pages/categories-form.js';
import './pages/auth-reset-password.js';
import './pages/auth-forgot-password.js';
import './pages/auth-register.js';
import './pages/auth-login.js';
import './pages/categories-edit.js';
import './pages/categories-create.js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
