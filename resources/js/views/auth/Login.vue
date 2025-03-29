<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="flex justify-center mb-6">
        <router-link to="/">
          <img 
            src="/logo.svg" 
            alt="Todo App Logo" 
            class="h-16 w-auto"
          />
        </router-link>
      </div>
      <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-white">
        Sign in to your account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
        Or
        <router-link to="/register" class="font-medium text-primary hover:text-primary-dark transition-colors">
          create a new account
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-md sm:rounded-lg sm:px-10 border border-gray-200 dark:border-gray-700">
        <!-- Error alert -->
        <div v-if="formError" class="mb-6 alert alert-danger">
          <div class="flex">
            <div class="flex-shrink-0">
              <FontAwesomeIcon 
                icon="exclamation-circle" 
                class="h-5 w-5 text-red-400"
                aria-hidden="true"
              />
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700 dark:text-red-400">
                {{ formError }}
              </p>
            </div>
          </div>
        </div>

        <form @submit.prevent="login" class="space-y-6">
          <div class="form-group">
            <BaseInput
              v-model="form.email"
              type="email"
              label="Email address"
              :error="errors.email"
              required
              autocomplete="email"
              placeholder="you@example.com"
            >
              <template #prefix>
                <FontAwesomeIcon icon="envelope" class="text-gray-400" />
              </template>
            </BaseInput>
          </div>

          <div class="form-group">
            <BaseInput
              v-model="form.password"
              type="password"
              label="Password"
              :error="errors.password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
            >
              <template #prefix>
                <FontAwesomeIcon icon="lock" class="text-gray-400" />
              </template>
            </BaseInput>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember_me"
                v-model="form.remember"
                type="checkbox"
                class="form-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded"
              />
              <label for="remember_me" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                Remember me
              </label>
            </div>

            <div class="text-sm">
              <router-link to="/forgot-password" class="font-medium text-primary hover:text-primary-dark transition-colors">
                Forgot your password?
              </router-link>
            </div>
          </div>

          <div>
            <BaseButton
              type="submit"
              variant="primary"
              size="lg"
              class="w-full"
              :loading="isLoading"
              :disabled="isLoading"
            >
              <FontAwesomeIcon icon="sign-in-alt" class="mr-2" />
              Sign in
            </BaseButton>
          </div>
          
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                Or continue with
              </span>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <BaseButton
              type="button"
              variant="default"
              class="w-full"
              @click="handleOAuthClick('github')"
            >
              <FontAwesomeIcon :icon="['fab', 'github']" class="mr-2" />
              GitHub
            </BaseButton>
            <BaseButton
              type="button"
              variant="default"
              class="w-full"
              @click="handleOAuthClick('google')"
            >
              <FontAwesomeIcon :icon="['fab', 'google']" class="mr-2" />
              Google
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/app';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const router = useRouter();
const authStore = useAuthStore();
const appStore = useAppStore();
const { isLoading } = storeToRefs(appStore);

// Form data
const form = reactive({
  email: '',
  password: '',
  remember: false
});

// Form errors
const errors = reactive({
  email: '',
  password: ''
});

// General form error
const formError = ref('');

// Email validation
const isValidEmail = (email) => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

// Clear all errors
const clearErrors = () => {
  Object.keys(errors).forEach(key => errors[key] = '');
  formError.value = '';
};

// Handle OAuth click
const handleOAuthClick = (provider) => {
  window.location.href = `/auth/${provider}/redirect`;
};

// Handle login
const login = async () => {
  // Clear previous errors
  clearErrors();
  
  // Validate form
  let isValid = true;
  
  if (!form.email) {
    errors.email = 'Email is required';
    isValid = false;
  } else if (!isValidEmail(form.email)) {
    errors.email = 'Email is invalid';
    isValid = false;
  }
  
  if (!form.password) {
    errors.password = 'Password is required';
    isValid = false;
  }
  
  if (!isValid) return;
  
  // Submit form
  try {
    await authStore.login({
      email: form.email,
      password: form.password,
      remember: form.remember
    });
    
    // Redirect to dashboard on success
    router.push({ name: 'dashboard' });
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation errors
      const responseErrors = error.response.data.errors;
      
      if (responseErrors.email) {
        errors.email = responseErrors.email[0];
      }
      
      if (responseErrors.password) {
        errors.password = responseErrors.password[0];
      }
    } else if (error.response?.status === 401) {
      // Authentication error
      formError.value = error.response.data.message || 'Invalid credentials';
    } else {
      // Show general error
      formError.value = authStore.authError || 'An error occurred during login. Please try again.';
    }
  }
};
</script> 