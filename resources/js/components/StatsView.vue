<template>
  <div class="container mx-auto px-4 py-6 pb-20 max-w-4xl" data-testid="stats-view">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
      Your Task Statistics
    </h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Completion Rate Card -->
      <div class="task-card">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Completion Rate</h2>
        <div class="flex items-end mb-3">
          <div class="text-4xl font-bold text-purple-600">{{ completionRate }}%</div>
          <div class="ml-2 text-sm text-gray-500 dark:text-gray-400 mb-1">tasks completed</div>
        </div>
        
        <div class="progress-bar mb-1">
          <div class="progress-bar-fill" :style="{ width: `${completionRate}%` }"></div>
        </div>
        
        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
          <span>0%</span>
          <span>50%</span>
          <span>100%</span>
        </div>
      </div>
      
      <!-- Task Count Card -->
      <div class="task-card">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Task Count</h2>
        <div class="flex items-center mb-4">
          <div class="text-4xl font-bold text-orange-500">{{ totalTasks }}</div>
          <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">total tasks</div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
          <div class="bg-green-50 dark:bg-green-900 dark:bg-opacity-20 rounded-lg p-3">
            <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ completedTasks }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Completed</div>
          </div>
          
          <div class="bg-yellow-50 dark:bg-yellow-900 dark:bg-opacity-20 rounded-lg p-3">
            <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ pendingTasks }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Pending</div>
          </div>
        </div>
      </div>
      
      <!-- Priority Distribution -->
      <div class="task-card">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Priority Distribution</h2>
        <div class="space-y-3">
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">High Priority</span>
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ highPriorityCount }}</span>
            </div>
            <div class="progress-bar">
              <div 
                class="h-full bg-red-500 rounded-full transition-all duration-500"
                :style="{ width: `${highPriorityPercentage}%` }"
              ></div>
            </div>
          </div>
          
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Medium Priority</span>
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ mediumPriorityCount }}</span>
            </div>
            <div class="progress-bar">
              <div 
                class="h-full bg-yellow-500 rounded-full transition-all duration-500"
                :style="{ width: `${mediumPriorityPercentage}%` }"
              ></div>
            </div>
          </div>
          
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Low Priority</span>
              <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ lowPriorityCount }}</span>
            </div>
            <div class="progress-bar">
              <div 
                class="h-full bg-green-500 rounded-full transition-all duration-500"
                :style="{ width: `${lowPriorityPercentage}%` }"
              ></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Due Soon Tasks -->
      <div class="task-card">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Due Soon</h2>
        
        <div v-if="dueSoonTasks.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
          No tasks due in the next 7 days.
        </div>
        
        <div v-else class="space-y-2 max-h-40 overflow-y-auto">
          <div 
            v-for="task in dueSoonTasks" 
            :key="task.id"
            class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center"
          >
            <span 
              class="w-3 h-3 rounded-full mr-2"
              :class="{
                'bg-red-500': task.priority === 2,
                'bg-yellow-500': task.priority === 1,
                'bg-green-500': task.priority === 0
              }"
            ></span>
            <span class="flex-grow truncate text-gray-800 dark:text-gray-200">{{ task.title }}</span>
            <span class="text-xs text-orange-500 dark:text-orange-400">{{ formatDate(task.due_date) }}</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="task-card mt-6">
      <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Category Distribution</h2>
      
      <div v-if="categories.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
        No categories created yet.
      </div>
      
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
        <div 
          v-for="category in categorySummary" 
          :key="category.id"
          class="p-3 rounded-lg border border-gray-200 dark:border-gray-700"
        >
          <div class="flex justify-between items-center mb-1">
            <span class="font-medium text-gray-800 dark:text-gray-200">{{ category.name }}</span>
            <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ category.count }}</span>
          </div>
          <div class="progress-bar">
            <div 
              class="h-full rounded-full transition-all duration-500"
              :style="{ 
                width: `${category.percentage}%`,
                backgroundColor: getCategoryColor(category.id)
              }"
            ></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, onMounted, ref } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'StatsView',
  
  setup() {
    const store = useStore();
    const todos = ref([]);
    const categories = ref([]);
    
    onMounted(async () => {
      try {
        // Fetch data if not already in store
        if (store.state.todos.length === 0) {
          await store.dispatch('fetchTodos');
        }
        
        if (store.state.categories.length === 0) {
          await store.dispatch('fetchCategories');
        }
        
        todos.value = store.state.todos;
        categories.value = store.state.categories;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    });
    
    // Watch for changes in store
    store.subscribe((mutation, state) => {
      if (mutation.type === 'setTodos') {
        todos.value = state.todos;
      }
      if (mutation.type === 'setCategories') {
        categories.value = state.categories;
      }
    });
    
    // Computed properties for statistics
    const totalTasks = computed(() => todos.value.length);
    
    const completedTasks = computed(() => todos.value.filter(todo => todo.completed).length);
    
    const pendingTasks = computed(() => todos.value.filter(todo => !todo.completed).length);
    
    const completionRate = computed(() => {
      if (totalTasks.value === 0) return 0;
      return Math.round((completedTasks.value / totalTasks.value) * 100);
    });
    
    const highPriorityCount = computed(() => 
      todos.value.filter(todo => todo.priority === 2).length
    );
    
    const mediumPriorityCount = computed(() => 
      todos.value.filter(todo => todo.priority === 1).length
    );
    
    const lowPriorityCount = computed(() => 
      todos.value.filter(todo => todo.priority === 0 || todo.priority === null).length
    );
    
    const highPriorityPercentage = computed(() => {
      if (totalTasks.value === 0) return 0;
      return Math.round((highPriorityCount.value / totalTasks.value) * 100);
    });
    
    const mediumPriorityPercentage = computed(() => {
      if (totalTasks.value === 0) return 0;
      return Math.round((mediumPriorityCount.value / totalTasks.value) * 100);
    });
    
    const lowPriorityPercentage = computed(() => {
      if (totalTasks.value === 0) return 0;
      return Math.round((lowPriorityCount.value / totalTasks.value) * 100);
    });
    
    const dueSoonTasks = computed(() => {
      const today = new Date();
      const nextWeek = new Date();
      nextWeek.setDate(today.getDate() + 7);
      
      return todos.value
        .filter(todo => {
          if (!todo.due_date || todo.completed) return false;
          const dueDate = new Date(todo.due_date);
          return dueDate >= today && dueDate <= nextWeek;
        })
        .sort((a, b) => new Date(a.due_date) - new Date(b.due_date))
        .slice(0, 5); // Show only the first 5
    });
    
    const categorySummary = computed(() => {
      if (categories.value.length === 0) return [];
      
      return categories.value.map(category => {
        const tasksInCategory = todos.value.filter(todo => todo.category_id === category.id);
        return {
          id: category.id,
          name: category.name,
          count: tasksInCategory.length,
          completed: tasksInCategory.filter(todo => todo.completed).length,
          percentage: totalTasks.value === 0 ? 0 : Math.round((tasksInCategory.length / totalTasks.value) * 100)
        };
      });
    });
    
    // Format date: "Jan 15"
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    };
    
    // Generate a color for each category
    const getCategoryColor = (categoryId) => {
      const colors = [
        '#8b5cf6', // purple-500
        '#ec4899', // pink-500
        '#3b82f6', // blue-500
        '#10b981', // emerald-500
        '#f97316', // orange-500
        '#14b8a6', // teal-500
        '#ef4444', // red-500
        '#f59e0b', // amber-500
      ];
      
      return colors[categoryId % colors.length];
    };
    
    return {
      totalTasks,
      completedTasks,
      pendingTasks,
      completionRate,
      highPriorityCount,
      mediumPriorityCount,
      lowPriorityCount,
      highPriorityPercentage,
      mediumPriorityPercentage,
      lowPriorityPercentage,
      dueSoonTasks,
      categories,
      categorySummary,
      formatDate,
      getCategoryColor
    };
  }
};
</script>

<style scoped>
/* Progress bar animation */
.bg-\[var\(--primary\)\] {
  transition: width 0.5s ease-in-out;
}
</style> 