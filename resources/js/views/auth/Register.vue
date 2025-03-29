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
        Create a new account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
        Or
        <router-link to="/login" class="font-medium text-primary hover:text-primary-dark transition-colors">
          sign in to your existing account
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

        <form @submit.prevent="register" class="space-y-6">
          <div class="form-group">
            <BaseInput
              v-model="form.name"
              type="text"
              label="Full Name"
              :error="errors.name"
              required
              autocomplete="name"
              placeholder="John Doe"
              help-text="Enter your full name as it will appear on your account"
            >
              <template #prefix>
                <FontAwesomeIcon icon="user" class="text-gray-400" />
              </template>
            </BaseInput>
          </div>

          <div class="form-group">
            <BaseInput
              v-model="form.email"
              type="email"
              label="Email address"
              :error="errors.email"
              required
              autocomplete="email"
              placeholder="you@example.com"
              help-text="We'll never share your email with anyone else"
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
              autocomplete="new-password"
              placeholder="••••••••"
              help-text="Must be at least 8 characters long"
            >
              <template #prefix>
                <FontAwesomeIcon icon="lock" class="text-gray-400" />
              </template>
            </BaseInput>
          </div>

          <div class="form-group">
            <BaseInput
              v-model="form.password_confirmation"
              type="password"
              label="Confirm password"
              :error="errors.password_confirmation"
              required
              autocomplete="new-password"
              placeholder="••••••••"
            >
              <template #prefix>
                <FontAwesomeIcon icon="lock" class="text-gray-400" />
              </template>
            </BaseInput>
          </div>

          <div class="flex items-center">
            <input 
              id="terms" 
              name="terms" 
              type="checkbox" 
              v-model="form.terms"
              class="form-checkbox"
              required
            />
            <label for="terms" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
              I agree to the 
              <a href="#" class="text-primary hover:text-primary-dark">Terms of Service</a>
               and 
              <a href="#" class="text-primary hover:text-primary-dark">Privacy Policy</a>
            </label>
          </div>
          <div v-if="errors.terms" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ errors.terms }}
          </div>

          <div>
            <BaseButton
              type="submit"
              variant="primary"
              size="lg"
              class="w-full bg-primary text-white hover:bg-primary-dark focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200"
              :loading="isLoading"
              :disabled="isLoading"
            >
              <FontAwesomeIcon icon="user-plus" class="mr-2" />
              Create account
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
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
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false
});

// Form errors
const errors = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: ''
});

// General form error
const formError = ref('');

// Email validation
const isValidEmail = (email) => {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

// Password strength
const passwordStrength = computed(() => {
  const password = form.password;
  
  if (!password) return { text: 'None', score: 0 };
  if (password.length < 8) return { text: 'Weak', score: 1 };
  
  let score = 0;
  
  // Length check
  if (password.length >= 8) score += 1;
  if (password.length >= 12) score += 1;
  
  // Complexity checks
  if (/[A-Z]/.test(password)) score += 1; // Has uppercase
  if (/[a-z]/.test(password)) score += 1; // Has lowercase
  if (/[0-9]/.test(password)) score += 1; // Has number
  if (/[^A-Za-z0-9]/.test(password)) score += 1; // Has special chars
  
  // Score interpretation
  if (score < 3) return { text: 'Weak', score: 1 };
  if (score < 5) return { text: 'Medium', score: 2 };
  return { text: 'Strong', score: 3 };
});

// Clear all errors
const clearErrors = () => {
  Object.keys(errors).forEach(key => errors[key] = '');
  formError.value = '';
};

// Register function
const register = async () => {
  // Clear previous errors
  clearErrors();
  
  // Submit directly to the server for validation
  try {
    appStore.setLoading(true);
    const response = await authStore.register(form);
    
    // If there are validation errors returned from the backend
    if (response && response.validationErrors) {
      // Map backend validation errors to form errors
      Object.keys(response.validationErrors).forEach(key => {
        if (errors[key] !== undefined) {
          errors[key] = response.validationErrors[key][0];
        } else {
          // If the error key doesn't match our form fields
          formError.value = response.validationErrors[key][0];
        }
      });
      return;
    }
    
    // Registration successful, redirect to dashboard
    router.push('/dashboard');
  } catch (error) {
    // Handle specific error cases
    if (error.response && error.response.status === 422) {
      // Validation errors
      const validationErrors = error.response.data.errors;
      if (validationErrors) {
        Object.keys(validationErrors).forEach(key => {
          if (errors[key] !== undefined) {
            errors[key] = validationErrors[key][0];
          } else {
            // For errors that don't map to specific fields
            formError.value = validationErrors[key][0];
          }
        });
      }
    } else {
      // General error
      formError.value = authStore.authError || 'An error occurred during registration. Please try again.';
    }
  } finally {
    appStore.setLoading(false);
  }
};
</script> 