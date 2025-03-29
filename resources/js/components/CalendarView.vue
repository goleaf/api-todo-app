<template>
  <div class="page-container">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">Task Calendar</h1>
    
    <div class="task-card">
      <!-- Calendar Header -->
      <div class="flex items-center justify-between mb-6">
        <button 
          @click="previousMonth" 
          class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
          {{ currentMonthName }} {{ currentYear }}
        </h2>
        
        <button 
          @click="nextMonth" 
          class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
      
      <!-- Days of Week -->
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div v-for="day in daysOfWeek" :key="day" class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">
          {{ day }}
        </div>
      </div>
      
      <!-- Calendar Days -->
      <div class="grid grid-cols-7 gap-1">
        <div 
          v-for="(day, index) in calendarDays" 
          :key="index" 
          class="aspect-square p-1 border rounded-lg transition-colors"
          :class="[
            day.isCurrentMonth ? 'border-gray-200 dark:border-gray-700' : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800',
            day.isToday ? 'bg-purple-50 dark:bg-purple-900 dark:bg-opacity-20 border-purple-200 dark:border-purple-800' : '',
            day.date.getDate() === selectedDate?.getDate() && day.date.getMonth() === selectedDate?.getMonth() ? 'border-purple-500 dark:border-purple-500' : ''
          ]"
          @click="selectDate(day)"
        >
          <!-- Day number -->
          <div class="flex justify-between items-center">
            <span 
              class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full"
              :class="[
                day.isCurrentMonth ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600',
                day.isToday ? 'bg-purple-500 text-white' : ''
              ]"
            >
              {{ day.date.getDate() }}
            </span>
            
            <!-- Task count indicator -->
            <span 
              v-if="day.taskCount" 
              class="text-xs font-medium px-1.5 py-0.5 rounded-full"
              :class="getTaskCountClass(day.taskCount)"
            >
              {{ day.taskCount }}
            </span>
          </div>
          
          <!-- Task dots (up to 3) -->
          <div class="mt-1 flex flex-wrap gap-1 justify-center" v-if="day.tasks.length > 0">
            <div 
              v-for="(task, taskIndex) in day.tasks.slice(0, 3)" 
              :key="taskIndex"
              class="w-2 h-2 rounded-full"
              :class="getPriorityClass(task.priority)"
              :title="task.title"
            ></div>
            <div v-if="day.tasks.length > 3" class="w-2 h-2 rounded-full bg-gray-400 dark:bg-gray-500"></div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Tasks for Selected Date -->
    <div v-if="selectedDate && tasksForSelectedDate.length > 0" class="task-card mt-6">
      <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
        Tasks for {{ formatDate(selectedDate) }}
      </h3>
      
      <div class="space-y-3">
        <div 
          v-for="task in tasksForSelectedDate" 
          :key="task.id"
          class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-gray-300 dark:hover:border-gray-600 transition-colors"
          @click="goToTaskDetail(task.id)"
        >
          <div class="flex items-center">
            <span 
              class="w-3 h-3 rounded-full mr-2"
              :class="getPriorityClass(task.priority)"
            ></span>
            <span 
              class="font-medium text-gray-800 dark:text-gray-200"
              :class="{ 'line-through text-gray-400 dark:text-gray-500': task.completed }"
            >
              {{ task.title }}
            </span>
            
            <!-- Priority badge -->
            <span 
              class="ml-2 text-xs rounded-full px-2 py-0.5"
              :class="{
                'bg-red-100 text-red-800 dark:bg-red-900 dark:bg-opacity-30 dark:text-red-300': task.priority === 2,
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:bg-opacity-30 dark:text-yellow-300': task.priority === 1,
                'bg-green-100 text-green-800 dark:bg-green-900 dark:bg-opacity-30 dark:text-green-300': task.priority === 0
              }"
            >
              {{ getPriorityLabel(task.priority) }}
            </span>
            
            <!-- Category badge if available -->
            <span 
              v-if="task.category"
              class="ml-2 text-xs rounded-full px-2 py-0.5 bg-opacity-20 dark:bg-opacity-20"
              :style="{ 
                backgroundColor: task.completed ? 'rgba(156, 163, 175, 0.2)' : getCategoryColor(task.category.id, 0.2),
                color: task.completed ? 'rgb(156, 163, 175)' : getCategoryColor(task.category.id)
              }"
            >
              {{ task.category.name }}
            </span>
            
            <!-- Status indicator -->
            <span 
              class="ml-auto text-xs rounded-full px-2 py-0.5"
              :class="{
                'bg-green-100 text-green-800 dark:bg-green-900 dark:bg-opacity-30 dark:text-green-300': task.completed,
                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:bg-opacity-30 dark:text-blue-300': !task.completed
              }"
            >
              {{ task.completed ? 'Completed' : 'In Progress' }}
            </span>
          </div>
          
          <!-- Progress bar if not completed -->
          <div v-if="!task.completed && task.progress > 0" class="mt-2">
            <div class="progress-bar">
              <div class="progress-bar-fill" :style="{ width: `${task.progress}%` }"></div>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {{ task.progress }}% complete
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- No tasks message -->
    <div v-else-if="selectedDate && tasksForSelectedDate.length === 0" class="task-card mt-6">
      <div class="text-center py-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">No tasks for this date</h3>
        <p class="text-gray-500 dark:text-gray-400">You don't have any tasks scheduled for {{ formatDate(selectedDate) }}.</p>
        <button 
          @click="$router.push('/')"
          class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
        >
          Add a Task
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'CalendarView',
  
  setup() {
    const store = useStore();
    const router = useRouter();
    const todos = computed(() => store.state.todos);
    const currentDate = ref(new Date());
    const selectedDate = ref(null);
    
    // Days of week abbreviations
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    // Current month and year
    const currentMonthName = computed(() => {
      return currentDate.value.toLocaleDateString('en-US', { month: 'long' });
    });
    
    const currentYear = computed(() => {
      return currentDate.value.getFullYear();
    });
    
    // Generate calendar days
    const calendarDays = computed(() => {
      const days = [];
      const year = currentDate.value.getFullYear();
      const month = currentDate.value.getMonth();
      
      // Create a date for the first day of the month
      const firstDay = new Date(year, month, 1);
      
      // Find the first Sunday before or on the first day of the month
      const startDate = new Date(firstDay);
      startDate.setDate(firstDay.getDate() - firstDay.getDay());
      
      // Create a date for the last day of the month
      const lastDay = new Date(year, month + 1, 0);
      
      // Find the last Saturday after or on the last day of the month
      const endDate = new Date(lastDay);
      const daysToAdd = 6 - endDate.getDay();
      endDate.setDate(lastDay.getDate() + daysToAdd);
      
      // Fill in all days
      let currentDay = new Date(startDate);
      while (currentDay <= endDate) {
        const isCurrentMonth = currentDay.getMonth() === month;
        const isToday = isSameDay(currentDay, new Date());
        
        // Find tasks for this day
        const tasksForDay = todos.value.filter(todo => {
          if (!todo.due_date) return false;
          const dueDate = new Date(todo.due_date);
          return isSameDay(dueDate, currentDay);
        });
        
        days.push({
          date: new Date(currentDay),
          isCurrentMonth,
          isToday,
          tasks: tasksForDay,
          taskCount: tasksForDay.length
        });
        
        // Move to next day
        currentDay.setDate(currentDay.getDate() + 1);
      }
      
      return days;
    });
    
    // Tasks for selected date
    const tasksForSelectedDate = computed(() => {
      if (!selectedDate.value) return [];
      
      return todos.value.filter(todo => {
        if (!todo.due_date) return false;
        const dueDate = new Date(todo.due_date);
        return isSameDay(dueDate, selectedDate.value);
      }).sort((a, b) => {
        // Sort by priority (high to low) and then by completion status
        if (a.priority !== b.priority) {
          return b.priority - a.priority;
        }
        return a.completed === b.completed ? 0 : a.completed ? 1 : -1;
      });
    });
    
    // Navigate to previous month
    const previousMonth = () => {
      const date = new Date(currentDate.value);
      date.setMonth(date.getMonth() - 1);
      currentDate.value = date;
      selectedDate.value = null;
    };
    
    // Navigate to next month
    const nextMonth = () => {
      const date = new Date(currentDate.value);
      date.setMonth(date.getMonth() + 1);
      currentDate.value = date;
      selectedDate.value = null;
    };
    
    // Select a date
    const selectDate = (day) => {
      selectedDate.value = new Date(day.date);
    };
    
    // Navigate to task detail
    const goToTaskDetail = (taskId) => {
      router.push(`/todos/${taskId}`);
    };
    
    // Format date for display
    const formatDate = (date) => {
      return date.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    };
    
    // Check if two dates are the same day
    const isSameDay = (date1, date2) => {
      return date1.getDate() === date2.getDate() &&
        date1.getMonth() === date2.getMonth() &&
        date1.getFullYear() === date2.getFullYear();
    };
    
    // Get priority class for coloring
    const getPriorityClass = (priority) => {
      switch(priority) {
        case 2: return 'bg-red-500';
        case 1: return 'bg-yellow-500';
        case 0:
        default: return 'bg-green-500';
      }
    };
    
    // Get task count class
    const getTaskCountClass = (count) => {
      if (count >= 5) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:bg-opacity-30 dark:text-red-300';
      if (count >= 3) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:bg-opacity-30 dark:text-yellow-300';
      return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:bg-opacity-30 dark:text-blue-300';
    };
    
    // Get priority label
    const getPriorityLabel = (priority) => {
      switch(priority) {
        case 2: return 'High';
        case 1: return 'Medium';
        case 0:
        default: return 'Low';
      }
    };
    
    // Generate a color for each category
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
    
    // Fetch todos if not already in store
    onMounted(async () => {
      if (store.state.todos.length === 0) {
        await store.dispatch('fetchTodos');
      }
    });
    
    return {
      currentDate,
      currentMonthName,
      currentYear,
      daysOfWeek,
      calendarDays,
      selectedDate,
      tasksForSelectedDate,
      previousMonth,
      nextMonth,
      selectDate,
      formatDate,
      goToTaskDetail,
      getPriorityClass,
      getTaskCountClass,
      getPriorityLabel,
      getCategoryColor
    };
  }
};
</script>

<style scoped>
.aspect-square {
  aspect-ratio: 1 / 1;
}
</style> 