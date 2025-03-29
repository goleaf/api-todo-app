<template>
  <div class="w-64 bg-white dark:bg-gray-800 h-screen shadow-md fixed md:static transition-all duration-300 z-30 border-r border-gray-200 dark:border-gray-700">
    <div class="h-full flex flex-col">
      <!-- Sidebar Header -->
      <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Navigation</h2>
        <button 
          @click="appStore.toggleSidebar" 
          class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden focus:outline-none focus:ring-2 focus:ring-primary transition-colors duration-200"
          aria-label="Close sidebar"
        >
          <FontAwesomeIcon icon="times" class="h-5 w-5" />
        </button>
      </div>
      
      <!-- Navigation Menu -->
      <nav class="flex-1 overflow-y-auto pt-4">
        <ul class="space-y-1 px-2">
          <li v-for="item in navItems" :key="item.path">
            <router-link 
              :to="item.path" 
              class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out" 
              :class="[
                isActive(item.path) 
                  ? 'bg-primary-50 text-primary dark:bg-primary-900/30 dark:text-primary-light border-l-4 border-primary shadow-sm'
                  : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-primary-light'
              ]"
            >
              <FontAwesomeIcon :icon="item.icon" class="h-5 w-5 mr-3" :class="{ 'text-primary': isActive(item.path) }" />
              <span>{{ item.name }}</span>
            </router-link>
          </li>
        </ul>
        
        <!-- Categories Section -->
        <div class="mt-8 px-3">
          <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            Categories
          </h3>
          <div class="mt-2 space-y-1">
            <router-link 
              to="/categories" 
              class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
              :class="{ 'bg-primary-50 text-primary dark:bg-primary-900/30 dark:text-primary-light': isActive('/categories') }"
            >
              <FontAwesomeIcon icon="tags" class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-400 group-hover:text-primary dark:group-hover:text-primary-light" />
              <span>Manage Categories</span>
            </router-link>
            
            <div class="pl-3 mt-3 space-y-1">
              <router-link 
                v-for="category in categories"
                :key="category.id"
                :to="`/tasks?category=${category.id}`"
                class="group flex items-center px-3 py-2 text-sm rounded-md transition-colors duration-200 hover:text-primary"
                :class="{
                  'font-medium': isActiveCategoryFilter(category.id)
                }"
              >
                <span 
                  class="h-3 w-3 rounded-full mr-3"
                  :style="{ backgroundColor: category.color || '#9CA3AF' }"
                ></span>
                <span 
                  class="truncate"
                  :class="isActiveCategoryFilter(category.id) ? 'text-primary dark:text-primary-light' : 'text-gray-700 dark:text-gray-300'"
                >
                  {{ category.name }}
                </span>
              </router-link>
            </div>
          </div>
        </div>
      </nav>
      
      <!-- Sidebar Footer -->
      <div class="p-4 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-500 dark:text-gray-400">
            <p>{{ appInfo.name }}</p>
            <p class="text-xs">v{{ appInfo.version }}</p>
          </div>
          
          <div>
            <button 
              @click="appStore.toggleDarkMode" 
              class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200"
              :aria-label="appStore.darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <FontAwesomeIcon 
                :icon="appStore.darkMode ? 'sun' : 'moon'" 
                class="h-5 w-5"
              />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAppStore } from '@/stores/app';
import { useTaskStore } from '@/stores/tasks';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const route = useRoute();
const appStore = useAppStore();
const taskStore = useTaskStore();

// Categories
const categories = ref([]);

// Navigation items
const navItems = [
  {
    name: 'Dashboard',
    path: '/',
    icon: 'home'
  },
  {
    name: 'All Tasks',
    path: '/tasks',
    icon: 'tasks'
  },
  {
    name: 'Create Task',
    path: '/tasks/create',
    icon: 'plus-circle'
  },
  {
    name: 'Completed Tasks',
    path: '/tasks?status=completed',
    icon: 'check-circle'
  },
  {
    name: 'Pending Tasks',
    path: '/tasks?status=pending',
    icon: 'clock'
  },
  {
    name: 'Due Today',
    path: '/tasks?due=today',
    icon: 'calendar-day'
  }
];

// Check if route is active
const isActive = (path) => {
  if (path === '/') {
    return route.path === '/';
  }
  
  // Handle status query param specifically
  if (path.includes('?status=')) {
    const status = path.split('?status=')[1];
    return route.path === '/tasks' && route.query.status === status;
  }
  
  // Handle due query param specifically
  if (path.includes('?due=')) {
    const due = path.split('?due=')[1];
    return route.path === '/tasks' && route.query.due === due;
  }
  
  return route.path === path || 
    (path !== '/' && route.path.startsWith(path + '/'));
};

// Check if category filter is active
const isActiveCategoryFilter = (categoryId) => {
  return route.path === '/tasks' && route.query.category === categoryId.toString();
};

// Fetch categories
const fetchCategories = async () => {
  try {
    await taskStore.fetchCategories();
    categories.value = taskStore.categories;
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

// App info
const appInfo = computed(() => ({
  name: import.meta.env.VITE_APP_NAME || 'Todo App',
  version: import.meta.env.VITE_APP_VERSION || '1.0.0'
}));

// Lifecycle hooks
onMounted(() => {
  fetchCategories();
});
</script> 