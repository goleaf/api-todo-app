<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- App Layout -->
    <div class="flex flex-col min-h-screen">
      <!-- Navbar -->
      <AppNavbar v-if="authStore.isAuthenticated" />
      
      <!-- Main Content -->
      <main class="flex-grow">
        <div v-if="authStore.isAuthenticated" class="flex">
          <!-- Sidebar -->
          <AppSidebar v-if="appStore.isSidebarOpen" />
          
          <!-- Content -->
          <div class="flex-1 p-4 sm:p-6 transition-all duration-200">
            <router-view v-slot="{ Component }">
              <transition name="fade" mode="out-in">
                <component :is="Component" />
              </transition>
            </router-view>
          </div>
        </div>
        
        <!-- Auth Pages (no sidebar) -->
        <div v-else class="w-full">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
    
    <!-- Loading Overlay -->
    <div v-if="appStore.isLoading" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
    
    <!-- Toasts -->
    <div class="fixed bottom-4 right-4 z-50 space-y-2">
      <transition-group name="toast">
        <div
          v-for="toast in appStore.toasts"
          :key="toast.id"
          class="toast p-4 rounded-lg shadow-lg max-w-md flex items-start"
          :class="{
            'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100': toast.type === 'success',
            'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100': toast.type === 'error',
            'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100': toast.type === 'info',
            'bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100': toast.type === 'warning'
          }"
        >
          <div class="flex-1">{{ toast.message }}</div>
          <button 
            @click="appStore.removeToast(toast.id)" 
            class="ml-4 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100"
          >
            &times;
          </button>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/app';
import AppNavbar from '@/components/layout/AppNavbar.vue';
import AppSidebar from '@/components/layout/AppSidebar.vue';

const authStore = useAuthStore();
const appStore = useAppStore();

// Initialization
onMounted(() => {
  // Set page title
  document.title = import.meta.env.VITE_APP_NAME || 'Todo App';
});
</script>

<style>
/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateY(30px);
}
</style> 