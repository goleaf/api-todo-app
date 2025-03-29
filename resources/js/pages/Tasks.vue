<template>
  <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <TaskList />
  </div>
</template>

<script>
import TaskList from '../components/TaskList.vue';

export default {
  name: 'TasksPage',
  components: {
    TaskList
  }
}
</script> 