<template>
  <div class="container pb-20" data-testid="profile-view">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Profile</h1>
    
    <div class="grid gap-6">
      <!-- User Profile Card -->
      <div class="task-card p-4">
        <div class="flex items-center">
          <div class="w-16 h-16 rounded-full bg-[var(--primary)] flex items-center justify-center text-white text-2xl font-semibold">
            {{ userInitials }}
          </div>
          <div class="ml-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ user.name }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
          </div>
        </div>
      </div>
      
      <!-- Settings -->
      <div class="task-card p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Settings</h2>
        
        <!-- Theme Toggle -->
        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-medium text-gray-800 dark:text-gray-200">Dark Mode</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Toggle between light and dark theme</p>
            </div>
            <button 
              @click="toggleDarkMode" 
              class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:ring-offset-2"
              :class="isDarkMode ? 'bg-[var(--primary)]' : 'bg-gray-200'"
              data-testid="dark-mode-toggle"
            >
              <span 
                :class="isDarkMode ? 'translate-x-5' : 'translate-x-0'"
                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
              >
                <span 
                  v-if="isDarkMode" 
                  class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                >
                  <svg class="h-3 w-3 text-[var(--primary)]" fill="currentColor" viewBox="0 0 12 12">
                    <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" />
                  </svg>
                </span>
                <span 
                  v-else
                  class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                >
                  <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                    <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>
              </span>
            </button>
          </div>
        </div>
        
        <!-- Notification Settings -->
        <div class="py-3 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-medium text-gray-800 dark:text-gray-200">Notifications</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Enable task due notifications</p>
            </div>
            <button 
              @click="toggleNotifications" 
              class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-[var(--primary)] focus:ring-offset-2"
              :class="notificationsEnabled ? 'bg-[var(--primary)]' : 'bg-gray-200'"
            >
              <span 
                :class="notificationsEnabled ? 'translate-x-5' : 'translate-x-0'"
                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
              />
            </button>
          </div>
        </div>
        
        <!-- Language Settings -->
        <div class="py-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-medium text-gray-800 dark:text-gray-200">Language</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">Choose your preferred language</p>
            </div>
            <select 
              v-model="language" 
              class="block w-28 rounded-md border-gray-300 shadow-sm focus:border-[var(--primary)] focus:ring-[var(--primary)] sm:text-sm"
            >
              <option value="en">English</option>
              <option value="es">Spanish</option>
              <option value="fr">French</option>
              <option value="de">German</option>
            </select>
          </div>
        </div>
      </div>
      
      <!-- Account Actions -->
      <div class="task-card p-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Account</h2>
        
        <div class="space-y-3">
          <button class="w-full py-2 px-4 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-800 dark:text-gray-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Edit Profile
          </button>
          
          <button class="w-full py-2 px-4 text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition text-gray-800 dark:text-gray-200 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Change Password
          </button>
          
          <button 
            @click="logout" 
            class="w-full py-2 px-4 text-left rounded-lg hover:bg-red-50 dark:hover:bg-red-900 transition text-red-600 dark:text-red-400 flex items-center"
            data-testid="logout-button"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const router = useRouter();
    
    const isDarkMode = ref(localStorage.getItem('darkMode') === 'true');
    const notificationsEnabled = ref(localStorage.getItem('notifications') !== 'false');
    const language = ref(localStorage.getItem('language') || 'en');
    
    const user = computed(() => store.state.user);
    
    const userInitials = computed(() => {
      if (!user.value || !user.value.name) return '';
      return user.value.name
        .split(' ')
        .map(name => name[0])
        .join('')
        .toUpperCase();
    });
    
    function toggleDarkMode() {
      isDarkMode.value = !isDarkMode.value;
      localStorage.setItem('darkMode', isDarkMode.value);
      document.documentElement.classList.toggle('dark', isDarkMode.value);
    }
    
    function toggleNotifications() {
      notificationsEnabled.value = !notificationsEnabled.value;
      localStorage.setItem('notifications', notificationsEnabled.value);
    }
    
    async function logout() {
      try {
        await store.dispatch('logout');
        router.push('/login');
      } catch (error) {
        console.error('Error during logout:', error);
      }
    }
    
    return {
      user,
      userInitials,
      isDarkMode,
      notificationsEnabled,
      language,
      toggleDarkMode,
      toggleNotifications,
      logout
    };
  }
};
</script> 