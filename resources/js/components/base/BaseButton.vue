<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center justify-center rounded-md font-medium focus:outline-none transition-all duration-200 ease-in-out',
      sizeClasses,
      variantClasses,
      outlineClasses,
      roundedClasses,
      { 'opacity-50 cursor-not-allowed': disabled || loading },
      { 'shadow-sm hover:shadow-md transform hover:-translate-y-0.5 active:translate-y-0': !outline && variant !== 'default' && !disabled && !loading },
      { 'focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800': !text },
      'relative overflow-hidden',
      className
    ]"
    @click="handleClick"
  >
    <!-- Button Ripple Effect -->
    <span 
      class="absolute inset-0 bg-white dark:bg-gray-100 opacity-0 transition-opacity duration-300 rounded-md pointer-events-none"
      :class="{'opacity-10 animate-ripple': isActive}"
      @animationend="isActive = false"
    ></span>
    
    <!-- Left Icon -->
    <span v-if="$slots.leftIcon && !loading && iconPosition === 'left'" class="mr-2 -ml-1">
      <slot name="leftIcon"></slot>
    </span>
    
    <!-- Loading Spinner -->
    <span v-if="loading" class="mr-2 -ml-1">
      <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </span>
    
    <!-- Button Text -->
    <span>
      <slot>{{ text }}</slot>
    </span>
    
    <!-- Right Icon -->
    <span v-if="$slots.rightIcon && !loading && iconPosition === 'right'" class="ml-2 -mr-1">
      <slot name="rightIcon"></slot>
    </span>
  </button>
</template>

<script setup>
import { computed, ref } from 'vue';

// Props
const props = defineProps({
  type: {
    type: String,
    default: 'button',
    validator: value => ['button', 'submit', 'reset'].includes(value)
  },
  variant: {
    type: String,
    default: 'primary',
    validator: value => ['primary', 'secondary', 'danger', 'warning', 'success', 'info', 'default'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: value => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  text: {
    type: String,
    default: ''
  },
  iconPosition: {
    type: String,
    default: 'left',
    validator: value => ['left', 'right'].includes(value)
  },
  outline: {
    type: Boolean,
    default: false
  },
  textOnly: {
    type: Boolean,
    default: false
  },
  rounded: {
    type: String,
    default: 'md',
    validator: value => ['none', 'sm', 'md', 'lg', 'full'].includes(value)
  },
  className: {
    type: String,
    default: ''
  }
});

// State
const isActive = ref(false);

// Emits
const emit = defineEmits(['click']);

// Click Handler with ripple effect
const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    isActive.value = true;
    emit('click', event);
  }
};

// Computed properties
const sizeClasses = computed(() => {
  switch (props.size) {
    case 'xs': return 'px-2 py-1 text-xs';
    case 'sm': return 'px-2.5 py-1.5 text-xs';
    case 'lg': return 'px-5 py-3 text-base';
    case 'xl': return 'px-6 py-3.5 text-lg';
    default: return 'px-4 py-2 text-sm';
  }
});

const roundedClasses = computed(() => {
  switch (props.rounded) {
    case 'none': return 'rounded-none';
    case 'sm': return 'rounded';
    case 'lg': return 'rounded-lg';
    case 'full': return 'rounded-full';
    default: return 'rounded-md';
  }
});

