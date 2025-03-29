<template>
  <div class="page-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <header class="mb-10">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
            <img src="/logo.svg" alt="Logo" class="h-12 w-12 mr-4" />
            <span>Dashboard</span>
            <FontAwesomeIcon icon="tachometer-alt" class="ml-3 h-5 w-5 text-primary" />
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-2 flex items-center">
            <FontAwesomeIcon icon="user" class="mr-2 h-4 w-4 text-primary-500" />
            Welcome back, <span class="font-medium ml-1">{{ user?.name || 'User' }}</span>
          </p>
        </div>
        <div>
          <BaseButton variant="primary" size="lg" class="shadow-lg hover:shadow-xl transition-all duration-300">
            <template #leftIcon>
              <FontAwesomeIcon 
                icon="plus-circle" 
                class="h-5 w-5" 
              />
            </template>
            Create New Task
          </BaseButton>
        </div>
      </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
      <!-- Tasks Summary Card -->
      <BaseCard class="col-span-1 shadow-md hover:shadow-lg transition-all duration-300 border-t-4 border-primary">
        <div class="p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
              <FontAwesomeIcon icon="tasks" class="h-5 w-5 text-primary mr-2" />
              Tasks
            </h3>
            <span class="bg-primary-100 text-primary-800 dark:bg-primary-900 dark:bg-opacity-30 dark:text-primary-300 px-3 py-1 rounded-full text-xs font-medium">
              Total
            </span>
          </div>
          <div class="flex justify-between items-center">
            <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats.tasks || 0 }}</div>
            <div class="flex flex-col text-right">
              <span class="text-sm font-medium text-success flex items-center justify-end">
                <FontAwesomeIcon icon="check-circle" class="h-4 w-4 mr-1" />
                {{ stats.completedTasks || 0 }} completed
              </span>
              <span class="text-sm font-medium text-warning mt-1 flex items-center justify-end">
                <FontAwesomeIcon icon="clock" class="h-4 w-4 mr-1" />
                {{ stats.pendingTasks || 0 }} pending
              </span>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
          <BaseButton variant="primary" textOnly class="w-full justify-between items-center group">
            View all tasks
            <FontAwesomeIcon icon="arrow-right" class="ml-2 h-3 w-3 group-hover:translate-x-1 transition-transform" />
          </BaseButton>
        </div>
      </BaseCard>

      <!-- Categories Card -->
      <BaseCard class="col-span-1 shadow-md hover:shadow-lg transition-all duration-300 border-t-4 border-secondary">
        <div class="p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
              <FontAwesomeIcon icon="tags" class="h-5 w-5 text-secondary mr-2" />
              Categories
            </h3>
            <span class="bg-secondary-100 text-secondary-800 dark:bg-secondary-900 dark:bg-opacity-30 dark:text-secondary-300 px-3 py-1 rounded-full text-xs font-medium">
              Overview
            </span>
          </div>
          <div class="flex justify-between items-center">
            <div class="text-4xl font-bold text-gray-900 dark:text-white">{{ stats.categories || 0 }}</div>
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center">
              <FontAwesomeIcon icon="star" class="h-4 w-4 text-yellow-500 mr-1" />
              Most used: <span class="text-secondary ml-1 font-semibold">{{ stats.mostUsedCategory || 'None' }}</span>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
          <BaseButton variant="secondary" textOnly class="w-full justify-between items-center group">
            Manage categories
            <FontAwesomeIcon icon="arrow-right" class="ml-2 h-3 w-3 group-hover:translate-x-1 transition-transform" />
          </BaseButton>
        </div>
      </BaseCard>

      <!-- Activity Card -->
      <BaseCard class="col-span-1 shadow-md hover:shadow-lg transition-all duration-300 border-t-4 border-success">
        <div class="p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
              <FontAwesomeIcon icon="chart-line" class="h-5 w-5 text-success mr-2" />
              Activity
            </h3>
            <span class="bg-success-100 text-success-800 dark:bg-success-900 dark:bg-opacity-30 dark:text-success-300 px-3 py-1 rounded-full text-xs font-medium">
              Today
            </span>
          </div>
          <div class="flex flex-col">
            <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2 flex items-center">
              {{ stats.completedToday || 0 }}
              <span class="ml-2 text-success text-sm font-medium" v-if="stats.completedToday > 0">
                <FontAwesomeIcon icon="arrow-up" class="h-3 w-3 mr-1" />
                Great work!
              </span>
            </div>
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400 flex items-center">
              <FontAwesomeIcon icon="check-double" class="h-4 w-4 text-success mr-1" />
              Tasks completed today
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
          <BaseButton variant="success" textOnly class="w-full justify-between items-center group">
            View statistics
            <FontAwesomeIcon icon="arrow-right" class="ml-2 h-3 w-3 group-hover:translate-x-1 transition-transform" />
          </BaseButton>
        </div>
      </BaseCard>
    </div>

    <!-- Recent Tasks -->
    <BaseCard class="mb-10 shadow-md hover:shadow-lg transition-all duration-300">
      <div class="p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
            <FontAwesomeIcon icon="history" class="h-5 w-5 text-primary mr-2" />
            Recent Tasks
          </h3>
          <BaseButton size="sm" variant="primary" outline class="hover:-translate-y-1 transition-transform">
            <template #leftIcon>
              <FontAwesomeIcon icon="sync" class="h-4 w-4 animate-spin-slow" />
            </template>
            Refresh
          </BaseButton>
        </div>
        
        <div v-if="recentTasks.length > 0">
          <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <li v-for="task in recentTasks" :key="task.id" class="py-4 flex items-center hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors px-2">
              <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                  <FontAwesomeIcon 
                    :icon="task.completed ? 'check-circle' : 'clock'" 
                    class="h-6 w-6" 
                    :class="task.completed ? 'text-success' : 'text-warning'" 
                  />
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-base font-medium text-gray-900 dark:text-white" :class="{'line-through': task.completed}">
                  {{ task.title }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                  <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 mr-2">{{ task.category }}</span>
                  <span class="flex items-center">
                    <FontAwesomeIcon icon="calendar-alt" class="h-3 w-3 mr-1" />
                    Due {{ task.due_date }}
                  </span>
                </p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <BaseButton size="sm" variant="primary" class="mr-2">
                  <template #leftIcon>
                    <FontAwesomeIcon icon="eye" class="h-3 w-3" />
                  </template>
                  View
                </BaseButton>
              </div>
            </li>
          </ul>
        </div>
        <div v-else class="py-12 text-center">
          <FontAwesomeIcon icon="inbox" class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" />
          <p class="text-gray-500 dark:text-gray-400 text-lg mb-6">No recent tasks found</p>
          <BaseButton variant="primary" size="lg" class="shadow-md">Create your first task</BaseButton>
        </div>
      </div>
    </BaseCard>

    <!-- Upcoming Events -->
    <BaseCard class="shadow-md hover:shadow-lg transition-all duration-300">
      <div class="p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
          <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
            <FontAwesomeIcon icon="calendar-alt" class="h-5 w-5 text-info mr-2" />
            Upcoming Deadlines
          </h3>
          <BaseButton variant="info" size="sm" outline class="hover:-translate-y-1 transition-transform">
            <template #leftIcon>
              <FontAwesomeIcon icon="calendar" class="h-4 w-4 mr-1" />
            </template>
            View Calendar
          </BaseButton>
        </div>
        
        <div v-if="upcomingDeadlines.length > 0">
          <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            <li v-for="task in upcomingDeadlines" :key="task.id" class="py-4 flex items-center hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md transition-colors px-2">
              <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                  <FontAwesomeIcon 
                    icon="calendar-day" 
                    class="h-5 w-5 text-info" 
                  />
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-base font-medium text-gray-900 dark:text-white">
                  {{ task.title }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                  <FontAwesomeIcon icon="clock" class="h-3 w-3 mr-1" />
                  Due {{ task.due_date }}
                </p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" 
                  :class="{
                    'bg-danger-100 text-danger-800 dark:bg-danger-900 dark:bg-opacity-30 dark:text-danger-400': task.priority === 'high',
                    'bg-warning-100 text-warning-800 dark:bg-warning-900 dark:bg-opacity-30 dark:text-warning-400': task.priority === 'medium',
                    'bg-success-100 text-success-800 dark:bg-success-900 dark:bg-opacity-30 dark:text-success-400': task.priority === 'low'
                  }"
                >
                  {{ task.priority }}
                </span>
              </div>
            </li>
          </ul>
        </div>
        <div v-else class="py-12 text-center">
          <FontAwesomeIcon icon="calendar-check" class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" />
          <p class="text-gray-500 dark:text-gray-400 text-lg">No upcoming deadlines</p>
        </div>
      </div>
    </BaseCard>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import axios from 'axios';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseCard from '@/components/base/BaseCard.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const user = computed(() => authStore.currentUser);

// Stats data (mock data for now, will be replaced with real API call)
const stats = ref({
  tasks: 0,
  completedTasks: 0,
  pendingTasks: 0,
  categories: 0,
  mostUsedCategory: 'None',
  completedToday: 0
});

// Recent tasks (mock data)
const recentTasks = ref([]);

// Upcoming deadlines (mock data)
const upcomingDeadlines = ref([]);

// Fetch dashboard data
const fetchDashboardData = async () => {
  try {
    const response = await axios.get('/api/dashboard');
    
    // Set stats
    stats.value = response.data.data.stats;
    
    // Set recent tasks
    recentTasks.value = response.data.data.recentTasks;
    
    // Set upcoming deadlines
    upcomingDeadlines.value = response.data.data.upcomingDeadlines;
    
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    
    // Set mock data for demo
    stats.value = {
      tasks: 12,
      completedTasks: 5,
      pendingTasks: 7,
      categories: 3,
      mostUsedCategory: 'Work',
      completedToday: 2
    };
    
    recentTasks.value = [
      { id: 1, title: 'Finish project proposal', category: 'Work', due_date: 'Today', completed: true },
      { id: 2, title: 'Call client about requirements', category: 'Work', due_date: 'Tomorrow', completed: false },
      { id: 3, title: 'Buy groceries', category: 'Personal', due_date: 'Today', completed: false }
    ];
    
    upcomingDeadlines.value = [
      { id: 2, title: 'Call client about requirements', due_date: 'Tomorrow', priority: 'high' },
      { id: 4, title: 'Submit tax documents', due_date: 'May 15', priority: 'medium' },
      { id: 5, title: 'Schedule dentist appointment', due_date: 'May 20', priority: 'low' }
    ];
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script> 