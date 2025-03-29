<template>
  <div 
    class="task-card flex flex-col transition-all duration-200 hover:shadow-md cursor-pointer group"
    :class="{
      'border-l-4 border-red-500 dark:border-red-600': todo.priority == 2,
      'border-l-4 border-yellow-500 dark:border-yellow-600': todo.priority == 1,
      'border-l-4 border-purple-500 dark:border-purple-400': todo.priority == 0
    }"
    :dusk="todo.completed ? 'completed-todo' : 'active-todo'"
  >
    <router-link :to="`/todos/${todo.id}`" class="flex-1">
      <div class="flex items-center">
        <div @click.stop>
          <input 
            type="checkbox" 
            :id="`todo-${todo.id}`" 
            :checked="todo.completed"
            @change="toggleComplete"
            class="h-5 w-5 rounded-full border-2 border-purple-500 dark:border-purple-400 text-purple-500 focus:ring-purple-500"
          >
        </div>
        <label 
          :for="`todo-${todo.id}`"
          class="ml-3 block text-lg font-medium transition-all"
          :class="{
            'line-through text-gray-400 dark:text-gray-500': todo.completed, 
            'text-gray-800 dark:text-gray-200': !todo.completed
          }"
          @click.stop
        >
          {{ todo.title }}
        </label>
        
        <!-- Priority Badge -->
        <div class="ml-2">
          <span 
            class="text-xs font-medium rounded-full px-2 py-0.5"
            :class="{
              'bg-red-100 text-red-800 dark:bg-red-900 dark:bg-opacity-30 dark:text-red-300': todo.priority == 2,
              'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:bg-opacity-30 dark:text-yellow-300': todo.priority == 1,
              'bg-green-100 text-green-800 dark:bg-green-900 dark:bg-opacity-30 dark:text-green-300': todo.priority == 0
            }"
          >
            {{ getPriorityLabel(todo.priority) }}
          </span>
        </div>
        
        <!-- Category -->
        <div v-if="todo.category" class="ml-2">
          <span 
            class="text-xs rounded-full px-2 py-0.5 bg-opacity-20 dark:bg-opacity-20"
            :style="{ 
              backgroundColor: todo.completed ? 'rgba(156, 163, 175, 0.2)' : getCategoryColor(todo.category.id, 0.2),
              color: todo.completed ? 'rgb(156, 163, 175)' : getCategoryColor(todo.category.id)
            }"
          >
            {{ todo.category.name }}
          </span>
        </div>
        
        <button 
          @click.stop="confirmDelete"
          class="ml-auto text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200 opacity-0 group-hover:opacity-100"
          title="Delete todo"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
      
      <!-- Progress bar -->
      <div class="mt-3">
        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
          <div 
            class="h-full rounded-full transition-all duration-500"
            :class="{
              'bg-purple-500': !todo.completed,
              'bg-gray-400 dark:bg-gray-500': todo.completed
            }"
            :style="{ width: `${todo.progress || 0}%` }"
          ></div>
        </div>
        <div class="flex justify-between mt-1">
          <span 
            class="text-xs"
            :class="{
              'text-gray-400 dark:text-gray-500': todo.completed,
              'text-purple-500': !todo.completed
            }"
          >
            {{ todo.progress || 0 }}%
          </span>
          <div v-if="!todo.completed" class="flex opacity-0 group-hover:opacity-100 transition-opacity duration-200" @click.stop>
            <button 
              @click.prevent="updateProgress(Math.max(0, (todo.progress || 0) - 10))"
              class="text-xs text-gray-500 dark:text-gray-400 hover:text-purple-500 dark:hover:text-purple-400 transition-colors"
              :disabled="todo.progress <= 0"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
            <button 
              @click.prevent="updateProgress(Math.min(100, (todo.progress || 0) + 10))"
              class="text-xs text-gray-500 dark:text-gray-400 hover:text-purple-500 dark:hover:text-purple-400 transition-colors ml-1"
              :disabled="todo.progress >= 100"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <div class="mt-3 space-y-2">
        <!-- Description -->
        <div v-if="todo.description" class="text-sm" :class="{ 'text-gray-400 dark:text-gray-500': todo.completed, 'text-gray-600 dark:text-gray-300': !todo.completed }">
          {{ truncateDescription(todo.description) }}
        </div>
        
        <!-- Due date -->
        <div v-if="todo.due_date" 
          class="inline-flex items-center text-sm"
          :class="{
            'text-orange-500 dark:text-orange-400': isOverdue && !todo.completed,
            'text-gray-500 dark:text-gray-400': !isOverdue || todo.completed
          }"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          Due: {{ formatDate(todo.due_date) }}
        </div>
      </div>
    </router-link>
    
    <!-- Delete confirmation dialog -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center" @click.stop>
      <div class="absolute inset-0 bg-black bg-opacity-50" @click="showDeleteConfirm = false"></div>
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full mx-auto z-10 relative">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Delete Todo</h3>
        <p class="text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to delete this todo? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
          <button 
            @click="showDeleteConfirm = false"
            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="deleteTodo"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

