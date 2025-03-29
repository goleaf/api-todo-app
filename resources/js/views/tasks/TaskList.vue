<template>
  <div class="container mx-auto max-w-6xl">
    <div class="pb-5 border-b border-gray-200 dark:border-gray-700 mb-5 flex justify-between items-center">
      <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white">Tasks</h1>
      <router-link 
        to="/tasks/create" 
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
      >
        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Task
      </router-link>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Status filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
          <select 
            v-model="filters.status" 
            @change="applyFilters"
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
          >
            <option value="all">All</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="overdue">Overdue</option>
          </select>
        </div>
        
        <!-- Category filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
          <select 
            v-model="filters.category" 
            @change="applyFilters"
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
        
        <!-- Search filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
          <input 
            v-model="filters.search" 
            @input="debounceSearch"
            type="text" 
            placeholder="Search tasks..." 
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
          >
        </div>
        
        <!-- Due date filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
          <input 
            v-model="filters.dueDate" 
            @change="applyFilters"
            type="date" 
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
          >
        </div>
      </div>
      
      <div class="flex justify-end mt-4">
        <button 
          @click="clearFilters" 
          class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        >
          Clear Filters
        </button>
      </div>
    </div>

    <!-- Task List -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
      <!-- Loading state -->
      <div v-if="isLoading" class="p-6 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Loading tasks...</p>
      </div>
      
      <!-- No tasks state -->
      <div v-else-if="tasks.length === 0" class="p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No tasks found</h3>
        <p class="mt-1 text-gray-500 dark:text-gray-400">
          {{ isFiltered ? 'Try changing your filters or' : 'Get started by' }} creating a new task.
        </p>
        <div class="mt-6">
          <router-link 
            to="/tasks/create" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
          >
            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add Task
          </router-link>
        </div>
      </div>
      
      <!-- Task list -->
      <div v-else>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12">
                  Status
                </th>
                <th 
                  scope="col" 
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="sortBy('title')"
                >
                  Title
                  <span v-if="filters.sort === 'title'">
                    {{ filters.order === 'asc' ? '↑' : '↓' }}
                  </span>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Category
                </th>
                <th 
                  scope="col" 
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="sortBy('due_date')"
                >
                  Due Date
                  <span v-if="filters.sort === 'due_date'">
                    {{ filters.order === 'asc' ? '↑' : '↓' }}
                  </span>
                </th>
                <th 
                  scope="col" 
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                  @click="sortBy('priority')"
                >
                  Priority
                  <span v-if="filters.sort === 'priority'">
                    {{ filters.order === 'asc' ? '↑' : '↓' }}
                  </span>
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <input
                    :id="`task-${task.id}`"
                    :checked="task.completed"
                    type="checkbox"
                    @change="toggleTaskCompletion(task.id, !task.completed)"
                    class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="min-w-0 flex-1">
                      <router-link :to="`/tasks/${task.id}`" class="focus:outline-none">
                        <p :class="[
                          'text-sm font-medium truncate',
                          task.completed ? 'text-gray-400 line-through' : 'text-gray-900 dark:text-white'
                        ]">
                          {{ task.title }}
                        </p>
                        <p v-if="task.description" class="text-sm text-gray-500 dark:text-gray-400 truncate">
                          {{ task.description }}
                        </p>
                      </router-link>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="task.category" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :style="{ 
                    backgroundColor: task.category.color ? task.category.color + '33' : '#9CA3AF33',
                    color: task.category.color || '#4B5563'
                  }">
                    {{ task.category.name }}
                  </span>
                  <span v-else class="text-gray-400 dark:text-gray-500 text-xs">None</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    v-if="task.due_date" 
                    :class="[
                      'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                      isOverdue(task) && !task.completed ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:bg-opacity-30 dark:text-red-300' : 'text-orange-500 dark:text-orange-400'
                    ]"
                  >
                    {{ formatDate(task.due_date) }}
                  </span>
                  <span v-else class="text-gray-400 dark:text-gray-500 text-xs">None</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    task.priority === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:bg-opacity-30 dark:text-red-300' : 
                    task.priority === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:bg-opacity-30 dark:text-yellow-300' :
                    'bg-green-100 text-green-800 dark:bg-green-900 dark:bg-opacity-30 dark:text-green-300'
                  ]">
                    {{ task.priority.charAt(0).toUpperCase() + task.priority.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <router-link 
                    :to="`/tasks/${task.id}`" 
                    class="text-primary hover:text-primary-dark mr-3"
                  >
                    Edit
                  </router-link>
                  <button 
                    @click="confirmTaskDeletion(task)" 
                    class="text-red-600 hover:text-red-900 dark:text-red-500 dark:hover:text-red-400"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                Showing
                <span class="font-medium">{{ paginationStart }}</span>
                to
                <span class="font-medium">{{ paginationEnd }}</span>
                of
                <span class="font-medium">{{ pagination.total }}</span>
                results
              </p>
            </div>
            <div v-if="pagination.totalPages > 1" class="flex">
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                  @click="goToPage(pagination.currentPage - 1)"
                  :disabled="pagination.currentPage === 1"
                  :class="[
                    pagination.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : '',
                    'relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                  ]"
                >
                  <span class="sr-only">Previous</span>
                  <!-- Heroicon name: solid/chevron-left -->
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  v-for="page in paginationPages"
                  :key="page"
                  @click="goToPage(page)"
                  :class="[
                    page === pagination.currentPage ? 'z-10 bg-primary-50 border-primary text-primary dark:bg-primary-900 dark:text-primary-light' : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700',
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                  ]"
                >
                  {{ page }}
                </button>
                <button
                  @click="goToPage(pagination.currentPage + 1)"
                  :disabled="pagination.currentPage === pagination.totalPages"
                  :class="[
                    pagination.currentPage === pagination.totalPages ? 'opacity-50 cursor-not-allowed' : '',
                    'relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
                  ]"
                >
                  <span class="sr-only">Next</span>
                  <!-- Heroicon name: solid/chevron-right -->
                  <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <BaseModal
      v-model="confirmDelete"
      title="Confirm Delete"
    >
      <p class="text-gray-700 dark:text-gray-300">
        Are you sure you want to delete this task? This action cannot be undone.
      </p>
      
      <template #footer>
        <BaseButton 
          @click="confirmDelete = false" 
          variant="default"
        >
          Cancel
        </BaseButton>
        <BaseButton 
          @click="deleteTask" 
          variant="danger"
          :loading="isLoading"
        >
          Delete
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useTaskStore } from '@/stores/tasks';
import { useAppStore } from '@/stores/app';
import { format, isAfter } from 'date-fns';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseModal from '@/components/base/BaseModal.vue';

