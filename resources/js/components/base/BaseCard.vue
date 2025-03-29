<template>
  <div 
    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden"
    :class="[
      bordered ? 'border border-gray-200 dark:border-gray-700' : '',
      className
    ]"
  >
    <!-- Card Header -->
    <div 
      v-if="title || $slots.header" 
      class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center"
    >
      <h3 
        v-if="title" 
        class="text-lg font-medium text-gray-900 dark:text-white"
      >
        {{ title }}
      </h3>
      <slot name="header"></slot>
    </div>
    
    <!-- Card Body -->
    <div 
      class="px-4 py-5" 
      :class="[padding]"
    >
      <slot></slot>
    </div>
    
    <!-- Card Footer -->
    <div 
      v-if="$slots.footer" 
      class="px-4 py-3 border-t border-gray-200 dark:border-gray-700"
    >
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
// Props
const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  bordered: {
    type: Boolean,
    default: false
  },
  padding: {
    type: String,
    default: 'p-4'
  },
  className: {
    type: String,
    default: ''
  }
});
</script> 