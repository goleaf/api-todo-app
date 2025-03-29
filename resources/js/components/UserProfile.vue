<template>
  <div>
    <div class="task-card mb-6">
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-6">My Profile</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 flex flex-col items-center mb-4 md:mb-0">
          <div v-if="!user.photo_url" 
               class="rounded-full bg-[var(--primary)] text-white flex items-center justify-center shadow-md"
               style="width: 150px; height: 150px;">
            <span class="text-5xl font-light">{{ userInitial }}</span>
          </div>
          <img v-else 
               :src="user.photo_url" 
               :alt="user.name" 
               class="rounded-full border-4 border-white dark:border-gray-800 shadow-md" 
               style="width: 150px; height: 150px; object-fit: cover;">
          
          <h3 class="text-lg font-medium mt-4 text-gray-800 dark:text-gray-200">{{ user.name }}</h3>
          <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
          
          <div class="flex flex-col w-full gap-2 mt-4">
            <button type="button" class="px-4 py-2 border border-[var(--primary)] text-[var(--primary)] rounded-full hover:bg-[var(--primary)] hover:bg-opacity-10 transition-colors">
              Edit Profile
            </button>
            <form @submit.prevent="logout" class="w-full">
              <button type="submit" class="w-full px-4 py-2 border border-red-500 text-red-500 rounded-full hover:bg-red-500 hover:bg-opacity-10 transition-colors">
                Sign Out
              </button>
            </form>
          </div>
        </div>
        
        <div class="md:col-span-2">
          <div class="task-card mb-4">
            <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">Account Information</h5>
            <div class="space-y-4">
              <div>
                <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Name</label>
                <div class="text-gray-800 dark:text-gray-200">{{ user.name }}</div>
              </div>
              
              <div>
                <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Email</label>
                <div class="text-gray-800 dark:text-gray-200">{{ user.email }}</div>
              </div>
              
              <div>
                <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Member Since</label>
                <div class="text-gray-800 dark:text-gray-200">{{ formatDate(user.created_at) }}</div>
              </div>
            </div>
          </div>
          
          <div class="task-card">
            <div class="flex justify-between items-center mb-4">
              <h5 class="text-lg font-medium text-gray-800 dark:text-gray-200">Task Summary</h5>
              <router-link to="/" class="btn-primary text-sm">View All</router-link>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
              <div class="task-card p-4 bg-opacity-50">
                <div class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-1">{{ taskCounts.pending }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
              </div>
              <div class="task-card p-4 bg-opacity-50">
                <div class="text-2xl font-semibold text-[var(--primary)] mb-1">{{ taskCounts.in_progress }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">In Progress</div>
              </div>
              <div class="task-card p-4 bg-opacity-50">
                <div class="text-2xl font-semibold text-green-600 dark:text-green-500 mb-1">{{ taskCounts.completed }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import axios from 'axios';

export default {
  setup() {
    const store = useStore();
    const user = ref({
      name: '',
      email: '',
      photo_url: null,
      created_at: null
    });
    
    const taskCounts = ref({
      pending: 0,
      in_progress: 0,
      completed: 0
    });
    
    const userInitial = computed(() => {
      return user.value.name ? user.value.name.charAt(0).toUpperCase() : 'U';
    });
    
    const fetchUserData = async () => {
      try {
        const response = await axios.get('/api/user');
        user.value = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    };
    
    const fetchTaskCounts = async () => {
      try {
        const response = await axios.get('/api/tasks/counts');
        taskCounts.value = response.data;
      } catch (error) {
        console.error('Error fetching task counts:', error);
        // Fallback to store data if API fails
        const todos = store.state.todos;
        if (todos.length > 0) {
          taskCounts.value = {
            pending: todos.filter(t => t.status === 'pending').length,
            in_progress: todos.filter(t => t.status === 'in_progress').length,
            completed: todos.filter(t => t.status === 'completed').length
          };
        }
      }
    };
    
    const logout = async () => {
      try {
        await axios.post('/logout');
        window.location.href = '/';
      } catch (error) {
        console.error('Error logging out:', error);
      }
    };
    
    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
      });
    };
    
    onMounted(() => {
      fetchUserData();
      fetchTaskCounts();
    });
    
    return {
      user,
      userInitial,
      taskCounts,
      logout,
      formatDate
    };
  }
};
</script> 