<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
      <!-- Logo -->
      <div class="flex justify-center mb-8">
        <img src="/logo.svg" alt="Logo" class="h-16 w-16" />
      </div>
      
      <!-- Heading -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Create your account</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Or
          <a :href="route('login')" class="text-primary hover:text-primary-dark font-medium">
            sign in to your existing account
          </a>
        </p>
      </div>
      
      <!-- Alert (for errors) -->
      <div v-if="Object.keys(form.errors).length" 
        class="rounded-md bg-red-50 dark:bg-red-900 dark:bg-opacity-20 p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <FontAwesomeIcon icon="exclamation-circle" class="h-5 w-5 text-red-400" />
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">
              There were errors with your submission
            </h3>
            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
              <ul class="list-disc pl-5 space-y-1">
                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="mt-8 space-y-6">
        <div class="space-y-4">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Full Name
            </label>
            <BaseInput
              id="name"
              v-model="form.name"
              type="text"
              autocomplete="name"
              required
              placeholder="John Doe"
              :error="form.errors.name"
              @focus="form.clearErrors('name')"
              class="mt-1"
            >
              <template #prefix>
                <FontAwesomeIcon icon="user" class="h-5 w-5 text-gray-400" />
              </template>
            </BaseInput>
          </div>
          
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email Address
            </label>
            <BaseInput
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
              required
              placeholder="you@example.com"
              :error="form.errors.email"
              @focus="form.clearErrors('email')"
              class="mt-1"
            >
              <template #prefix>
                <FontAwesomeIcon icon="envelope" class="h-5 w-5 text-gray-400" />
              </template>
            </BaseInput>
          </div>
          
          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Password
            </label>
            <BaseInput
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
              required
              placeholder="••••••••"
              :error="form.errors.password"
              @focus="form.clearErrors('password')"
              class="mt-1"
            >
              <template #prefix>
                <FontAwesomeIcon icon="lock" class="h-5 w-5 text-gray-400" />
              </template>
              <template #suffix>
                <button type="button" @click="togglePassword" class="focus:outline-none">
                  <FontAwesomeIcon 
                    :icon="showPassword ? 'eye-slash' : 'eye'" 
                    class="h-5 w-5 text-gray-400 hover:text-gray-500" 
                  />
                </button>
              </template>
            </BaseInput>
          </div>
          
          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Confirm Password
            </label>
            <BaseInput
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="new-password"
              required
              placeholder="••••••••"
              :error="form.errors.password_confirmation"
              @focus="form.clearErrors('password_confirmation')"
              class="mt-1"
            >
              <template #prefix>
                <FontAwesomeIcon icon="lock" class="h-5 w-5 text-gray-400" />
              </template>
            </BaseInput>
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-center">
          <input
            id="terms"
            name="terms"
            type="checkbox"
            v-model="form.terms"
            required
            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
            @focus="form.clearErrors('terms')"
          />
          <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
            I agree to the 
            <a href="#" class="text-primary hover:text-primary-dark">Terms of Service</a>
            and
            <a href="#" class="text-primary hover:text-primary-dark">Privacy Policy</a>
          </label>
        </div>
        <div v-if="form.errors.terms" class="mt-1 text-sm text-red-600 dark:text-red-500">
          {{ form.errors.terms }}
        </div>

        <div>
          <BaseButton
            type="submit"
            variant="primary"
            class="w-full"
            :loading="form.processing"
            :disabled="form.processing"
          >
            <template #leftIcon v-if="!form.processing">
              <FontAwesomeIcon icon="user-plus" class="h-5 w-5" />
            </template>
            {{ form.processing ? 'Creating account...' : 'Create account' }}
          </BaseButton>
        </div>
      </form>
      
      <!-- Social registration options -->
      <div class="mt-8">
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400">
              Or continue with
            </span>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
          <div>
            <a
              href="#"
              class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <FontAwesomeIcon icon="fab,google" class="h-5 w-5 text-red-500 mr-2" />
              Google
            </a>
          </div>

          <div>
            <a
              href="#"
              class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <FontAwesomeIcon icon="fab,github" class="h-5 w-5 text-gray-900 dark:text-white mr-2" />
              GitHub
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Password visibility toggle
const showPassword = ref(false);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

// Form handling
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  terms: false,
  device_name: navigator.userAgent,
  clearErrors(field) {
    if (field) {
      delete this.errors[field];
    } else {
      this.errors = {};
    }
  }
});

// Submit form
const submit = () => {
  form.post(route('register'), {
    onError: (errors) => {
      // If we get a general error, store it as a special _error property
      if (errors.message) {
        form.errors._error = errors.message;
      }
    },
    preserveScroll: true
  });
};
</script> 