<template>
  <div class="container mx-auto px-4 py-6 pb-20 max-w-4xl" data-testid="main-content">
    <!-- Filter and sort controls -->
    <div class="task-card mb-4">
      <div class="flex flex-col md:flex-row justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2 md:mb-0">My Tasks</h2>
        <div class="flex flex-wrap gap-2">
          <!-- Status filter -->
          <div class="relative">
            <select
              v-model="filterOption"
              class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
              @change="applyFilters"
            >
              <option value="all">All Tasks</option>
              <option value="completed">Completed</option>
              <option value="active">Pending</option>
              <option value="overdue">Overdue</option>
              <option value="today">Due Today</option>
            </select>
          </div>
          
          <!-- Category filter -->
          <div class="relative">
            <select
              v-model="categoryFilter"
              class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
              @change="applyFilters"
            >
              <option value="all">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          
          <!-- Sort options -->
          <div class="relative">
            <select
              v-model="sortOption"
              class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
            >
              <option value="date-desc">Newest First</option>
              <option value="date-asc">Oldest First</option>
              <option value="priority-desc">Priority (High-Low)</option>
              <option value="priority-asc">Priority (Low-High)</option>
              <option value="due-date">Due Date</option>
            </select>
          </div>
          
          <!-- Calendar button -->
          <button 
            @click="$router.push('/calendar')"
            class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-1 focus:ring-purple-500 transition-colors flex items-center"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            Calendar
          </button>
        </div>
      </div>
      
      <!-- Search bar -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search tasks..."
          class="w-full px-4 py-3 pr-10 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          @input="debounceSearch"
        >
        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>
    
    <!-- Add task button on desktop -->
    <button 
      v-if="!showForm && window.innerWidth >= 768"
      @click="showForm = true"
      dusk="add-todo-button"
      class="mb-4 md:hidden px-4 py-2 bg-[var(--primary)] text-white rounded-lg shadow-md hover:bg-opacity-90 transition-colors flex items-center"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
      </svg>
      Add Task
    </button>
    
    <!-- Task form (on desktop only) -->
    <div v-if="showForm" class="task-card mb-4">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Create New Task</h3>
      <form @submit.prevent="addTodo" class="space-y-4" dusk="todo-form">
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
          <input
            v-model="newTodo.title"
            type="text"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
            id="title"
            placeholder="What needs to be done?"
            required
          >
        </div>
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (optional)</label>
          <textarea
            v-model="newTodo.description"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
            id="description"
            rows="2"
            placeholder="Add details..."
          ></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <div class="flex space-x-2">
              <select
                v-model="newTodo.category_id"
                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                id="category"
              >
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <button 
                type="button" 
                @click="showCategoryModal = true"
                class="px-3 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors"
                title="Add new category"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
              </button>
            </div>
          </div>
          <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
            <input
              v-model="newTodo.due_date"
              type="date"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="due_date"
            >
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="reminder_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reminder Time</label>
            <input
              v-model="newTodo.reminder_at"
              type="datetime-local"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="reminder_at"
            >
          </div>
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
            <select
              v-model="newTodo.priority"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="priority"
            >
              <option value="0">Low</option>
              <option value="1">Medium</option>
              <option value="2">High</option>
            </select>
          </div>
        </div>
        
        <div>
          <label for="progress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Progress: {{ newTodo.progress }}%
          </label>
          <input
            v-model.number="newTodo.progress"
            type="range"
            min="0"
            max="100"
            step="5"
            class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[var(--primary)]"
            id="progress"
          >
        </div>
        
        <div class="flex justify-end space-x-3">
          <button
            v-if="window.innerWidth < 768"
            type="button"
            @click="showForm = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-[var(--primary)] text-white rounded-lg hover:bg-opacity-90 transition-colors"
            :disabled="isSubmitting"
          >
            <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isSubmitting ? 'Adding...' : 'Add Task' }}
          </button>
        </div>
      </form>
    </div>
    
    <!-- Tasks list -->
    <div v-if="loading" class="text-center py-8">
      <div class="animate-spin h-10 w-10 border-4 border-[var(--primary)] border-opacity-50 border-t-[var(--primary)] rounded-full mx-auto"></div>
      <p class="mt-2 text-gray-500 dark:text-gray-400">Loading tasks...</p>
    </div>
    
    <div v-else-if="filteredTodos.length === 0" class="task-card p-8 text-center">
      <div class="text-gray-400 dark:text-gray-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ searchQuery ? 'No matching tasks found' : 'No tasks yet' }}
      </h3>
      <p class="text-gray-500 dark:text-gray-400 mb-4">
        {{ searchQuery ? 'Try changing your search or filter criteria.' : 'Add a new task to get started.' }}
      </p>
      <button 
        @click="showForm = true; searchQuery = ''"
        class="px-4 py-2 bg-[var(--primary)] text-white rounded-lg shadow-md hover:bg-opacity-90 transition-colors flex items-center mx-auto"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Add Task
      </button>
    </div>
    
    <div v-else>
      <div class="space-y-4">
        <todo-item
          v-for="todo in filteredTodos"
          :key="todo.id"
          :todo="todo"
          @toggle="toggleTodo"
          @delete="deleteTodo"
        ></todo-item>
      </div>
    </div>
    
    <!-- Category Modal -->
    <div v-if="showCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center" @click.stop>
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="showCategoryModal = false"></div>
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-auto z-10 relative">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Manage Categories</h3>
        
        <!-- Add new category form -->
        <form @submit.prevent="addCategory" class="mb-6">
          <div class="flex space-x-2">
            <input
              v-model="newCategory"
              type="text"
              placeholder="New category name"
              class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              required
            >
            <button 
              type="submit"
              class="px-4 py-2 bg-[var(--primary)] text-white rounded-lg hover:bg-opacity-90 transition-colors"
            >
              Add
            </button>
          </div>
        </form>
        
        <!-- Category list -->
        <div class="space-y-2 max-h-60 overflow-y-auto">
          <div 
            v-for="category in categories" 
            :key="category.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <span class="text-gray-800 dark:text-gray-200">{{ category.name }}</span>
            <button 
              @click="deleteCategory(category.id)"
              class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
        
        <div class="mt-6 flex justify-end">
          <button 
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            @click="showCategoryModal = false"
          >
            Close
          </button>
        </div>
      </div>
    </div>
    
    <!-- Floating Action Button (FAB) -->
    <button 
      @click="openTaskModal"
      class="fixed right-6 bottom-24 w-14 h-14 rounded-full bg-purple-600 text-white shadow-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 z-20 flex items-center justify-center transform transition-transform hover:scale-110"
      data-testid="add-todo-button"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
      </svg>
    </button>
    
    <!-- Task Modal (for mobile) -->
    <div v-if="showTaskModal" class="fixed inset-0 z-50 flex items-center justify-center" @click.stop>
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="showTaskModal = false"></div>
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-auto z-10 relative max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Create New Task</h3>
        
        <form @submit.prevent="addTodoFromModal" class="space-y-4">
          <div>
            <label for="modal-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
            <input
              v-model="modalTodo.title"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="modal-title"
              placeholder="What needs to be done?"
              required
            >
          </div>
          
          <div>
            <label for="modal-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (optional)</label>
            <textarea
              v-model="modalTodo.description"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="modal-description"
              rows="2"
              placeholder="Add details..."
            ></textarea>
          </div>
          
          <div>
            <label for="modal-category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <div class="flex space-x-2">
              <select
                v-model="modalTodo.category_id"
                class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                id="modal-category"
              >
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
          </div>
          
          <div>
            <label for="modal-due-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
            <input
              v-model="modalTodo.due_date"
              type="date"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="modal-due-date"
            >
          </div>
          
          <div>
            <label for="modal-priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
            <select
              v-model="modalTodo.priority"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              id="modal-priority"
            >
              <option value="0">Low</option>
              <option value="1">Medium</option>
              <option value="2">High</option>
            </select>
          </div>
          
          <div>
            <label for="modal-progress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Progress: {{ modalTodo.progress }}%
            </label>
            <input
              v-model.number="modalTodo.progress"
              type="range"
              min="0"
              max="100"
              step="5"
              class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-[var(--primary)]"
              id="modal-progress"
            >
          </div>
          
          <div class="flex justify-end space-x-3 pt-2">
            <button
              type="button"
              @click="showTaskModal = false"
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-[var(--primary)] text-white rounded-lg hover:bg-opacity-90 transition-colors"
              :disabled="isSubmitting"
            >
              <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isSubmitting ? 'Adding...' : 'Add Task' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useStore } from 'vuex';
