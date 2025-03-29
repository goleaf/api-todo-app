<template>
  <div class="container py-4 pb-20" data-testid="todo-detail">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-[var(--primary)] border-t-transparent"></div>
      <p class="mt-2 text-gray-600 dark:text-gray-400">Loading todo details...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="task-card p-5 text-center">
      <h4 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-2">Error Loading Todo</h4>
      <p class="text-gray-700 dark:text-gray-300">{{ error }}</p>
      <hr class="my-4 border-gray-200 dark:border-gray-700">
      <div class="flex justify-center mt-3 space-x-3">
        <button class="px-4 py-2 border border-[var(--primary)] text-[var(--primary)] rounded-full hover:bg-[var(--primary)] hover:bg-opacity-10 transition-colors" @click="$router.push('/')">
          Back to Todo List
        </button>
        <button class="btn-primary" @click="loadTodo">
          Try Again
        </button>
      </div>
    </div>

    <!-- Todo Details -->
    <div v-else-if="todo" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Back button -->
      <div class="col-span-1 md:col-span-3 mb-2">
        <button class="px-4 py-2 border border-[var(--primary)] text-[var(--primary)] rounded-full hover:bg-[var(--primary)] hover:bg-opacity-10 transition-colors" @click="$router.push('/')">
          Back to Todo List
        </button>
      </div>

      <!-- Main Info Card -->
      <div class="col-span-1 md:col-span-2">
        <div class="task-card"
             :class="{
               'border-l-4 border-red-500 dark:border-red-600': todo.priority == 2,
               'border-l-4 border-yellow-500 dark:border-yellow-600': todo.priority == 1,
               'border-l-4 border-[var(--primary)] dark:border-[var(--primary)]': todo.priority == 0
             }">
          <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
            <h4 class="text-xl font-semibold" :class="{'line-through text-gray-400 dark:text-gray-500': todo.completed}">
              {{ todo.title }}
            </h4>
            <span class="rounded-full px-3 py-1 text-sm font-medium"
                  :class="todo.completed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'">
              {{ todo.completed ? 'Completed' : 'Active' }}
            </span>
          </div>
          
          <div class="mb-4 flex items-center">
            <input 
              type="checkbox" 
              id="completeSwitch" 
              :checked="isCompleted"
              @change="updateStatus"
              class="h-5 w-5 rounded-full border-2 border-[var(--primary)] dark:border-[var(--primary)] text-[var(--primary)] focus:ring-[var(--primary)]">
            <label class="ml-3 block text-gray-700 dark:text-gray-300" for="completeSwitch">
              {{ todo.completed ? 'Completed' : 'Mark as complete' }}
            </label>
          </div>

          <div class="mb-6">
            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">Description</h5>
            <p class="text-gray-700 dark:text-gray-300" v-if="todo.description">{{ todo.description }}</p>
            <p class="text-gray-500 dark:text-gray-500 italic" v-else>No description provided</p>
          </div>

          <div class="mb-6" v-if="todo.progress !== undefined">
            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-2">Progress: {{ todo.progress }}%</h5>
            <div class="h-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full">
              <div 
                class="h-full rounded-full bg-[var(--primary)]" 
                :style="{ width: `${todo.progress || 0}%` }">
              </div>
            </div>
            <div class="flex justify-end mt-1">
              <small class="text-gray-500 dark:text-gray-400">{{ todo.progress || 0 }}% complete</small>
            </div>
          </div>
          
          <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex space-x-2">
              <span class="text-xs text-gray-500 dark:text-gray-400">Created: {{ formatDate(todo.created_at) }}</span>
              <span v-if="todo.updated_at !== todo.created_at" class="text-xs text-gray-500 dark:text-gray-400">
                Updated: {{ formatDate(todo.updated_at) }}
              </span>
            </div>
            <div class="flex space-x-2">
              <button class="btn-primary" @click="editTodo">
                Edit
              </button>
              <button class="px-4 py-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors" @click="deleteTodo">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Info -->
      <div class="col-span-1">
        <!-- Priority Card -->
        <div class="task-card mb-4">
          <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Priority</h5>
          <span 
            class="inline-block rounded-full px-4 py-2 text-sm font-medium"
            :class="{
              'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': todo.priority == 2,
              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': todo.priority == 1,
              'bg-[var(--primary)] bg-opacity-10 text-[var(--primary)]': todo.priority == 0
            }">
            {{ priorityLabel }}
          </span>
        </div>

        <!-- Due Date Card -->
        <div class="task-card mb-4">
          <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Due Date</h5>
          <div v-if="todo.due_date" class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[var(--primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-gray-700 dark:text-gray-300">{{ formatDate(todo.due_date) }}</span>
          </div>
          <p class="text-gray-500 dark:text-gray-500 italic" v-else>No due date set</p>
          
          <div v-if="todo.due_date" class="mt-3">
            <p class="text-sm"
              :class="{
                'text-red-600 dark:text-red-400': isOverdue && !todo.completed,
                'text-gray-500 dark:text-gray-400': !isOverdue || todo.completed
              }">
              {{ getDueStatus }}
            </p>
          </div>
        </div>

        <!-- Activity Card -->
        <div class="task-card">
          <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3">Activity</h5>
          <ul class="space-y-3">
            <li class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
              </div>
              <span class="text-gray-700 dark:text-gray-300">Created {{ formatTimeAgo(todo.created_at) }}</span>
            </li>
            <li v-if="todo.updated_at !== todo.created_at" class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-[var(--primary)] bg-opacity-10 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[var(--primary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </div>
              <span class="text-gray-700 dark:text-gray-300">Updated {{ formatTimeAgo(todo.updated_at) }}</span>
            </li>
            <li v-if="todo.completed" class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <span class="text-gray-700 dark:text-gray-300">Completed {{ formatTimeAgo(todo.completed_at || todo.updated_at) }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else class="text-center py-5">
      <div class="task-card p-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Todo Not Found</h4>
        <p class="text-gray-600 dark:text-gray-400 mb-6">
          The todo you're looking for doesn't exist or may have been deleted.
        </p>
        <button class="btn-primary" @click="$router.push('/')">
          Back to Todo List
        </button>
      </div>
    </div>

    <!-- Edit Modal -->
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50" v-if="showEditModal">
      <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full m-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Edit Todo</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400" @click="showEditModal = false">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          <form class="space-y-5">
            <div>
              <label for="editTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
              <input 
                type="text" 
                id="editTitle" 
                v-model="editingTodo.title" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                required
              >
            </div>
            <div>
              <label for="editDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
              <textarea 
                id="editDescription" 
                v-model="editingTodo.description" 
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              ></textarea>
            </div>
            <div>
              <label for="editDueDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
              <input 
                type="date" 
                id="editDueDate" 
                v-model="editingTodo.due_date"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                :min="new Date().toISOString().split('T')[0]"
              >
              <div v-if="editingTodo.due_date && !isValidDate(editingTodo.due_date)" class="text-xs text-red-500 mt-1">
                Please enter a valid date
              </div>
            </div>
            <div>
              <label for="editPriority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
              <select 
                id="editPriority" 
                v-model="editingTodo.priority"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
              >
                <option value="0">Low</option>
                <option value="1">Medium</option>
                <option value="2">High</option>
              </select>
            </div>
            <div>
              <label for="editProgress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Progress: {{ editingTodo.progress || 0 }}%
              </label>
              <input
                v-model.number="editingTodo.progress"
                type="range"
                class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full appearance-none cursor-pointer"
                min="0"
                max="100"
                step="5"
                id="editProgress"
              >
            </div>
            <div class="flex items-center">
              <input 
                type="checkbox" 
                id="editCompleted" 
                v-model="editingTodo.completed"
                class="h-5 w-5 rounded-full border-2 border-[var(--primary)] dark:border-[var(--primary)] text-[var(--primary)] focus:ring-[var(--primary)]"
              >
              <label class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300" for="editCompleted">
                Completed
              </label>
            </div>
          </form>
          <div class="mt-8 flex justify-end space-x-3">
            <button 
              type="button" 
              class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              @click="showEditModal = false"
            >
              Cancel
            </button>
            <button 
              type="button" 
              class="btn-primary"
              @click="saveEditedTodo"
            >
              Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRoute, useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const route = useRoute();
    const router = useRouter();
    
    const todo = ref(null);
    const loading = ref(true);
    const error = ref(null);
    const editingTodo = ref({});
    const showEditModal = ref(false);
    
    const isCompleted = computed({
      get: () => todo.value?.completed || false,
      set: (value) => {
        if (todo.value) {
          todo.value.completed = value;
        }
      }
    });
    
    // Load todo data
    const loadTodo = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const todoId = parseInt(route.params.id);
        
        // First check if it's already in the store
        const storeState = store.state.todos;
        const todoInStore = storeState.find(t => t.id === todoId);
        
        if (todoInStore) {
          todo.value = { ...todoInStore };
        } else {
          // Otherwise fetch it from the API
          const response = await store.dispatch('fetchTodo', todoId);
          todo.value = response;
        }
      } catch (err) {
        console.error('Error loading todo:', err);
        error.value = 'Failed to load todo details. Please try again.';
      } finally {
        loading.value = false;
      }
    };
    
    onMounted(async () => {
      await loadTodo();
    });
    
    // Computed properties for styling and display
    const priorityBorderClass = computed(() => {
      if (!todo.value?.priority) return '';
      
      switch(todo.value.priority) {
        case 'high':
          return 'border-danger border-2';
        case 'medium':
          return 'border-warning border-2';
        case 'low':
          return 'border-info border-2';
        default:
          return '';
      }
    });
    
    const priorityBadgeClass = computed(() => {
      if (!todo.value?.priority) return 'bg-secondary';
      
      switch(todo.value.priority) {
        case 'high':
          return 'bg-danger';
        case 'medium':
          return 'bg-warning text-dark';
        case 'low':
          return 'bg-info text-dark';
        default:
          return 'bg-secondary';
      }
    });
    
    const priorityLabel = computed(() => {
      if (!todo.value?.priority) return 'No Priority';
      
      return todo.value.priority.charAt(0).toUpperCase() + todo.value.priority.slice(1);
    });
    
    const statusClass = computed(() => {
      return todo.value?.completed ? 'bg-success' : 'bg-warning';
    });
    
    const statusHeaderClass = computed(() => {
      return todo.value?.completed ? 'bg-success bg-opacity-25' : 'bg-warning bg-opacity-25';
    });
    
    const getProgressBarClass = computed(() => {
      if (todo.value?.completed) return 'bg-success';
      
      const progress = todo.value?.progress || 0;
      if (progress < 30) return 'bg-danger';
      if (progress < 70) return 'bg-warning';
      return 'bg-success';
    });
    
    const getDueStatus = computed(() => {
      if (!todo.value?.due_date) return '';
      
      const now = new Date();
      const dueDate = new Date(todo.value.due_date);
      
      // Check if the date is valid
      if (isNaN(dueDate.getTime())) return 'Invalid date';
      
      if (todo.value.completed) {
        return 'Task completed';
      }
      
      if (dueDate < now) {
        return 'Overdue';
      }
      
      // Calculate days remaining
      const diffTime = Math.abs(dueDate - now);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays === 0) {
        return 'Due today';
      } else if (diffDays === 1) {
        return 'Due tomorrow';
      } else {
        return `Due in ${diffDays} days`;
      }
    });
    
    // Utility methods
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      
      const date = new Date(dateString);
      
      // Check if the date is valid
      if (isNaN(date.getTime())) return 'Invalid date';
      
      return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric'
      });
    };
    
    const formatTimeAgo = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      const now = new Date();
      const diffInSeconds = Math.floor((now - date) / 1000);
      
      if (diffInSeconds < 60) {
        return 'just now';
      } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} ${minutes === 1 ? 'minute' : 'minutes'} ago`;
      } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} ${hours === 1 ? 'hour' : 'hours'} ago`;
      } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} ${days === 1 ? 'day' : 'days'} ago`;
      }
    };
    
    // Event handlers
    const updateStatus = async () => {
      try {
        const updatedTodo = { ...todo.value };
        await store.dispatch('updateTodo', updatedTodo);
        // Update local todo with the response
        todo.value = updatedTodo;
      } catch (error) {
        console.error('Error updating todo status:', error);
        // Revert the UI change
        isCompleted.value = !isCompleted.value;
      }
    };
    
    const editTodo = () => {
      editingTodo.value = { ...todo.value };
      
      // Format date for input element
      if (editingTodo.value.due_date) {
        const date = new Date(editingTodo.value.due_date);
        editingTodo.value.due_date = date.toISOString().split('T')[0];
      }
      
      // Show modal
      showEditModal.value = true;
    };
    
    const saveEditedTodo = async () => {
      try {
        // Validate due_date if provided
        let todoToUpdate = { ...editingTodo.value };
        
        if (todoToUpdate.due_date) {
          const dueDate = new Date(todoToUpdate.due_date);
          if (isNaN(dueDate.getTime())) {
            // If due date is invalid, set it to null
            todoToUpdate.due_date = null;
          }
        }
        
        await store.dispatch('updateTodo', todoToUpdate);
        // Update the displayed todo
        todo.value = { ...todoToUpdate };
        
        // Hide modal after save
        showEditModal.value = false;
      } catch (error) {
        console.error('Error updating todo:', error);
      }
    };
    
    const deleteTodo = async () => {
      if (confirm('Are you sure you want to delete this todo?')) {
        try {
          await store.dispatch('deleteTodo', todo.value.id);
          router.push('/');
        } catch (error) {
          console.error('Error deleting todo:', error);
        }
      }
    };

    const isValidDate = (dateString) => {
      const date = new Date(dateString);
      return !isNaN(date.getTime());
    };
    
    return {
      todo,
      loading,
      error,
      editingTodo,
      showEditModal,
      isCompleted,
      priorityBorderClass,
      priorityBadgeClass,
      priorityLabel,
      statusClass,
      statusHeaderClass,
      getProgressBarClass,
      getDueStatus,
      formatDate,
      formatTimeAgo,
      loadTodo,
      updateStatus,
      editTodo,
      saveEditedTodo,
      deleteTodo,
      isValidDate
    };
  }
};
</script>

<style scoped>
.card {
  border-radius: 8px;
  transition: all 0.2s ease;
}

.form-check-input:checked {
  background-color: var(--bs-success);
  border-color: var(--bs-success);
}

.form-range::-webkit-slider-thumb {
  background: var(--bs-primary);
}

.form-range::-moz-range-thumb {
  background: var(--bs-primary);
}

.form-range::-ms-thumb {
  background: var(--bs-primary);
}

/* Progress bar styles */
.progress {
  background-color: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.progress-bar {
  transition: width 0.5s ease;
}

.progress-bar-striped {
  background-image: linear-gradient(
    45deg,
    rgba(255, 255, 255, 0.15) 25%,
    transparent 25%,
    transparent 50%,
    rgba(255, 255, 255, 0.15) 50%,
    rgba(255, 255, 255, 0.15) 75%,
    transparent 75%,
    transparent
  );
  background-size: 1rem 1rem;
}

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