<template>
  <nav class="bg-white dark:bg-gray-800 shadow border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <!-- Mobile sidebar toggle -->
          <div class="flex items-center mr-4 md:hidden">
            <button 
              @click="appStore.toggleSidebar" 
              class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-200"
              aria-label="Toggle sidebar"
            >
              <FontAwesomeIcon icon="bars" class="h-6 w-6" />
            </button>
          </div>
          
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <router-link to="/" class="flex items-center space-x-2">
              <img src="/logo.svg" alt="TodoApp Logo" class="h-8 w-8" />
              <span class="text-xl font-bold bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent hidden sm:block">
                TodoApp
              </span>
            </router-link>
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <!-- Notifications -->
          <div class="relative" ref="notificationsRef">
            <button 
              @click="toggleNotifications" 
              class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-200 relative"
              aria-label="Notifications"
            >
              <FontAwesomeIcon icon="bell" class="h-5 w-5" />
              <span v-if="unreadNotifications > 0" class="absolute top-0 right-0 transform translate-x-1/4 -translate-y-1/4 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
              </span>
            </button>
            
            <div v-if="isNotificationsOpen" 
              class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10 border border-gray-200 dark:border-gray-700"
            >
              <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <span class="font-medium text-gray-700 dark:text-gray-200">Notifications</span>
                <button v-if="unreadNotifications > 0" @click="markAllAsRead" class="text-xs text-primary hover:text-primary-dark">
                  Mark all as read
                </button>
              </div>
              
              <div v-if="notifications.length === 0" class="py-6 px-4 text-center text-gray-500 dark:text-gray-400">
                <FontAwesomeIcon icon="bell-slash" class="h-8 w-8 mb-2" />
                <p>No notifications yet</p>
              </div>
              
              <div v-else class="max-h-80 overflow-y-auto">
                <div
                  v-for="notification in notifications"
                  :key="notification.id"
                  class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer"
                  :class="{ 'border-l-4 border-primary': !notification.read }"
                  @click="readNotification(notification.id)"
                >
                  <div class="flex items-start">
                    <div class="flex-shrink-0">
                      <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                        <FontAwesomeIcon 
                          :icon="notification.icon || 'info-circle'" 
                          class="h-4 w-4 text-primary" 
                        />
                      </div>
                    </div>
                    <div class="ml-3 flex-1">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ notification.title }}
                      </p>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        {{ notification.message }}
                      </p>
                      <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        {{ formatNotificationTime(notification.created_at) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                <router-link to="/notifications" class="block text-center text-sm text-primary hover:text-primary-dark" @click="isNotificationsOpen = false">
                  View all notifications
                </router-link>
              </div>
            </div>
          </div>

          <!-- Dark mode toggle -->
          <button 
            @click="appStore.toggleDarkMode" 
            class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-200"
            :aria-label="appStore.darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <FontAwesomeIcon 
              :icon="appStore.darkMode ? 'sun' : 'moon'" 
              class="h-5 w-5" 
            />
          </button>
          
          <!-- Search button -->
          <button 
            @click="toggleSearch" 
            class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-200 hidden sm:block"
            aria-label="Search"
          >
            <FontAwesomeIcon icon="search" class="h-5 w-5" />
          </button>
          
          <!-- User menu -->
          <div class="relative" ref="userMenuRef">
            <button 
              @click="toggleUserMenu" 
              class="flex items-center text-sm focus:outline-none rounded-full p-1 hover:ring-2 hover:ring-primary transition-all duration-200"
              aria-expanded="false"
              aria-haspopup="true"
            >
              <div class="flex items-center space-x-2">
                <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary to-primary-dark flex items-center justify-center text-white shadow-sm">
                  {{ userInitials }}
                </div>
                <span class="hidden md:inline-block text-gray-700 dark:text-gray-300 font-medium">
                  {{ authStore.currentUser?.name || 'User' }}
                </span>
                <FontAwesomeIcon 
                  icon="angle-down" 
                  class="h-4 w-4 text-gray-400 hidden md:block transition-transform duration-200"
                  :class="{ 'transform rotate-180': isUserMenuOpen }"
                />
              </div>
            </button>
            
            <div 
              v-if="isUserMenuOpen" 
              class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10 border border-gray-200 dark:border-gray-700"
            >
              <div class="border-b border-gray-200 dark:border-gray-700 pb-2 px-4 pt-2">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ authStore.currentUser?.name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ authStore.currentUser?.email }}</p>
              </div>
              <router-link to="/profile" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" @click="isUserMenuOpen = false">
                <FontAwesomeIcon icon="user" class="mr-3 h-4 w-4 text-gray-500 group-hover:text-gray-600 dark:text-gray-400 dark:group-hover:text-gray-300" />
                Your Profile
              </router-link>
              <router-link to="/settings" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" @click="isUserMenuOpen = false">
                <FontAwesomeIcon icon="cog" class="mr-3 h-4 w-4 text-gray-500 group-hover:text-gray-600 dark:text-gray-400 dark:group-hover:text-gray-300" />
                Settings
              </router-link>
              <button @click="handleLogout" class="w-full text-left group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <FontAwesomeIcon icon="sign-out-alt" class="mr-3 h-4 w-4 text-gray-500 group-hover:text-gray-600 dark:text-gray-400 dark:group-hover:text-gray-300" />
                Sign out
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Search Dialog -->
    <div 
      v-if="isSearchOpen"
      class="fixed inset-0 z-50 overflow-y-auto"
      aria-labelledby="search-dialog"
      role="dialog"
      aria-modal="true"
    >
      <div class="min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" @click="toggleSearch"></div>

        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
            <div class="relative">
              <FontAwesomeIcon icon="search" class="h-5 w-5 text-gray-400 absolute left-3 top-3" />
              <input 
                type="text" 
                placeholder="Search..." 
                v-model="searchQuery"
                @keyup.enter="performSearch"
                class="form-input pl-10 py-2 w-full"
                ref="searchInput"
              />
              <button 
                v-if="searchQuery" 
                @click="searchQuery = ''"
                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
              >
                <FontAwesomeIcon icon="times" class="h-5 w-5" />
              </button>
            </div>
            
            <div v-if="recentSearches.length > 0" class="mt-4">
              <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Recent Searches</h3>
              <ul class="mt-2 space-y-1">
                <li v-for="(search, index) in recentSearches" :key="index">
                  <button 
                    @click="selectRecentSearch(search)"
                    class="w-full text-left flex items-center px-3 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-sm"
                  >
                    <FontAwesomeIcon icon="history" class="h-4 w-4 text-gray-400 mr-3" />
                    {{ search }}
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              type="button" 
              class="btn-primary w-full sm:w-auto sm:ml-3"
              @click="performSearch"
            >
              Search
            </button>
            <button 
              type="button" 
              class="btn-light mt-3 sm:mt-0 w-full sm:w-auto"
              @click="toggleSearch"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/app';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { formatDistanceToNow } from 'date-fns';

