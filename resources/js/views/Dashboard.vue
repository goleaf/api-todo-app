<template>
  <div>
    <div class="mb-8">
      <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white">
        Dashboard
      </h1>
      <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Welcome back, {{ authStore.currentUser?.name || 'User' }}! Here's an overview of your tasks.
      </p>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <!-- Total Tasks Card -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-indigo-500 dark:bg-indigo-600 rounded-md p-3">
              <FontAwesomeIcon icon="clipboard-list" class="h-6 w-6 text-white" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                Total Tasks
              </dt>
              <dd class="flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ stats.overview.total || 0 }}
                </div>
              </dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Completed Tasks Card -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-500 dark:bg-green-600 rounded-md p-3">
              <FontAwesomeIcon icon="check-circle" class="h-6 w-6 text-white" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                Completed Tasks
              </dt>
              <dd class="flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ stats.overview.completed || 0 }}
                </div>
                <div v-if="completionRate !== null" class="ml-2 flex items-baseline text-sm font-semibold">
                  <span :class="completionRate > 50 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'">
                    {{ completionRate }}%
                  </span>
                </div>
              </dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Tasks Card -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-yellow-500 dark:bg-yellow-600 rounded-md p-3">
              <FontAwesomeIcon icon="clock" class="h-6 w-6 text-white" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                Pending Tasks
              </dt>
              <dd class="flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ stats.overview.pending || 0 }}
                </div>
              </dd>
            </div>
          </div>
        </div>
      </div>

      <!-- Overdue Tasks Card -->
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0 bg-red-500 dark:bg-red-600 rounded-md p-3">
              <FontAwesomeIcon icon="exclamation-circle" class="h-6 w-6 text-white" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                Overdue Tasks
              </dt>
              <dd class="flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ stats.overview.overdue || 0 }}
                </div>
                <div v-if="stats.overview.overdue > 0" class="ml-2">
                  <router-link 
                    to="/tasks?status=overdue" 
                    class="text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                  >
                    <span class="sr-only">View all overdue tasks</span>
                    <FontAwesomeIcon icon="arrow-right" class="h-4 w-4" />
                  </router-link>
                </div>
              </dd>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Recent Tasks Section -->
      <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Recent Tasks</h2>
            <router-link 
              to="/tasks/create" 
              class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
            >
              <FontAwesomeIcon icon="plus" class="mr-1.5 h-3 w-3" />
              Add Task
            </router-link>
          </div>

          <div v-if="isLoading" class="py-12 flex justify-center">
            <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary"></div>
          </div>

          <div v-else>
            <ul v-if="recentTasks.length > 0" class="divide-y divide-gray-200 dark:divide-gray-700">
              <li 
                v-for="task in recentTasks" 
                :key="task.id" 
                class="px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                <div class="flex items-center">
                  <div class="min-w-0 flex-1">
                    <div class="flex items-center">
                      <div class="relative inline-block shrink-0">
                        <input
                          :id="`task-${task.id}`"
                          :checked="task.completed"
                          type="checkbox"
                          @change="toggleTaskCompletion(task.id)"
                          class="form-checkbox h-5 w-5 text-primary focus:ring-primary border-gray-300 rounded transition-colors cursor-pointer"
                        />
                      </div>
                      <div class="ml-3">
                        <router-link :to="`/tasks/${task.id}`" class="hover:underline focus:outline-none">
                          <p 
                            :class="['font-medium text-sm sm:text-base leading-5', 
                              task.completed 
                                ? 'text-gray-400 dark:text-gray-500 line-through' 
                                : 'text-gray-900 dark:text-white'
                            ]"
                          >
                            {{ task.title }}
                          </p>
                          <p v-if="task.description" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate mt-0.5">
                            {{ truncateText(task.description, 60) }}
                          </p>
                        </router-link>
                      </div>
                    </div>
                  </div>
                  <div class="flex-shrink-0 flex ml-4 space-x-2">
                    <span
                      v-if="task.category"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="{ 
                        backgroundColor: hexToRgba(task.category.color || '#9CA3AF', 0.15), 
                        color: task.category.color || '#4B5563',
                        borderColor: hexToRgba(task.category.color || '#9CA3AF', 0.3),
                      }"
                      :class="'dark:text-white border'"
                    >
                      {{ task.category.name }}
                    </span>
                    <span
                      v-if="task.due_date"
                      :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border',
                        isOverdue(task.due_date) && !task.completed 
                          ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border-red-200 dark:border-red-800' 
                          : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-700'
                      ]"
                    >
                      <FontAwesomeIcon 
                        :icon="isOverdue(task.due_date) && !task.completed ? 'exclamation-circle' : 'calendar-alt'" 
                        class="mr-1 h-3 w-3" 
                        :class="isOverdue(task.due_date) && !task.completed ? 'text-red-500' : 'text-gray-500 dark:text-gray-400'"
                      />
                      {{ formatDate(task.due_date) }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>

            <div v-else class="py-12 text-center">
              <div class="bg-gray-50 dark:bg-gray-700 rounded-full h-16 w-16 flex items-center justify-center mx-auto">
                <FontAwesomeIcon icon="clipboard" class="h-8 w-8 text-gray-400 dark:text-gray-300" />
              </div>
              <h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No tasks found</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Get started by creating a new task
              </p>
              <div class="mt-6">
                <router-link
                  to="/tasks/create"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                >
                  <FontAwesomeIcon icon="plus" class="mr-2 h-4 w-4" />
                  New Task
                </router-link>
              </div>
            </div>

            <div v-if="recentTasks.length > 0" class="bg-gray-50 dark:bg-gray-700 px-4 py-4 sm:px-6 border-t border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-center">
                <router-link 
                  to="/tasks" 
                  class="text-sm font-medium text-primary hover:text-primary-dark flex items-center transition-colors"
                >
                  View all tasks
                  <FontAwesomeIcon icon="arrow-right" class="ml-1 h-4 w-4" />
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Widgets Column -->
      <div class="space-y-6">
        <!-- Task Categories Widget -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Task Categories</h2>
          </div>
          
          <div v-if="isLoading" class="py-8 flex justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
          </div>
          
          <div v-else-if="categories.length > 0" class="px-4 py-3">
            <div class="space-y-2">
              <div 
                v-for="category in categories.slice(0, 5)" 
                :key="category.id"
                class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
              >
                <div class="flex items-center">
                  <span 
                    class="w-3 h-3 rounded-full mr-3" 
                    :style="{ backgroundColor: category.color || '#9CA3AF' }"
                  ></span>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ category.name }}</span>
                </div>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ categoryTaskCount(category.id) || 0 }}
                </span>
              </div>
            </div>
            
            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700 text-center">
              <router-link to="/categories" class="text-sm text-primary hover:text-primary-dark transition-colors">
                Manage Categories
              </router-link>
            </div>
          </div>
          
          <div v-else class="py-8 text-center px-4">
            <FontAwesomeIcon icon="tag" class="h-8 w-8 text-gray-400 dark:text-gray-300 mx-auto" />
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No categories yet</p>
            <router-link 
              to="/categories" 
              class="mt-4 inline-block text-sm text-primary hover:text-primary-dark transition-colors"
            >
              Create Category
            </router-link>
          </div>
        </div>
        
        <!-- Quick Actions Widget -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
          <div class="px-4 py-5 sm:p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            
            <div class="space-y-3">
              <router-link 
                to="/tasks/create" 
                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <div class="flex items-center">
                  <div class="bg-primary bg-opacity-10 dark:bg-opacity-20 p-2 rounded-md">
                    <FontAwesomeIcon icon="plus" class="h-5 w-5 text-primary" />
                  </div>
                  <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Create New Task</span>
                </div>
                <FontAwesomeIcon icon="chevron-right" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
              </router-link>
              
              <router-link 
                to="/tasks?status=pending" 
                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <div class="flex items-center">
                  <div class="bg-yellow-100 dark:bg-yellow-900 p-2 rounded-md">
                    <FontAwesomeIcon icon="clock" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                  </div>
                  <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">View Pending Tasks</span>
                </div>
                <FontAwesomeIcon icon="chevron-right" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
              </router-link>
              
              <router-link 
                to="/tasks?due=today" 
                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <div class="flex items-center">
                  <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-md">
                    <FontAwesomeIcon icon="calendar-day" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                  </div>
                  <span class="ml-3 text-sm font-medium text-gray-900 dark:text-white">Due Today</span>
                </div>
                <FontAwesomeIcon icon="chevron-right" class="h-4 w-4 text-gray-400 dark:text-gray-500" />
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useTaskStore } from '@/stores/tasks';
import { useAppStore } from '@/stores/app';
import { format, isAfter, parseISO } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Stores
const authStore = useAuthStore();
const taskStore = useTaskStore();
const appStore = useAppStore();

