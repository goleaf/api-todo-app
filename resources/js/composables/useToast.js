import { ref, reactive } from 'vue';

// Create a unique ID for each toast
let id = 0;

// Create a reactive toast array that will be shared across all instances
const toasts = reactive([]);

/**
 * Toast composable for showing notifications
 */
export function useToast() {
  const defaultDuration = 5000;

  // Private function to remove a toast after its duration ends
  const removeToast = (toastId) => {
    const index = toasts.findIndex(toast => toast.id === toastId);
    if (index !== -1) {
      toasts.splice(index, 1);
    }
  };

  // Add a new toast
  const addToast = (message, type, options = {}) => {
    const toastId = id++;
    const toast = {
      id: toastId,
      message,
      type,
      duration: options.duration || defaultDuration,
      icon: getIconForType(type),
      ...options
    };

    // Add the toast to the array
    toasts.unshift(toast);

    // Set up automatic removal (unless sticky is true)
    if (!options.sticky) {
      setTimeout(() => {
        removeToast(toastId);
      }, toast.duration);
    }

    return toastId;
  };

  // Helper function to get the icon based on toast type
  const getIconForType = (type) => {
    switch (type) {
      case 'success': return 'check-circle';
      case 'error': return 'exclamation-circle';
      case 'warning': return 'exclamation-triangle';
      case 'info': return 'info-circle';
      default: return 'bell';
    }
  };

  // Create shorthand methods for different toast types
  const toast = {
    success: (message, options = {}) => addToast(message, 'success', options),
    error: (message, options = {}) => addToast(message, 'error', options),
    warning: (message, options = {}) => addToast(message, 'warning', options),
    info: (message, options = {}) => addToast(message, 'info', options),
    remove: removeToast,
    clear: () => {
      toasts.splice(0, toasts.length);
    }
  };

  return {
    toasts,
    toast
  };
}

// Create a ToastContainer component
export const ToastContainer = {
  name: 'ToastContainer',
  setup() {
    const { toasts, toast } = useToast();

    return {
      toasts,
      removeToast: toast.remove,
    };
  },
  template: `
    <div class="fixed right-0 top-0 z-50 p-4 space-y-4 w-full sm:max-w-xs">
      <transition-group name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="{
            'bg-green-50 text-green-800 dark:bg-green-900 dark:bg-opacity-80 dark:text-green-100': toast.type === 'success',
            'bg-red-50 text-red-800 dark:bg-red-900 dark:bg-opacity-80 dark:text-red-100': toast.type === 'error',
            'bg-yellow-50 text-yellow-800 dark:bg-yellow-900 dark:bg-opacity-80 dark:text-yellow-100': toast.type === 'warning',
            'bg-blue-50 text-blue-800 dark:bg-blue-900 dark:bg-opacity-80 dark:text-blue-100': toast.type === 'info',
          }"
          class="flex items-center p-4 rounded-lg shadow-md backdrop-blur-sm border"
          :class="{
            'border-green-200 dark:border-green-800': toast.type === 'success',
            'border-red-200 dark:border-red-800': toast.type === 'error',
            'border-yellow-200 dark:border-yellow-800': toast.type === 'warning',
            'border-blue-200 dark:border-blue-800': toast.type === 'info',
          }"
        >
          <div 
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
            :class="{
              'text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200': toast.type === 'success',
              'text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200': toast.type === 'error',
              'text-yellow-500 bg-yellow-100 dark:bg-yellow-800 dark:text-yellow-200': toast.type === 'warning',
              'text-blue-500 bg-blue-100 dark:bg-blue-800 dark:text-blue-200': toast.type === 'info',
            }"
          >
            <font-awesome-icon :icon="toast.icon" class="h-5 w-5" aria-hidden="true" />
          </div>
          <div class="ml-3 text-sm font-normal">{{ toast.message }}</div>
          <button 
            type="button" 
            @click="removeToast(toast.id)"
            class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 focus:outline-none focus:ring-2 focus:ring-gray-300"
            :class="{
              'hover:bg-green-200 dark:hover:bg-green-800 text-green-500 dark:text-green-200': toast.type === 'success',
              'hover:bg-red-200 dark:hover:bg-red-800 text-red-500 dark:text-red-200': toast.type === 'error',
              'hover:bg-yellow-200 dark:hover:bg-yellow-800 text-yellow-500 dark:text-yellow-200': toast.type === 'warning',
              'hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-500 dark:text-blue-200': toast.type === 'info',
            }"
          >
            <span class="sr-only">Close</span>
            <font-awesome-icon icon="times" class="h-3 w-3" aria-hidden="true" />
          </button>
        </div>
      </transition-group>
    </div>
  `
};

export default useToast; 