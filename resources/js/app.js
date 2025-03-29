import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { Capacitor } from '@capacitor/core';
import { initPushNotifications } from './services/pushNotifications';
import { initFonts } from './utils/fonts';

// Import base components
import BaseCard from './components/base/BaseCard.vue';
import BaseButton from './components/base/BaseButton.vue';
import BaseInput from './components/base/BaseInput.vue';
import BaseModal from './components/base/BaseModal.vue';

// Import FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { 
    faUser, faLock, faEnvelope, faExclamationCircle, 
    faCheckCircle, faUserPlus, faSignInAlt, faTasks, 
    faCalendar, faChartBar, faUserCircle, faHome,
    faPlus, faTrash, faEdit, faCheck, faTimes,
    faBars, faSignOutAlt, faMoon, faSun, faSearch,
    faFilter, faSort, faEye, faEyeSlash, faClipboardList,
    faListAlt, faStar, faExclamationTriangle, faCaretUp,
    faCaretDown, faClock, faCalendarCheck, faCalendarTimes,
    faFlag, faPaperclip, faTag, faFolder,
    faSave, faUpload, faDownload, faTrashAlt, faPencilAlt,
    faMinus, faAngleRight, faAngleLeft, faAngleDown,
    faAngleUp, faEllipsisV, faEllipsisH, faInfoCircle,
    faQuestionCircle, faThumbsUp, faThumbsDown, faArrowRight,
    faArrowLeft, faArrowUp, faArrowDown, faCalendarDay
} from '@fortawesome/free-solid-svg-icons';

import {
    faCircle, faSquare, faCheckCircle as farCheckCircle,
    faClock as farClock, faHeart, faStar as farStar,
    faUser as farUser, faEnvelope as farEnvelope, faBell as farBell,
    faFile, faFolder as farFolder, faEye as farEye,
    faEyeSlash as farEyeSlash, faTrashAlt as farTrashAlt,
    faEdit as farEdit, faClipboard as farClipboard,
    faSave as farSave, faCheckSquare, faSquare as farSquare,
    faBellSlash, faCalendarAlt as farCalendarAlt
} from '@fortawesome/free-regular-svg-icons';

import {
    faGithub, faGoogle, faTwitter, faFacebook, faLinkedin,
    faInstagram, faYoutube, faApple, faMicrosoft
} from '@fortawesome/free-brands-svg-icons';

// Import main CSS
import '../css/app.css';

// Initialize fonts
initFonts();

// Add icons to the library
library.add(
    // Solid icons
    faUser, faLock, faEnvelope, faExclamationCircle, faCheckCircle,
    faUserPlus, faSignInAlt, faTasks, faCalendar, faChartBar,
    faUserCircle, faHome, faPlus, faTrash, faEdit, faCheck, faTimes,
    faBars, faSignOutAlt, faMoon, faSun, faSearch, faFilter, faSort,
    faEye, faEyeSlash, faClipboardList, faListAlt, faStar,
    faExclamationTriangle, faCaretUp, faCaretDown, faClock,
    faCalendarCheck, faCalendarTimes, faFlag, faPaperclip, faTag,
    faFolder, faSave, faUpload, faDownload, faTrashAlt,
    faPencilAlt, faPlus, faMinus, faAngleRight, faAngleLeft,
    faAngleDown, faAngleUp, faEllipsisV, faEllipsisH, faInfoCircle,
    faQuestionCircle, faThumbsUp, faThumbsDown, faArrowRight,
    faArrowLeft, faArrowUp, faArrowDown, faCalendarDay,
    
    // Regular icons
    faCircle, faSquare, farCheckCircle, farCalendarAlt, farClock,
    faHeart, farStar, farUser, farEnvelope, farBell, faFile, farFolder,
    farEye, farEyeSlash, farTrashAlt, farEdit, farClipboard, farSave,
    faCheckSquare, farSquare, faBellSlash,
    
    // Brand icons
    faGithub, faGoogle, faTwitter, faFacebook, faLinkedin,
    faInstagram, faYoutube, faApple, faMicrosoft
);

// Configure Inertia.js progress indicator
InertiaProgress.init({
  color: '#8b5cf6',
  showSpinner: true,
});

// Axios setup
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Get token from local storage and set as default authorization header
const localToken = localStorage.getItem('auth_token');
if (localToken) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${localToken}`;
}

// Pusher setup
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Create Inertia App
createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        return pages[`./pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        // Create Pinia store
        const pinia = createPinia();
        
        // Register plugins
        app.use(plugin);
        app.use(pinia);
        
        // Register global components
        app.component('BaseCard', BaseCard);
        app.component('BaseButton', BaseButton);
        app.component('BaseInput', BaseInput);
        app.component('BaseModal', BaseModal);
        app.component('FontAwesomeIcon', FontAwesomeIcon);
        
        // Initialize dark mode
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        
        // Initialize app
        initApp();
        
        app.mount(el);
    },
});

// Define main app initialization function to handle async operations
async function initApp() {
    // Initialize push notifications if we're on a native mobile platform
    if (Capacitor.isNativePlatform()) {
        await initPushNotifications();
    }
}