// Store
const taskStore = useTaskStore();
const appStore = useAppStore();
const { isLoading } = storeToRefs(appStore);

// Reactive data
const tasks = ref([]);
const categories = ref([]);
const confirmDelete = ref(false);
const taskToDelete = ref(null);
const searchTimeout = ref(null);

// Filters and pagination
const filters = reactive({
  status: 'all',
  category: '',
  search: '',
  dueDate: '',
  sort: 'created_at',
  order: 'desc'
});

const pagination = reactive({
  currentPage: 1,
  totalPages: 1,
  perPage: 10,
  total: 0
});

// Computed properties
const isFiltered = computed(() => {
  return filters.status !== 'all' || 
    filters.category !== '' || 
    filters.search !== '' || 
    filters.dueDate !== '';
});

const paginationStart = computed(() => {
  return ((pagination.currentPage - 1) * pagination.perPage) + 1;
});

const paginationEnd = computed(() => {
  return Math.min(pagination.currentPage * pagination.perPage, pagination.total);
});

const paginationPages = computed(() => {
  const totalPages = pagination.totalPages;
  const currentPage = pagination.currentPage;
  
  if (totalPages <= 5) {
    return Array.from({ length: totalPages }, (_, i) => i + 1);
  }
  
  // Show first page, last page, current page, and pages around the current page
  const pages = [1];
  
  const start = Math.max(2, currentPage - 1);
  const end = Math.min(totalPages - 1, currentPage + 1);
  
  if (start > 2) pages.push('...');
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  if (end < totalPages - 1) pages.push('...');
  
  if (totalPages > 1) pages.push(totalPages);
  
  return pages;
});

// Methods
const fetchTasks = async () => {
  try {
    // Set filters in store
    Object.keys(filters).forEach(key => {
      taskStore.setFilter(key, filters[key]);
    });
    
    // Set pagination in store
    taskStore.setPage(pagination.currentPage);
    
    // Fetch tasks
    const data = await taskStore.fetchTasks();
    
    // Update local data
    tasks.value = data.data;
    
    // Update pagination
    pagination.currentPage = data.current_page;
    pagination.totalPages = data.last_page;
    pagination.total = data.total;
  } catch (error) {
    console.error('Error fetching tasks:', error);
  }
};

const fetchCategories = async () => {
  try {
    categories.value = await taskStore.fetchCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const applyFilters = () => {
  pagination.currentPage = 1;
  fetchTasks();
};

const clearFilters = () => {
  filters.status = 'all';
  filters.category = '';
  filters.search = '';
  filters.dueDate = '';
  filters.sort = 'created_at';
  filters.order = 'desc';
  
  applyFilters();
};

const debounceSearch = () => {
  clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    applyFilters();
  }, 500);
};

const sortBy = (field) => {
  if (filters.sort === field) {
    // Toggle sort order
    filters.order = filters.order === 'asc' ? 'desc' : 'asc';
  } else {
    // Set new sort field
    filters.sort = field;
    filters.order = 'asc';
  }
  
  fetchTasks();
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.totalPages) return;
  
  pagination.currentPage = page;
  fetchTasks();
};

const toggleTaskCompletion = async (id, completed) => {
  try {
    await taskStore.toggleTaskCompletion(id, completed);
    fetchTasks();
  } catch (error) {
    console.error('Error toggling task completion:', error);
  }
};

const confirmTaskDeletion = (task) => {
  taskToDelete.value = task;
  confirmDelete.value = true;
};

const deleteTask = async () => {
  if (!taskToDelete.value) return;
  
  try {
    await taskStore.deleteTask(taskToDelete.value.id);
    confirmDelete.value = false;
    taskToDelete.value = null;
    fetchTasks();
  } catch (error) {
    console.error('Error deleting task:', error);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'MMM d, yyyy');
};

const isOverdue = (task) => {
  if (!task.due_date || task.completed) return false;
  return isAfter(new Date(), new Date(task.due_date));
};

// Lifecycle hooks
onMounted(async () => {
  await Promise.all([
    fetchTasks(),
    fetchCategories()
  ]);
});
</script> 