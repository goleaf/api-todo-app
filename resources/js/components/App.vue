<template>
  <div>
    <!-- Show WelcomeView for non-authenticated users -->
    <welcome-view v-if="!isAuthenticated && isWelcomePage" />
    
    <!-- Main app for authenticated users -->
    <div v-else class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
      <nav class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex items-center">
              <div class="flex-shrink-0 flex items-center">
                <h1 class="text-xl font-bold text-[var(--primary)] dark:text-[var(--primary)]">TaskMaster</h1>
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
    </div>
  </div>
</template>

<script>
import { computed, ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';
import DarkModeToggle from './DarkModeToggle.vue';
import UserHeader from './UserHeader.vue';
import BottomNavigation from './BottomNavigation.vue';
import WelcomeView from './WelcomeView.vue';

export default {
  components: {
    DarkModeToggle,
    UserHeader,
    BottomNavigation,
    WelcomeView
  },
  setup() {
    const store = useStore();
    const router = useRouter();
    const route = useRoute();
    
    const isWelcomePage = ref(window.location.pathname === '/' || window.location.pathname === '/welcome');
    
    const user = computed(() => store.state.user);
    const isAuthenticated = computed(() => store.getters.isAuthenticated);

    const logout = async () => {
      store.dispatch('logout');
      router.push('/');
    };
    
    // Check if we should show the welcome page
    onMounted(() => {
      isWelcomePage.value = window.location.pathname === '/' || window.location.pathname === '/welcome';
    });

    return {
      user,
      isAuthenticated,
      isWelcomePage,
      logout
    };
  }
};
</script>

<style scoped>
:root {
  --primary: #8b5cf6; /* Purple-500 */
}

.dark {
  --primary: #a78bfa; /* Purple-400 */
}
</style> 