const router = useRouter();
const authStore = useAuthStore();
const appStore = useAppStore();

// User menu state
const isUserMenuOpen = ref(false);
const userMenuRef = ref(null);

// Notifications state
const isNotificationsOpen = ref(false);
const notificationsRef = ref(null);
const unreadNotifications = ref(0);
const notifications = ref([
  {
    id: 1,
    title: 'New feature added',
    message: 'Check out the new dashboard interface!',
    icon: 'bell',
    read: false,
    created_at: new Date(Date.now() - 3600000) // 1 hour ago
  },
  {
    id: 2,
    title: 'Task completed',
    message: 'You completed "Update documentation" task',
    icon: 'check-circle',
    read: true,
    created_at: new Date(Date.now() - 86400000) // 1 day ago
  }
]);

// Search state
const isSearchOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);
const recentSearches = ref(['dashboard redesign', 'user authentication', 'API documentation']);

// Toggle user menu
const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
  if (isUserMenuOpen.value) {
    isNotificationsOpen.value = false;
  }
};

// Toggle notifications
const toggleNotifications = () => {
  isNotificationsOpen.value = !isNotificationsOpen.value;
  if (isNotificationsOpen.value) {
    isUserMenuOpen.value = false;
  }
};

// Toggle search dialog
const toggleSearch = () => {
  isSearchOpen.value = !isSearchOpen.value;
  if (isSearchOpen.value) {
    nextTick(() => {
      searchInput.value?.focus();
    });
  }
};

// Mark notification as read
const readNotification = (id) => {
  const notification = notifications.value.find(n => n.id === id);
  if (notification && !notification.read) {
    notification.read = true;
    unreadNotifications.value--;
  }
};

// Mark all notifications as read
const markAllAsRead = () => {
  notifications.value.forEach(notification => {
    notification.read = true;
  });
  unreadNotifications.value = 0;
};

// Perform search
const performSearch = () => {
  if (searchQuery.value.trim()) {
    // Add to recent searches if not already there
    if (!recentSearches.value.includes(searchQuery.value)) {
      recentSearches.value.unshift(searchQuery.value);
      // Keep only the 5 most recent searches
      if (recentSearches.value.length > 5) {
        recentSearches.value.pop();
      }
    }
    
    // Navigate to search results
    router.push({
      path: '/search',
      query: { q: searchQuery.value }
    });
    
    toggleSearch();
  }
};

// Select a recent search
const selectRecentSearch = (search) => {
  searchQuery.value = search;
  performSearch();
};

// Format notification time
const formatNotificationTime = (date) => {
  return formatDistanceToNow(new Date(date), { addSuffix: true });
};

// Handle logout
const handleLogout = async () => {
  await authStore.logout();
  isUserMenuOpen.value = false;
  router.push('/login');
};

// Handle clicks outside menus
const handleClickOutside = (event) => {
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    isUserMenuOpen.value = false;
  }
  
  if (notificationsRef.value && !notificationsRef.value.contains(event.target)) {
    isNotificationsOpen.value = false;
  }
};

// Computed properties
const userInitials = computed(() => {
  const user = authStore.currentUser;
  if (!user || !user.name) return '';
  
  const nameParts = user.name.split(' ');
  if (nameParts.length === 1) return nameParts[0].charAt(0).toUpperCase();
  
  return (nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0)).toUpperCase();
});

// Calculate unread notifications
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  unreadNotifications.value = notifications.value.filter(n => !n.read).length;
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script> 