const outlineClasses = computed(() => {
  if (!props.outline && !props.textOnly) return '';
  
  if (props.textOnly) {
    switch (props.variant) {
      case 'primary':
        return 'bg-transparent text-primary hover:bg-primary-50 focus:ring-primary active:bg-primary-100 dark:hover:bg-primary-900/20 dark:active:bg-primary-900/30';
      case 'secondary':
        return 'bg-transparent text-secondary hover:bg-secondary-50 focus:ring-secondary-500 active:bg-secondary-100 dark:text-secondary-400 dark:hover:bg-secondary-900/30 dark:active:bg-secondary-900/40';
      case 'danger':
        return 'bg-transparent text-danger hover:bg-danger-50 focus:ring-danger active:bg-danger-100 dark:text-danger-400 dark:hover:bg-danger-900/30 dark:active:bg-danger-900/40';
      case 'warning':
        return 'bg-transparent text-warning hover:bg-warning-50 focus:ring-warning active:bg-warning-100 dark:text-warning-400 dark:hover:bg-warning-900/30 dark:active:bg-warning-900/40';
      case 'success':
        return 'bg-transparent text-success hover:bg-success-50 focus:ring-success active:bg-success-100 dark:text-success-400 dark:hover:bg-success-900/30 dark:active:bg-success-900/40';
      case 'info':
        return 'bg-transparent text-info hover:bg-info-50 focus:ring-info active:bg-info-100 dark:text-info-400 dark:hover:bg-info-900/30 dark:active:bg-info-900/40';
      default:
        return 'bg-transparent text-gray-700 hover:bg-gray-50 focus:ring-gray-500 active:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700';
    }
  }
  
  switch (props.variant) {
    case 'primary':
      return 'bg-transparent text-primary hover:bg-primary hover:text-white border-2 border-primary focus:ring-primary-500 active:bg-primary-600 dark:border-primary-400 dark:text-primary-400 dark:hover:bg-primary-600 dark:active:bg-primary-700';
    case 'secondary':
      return 'bg-transparent text-secondary hover:bg-secondary hover:text-white border-2 border-secondary focus:ring-secondary-500 active:bg-secondary-600 dark:text-secondary-400 dark:border-secondary-500 dark:hover:bg-secondary-600 dark:active:bg-secondary-700';
    case 'danger':
      return 'bg-transparent text-danger hover:bg-danger hover:text-white border-2 border-danger focus:ring-danger-500 active:bg-danger-600 dark:text-danger-400 dark:border-danger-500 dark:hover:bg-danger-600 dark:active:bg-danger-700';
    case 'warning':
      return 'bg-transparent text-warning hover:bg-warning hover:text-white border-2 border-warning focus:ring-warning-500 active:bg-warning-600 dark:text-warning-400 dark:border-warning-500 dark:hover:bg-warning-600 dark:active:bg-warning-700';
    case 'success':
      return 'bg-transparent text-success hover:bg-success hover:text-white border-2 border-success focus:ring-success-500 active:bg-success-600 dark:text-success-400 dark:border-success-500 dark:hover:bg-success-600 dark:active:bg-success-700';
    case 'info':
      return 'bg-transparent text-info hover:bg-info hover:text-white border-2 border-info focus:ring-info-500 active:bg-info-600 dark:text-info-400 dark:border-info-500 dark:hover:bg-info-600 dark:active:bg-info-700';
    default:
      return 'bg-transparent text-gray-700 hover:bg-gray-700 hover:text-white border-2 border-gray-300 focus:ring-gray-500 active:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:active:bg-gray-800';
  }
});

const variantClasses = computed(() => {
  if (props.outline || props.textOnly) return '';
  
  switch (props.variant) {
    case 'primary':
      return 'bg-primary hover:bg-primary-600 text-white focus:ring-primary-500 active:bg-primary-700 border border-transparent';
    case 'secondary':
      return 'bg-secondary hover:bg-secondary-600 text-white focus:ring-secondary-500 border border-transparent active:bg-secondary-700';
    case 'danger':
      return 'bg-danger hover:bg-danger-600 text-white focus:ring-danger-500 border border-transparent active:bg-danger-700';
    case 'warning':
      return 'bg-warning hover:bg-warning-600 text-white focus:ring-warning-500 border border-transparent active:bg-warning-700';
    case 'success':
      return 'bg-success hover:bg-success-600 text-white focus:ring-success-500 border border-transparent active:bg-success-700';
    case 'info':
      return 'bg-info hover:bg-info-600 text-white focus:ring-info-500 border border-transparent active:bg-info-700';
    default:
      return 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 focus:ring-gray-500 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white dark:border-gray-600 active:bg-gray-200 dark:active:bg-gray-900';
  }
});
</script>

<style>
@keyframes ripple {
  from {
    opacity: 0.4;
    transform: scale(0);
  }
  to {
    opacity: 0;
    transform: scale(4);
  }
}
.animate-ripple {
  animation: ripple 0.6s linear;
}
</style> 