import { useRoute, useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const route = useRoute();
    const router = useRouter();
    const todos = ref([]);
    const categories = ref([]);
    const loading = ref(true);
    const isSubmitting = ref(false);
    const searchQuery = ref('');
    const filterOption = ref('all');
    const categoryFilter = ref('all');
    const sortOption = ref('date-desc');
    const showForm = ref(false);
    const showCategoryModal = ref(false);
    const showTaskModal = ref(false);
    
    const newTodo = reactive({
      title: '',
      description: '',
      completed: false,
      due_date: '',
      reminder_at: '',
      priority: 0,
      progress: 0,
      category_id: null
    });

    const newCategory = ref('');
    
    const modalTodo = reactive({
      title: '',
      description: '',
      completed: false,
      due_date: '',
      reminder_at: '',
      priority: 0,
      progress: 0,
      category_id: null
    });

    // Handle visibility of the form on larger screens
    const setFormVisibility = () => {
      if (window.innerWidth >= 768) { // md breakpoint
        showForm.value = true;
      } else {
        showForm.value = false;
      }
    };

    // Filtered and sorted todos
    const filteredTodos = computed(() => {
      let result = [...todos.value];
      
      // Apply search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(todo => 
          todo.title.toLowerCase().includes(query) || 
          (todo.description && todo.description.toLowerCase().includes(query))
        );
      }
      
      // Apply status filter
      if (filterOption.value === 'completed') {
        result = result.filter(todo => todo.completed);
      } else if (filterOption.value === 'active') {
        result = result.filter(todo => !todo.completed);
      } else if (filterOption.value === 'overdue') {
        const now = new Date();
        result = result.filter(todo => 
          todo.due_date && new Date(todo.due_date) < now && !todo.completed
        );
      } else if (filterOption.value === 'today') {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        result = result.filter(todo => 
          todo.due_date && 
          new Date(todo.due_date) >= today && 
          new Date(todo.due_date) < tomorrow
        );
      }
      
      // Apply category filter
      if (categoryFilter.value !== 'all') {
        result = result.filter(todo => todo.category_id === parseInt(categoryFilter.value));
      }
      
      // Apply sorting
      if (sortOption.value === 'date-desc') {
        result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } else if (sortOption.value === 'date-asc') {
        result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      } else if (sortOption.value === 'priority-desc') {
        result.sort((a, b) => b.priority - a.priority);
      } else if (sortOption.value === 'priority-asc') {
        result.sort((a, b) => a.priority - b.priority);
      } else if (sortOption.value === 'due-date') {
        result.sort((a, b) => {
          // Sort by due date (null values at the end)
          if (!a.due_date && !b.due_date) return 0;
          if (!a.due_date) return 1;
          if (!b.due_date) return -1;
          return new Date(a.due_date) - new Date(b.due_date);
        });
      }
      
      return result;
    });

    // Load todos on component mount
    onMounted(() => {
      setFormVisibility();
      window.addEventListener('resize', setFormVisibility);
      
      // Check URL params for filters
      if (route.query.filter) {
        filterOption.value = route.query.filter;
      }
      
      if (route.query.category) {
        categoryFilter.value = route.query.category;
      }
      
      // Fetch categories and todos
      fetchCategories();
      fetchTodos();
    });

    // Fetch todos from the store
    const fetchTodos = async () => {
      loading.value = true;
      try {
        const params = {};
        
        // Add filter parameters for API call
        if (filterOption.value !== 'all') {
          params.status = filterOption.value;
        }
        
        if (categoryFilter.value !== 'all') {
          params.category = categoryFilter.value;
        }
        
        if (searchQuery.value.trim()) {
          params.search = searchQuery.value.trim();
        }
        
        const result = await store.dispatch('fetchTodos', params);
        todos.value = result;
      } catch (error) {
        console.error('Error fetching todos:', error);
      } finally {
        loading.value = false;
      }
    };
    
    // Fetch categories from the store
    const fetchCategories = async () => {
      try {
        const result = await store.dispatch('fetchCategories');
        categories.value = result;
        
        // Set default category for new todos if none exists
        if (categories.value.length > 0 && !newTodo.category_id) {
          newTodo.category_id = categories.value[0].id;
        }
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    };

    // Add a new todo
    const addTodo = async () => {
      if (!newTodo.title.trim()) return;
      
      isSubmitting.value = true;
      
      try {
        // Validate due_date if provided
        let formattedDueDate = null;
        if (newTodo.due_date) {
          const dueDate = new Date(newTodo.due_date);
          if (!isNaN(dueDate.getTime())) {
            formattedDueDate = newTodo.due_date;
          }
        }
        
        await store.dispatch('createTodo', { 
          title: newTodo.title, 
          description: newTodo.description,
          completed: false,
          due_date: formattedDueDate,
          reminder_at: newTodo.reminder_at || null,
          priority: parseInt(newTodo.priority),
          progress: newTodo.progress || 0,
          category_id: newTodo.category_id
        });
        
        // Reset form
        newTodo.title = '';
        newTodo.description = '';
        newTodo.due_date = '';
        newTodo.reminder_at = '';
        newTodo.priority = 0;
        newTodo.progress = 0;
        newTodo.category_id = categories.value.length > 0 ? categories.value[0].id : null;
        
        // On mobile, hide the form after adding
        if (window.innerWidth < 768) {
          showForm.value = false;
        }
        
        // Refresh todos list
        await fetchTodos();
      } catch (error) {
        console.error('Error adding todo:', error);
      } finally {
        isSubmitting.value = false;
      }
    };

    // Toggle todo completed status
    const toggleTodo = async (todo) => {
      try {
        await store.dispatch('updateTodo', {
          id: todo.id,
          completed: !todo.completed
        });
        await fetchTodos();
      } catch (error) {
        console.error('Error toggling todo:', error);
      }
    };

    // Delete a todo
    const deleteTodo = async (id) => {
      try {
        await store.dispatch('deleteTodo', id);
        await fetchTodos();
      } catch (error) {
        console.error('Error deleting todo:', error);
      }
    };
    
    // Add a new category
    const addCategory = async () => {
      if (!newCategory.value.trim()) return;
      
      try {
        await store.dispatch('createCategory', { name: newCategory.value.trim() });
        newCategory.value = '';
        await fetchCategories();
      } catch (error) {
        console.error('Error adding category:', error);
      }
    };
    
    // Delete a category
    const deleteCategory = async (id) => {
      try {
        await store.dispatch('deleteCategory', id);
        await fetchCategories();
        await fetchTodos(); // Refresh todos since category was deleted
      } catch (error) {
        console.error('Error deleting category:', error);
      }
    };

    // Apply filters and update URL
    const applyFilters = () => {
      const query = { ...route.query };
      
      if (filterOption.value === 'all') {
        delete query.filter;
      } else {
        query.filter = filterOption.value;
      }
      
      if (categoryFilter.value === 'all') {
        delete query.category;
      } else {
        query.category = categoryFilter.value;
      }
      
      // Update route query parameters
      router.replace({ query });
      
      // Fetch filtered todos
      fetchTodos();
    };
    
    // Debounce search to avoid too many API calls
    let searchTimeout;
    const debounceSearch = () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        fetchTodos();
      }, 300);
    };

    const isValidDate = (dateString) => {
      const date = new Date(dateString);
      return !isNaN(date.getTime());
    };

    const openTaskModal = () => {
      showTaskModal.value = true;
    };

    const addTodoFromModal = async () => {
      if (!modalTodo.title.trim()) return;
      
      isSubmitting.value = true;
      
      try {
        // Validate due_date if provided
        let formattedDueDate = null;
        if (modalTodo.due_date) {
          const dueDate = new Date(modalTodo.due_date);
          if (!isNaN(dueDate.getTime())) {
            formattedDueDate = modalTodo.due_date;
          }
        }
        
        await store.dispatch('createTodo', { 
          title: modalTodo.title, 
          description: modalTodo.description,
          completed: false,
          due_date: formattedDueDate,
          reminder_at: modalTodo.reminder_at || null,
          priority: parseInt(modalTodo.priority),
          progress: modalTodo.progress || 0,
          category_id: modalTodo.category_id
        });
        
        // Reset form
        modalTodo.title = '';
        modalTodo.description = '';
        modalTodo.due_date = '';
        modalTodo.reminder_at = '';
        modalTodo.priority = 0;
        modalTodo.progress = 0;
        modalTodo.category_id = categories.value.length > 0 ? categories.value[0].id : null;
        
        // On mobile, hide the form after adding
        if (window.innerWidth < 768) {
          showTaskModal.value = false;
        }
        
        // Refresh todos list
        await fetchTodos();
      } catch (error) {
        console.error('Error adding todo:', error);
      } finally {
        isSubmitting.value = false;
      }
    };

    return {
      todos,
      filteredTodos,
      categories,
      loading,
      newTodo,
      modalTodo,
      isSubmitting,
      searchQuery,
      filterOption,
      categoryFilter,
      sortOption,
      showForm,
      showCategoryModal,
      showTaskModal,
      addTodo,
      toggleTodo,
      deleteTodo,
      newCategory,
      addCategory,
      deleteCategory,
      isValidDate,
      openTaskModal,
      addTodoFromModal,
      applyFilters,
      debounceSearch
    };
  }
};
</script>

<style scoped>
input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  background-color: var(--primary);
  border-radius: 50%;
  cursor: pointer;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background-color: var(--primary);
  border-radius: 50%;
  cursor: pointer;
  border: none;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.2);
}

input[type="range"]:focus {
  outline: none;
}

input[type="range"]:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 3px rgba(107, 71, 245, 0.25);
}

input[type="range"]:focus::-moz-range-thumb {
  box-shadow: 0 0 0 3px rgba(107, 71, 245, 0.25);
}
</style> 