export default {
  props: {
    todo: {
      type: Object,
      required: true
    }
  },
  
  emits: ['toggle', 'delete', 'update-progress'],
  
  setup(props, { emit }) {
    const router = useRouter();
    const store = useStore();
    const showDeleteConfirm = ref(false);
    
    const isOverdue = computed(() => {
      if (!props.todo.due_date) return false;
      const dueDate = new Date(props.todo.due_date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      return dueDate < today && !props.todo.completed;
    });
    
    const truncateDescription = (text) => {
      if (!text) return '';
      return text.length > 100 ? text.substring(0, 100) + '...' : text;
    };
    
    const formatDate = (dateString) => {
      const options = { month: 'short', day: 'numeric', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('en-US', options);
    };
    
    const getPriorityLabel = (priority) => {
      switch(priority) {
        case 2: return 'High';
        case 1: return 'Medium';
        case 0: 
        default: return 'Low';
      }
    };
    
    const toggleComplete = () => {
      emit('toggle', props.todo);
    };
    
    const confirmDelete = () => {
      showDeleteConfirm.value = true;
    };
    
    const deleteTodo = () => {
      emit('delete', props.todo.id);
      showDeleteConfirm.value = false;
    };
    
    const updateProgress = async (newProgress) => {
      try {
        await store.dispatch('updateTodo', {
          id: props.todo.id,
          progress: newProgress
        });
      } catch (error) {
        console.error('Error updating progress:', error);
      }
    };
    
    // Generate a consistent color for each category
    const getCategoryColor = (categoryId, alpha = 1) => {
      // A list of pleasant colors
      const colors = [
        'rgb(29, 78, 216)', // blue-700
        'rgb(109, 40, 217)', // violet-700
        'rgb(16, 185, 129)', // emerald-600
        'rgb(217, 70, 239)', // fuchsia-600
        'rgb(234, 88, 12)', // orange-600
        'rgb(6, 182, 212)', // cyan-600
        'rgb(190, 24, 93)', // pink-700
        'rgb(126, 34, 206)', // purple-700
      ];
      
      // Use the category ID as a seed to pick a color
      const colorIndex = (categoryId % colors.length);
      
      // If alpha is less than 1, convert to rgba
      if (alpha < 1) {
        const color = colors[colorIndex];
        const rgbValues = color.match(/\d+/g);
        return `rgba(${rgbValues[0]}, ${rgbValues[1]}, ${rgbValues[2]}, ${alpha})`;
      }
      
      return colors[colorIndex];
    };
    
    return {
      showDeleteConfirm,
      isOverdue,
      truncateDescription,
      formatDate,
      getPriorityLabel,
      toggleComplete,
      confirmDelete,
      deleteTodo,
      updateProgress,
      getCategoryColor
    };
  }
};
</script>

<style scoped>
.task-card {
  @apply bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.task-card:hover {
  @apply border-gray-300 dark:border-gray-600;
  transform: translateY(-2px);
}
</style> 