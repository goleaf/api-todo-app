<template>
  <div class="task-card p-4">
    <div class="flex justify-between items-center mb-6">
      <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ currentMonthName }} {{ currentYear }}</h2>
      <button class="px-3 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
    
    <!-- Days of week -->
    <div class="grid grid-cols-7 gap-1 mb-2">
      <div v-for="day in daysOfWeek" :key="day" class="text-center text-sm font-medium text-gray-500 dark:text-gray-400 py-2">
        {{ day }}
      </div>
    </div>
    
    <!-- Calendar grid -->
    <div class="grid grid-cols-7 gap-1">
      <div 
        v-for="date in calendarDays" 
        :key="date.date" 
        class="p-1 border border-gray-200 dark:border-gray-700 rounded-lg aspect-square"
        :class="{
          'bg-gray-100 dark:bg-gray-800': date.isCurrentMonth,
          'bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-600': !date.isCurrentMonth,
          'ring-2 ring-[var(--primary)] ring-opacity-50': date.isToday
        }"
      >
        <div class="h-full flex flex-col">
          <div class="flex justify-between items-center">
            <span class="text-sm">{{ date.day }}</span>
            <span v-if="date.hasOverdue" class="w-2 h-2 rounded-full bg-red-500 mr-1"></span>
          </div>
          <div v-if="date.taskCount > 0" class="mt-auto text-xs text-[var(--primary)]">
            {{ date.taskCount }} {{ date.taskCount === 1 ? 'task' : 'tasks' }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref } from 'vue';

export default {
  name: 'TodoCalendar',
  props: {
    todos: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    const currentMonth = ref(new Date().getMonth());
    const currentYear = ref(new Date().getFullYear());
    
    const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    const currentMonthName = computed(() => {
      return new Date(currentYear.value, currentMonth.value, 1)
        .toLocaleString('default', { month: 'long' });
    });
    
    const calendarDays = computed(() => {
      const days = [];
      const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay();
      const lastDate = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
      const prevMonthLastDate = new Date(currentYear.value, currentMonth.value, 0).getDate();
      const today = new Date();
      const currentDate = {
        day: today.getDate(),
        month: today.getMonth(),
        year: today.getFullYear()
      };
      
      // Previous month days
      for (let i = firstDay - 1; i >= 0; i--) {
        const day = prevMonthLastDate - i;
        const date = new Date(
          currentYear.value, 
          currentMonth.value - 1, 
          day
        );
        
        days.push({
          day: day,
          date: date.toISOString().split('T')[0],
          isCurrentMonth: false,
          isToday: false,
          taskCount: getTaskCountForDate(date),
          hasOverdue: hasOverdueTasks(date)
        });
      }
      
      // Current month days
      for (let i = 1; i <= lastDate; i++) {
        const date = new Date(
          currentYear.value, 
          currentMonth.value, 
          i
        );
        
        days.push({
          day: i,
          date: date.toISOString().split('T')[0],
          isCurrentMonth: true,
          isToday: i === currentDate.day && 
                  currentMonth.value === currentDate.month && 
                  currentYear.value === currentDate.year,
          taskCount: getTaskCountForDate(date),
          hasOverdue: hasOverdueTasks(date)
        });
      }
      
      // Next month days to fill the grid
      const remainingDays = 42 - days.length; // 6 rows x 7 days = 42
      for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(
          currentYear.value, 
          currentMonth.value + 1, 
          i
        );
        
        days.push({
          day: i,
          date: date.toISOString().split('T')[0],
          isCurrentMonth: false,
          isToday: false,
          taskCount: getTaskCountForDate(date),
          hasOverdue: hasOverdueTasks(date)
        });
      }
      
      return days;
    });
    
    // Function to count tasks for a specific date
    function getTaskCountForDate(date) {
      if (!props.todos || !props.todos.length) return 0;
      
      const dateString = date.toISOString().split('T')[0];
      return props.todos.filter(todo => {
        if (!todo.due_date) return false;
        return todo.due_date.startsWith(dateString);
      }).length;
    }
    
    // Function to check if there are overdue tasks for a date
    function hasOverdueTasks(date) {
      if (!props.todos || !props.todos.length) return false;
      
      const now = new Date();
      const dateString = date.toISOString().split('T')[0];
      
      return props.todos.some(todo => {
        if (!todo.due_date || todo.completed) return false;
        return todo.due_date.startsWith(dateString) && new Date(todo.due_date) < now;
      });
    }
    
    return {
      currentMonth,
      currentYear,
      currentMonthName,
      daysOfWeek,
      calendarDays
    };
  }
}
</script> 