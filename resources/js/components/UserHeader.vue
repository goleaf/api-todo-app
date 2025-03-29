<template>
  <div class="flex items-center justify-end">
    <!-- Greeting section -->
    <div class="mr-4 text-right">
      <div class="font-bold text-lg text-gray-800 dark:text-gray-200">Hello, {{ userName }}!</div>
      <div class="text-gray-500 dark:text-gray-400 text-sm">{{ taskCount }} {{ taskCount === 1 ? 'task' : 'tasks' }} pending</div>
    </div>
    
    <!-- Avatar linking to profile -->
    <router-link to="/profile" class="focus:outline-none">
      <div class="relative group">
        <div 
          v-if="!userPhotoUrl" 
          class="rounded-full bg-purple-600 text-white flex items-center justify-center shadow-md transition-transform group-hover:scale-105" 
          style="width: 48px; height: 48px;"
        >
          <span class="font-semibold text-lg">{{ userInitial }}</span>
        </div>
        <img 
          v-else 
          :src="userPhotoUrl" 
          :alt="userName" 
          class="rounded-full shadow-md object-cover transition-transform group-hover:scale-105" 
          width="48" 
          height="48"
        >
        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></div>
      </div>
    </router-link>
    
    <!-- User menu (shown on mobile) -->
    <div class="relative ml-2 md:hidden" ref="dropdown" v-click-outside="closeDropdown">
      <button @click="toggleDropdown" class="p-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      
      <div 
        v-show="isDropdownOpen" 
        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-10"
      >
        <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
          Signed in as <span class="font-medium text-gray-700 dark:text-gray-300">{{ userEmail }}</span>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700"></div>
        
        <router-link to="/profile" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          My Profile
        </router-link>
        
        <router-link to="/" class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Dashboard
        </router-link>
        
        <div class="border-t border-gray-200 dark:border-gray-700"></div>
        
        <form method="POST" action="/logout" @submit="logout">
          <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 dark:hover:bg-opacity-30 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Sign Out
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted, watchEffect } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

export default {
  setup() {
    const store = useStore();
    const user = ref({
      name: '',
      email: '',
      photo_url: null
    });
    const taskCount = ref(0);
    const isDropdownOpen = ref(false);
    const dropdown = ref(null);
    
    const userName = computed(() => user.value.name || 'User');
    const userEmail = computed(() => user.value.email || '');
    const userPhotoUrl = computed(() => user.value.photo_url);
    const userInitial = computed(() => {
      return user.value.name ? user.value.name.charAt(0).toUpperCase() : 'U';
    });
    
    const fetchUserData = async () => {
      try {
        // First try to get user from store
        const storeUser = store.state.user;
        if (storeUser && storeUser.name) {
          user.value = storeUser;
        } else {
          // Otherwise fetch from API
          const response = await axios.get('/api/user');
          user.value = response.data;
          // Update store
          store.commit('setUser', response.data);
        }
        fetchTaskCount();
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };
    
    const fetchTaskCount = async () => {
      try {
        // First check if todos are in the store
        const todos = store.state.todos;
        if (todos && todos.length > 0) {
          taskCount.value = todos.filter(t => !t.completed).length;
        } else {
          const response = await axios.get('/api/tasks/pending-count');
          taskCount.value = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching task count:', error);
      }
    };
    
    const logout = async (event) => {
      event.preventDefault();
      try {
        await store.dispatch('logout');
        window.location.href = '/login';
      } catch (error) {
        console.error('Error logging out:', error);
      }
    };
    
    const toggleDropdown = () => {
      isDropdownOpen.value = !isDropdownOpen.value;
    };
    
    const closeDropdown = (event) => {
      if (dropdown.value && !dropdown.value.contains(event.target)) {
        isDropdownOpen.value = false;
      }
    };
    
    // Listen for changes in the todos state
    watchEffect(() => {
      const todos = store.state.todos;
      if (todos && todos.length > 0) {
        taskCount.value = todos.filter(t => !t.completed).length;
      }
    });
    
    onMounted(() => {
      fetchUserData();
    });
    
    return {
      userName,
      userEmail,
      userInitial,
      userPhotoUrl,
      taskCount,
      logout,
      isDropdownOpen,
      toggleDropdown,
      closeDropdown,
      dropdown
    };
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = function(event) {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  }
};
</script>

<style scoped>
/* Animation for hover effect */
.group:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease;
}

:root {
  --primary: #8b5cf6;  /* Purple 500 */
  --secondary: #f97316; /* Orange 500 */
}
</style> 