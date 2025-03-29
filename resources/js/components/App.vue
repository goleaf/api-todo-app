<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <!-- Display auth pages without header/nav for welcome, login and register -->
    <template v-if="isAuthRoute">
      <router-view></router-view>
    </template>
    
    <!-- Main app layout for authenticated/protected routes -->
    <template v-else>
      <nav class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <div class="flex-shrink-0 flex items-center">
                <div class="h-8 w-8 mr-2 rounded-full bg-purple-600 flex items-center justify-content-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                  </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Taskify</h1>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <dark-mode-toggle />
              <user-header v-if="isAuthenticated" />
            </div>
          </div>
        </div>
      </nav>

      <main class="py-4 pb-20">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <router-view></router-view>
        </div>
      </main>

      <!-- Bottom Navigation -->
      <bottom-navigation v-if="isAuthenticated"></bottom-navigation>
    </template>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import DarkModeToggle from './DarkModeToggle.vue';
import UserHeader from './UserHeader.vue';
import BottomNavigation from './BottomNavigation.vue';

export default {
  components: {
    DarkModeToggle,
    UserHeader,
    BottomNavigation
  },
  setup() {
    const store = useStore();
    const route = useRoute();
    
    const isAuthenticated = computed(() => store.getters.isAuthenticated);
    const isAuthRoute = computed(() => {
      return route.path === '/' || route.path === '/login' || route.path === '/register';
    });

    return {
      isAuthenticated,
      isAuthRoute
    };
  }
};
</script>

<style>
:root {
  --primary: #8b5cf6; /* Purple-500 */
  --primary-light: #a78bfa; /* Purple-400 */
  --primary-dark: #7c3aed; /* Purple-600 */
}

.dark {
  --primary: #a78bfa; /* Purple-400 */
  --primary-light: #c4b5fd; /* Purple-300 */
  --primary-dark: #8b5cf6; /* Purple-500 */
}

/* Common button styles */
.btn-primary {
  @apply bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg text-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500;
}

/* Task card style */
.task-card {
  @apply bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 transition-colors;
}
</style> 