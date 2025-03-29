<template>
  <div class="task-item mb-3" :class="{'completed-task': task.completed}">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div class="d-flex align-items-start">
            <div class="task-checkbox me-2">
              <input 
                type="checkbox" 
                :id="`task-${task.id}`" 
                :checked="task.completed" 
                @change="toggleComplete"
                class="form-check-input rounded-circle"
              >
            </div>
            <div>
              <h5 class="card-title" :class="{'text-decoration-line-through text-muted': task.completed}">
                {{ task.title }}
                <span 
                  :class="`badge bg-${task.priority_color} ms-2`" 
                  :title="`Priority: ${task.priority_label}`"
                >
                  {{ task.priority_label }}
                </span>
              </h5>
              <p class="card-text text-muted" v-if="task.description" :class="{'text-decoration-line-through': task.completed}">
                {{ task.description }}
              </p>
            </div>
          </div>
          <div>
            <span :class="`badge bg-${statusColor} ${task.status === 'pending' ? 'text-dark' : ''}`">
              {{ formatStatus(task.status) }}
            </span>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
          <div class="text-muted small">
            <i class="bi bi-calendar-event me-1"></i>
            {{ formatDate(task.due_date) }}
          </div>
          <div>
            <button 
              class="btn btn-sm btn-outline-primary me-1" 
              @click="$emit('edit', task)"
              title="Edit task"
            >
              <i class="bi bi-pencil"></i>
            </button>
            <button 
              class="btn btn-sm btn-outline-danger" 
              @click="$emit('delete', task)"
              title="Delete task"
            >
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Task',
  props: {
    task: {
      type: Object,
      required: true
    }
  },
  computed: {
    statusColor() {
      switch(this.task.status) {
        case 'pending':
          return 'warning';
        case 'in_progress':
          return 'primary';
        case 'completed':
          return 'success';
        default:
          return 'secondary';
      }
    }
  },
  methods: {
    formatStatus(status) {
      if (!status) return 'Unknown';
      
      return status.split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },
    formatDate(dateString) {
      if (!dateString) return 'No due date';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    toggleComplete() {
      const updatedTask = { 
        ...this.task,
        completed: !this.task.completed,
        // If task is marked as completed, also update status to 'completed' if it wasn't already
        status: !this.task.completed ? 'completed' : this.task.status
      };
      
      this.$emit('toggle-complete', updatedTask);
    }
  }
}
</script>

<style scoped>
.task-item {
  transition: all 0.2s ease-in-out;
}

.task-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.completed-task {
  opacity: 0.8;
}

.task-checkbox input[type="checkbox"] {
  width: 1.25rem;
  height: 1.25rem;
  margin-top: 0.25rem;
  cursor: pointer;
  border-color: #adb5bd;
}

.task-checkbox input[type="checkbox"]:checked {
  background-color: #6f42c1;
  border-color: #6f42c1;
}

.task-checkbox input[type="checkbox"]:focus {
  box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
}
</style> 