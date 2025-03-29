<template>
  <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <h1 class="text-9xl font-bold text-primary">404</h1>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mt-4">Page not found</h2>
        <p class="mt-2 text-base text-gray-500 dark:text-gray-400">Sorry, we couldn't find the page you're looking for.</p>
        <div class="mt-6">
          <router-link to="/" class="text-base font-medium text-primary hover:text-primary-dark">
            Go back home
            <span aria-hidden="true"> &rarr;</span>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// No data or methods needed for this simple view
</script> 