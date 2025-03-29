<template>
  <div class="task-card max-w-md mx-auto">
    <h2 class="text-2xl font-semibold mb-8 text-center text-gray-800 dark:text-gray-200">Welcome Back</h2>
    
    <div v-if="error" class="bg-red-100 dark:bg-red-900 dark:bg-opacity-30 border border-red-400 dark:border-red-500 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-6">
      {{ error }}
    </div>
    
    <form @submit.prevent="handleSubmit" class="space-y-6">
      <div>
        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2" for="email">
          Email
        </label>
        <input 
          v-model="form.email" 
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" 
          id="email" 
          type="email" 
          placeholder="Enter your email"
          required
        >
      </div>
      
      <div>
        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-2" for="password">
          Password
        </label>
        <input 
          v-model="form.password" 
          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" 
          id="password" 
          type="password" 
          placeholder="Enter your password"
          required
        >
      </div>
      
      <div>
        <button 
          :disabled="loading" 
          class="btn-primary w-full py-3"
          type="submit"
        >
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </div>
      
      <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
        <router-link to="/register" class="text-[var(--primary)] hover:text-opacity-80 transition-colors">
          Don't have an account? Register
        </router-link>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const store = useStore();
    const router = useRouter();
    
    const form = reactive({
      email: '',
      password: ''
    });
    
    const loading = ref(false);
    const error = ref('');
    
    const handleSubmit = async () => {
      try {
        loading.value = true;
        error.value = '';
        
        await store.dispatch('login', form);
        router.push('/');
      } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
          error.value = err.response.data.message;
        } else if (err.response && err.response.data && err.response.data.errors) {
          const errorMessages = Object.values(err.response.data.errors).flat();
          error.value = errorMessages.join(' ');
        } else {
          error.value = 'An error occurred during login. Please try again.';
        }
      } finally {
        loading.value = false;
      }
    };
    
    return {
      form,
      loading,
      error,
      handleSubmit
    };
  }
};
</script> 