// Reactive data
const recentTasks = ref([]);
const categories = ref([]);
const isLoading = ref(true);

// Computed properties
const stats = computed(() => ({
  overview: taskStore.getStatistics
}));

const completionRate = computed(() => {
  const total = stats.value.overview.total || 0;
  if (total === 0) return null;
  
  const completed = stats.value.overview.completed || 0;
  return Math.round((completed / total) * 100);
});

// Methods
const fetchDashboardData = async () => {
  try {
    isLoading.value = true;
    
    // Fetch statistics
    await taskStore.fetchDashboardStats();
    
    // Fetch recent tasks
    await taskStore.fetchTasks();
    recentTasks.value = taskStore.getAllTasks.slice(0, 5);
    
    // Fetch categories
    await taskStore.fetchCategories();
    categories.value = taskStore.categories;
    
  } catch (error) {
    console.error('Error fetching dashboard data:', error);
    appStore.addToast({
      type: 'error',
      message: 'Failed to load dashboard data. Please try again.'
    });
  } finally {
    isLoading.value = false;
  }
};

const toggleTaskCompletion = async (taskId) => {
  try {
    appStore.setLoading(true);
    await taskStore.toggleTaskCompletion(taskId);
    await fetchDashboardData();
  } catch (error) {
    console.error('Error toggling task completion:', error);
    appStore.addToast({
      type: 'error',
      message: 'Failed to update task. Please try again.'
    });
  } finally {
    appStore.setLoading(false);
  }
};

const formatDate = (dateString) => {
  return format(parseISO(dateString), 'MMM d, yyyy');
};

const isOverdue = (dateString) => {
  return isAfter(new Date(), parseISO(dateString));
};

const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.slice(0, maxLength) + '...';
};

const hexToRgba = (hex, alpha = 1) => {
  if (!hex) return 'rgba(156, 163, 175, ' + alpha + ')'; // Default gray
  
  // Remove the hash
  const cleanHex = hex.replace('#', '');
  
  // Parse the hex values
  const r = parseInt(cleanHex.substring(0, 2), 16);
  const g = parseInt(cleanHex.substring(2, 4), 16);
  const b = parseInt(cleanHex.substring(4, 6), 16);
  
  // Return rgba
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

const categoryTaskCount = (categoryId) => {
  return taskStore.getAllTasks.filter(task => task.category_id === categoryId).length;
};

// Lifecycle hooks
onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 