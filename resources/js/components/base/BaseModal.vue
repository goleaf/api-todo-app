<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed z-50 inset-0 overflow-y-auto" @click="handleBackdropClick">
      <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>
        
        <!-- Modal content -->
        <div 
          class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
          @click.stop
          :class="sizeClasses"
        >
          <!-- Modal header -->
          <div v-if="title || $slots.header" class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <slot name="header">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ title }}</h3>
            </slot>
            <button 
              @click="$emit('update:modelValue', false)"
              class="text-gray-400 hover:text-gray-500 dark:text-gray-300 dark:hover:text-gray-200 focus:outline-none"
            >
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <!-- Modal body -->
          <div class="px-4 py-3 sm:p-6">
            <slot></slot>
          </div>
          
          <!-- Modal footer -->
          <div v-if="$slots.footer" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6 flex flex-col sm:flex-row-reverse gap-2">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue';

// Props
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl', 'full'].includes(value)
  },
  persistent: {
    type: Boolean,
    default: false
  }
});

// Emits
const emit = defineEmits(['update:modelValue']);

// Computed properties
const sizeClasses = computed(() => {
  return {
    'sm:max-w-sm': props.size === 'sm',
    'sm:max-w-lg': props.size === 'md',
    'sm:max-w-2xl': props.size === 'lg',
    'sm:max-w-4xl': props.size === 'xl',
    'sm:max-w-full sm:m-2': props.size === 'full'
  };
});

// Methods
const handleBackdropClick = () => {
  if (!props.persistent) {
    emit('update:modelValue', false);
  }
};

// Disable body scrolling when modal is open
watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      document.body.classList.add('overflow-hidden');
    } else {
      document.body.classList.remove('overflow-hidden');
    }
  },
  { immediate: true }
);
</script> 