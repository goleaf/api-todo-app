import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Import AlpineJS
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Import TomSelect CSS (if not handled globally by app.css/scss)
// You might import this in your main app.scss/app.css instead
// import 'tom-select/dist/css/tom-select.default.css'; 

// Note: TomSelect JS itself is imported dynamically in specific page scripts
// where needed (e.g., tasks-form.js), so no global import here is necessary
// unless you plan to use it everywhere.

// Echo, Pusher, etc. if needed
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// window.Echo = new Echo({ ... });
