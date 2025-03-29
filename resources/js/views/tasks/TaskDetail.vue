<template>
  <div class="container mx-auto max-w-3xl">
    <div class="pb-5 border-b border-gray-200 dark:border-gray-700 mb-5 flex justify-between items-center">
      <h1 class="text-3xl font-bold leading-tight text-gray-900 dark:text-white">
        {{ isNewTask ? 'Create Task' : 'Edit Task' }}
      </h1>
      <div v-if="!isNewTask">
        <BaseButton 
          variant="danger" 
          @click="confirmDelete = true" 
          class="mr-2"
        >
          Delete
        </BaseButton>
      </div>
    </div>

    <BaseCard>
      <form @submit.prevent="saveTask">
        <div class="space-y-6">
          <!-- Title -->
          <div>
            <BaseInput
              v-model="task.title"
              label="Task Title"
              :error="errors.title"
              required
              placeholder="Enter task title"
            />
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Description
            </label>
            <textarea
              v-model="task.description"
              rows="4"
              class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
              placeholder="Task description (optional)"
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Category -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Category
              </label>
              <select
                v-model="task.category_id"
                class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
              >
                <option value="">No category</option>
                <option 
                  v-for="category in categories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Due Date -->
            <div>
              <BaseInput
                v-model="task.due_date"
                type="date"
                label="Due Date"
                :error="errors.due_date"
                :helpText="task.due_date ? formatDate(task.due_date) : 'Optional'"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Priority -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Priority
              </label>
              <select
                v-model="task.priority"
                class="block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
              >
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Status
              </label>
              <div class="flex items-center mt-2">
                <input
                  id="completed"
                  v-model="task.completed"
                  type="checkbox"
                  class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                />
                <label for="completed" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                  Mark as completed
                </label>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <BaseButton 
              type="button" 
              variant="default" 
              @click="$router.push('/tasks')"
            >
              Cancel
            </BaseButton>
            <BaseButton 
              type="submit" 
              variant="primary"
              :loading="isLoading"
            >
              {{ isNewTask ? 'Create Task' : 'Update Task' }}
            </BaseButton>
          </div>
        </div>
      </form>
    </BaseCard>

    <!-- Delete Confirmation Modal -->
    <BaseModal
      v-model="confirmDelete"
      title="Confirm Delete"
    >
      <p class="text-gray-700 dark:text-gray-300">
        Are you sure you want to delete this task? This action cannot be undone.
      </p>
      
      <template #footer>
        <BaseButton 
          @click="confirmDelete = false" 
          variant="default"
        >
          Cancel
        </BaseButton>
        <BaseButton 
          @click="deleteTask" 
          variant="danger"
          :loading="isLoading"
        >
          Delete
        </BaseButton>
      </template>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useTaskStore } from '@/stores/tasks';
import { useAppStore } from '@/stores/app';
import { format } from 'date-fns';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import BaseCard from '@/components/base/BaseCard.vue';
import BaseModal from '@/components/base/BaseModal.vue';

const props = defineProps({
  id: {
    type: [String, Number],
    default: null
  }
});

const router = useRouter();
const route = useRoute();
const taskStore = useTaskStore();
const appStore = useAppStore();
const { isLoading } = storeToRefs(appStore);

// Task data
const task = reactive({
  title: '',
  description: '',
  category_id: '',
  due_date: '',
  priority: 'medium',
  completed: false
});

// Form errors
const errors = reactive({
  title: '',
  description: '',
  category_id: '',
  due_date: ''
});

// Delete confirmation
const confirmDelete = ref(false);

// Categories
const categories = ref([]);

// Computed properties
const isNewTask = computed(() => !props.id);
const taskId = computed(() => props.id || route.params.id);

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return '';
  return format(new Date(dateString), 'MMMM d, yyyy');
};

// Fetch task data
const fetchTask = async () => {
  if (isNewTask.value) return;
  
  try {
    const data = await taskStore.fetchTask(taskId.value);
    
    // Populate form with task data
    task.title = data.title;
    task.description = data.description || '';
    task.category_id = data.category_id || '';
    task.due_date = data.due_date || '';
    task.priority = data.priority || 'medium';
    task.completed = data.completed || false;
  } catch (error) {
    console.error('Error fetching task:', error);
    router.push('/tasks');
  }
};

// Fetch categories
const fetchCategories = async () => {
  try {
    categories.value = await taskStore.fetchCategories();
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

// Save task
const saveTask = async () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => errors[key] = '');
  
  // Validate form
  let isValid = true;
  
  if (!task.title) {
    errors.title = 'Title is required';
    isValid = false;
  }
  
  if (!isValid) return;
  
  try {
    if (isNewTask.value) {
      await taskStore.createTask(task);
    } else {
      await taskStore.updateTask(taskId.value, task);
    }
    
    // Redirect to tasks list
    router.push('/tasks');
  } catch (error) {
    if (error.response?.status === 422) {
      // Validation errors
      const responseErrors = error.response.data.errors;
      
      Object.keys(responseErrors).forEach(key => {
        if (key in errors) {
          errors[key] = responseErrors[key][0];
        }
      });
    }
  }
};

// Delete task
const deleteTask = async () => {
  try {
    await taskStore.deleteTask(taskId.value);
    router.push('/tasks');
  } catch (error) {
    console.error('Error deleting task:', error);
  }
};

// Lifecycle hooks
onMounted(async () => {
  await Promise.all([
    fetchTask(),
    fetchCategories()
  ]);
});
</script> 