<template>
  <div class="task-list">
    <!-- Task filters -->
    <div class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">My Tasks</h3>
        <button class="btn btn-primary" @click="openTaskModal(null)">
          <i class="bi bi-plus-lg me-1"></i> Add New Task
        </button>
      </div>
      
      <!-- Search bar -->
      <div class="search-container mb-3">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input 
            type="text" 
            class="form-control border-start-0 ps-0" 
            placeholder="Search tasks by title or description..." 
            v-model="searchQuery"
            @input="debounceSearch"
          >
          <button 
            v-if="searchQuery" 
            class="input-group-text bg-white border-start-0" 
            @click="clearSearch"
          >
            <i class="bi bi-x-circle text-muted"></i>
          </button>
        </div>
      </div>
      
      <div class="btn-group w-100">
        <button 
          class="btn" 
          :class="[filterStatus === 'all' ? 'btn-primary' : 'btn-outline-primary']"
          @click="filterStatus = 'all'"
        >
          All
        </button>
        <button 
          class="btn" 
          :class="[filterStatus === 'pending' ? 'btn-warning' : 'btn-outline-warning']"
          @click="filterStatus = 'pending'"
        >
          Pending
        </button>
        <button 
          class="btn" 
          :class="[filterStatus === 'in_progress' ? 'btn-primary' : 'btn-outline-primary']"
          @click="filterStatus = 'in_progress'"
        >
          In Progress
        </button>
        <button 
          class="btn" 
          :class="[filterStatus === 'completed' ? 'btn-success' : 'btn-outline-success']"
          @click="filterStatus = 'completed'"
        >
          Completed
        </button>
      </div>
    </div>
    
    <!-- Tasks -->
    <div v-if="isSearching" class="text-center py-3">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2 text-muted">Searching tasks...</p>
    </div>
    <div v-else-if="filteredTasks.length > 0">
      <Task 
        v-for="task in filteredTasks" 
        :key="task.id" 
        :task="task" 
        @edit="openTaskModal"
        @delete="confirmDeleteTask"
        @toggle-complete="toggleTaskComplete"
      />
    </div>
    <div v-else class="text-center py-5 bg-light rounded">
      <div class="text-muted">
        <i class="bi bi-clipboard2-x fs-1 mb-3"></i>
        <p class="mb-0">No tasks found</p>
        <p class="small" v-if="searchQuery">No results matching "{{ searchQuery }}"</p>
        <p class="small" v-else-if="filterStatus !== 'all'">Try changing your filter or create a new task.</p>
        <p class="small" v-else>Click "Add New Task" to create your first task.</p>
      </div>
    </div>
    
    <!-- Task Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="taskModalLabel">{{ editingTask.id ? 'Edit Task' : 'New Task' }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveTask">
                <div class="mb-3">
                  <label for="taskTitle" class="form-label">Title</label>
                  <input type="text" class="form-control" id="taskTitle" v-model="editingTask.title" required>
                </div>
                
                <div class="mb-3">
                  <label for="taskDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="taskDescription" rows="3" v-model="editingTask.description"></textarea>
                </div>
                
                <div class="mb-3">
                  <label for="taskStatus" class="form-label">Status</label>
                  <select class="form-select" id="taskStatus" v-model="editingTask.status">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                  </select>
                </div>
                
                <div class="mb-3">
                  <label for="taskPriority" class="form-label">Priority</label>
                  <select class="form-select" id="taskPriority" v-model="editingTask.priority">
                    <option value="0">Low</option>
                    <option value="1">Medium</option>
                    <option value="2">High</option>
                  </select>
                </div>
                
                <div class="mb-3">
                  <label for="taskDueDate" class="form-label">Due Date</label>
                  <input type="date" class="form-control" id="taskDueDate" v-model="editingTask.due_date">
                </div>
                
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Task</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this task?</p>
            <p class="fw-bold">{{ taskToDelete?.title }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" @click="deleteTask">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Task from './Task.vue';
import axios from 'axios';

export default {
  name: 'TaskList',
  components: {
    Task
  },
  data() {
    return {
      tasks: [],
      searchResults: null,
      searchQuery: '',
      searchTimeout: null,
      isSearching: false,
      filterStatus: 'all',
      editingTask: {
        title: '',
        description: '',
        status: 'pending',
        priority: 0,
        due_date: null
      },
      taskToDelete: null,
      taskModal: null,
      deleteModal: null,
      loading: false,
      error: null
    };
  },
  computed: {
    filteredTasks() {
      // If we have search results, use those instead of the full task list
      const tasksToFilter = this.searchResults !== null ? this.searchResults : this.tasks;
      
      // First sort by priority (high to low)
      const sortedTasks = [...tasksToFilter].sort((a, b) => b.priority - a.priority);
      
      // Then filter by status if needed
      if (this.filterStatus === 'all') {
        return sortedTasks;
      }
      
      return sortedTasks.filter(task => task.status === this.filterStatus);
    }
  },
  mounted() {
    this.fetchTasks();
    // Initialize modals after DOM is fully loaded
    this.$nextTick(() => {
      this.initializeModals();
    });
  },
  methods: {
    initializeModals() {
      // Initialize modals manually
      const taskModalEl = document.getElementById('taskModal');
      const deleteModalEl = document.getElementById('deleteTaskModal');
      
      if (taskModalEl) {
        this.taskModal = {
          show: () => {
            taskModalEl.classList.add('show');
            taskModalEl.style.display = 'block';
            document.body.classList.add('modal-open');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
          },
          hide: () => {
            taskModalEl.classList.remove('show');
            taskModalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
              backdrop.parentNode.removeChild(backdrop);
            }
          }
        };
      }
      
      if (deleteModalEl) {
        this.deleteModal = {
          show: () => {
            deleteModalEl.classList.add('show');
            deleteModalEl.style.display = 'block';
            document.body.classList.add('modal-open');
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
          },
          hide: () => {
            deleteModalEl.classList.remove('show');
            deleteModalEl.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
              backdrop.parentNode.removeChild(backdrop);
            }
          }
        };
      }
    },
    async fetchTasks() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/tasks');
        this.tasks = response.data;
      } catch (error) {
        this.error = 'Failed to load tasks. Please try again.';
        console.error('Error loading tasks:', error);
      } finally {
        this.loading = false;
      }
    },
    debounceSearch() {
      // Cancel previous timeout
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }
      
      // If search query is empty, reset search results and return
      if (!this.searchQuery.trim()) {
        this.searchResults = null;
        this.isSearching = false;
        return;
      }
      
      // Set a new timeout
      this.searchTimeout = setTimeout(() => {
        this.searchTasks();
      }, 300); // Wait 300ms after typing stops
    },
    async searchTasks() {
      this.isSearching = true;
      
      try {
        const response = await axios.get('/api/tasks/search', {
          params: { q: this.searchQuery }
        });
        
        this.searchResults = response.data;
      } catch (error) {
        console.error('Error searching tasks:', error);
        // In case of error, reset to showing all tasks
        this.searchResults = null;
      } finally {
        this.isSearching = false;
      }
    },
    clearSearch() {
      this.searchQuery = '';
      this.searchResults = null;
    },
    openTaskModal(task) {
      if (task) {
        // Clone the task to avoid direct mutations
        this.editingTask = { ...task };
        
        // Convert the dates to proper format for the date input
        if (this.editingTask.due_date) {
          const date = new Date(this.editingTask.due_date);
          this.editingTask.due_date = date.toISOString().split('T')[0];
        }
      } else {
        // Reset for a new task
        this.editingTask = {
          title: '',
          description: '',
          status: 'pending',
          priority: 0,
          due_date: null
        };
      }
      
      this.taskModal.show();
    },
    async saveTask() {
      try {
        let response;
        
        // Ensure priority is a number
        this.editingTask.priority = parseInt(this.editingTask.priority);
        
        if (this.editingTask.id) {
          // Update existing task
          response = await axios.put(`/api/tasks/${this.editingTask.id}`, this.editingTask);
          
          // Update task in the list
          const index = this.tasks.findIndex(t => t.id === this.editingTask.id);
          if (index !== -1) {
            this.tasks[index] = response.data;
          }
          
          // Also update in search results if present
          if (this.searchResults) {
            const searchIndex = this.searchResults.findIndex(t => t.id === this.editingTask.id);
            if (searchIndex !== -1) {
              this.searchResults[searchIndex] = response.data;
            }
          }
        } else {
          // Create new task
          response = await axios.post('/api/tasks', this.editingTask);
          this.tasks.push(response.data);
          
          // Clear search results to show all tasks including the new one
          if (this.searchResults) {
            this.searchQuery = '';
            this.searchResults = null;
          }
        }
        
        this.taskModal.hide();
      } catch (error) {
        console.error('Error saving task:', error);
        alert('Failed to save task. Please try again.');
      }
    },
    confirmDeleteTask(task) {
      this.taskToDelete = task;
      this.deleteModal.show();
    },
    async deleteTask() {
      if (!this.taskToDelete) return;
      
      try {
        await axios.delete(`/api/tasks/${this.taskToDelete.id}`);
        
        // Remove task from the list
        this.tasks = this.tasks.filter(t => t.id !== this.taskToDelete.id);
        
        // Also remove from search results if present
        if (this.searchResults) {
          this.searchResults = this.searchResults.filter(t => t.id !== this.taskToDelete.id);
        }
        
        this.deleteModal.hide();
        this.taskToDelete = null;
      } catch (error) {
        console.error('Error deleting task:', error);
        alert('Failed to delete task. Please try again.');
      }
    },
    async toggleTaskComplete(task) {
      try {
        // Keep a copy of the original task in case of error
        const originalTask = { ...this.tasks.find(t => t.id === task.id) };
        
        // Optimistic update - update the UI first
        const index = this.tasks.findIndex(t => t.id === task.id);
        if (index !== -1) {
          this.tasks[index] = task;
        }
        
        // Also update in search results if present
        if (this.searchResults) {
          const searchIndex = this.searchResults.findIndex(t => t.id === task.id);
          if (searchIndex !== -1) {
            this.searchResults[searchIndex] = task;
          }
        }
        
        // Send update to API
        await axios.put(`/api/tasks/${task.id}`, {
          completed: task.completed,
          status: task.status
        });
        
        // No need to update again as we did it optimistically
      } catch (error) {
        console.error('Error updating task completion status:', error);
        
        // Revert the change if there was an error
        const index = this.tasks.findIndex(t => t.id === task.id);
        if (index !== -1) {
          this.tasks[index] = originalTask;
        }
        
        // Also revert in search results if present
        if (this.searchResults) {
          const searchIndex = this.searchResults.findIndex(t => t.id === task.id);
          if (searchIndex !== -1) {
            this.searchResults[searchIndex] = originalTask;
          }
        }
        
        alert('Failed to update task. Please try again.');
      }
    }
  }
}
</script>

<style scoped>
.search-container {
  position: relative;
}

.search-container input:focus {
  box-shadow: none;
  border-color: #dee2e6;
}

.search-container .input-group-text {
  cursor: pointer;
}

.search-container .input-group-text:hover {
  color: #6f42c1;
}
</style> 