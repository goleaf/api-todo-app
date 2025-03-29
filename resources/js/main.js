import { createApp, h } from 'vue';
import { createPinia } from 'pinia';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import axios from 'axios';
import { useAuthStore } from './stores/auth';
import { useAppStore } from './stores/app';

// Import CSS
import '../css/app.css';

// Import Roboto font directly from npm package
import '@fontsource/roboto/300.css';
import '@fontsource/roboto/400.css';
import '@fontsource/roboto/500.css';
import '@fontsource/roboto/700.css';

// Import font utils
import { initFonts } from './utils/fonts';

// Setup Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Import solid icons
import { 
  faBars, faCheck, faCheckCircle, faTimes, faEdit, faTrash, 
  faUser, faCog, faSignOutAlt, faMoon, faSun, faCalendarAlt, faChartBar, 
  faExclamationCircle, faBell, faSearch, faFilter, faSort, faEye, faEyeSlash,
  faClipboardList, faListAlt, faHome, faTasks, faStar, faExclamationTriangle,
  faCaretUp, faCaretDown, faClock, faCalendarCheck, faCalendarTimes,
  faFlag, faPaperclip, faTag, faTags, faFolder, faEnvelope, faLock, faUserPlus,
  faSignInAlt, faUserCog, faSave, faUpload, faDownload,
  faTrashAlt, faPencilAlt, faPlus, faMinus, faAngleRight, faAngleLeft,
  faAngleDown, faAngleUp, faEllipsisV, faEllipsisH, faInfoCircle, faQuestionCircle,
  faThumbsUp, faThumbsDown, faArrowRight, faArrowLeft, faArrowUp, faArrowDown,
  faCalendarDay, faCloudUploadAlt, faCloudDownloadAlt, faLongArrowAltRight, 
  faLongArrowAltLeft, faClipboard, faHistory, faChevronRight, faCalendar,
  faPlusCircle, faCheckDouble, faChartLine, faTachometerAlt, faInbox
} from '@fortawesome/free-solid-svg-icons';

// Import regular icons
import { 
  faCircle, faSquare, faCheckCircle as farCheckCircle,
  faClock as farClock, faHeart, faStar as farStar,
  faUser as farUser, faEnvelope as farEnvelope, faBell as farBell,
  faFile, faFolder as farFolder, faEye as farEye, faEyeSlash as farEyeSlash,
  faTrashAlt as farTrashAlt, faEdit as farEdit, faClipboard as farClipboard,
  faSave as farSave, faCheckSquare, faSquare as farSquare, faBellSlash,
  faCalendarAlt as farCalendarAlt
} from '@fortawesome/free-regular-svg-icons';

// Import brand icons
import { 
  faGithub, faGoogle, faTwitter, faFacebook, faLinkedin, 
  faInstagram, faYoutube, faApple, faMicrosoft
} from '@fortawesome/free-brands-svg-icons';

// Add icons to the library
library.add(
  // Solid icons
  faBars, faCheck, faCheckCircle, faTimes, faEdit, faTrash, 
  faUser, faCog, faSignOutAlt, faMoon, faSun, faCalendarAlt, faChartBar, 
  faExclamationCircle, faBell, faSearch, faFilter, faSort, faEye, faEyeSlash,
  faClipboardList, faListAlt, faHome, faTasks, faStar, faExclamationTriangle,
  faCaretUp, faCaretDown, faClock, faCalendarCheck, faCalendarTimes,
  faFlag, faPaperclip, faTag, faFolder, faEnvelope, faLock, faUserPlus,
  faSignInAlt, faUserCog, faSave, faUpload, faDownload,
  faTrashAlt, faPencilAlt, faPlus, faMinus, faAngleRight, faAngleLeft,
  faAngleDown, faAngleUp, faEllipsisV, faEllipsisH, faInfoCircle, faQuestionCircle,
  faThumbsUp, faThumbsDown, faArrowRight, faArrowLeft, faArrowUp, faArrowDown,
  faCalendarDay, faCloudUploadAlt, faCloudDownloadAlt, faLongArrowAltRight, 
  faLongArrowAltLeft, faClipboard, faHistory, faChevronRight, faCalendar,
  faPlusCircle, faCheckDouble, faChartLine, faTachometerAlt, faInbox,
  
  // Regular icons
  faCircle, faSquare, farCheckCircle, farCalendarAlt, farClock,
  faHeart, farStar, farUser, farEnvelope, farBell, faFile, farFolder,
  farEye, farEyeSlash, farTrashAlt, farEdit, farClipboard, farSave,
  faCheckSquare, farSquare, faBellSlash,
  
  // Brand icons
  faGithub, faGoogle, faTwitter, faFacebook, faLinkedin, 
  faInstagram, faYoutube, faApple, faMicrosoft
);

// Configure axios
axios.defaults.baseURL = import.meta.env.VITE_API_URL || '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Add axios response interceptor for handling 401 responses
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      // Check if we're not already on the login page
      if (router.currentRoute.value.name !== 'login') {
        // Redirect to login
        router.push({
          name: 'login',
          query: { redirect: router.currentRoute.value.fullPath }
        });
      }
    }
    return Promise.reject(error);
  }
);

// Initialize Inertia app
createInertiaApp({
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    // Create pinia store
    const pinia = createPinia();
    
    // Initialize fonts
    initFonts();
    
    // Create the application
    const app = createApp({ render: () => h(App, props) });
    
    // Register plugins and components
    app.use(plugin);
    app.use(pinia);
    app.use(ZiggyVue);
    app.component('FontAwesomeIcon', FontAwesomeIcon);
    
    // Initialize app settings
    const appStore = useAppStore();
    appStore.initDarkMode();
    
    // Mount the app
    app.mount(el);
  },
}); 