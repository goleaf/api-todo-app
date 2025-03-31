import "./libs/trix";
import './bootstrap';
import '../css/app.css';

import axios from 'axios';
import { initFonts } from './utils/fonts';

// Import FontAwesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { 
    faTachometerAlt, faTasks, faCheck, faCheckCircle, faCalendar, faCalendarAlt, 
    faUser, faUserPlus, faSignInAlt, faSignOutAlt, faMoon, faSun, faPlus, 
    faEye, faEdit, faTrashAlt, faSync, faBars, faTimes, faChevronDown, 
    faFlag, faExclamationCircle, faExclamationTriangle, faInfoCircle, 
    faBell, faPlay, faCode, faPaintBrush, faArrowRight, faClock, faTags,
    faSearch, faAngleUp, faAngleDown, faHistory, faChartPie, faChartLine,
    faFilter, faSort, faCalendarCheck, faCalendarDay, faKey, faUserEdit,
    faSave, faCheckDouble, faCircle
} from '@fortawesome/free-solid-svg-icons';

import { 
    faGithub, faTwitter, faLinkedin, faFacebook 
} from '@fortawesome/free-brands-svg-icons';

import {
    faCheckCircle as farCheckCircle,
    faCircle as farCircle
} from '@fortawesome/free-regular-svg-icons';

// Add icons to the library
library.add(
    // Solid icons
    faTachometerAlt, faTasks, faCheck, faCheckCircle, faCalendar, faCalendarAlt, 
    faUser, faUserPlus, faSignInAlt, faSignOutAlt, faMoon, faSun, faPlus, 
    faEye, faEdit, faTrashAlt, faSync, faBars, faTimes, faChevronDown, 
    faFlag, faExclamationCircle, faExclamationTriangle, faInfoCircle, 
    faBell, faPlay, faCode, faPaintBrush, faArrowRight, faClock, faTags,
    faSearch, faAngleUp, faAngleDown, faHistory, faChartPie, faChartLine,
    faFilter, faSort, faCalendarCheck, faCalendarDay, faKey, faUserEdit,
    faSave, faCheckDouble, faCircle,
    
    // Brand icons
    faGithub, faTwitter, faLinkedin, faFacebook,
    
    // Regular icons
    farCheckCircle, farCircle
);

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

// Set up dark mode
const setupDarkMode = () => {
    if (localStorage.getItem('darkMode') === 'true' || 
        (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Listen for dark mode toggle events from Livewire
document.addEventListener('dark-mode-toggle', () => {
    const isDarkMode = document.documentElement.classList.contains('dark');
    localStorage.setItem('darkMode', !isDarkMode);
    
    if (isDarkMode) {
        document.documentElement.classList.remove('dark');
    } else {
        document.documentElement.classList.add('dark');
    }
});

// Define main app initialization function
function initApp() {
    // Set up dark mode
    setupDarkMode();
    
    // Initialize fonts
    if (typeof initFonts === 'function') {
        initFonts();
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initApp();
});
