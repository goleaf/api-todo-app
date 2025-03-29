<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div class="relative rounded-md shadow-sm">
      <!-- Prefix slot -->
      <div 
        v-if="$slots.prefix" 
        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 dark:text-gray-400"
      >
        <slot name="prefix"></slot>
      </div>
      
      <!-- Text, Email, Password, Number inputs -->
      <input 
        v-if="isStandardInput"
        :id="id"
        :name="name"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        @focus="isFocused = true"
        @blur="isFocused = false"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :autocomplete="autocomplete"
        :class="[
          'form-input block w-full sm:text-sm rounded-md transition-all duration-150 ease-in-out',
          $slots.prefix ? 'pl-10' : '',
          $slots.suffix ? 'pr-10' : '',
          error 
            ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-700 dark:text-red-100 dark:placeholder-red-300' 
            : 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary hover:border-gray-400 dark:hover:border-gray-500',
          disabled ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : '',
          isFocused ? 'shadow-sm ring-2 ring-primary ring-opacity-40' : ''
        ]"
      />
      
      <!-- Textarea -->
      <textarea 
        v-else-if="type === 'textarea'"
        :id="id"
        :name="name"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        @focus="isFocused = true"
        @blur="isFocused = false"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="[
          'form-textarea block w-full sm:text-sm rounded-md transition-all duration-150 ease-in-out',
          error 
            ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-700 dark:text-red-100 dark:placeholder-red-300' 
            : 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary hover:border-gray-400 dark:hover:border-gray-500',
          disabled ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : '',
          isFocused ? 'shadow-sm ring-2 ring-primary ring-opacity-40' : ''
        ]"
      ></textarea>
      
      <!-- Select -->
      <div v-else-if="type === 'select'" class="relative">
        <select 
          :id="id"
          :name="name"
          :value="modelValue"
          @change="$emit('update:modelValue', $event.target.value)"
          @focus="isFocused = true"
          @blur="isFocused = false"
          :required="required"
          :disabled="disabled"
          :class="[
            'form-select block w-full sm:text-sm rounded-md appearance-none pr-10 transition-all duration-150 ease-in-out',
            $slots.prefix ? 'pl-10' : '',
            error 
              ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-700 dark:text-red-100 dark:placeholder-red-300' 
              : 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary hover:border-gray-400 dark:hover:border-gray-500',
            disabled ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : '',
            isFocused ? 'shadow-sm ring-2 ring-primary ring-opacity-40' : ''
          ]"
        >
          <option v-if="placeholder" value="">{{ placeholder }}</option>
          <slot></slot>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700 dark:text-gray-300">
          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </div>
      </div>
      
      <!-- Date -->
      <input 
        v-else-if="type === 'date'"
        :id="id"
        :name="name"
        type="date"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        @focus="isFocused = true"
        @blur="isFocused = false"
        :required="required"
        :disabled="disabled"
        :min="min"
        :max="max"
        :class="[
          'form-input block w-full sm:text-sm rounded-md transition-all duration-150 ease-in-out',
          $slots.prefix ? 'pl-10' : '',
          $slots.suffix ? 'pr-10' : '',
          error 
            ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 dark:border-red-700 dark:text-red-100 dark:placeholder-red-300' 
            : 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary hover:border-gray-400 dark:hover:border-gray-500',
          disabled ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-gray-800' : '',
          isFocused ? 'shadow-sm ring-2 ring-primary ring-opacity-40' : ''
        ]"
      />
      
      <!-- Checkbox -->
      <div v-else-if="type === 'checkbox'" class="flex items-center">
        <input 
          :id="id"
          :name="name"
          type="checkbox"
          :checked="modelValue"
          @change="$emit('update:modelValue', $event.target.checked)"
          @focus="isFocused = true"
          @blur="isFocused = false"
          :required="required"
          :disabled="disabled"
          class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded transition-all duration-150 hover:border-primary"
          :class="[
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            error ? 'border-red-300' : 'border-gray-300',
            isFocused ? 'ring-2 ring-primary ring-opacity-50' : ''
          ]"
        />
        <label v-if="label" :for="id" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
          {{ label }}
        </label>
      </div>

      <!-- Suffix slot -->
      <div 
        v-if="$slots.suffix" 
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400"
      >
        <slot name="suffix"></slot>
      </div>
      
      <!-- Focus Border Animation -->
      <div 
        v-if="!error && !disabled" 
        class="absolute bottom-0 left-0 h-0.5 bg-primary transform scale-x-0 transition-transform origin-left rounded-b-md"
        :class="{ 'scale-x-100': isFocused }"
        style="width: 100%"
      ></div>
    </div>
    
    <!-- Help Text -->
    <p v-if="helpText && !error" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
      {{ helpText }}
    </p>
    
    <!-- Error Message -->
    <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-500 flex items-center">
      <span class="mr-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </span>
      {{ error }}
    </p>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

// Props
const props = defineProps({
  // Base props
  modelValue: {
    type: [String, Number, Boolean],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text',
    validator: value => [
      'text', 'email', 'password', 'number', 'tel', 
      'textarea', 'select', 'date', 'checkbox', 'search'
    ].includes(value)
  },
  id: {
    type: String,
    default: () => `input-${Math.random().toString(36).substr(2, 9)}`
  },
  name: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  helpText: {
    type: String,
    default: ''
  },
  autocomplete: {
    type: String,
    default: ''
  },
  
  // Textarea props
  rows: {
    type: Number,
    default: 4
  },
  
  // Date props
  min: {
    type: String,
    default: ''
  },
  max: {
    type: String,
    default: ''
  }
});

// State
const isFocused = ref(false);

// Emits
defineEmits(['update:modelValue']);

// Computed properties
const isStandardInput = computed(() => {
  return ['text', 'email', 'password', 'number', 'tel', 'search'].includes(props.type);
});
</script> 