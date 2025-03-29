<template>
  <nav 
    class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-md z-50"
    aria-label="Main Navigation"
  >
    <div class="max-w-screen-xl mx-auto px-4">
      <div class="flex justify-around items-center h-16">
        <!-- Home -->
        <router-link 
          to="/" 
          class="flex flex-col items-center justify-center relative nav-item"
          data-testid="home-nav"
        >
          <div 
            :class="[$route.path === '/' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']"
            class="transition-colors"
            data-testid="home-nav-item"
            :aria-current="$route.path === '/' ? 'page' : undefined"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <span class="text-xs mt-1" :class="[$route.path === '/' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']">Home</span>
          <div v-if="$route.path === '/'" class="absolute -top-1 w-1.5 h-1.5 rounded-full bg-purple-600"></div>
        </router-link>
        
        <!-- Calendar -->
        <router-link 
          to="/calendar" 
          class="flex flex-col items-center justify-center relative nav-item"
          data-testid="calendar-nav"
        >
          <div 
            :class="[$route.path === '/calendar' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']"
            class="transition-colors"
            data-testid="calendar-nav-item"
            :aria-current="$route.path === '/calendar' ? 'page' : undefined"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="text-xs mt-1" :class="[$route.path === '/calendar' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']">Calendar</span>
          <div v-if="$route.path === '/calendar'" class="absolute -top-1 w-1.5 h-1.5 rounded-full bg-purple-600"></div>
        </router-link>
        
        <!-- Stats -->
        <router-link 
          to="/stats" 
          class="flex flex-col items-center justify-center relative nav-item"
          data-testid="stats-nav"
        >
          <div 
            :class="[$route.path === '/stats' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']"
            class="transition-colors"
            data-testid="stats-nav-item"
            :aria-current="$route.path === '/stats' ? 'page' : undefined"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <span class="text-xs mt-1" :class="[$route.path === '/stats' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']">Stats</span>
          <div v-if="$route.path === '/stats'" class="absolute -top-1 w-1.5 h-1.5 rounded-full bg-purple-600"></div>
        </router-link>
        
        <!-- Profile -->
        <router-link 
          to="/profile" 
          class="flex flex-col items-center justify-center relative nav-item"
          data-testid="profile-nav"
        >
          <div 
            :class="[$route.path === '/profile' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']"
            class="transition-colors"
            data-testid="profile-nav-item"
            :aria-current="$route.path === '/profile' ? 'page' : undefined"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
          <span class="text-xs mt-1" :class="[$route.path === '/profile' ? 'text-purple-600' : 'text-gray-500 dark:text-gray-400']">Profile</span>
          <div v-if="$route.path === '/profile'" class="absolute -top-1 w-1.5 h-1.5 rounded-full bg-purple-600"></div>
        </router-link>
      </div>
    </div>
    <!-- FAB -->
    <div class="absolute -top-14 left-1/2 transform -translate-x-1/2">
      <button 
        @click="$router.push('/todos/new')" 
        class="w-14 h-14 rounded-full bg-purple-600 text-white flex items-center justify-center shadow-lg hover:bg-purple-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'BottomNavigation'
}
</script>

<style scoped>
.nav-item {
  @apply text-gray-500 dark:text-gray-400 w-16 h-16;
  transition: color 0.2s ease, transform 0.2s ease;
}

.nav-item:hover {
  @apply text-gray-800 dark:text-gray-200;
  transform: translateY(-2px);
}

.nav-item.router-link-active {
  @apply text-purple-600;
}

:root {
  --primary: #8b5cf6;  /* Purple 500 */
  --secondary: #f97316; /* Orange 500 */
}
</style> 