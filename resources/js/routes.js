import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

// Import components
import Dashboard from '@/views/Dashboard.vue';
import TaskList from '@/views/tasks/TaskList.vue';
import TaskDetail from '@/views/tasks/TaskDetail.vue';
import Login from '@/views/auth/Login.vue';
import Register from '@/views/auth/Register.vue';
import NotFound from '@/views/NotFound.vue';

const routes = [
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'tasks',
    component: TaskList,
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks/create',
    name: 'tasks.create',
    component: TaskDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks/:id',
    name: 'tasks.edit',
    component: TaskDetail,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    // Always scroll to top
    return { top: 0 };
  }
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const isLoggedIn = authStore.isAuthenticated;

  // Check if route requires authentication
  if (to.meta.requiresAuth && !isLoggedIn) {
    next({ name: 'login', query: { redirect: to.fullPath } });
  } 
  // Check if route is guest only and user is logged in
  else if (to.meta.guest && isLoggedIn) {
    next({ name: 'dashboard' });
  } 
  else {
    next();
  }
});

export default router; 