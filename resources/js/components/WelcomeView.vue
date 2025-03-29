<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-md w-full text-center">
      <!-- Logo/icon -->
      <div class="mb-6 flex justify-center">
        <div class="h-24 w-24 rounded-full bg-purple-600 flex items-center justify-center shadow-lg">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </div>
      </div>
      
      <!-- App name and tagline -->
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Taskify</h1>
      <p class="text-gray-600 dark:text-gray-300 text-lg mb-8">The smarter way to manage your tasks</p>
      
      <!-- Features -->
      <div class="space-y-4 mb-10 text-left bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-3">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Organize with Categories</h2>
            <p class="text-gray-500 dark:text-gray-400">Group your tasks by category to stay organized</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-3">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Calendar Integration</h2>
            <p class="text-gray-500 dark:text-gray-400">Visualize your tasks with our calendar view</p>
          </div>
        </div>
        
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div class="ml-3">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Track Your Progress</h2>
            <p class="text-gray-500 dark:text-gray-400">Monitor completion with progress indicators and stats</p>
          </div>
        </div>
      </div>
      
      <!-- Buttons -->
      <div class="space-y-3">
        <router-link to="/login" class="w-full block py-3 px-4 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg text-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
          Get Started
        </router-link>
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Already have an account? 
          <router-link to="/login" class="text-purple-600 hover:text-purple-500 font-medium">Sign In</router-link>
        </div>
      </div>
    </div>
    
    <!-- App version -->
    <div class="mt-8 text-sm text-gray-500 dark:text-gray-400">
      Version 1.0.0
    </div>
  </div>
</template>

<script>
export default {
  name: 'WelcomeView',
  
  // Redirect to home if already logged in
  beforeRouteEnter(to, from, next) {
    if (localStorage.getItem('auth_token')) {
      next('/');
    } else {
      next();
    }
  }
}
</script>

<style scoped>
/* Animation for the logo */
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.h-24 {
  animation: pulse 2s infinite;
}
</style> 