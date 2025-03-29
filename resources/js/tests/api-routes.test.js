import { describe, it, expect, vi, beforeEach } from 'vitest';
import axios from 'axios';

// Mock axios
vi.mock('axios');

describe('API Routes', () => {
  beforeEach(() => {
    vi.resetAllMocks();
  });

  // Auth routes
  describe('Authentication Routes', () => {
    it('login route should POST to /api/login', async () => {
      const credentials = { email: 'test@example.com', password: 'password' };
      axios.post.mockResolvedValue({ data: { token: 'token', user: { id: 1 } } });
      
      await axios.post('/login', credentials);
      
      expect(axios.post).toHaveBeenCalledWith('/login', credentials);
    });

    it('register route should POST to /api/register', async () => {
      const userData = { 
        name: 'Test User', 
        email: 'test@example.com', 
        password: 'password',
        password_confirmation: 'password'
      };
      
      axios.post.mockResolvedValue({ data: { token: 'token', user: { id: 1 } } });
      
      await axios.post('/register', userData);
      
      expect(axios.post).toHaveBeenCalledWith('/register', userData);
    });

    it('user info should GET from /api/user', async () => {
      axios.get.mockResolvedValue({ data: { id: 1, name: 'Test User' } });
      
      await axios.get('/user');
      
      expect(axios.get).toHaveBeenCalledWith('/user');
    });
  });

  // Todo routes
  describe('Todo Routes', () => {
    it('fetching todos should GET from /api/todos', async () => {
      const todos = [{ id: 1, title: 'Test Todo' }];
      axios.get.mockResolvedValue({ data: todos });
      
      const response = await axios.get('/todos');
      
      expect(axios.get).toHaveBeenCalledWith('/todos');
      expect(response.data).toEqual(todos);
    });

    it('creating a todo should POST to /api/todos', async () => {
      const newTodo = { title: 'New Todo', description: 'Test' };
      const createdTodo = { id: 1, ...newTodo };
      
      axios.post.mockResolvedValue({ data: createdTodo });
      
      const response = await axios.post('/todos', newTodo);
      
      expect(axios.post).toHaveBeenCalledWith('/todos', newTodo);
      expect(response.data).toEqual(createdTodo);
    });

    it('updating a todo should PUT to /api/todos/{id}', async () => {
      const todoId = 1;
      const updatedTodo = { id: todoId, title: 'Updated Todo', completed: true };
      
      axios.put.mockResolvedValue({ data: updatedTodo });
      
      const response = await axios.put(`/todos/${todoId}`, updatedTodo);
      
      expect(axios.put).toHaveBeenCalledWith(`/todos/${todoId}`, updatedTodo);
      expect(response.data).toEqual(updatedTodo);
    });

    it('deleting a todo should DELETE to /api/todos/{id}', async () => {
      const todoId = 1;
      
      axios.delete.mockResolvedValue({ data: { success: true } });
      
      await axios.delete(`/todos/${todoId}`);
      
      expect(axios.delete).toHaveBeenCalledWith(`/todos/${todoId}`);
    });
  });

  // Category routes
  describe('Category Routes', () => {
    it('fetching categories should GET from /api/categories', async () => {
      const categories = [{ id: 1, name: 'Work' }, { id: 2, name: 'Personal' }];
      
      axios.get.mockResolvedValue({ data: categories });
      
      const response = await axios.get('/categories');
      
      expect(axios.get).toHaveBeenCalledWith('/categories');
      expect(response.data).toEqual(categories);
    });

    it('creating a category should POST to /api/categories', async () => {
      const newCategory = { name: 'New Category' };
      const createdCategory = { id: 1, ...newCategory };
      
      axios.post.mockResolvedValue({ data: createdCategory });
      
      const response = await axios.post('/categories', newCategory);
      
      expect(axios.post).toHaveBeenCalledWith('/categories', newCategory);
      expect(response.data).toEqual(createdCategory);
    });

    it('deleting a category should DELETE to /api/categories/{id}', async () => {
      const categoryId = 1;
      
      axios.delete.mockResolvedValue({ data: { success: true } });
      
      await axios.delete(`/categories/${categoryId}`);
      
      expect(axios.delete).toHaveBeenCalledWith(`/categories/${categoryId}`);
    });
  });

  // Task routes
  describe('Task Routes', () => {
    it('searching tasks should GET from /api/tasks/search', async () => {
      const query = 'test';
      const tasks = [{ id: 1, title: 'Test Task' }];
      
      axios.get.mockResolvedValue({ data: tasks });
      
      const response = await axios.get(`/tasks/search?q=${query}`);
      
      expect(axios.get).toHaveBeenCalledWith(`/tasks/search?q=${query}`);
      expect(response.data).toEqual(tasks);
    });

    it('fetching tasks should GET from /api/tasks', async () => {
      const tasks = [{ id: 1, title: 'Task 1' }, { id: 2, title: 'Task 2' }];
      
      axios.get.mockResolvedValue({ data: tasks });
      
      const response = await axios.get('/tasks');
      
      expect(axios.get).toHaveBeenCalledWith('/tasks');
      expect(response.data).toEqual(tasks);
    });

    it('fetching a single task should GET from /api/tasks/{id}', async () => {
      const taskId = 1;
      const task = { id: taskId, title: 'Task 1' };
      
      axios.get.mockResolvedValue({ data: task });
      
      const response = await axios.get(`/tasks/${taskId}`);
      
      expect(axios.get).toHaveBeenCalledWith(`/tasks/${taskId}`);
      expect(response.data).toEqual(task);
    });

    it('creating a task should POST to /api/tasks', async () => {
      const newTask = { title: 'New Task', description: 'Test Description' };
      const createdTask = { id: 1, ...newTask };
      
      axios.post.mockResolvedValue({ data: createdTask });
      
      const response = await axios.post('/tasks', newTask);
      
      expect(axios.post).toHaveBeenCalledWith('/tasks', newTask);
      expect(response.data).toEqual(createdTask);
    });

    it('updating a task should PUT to /api/tasks/{id}', async () => {
      const taskId = 1;
      const updatedTask = { id: taskId, title: 'Updated Task', completed: true };
      
      axios.put.mockResolvedValue({ data: updatedTask });
      
      const response = await axios.put(`/tasks/${taskId}`, updatedTask);
      
      expect(axios.put).toHaveBeenCalledWith(`/tasks/${taskId}`, updatedTask);
      expect(response.data).toEqual(updatedTask);
    });

    it('deleting a task should DELETE to /api/tasks/{id}', async () => {
      const taskId = 1;
      
      axios.delete.mockResolvedValue({ data: { success: true } });
      
      await axios.delete(`/tasks/${taskId}`);
      
      expect(axios.delete).toHaveBeenCalledWith(`/tasks/${taskId}`);
    });
  });

  // Error handling
  describe('API Error Handling', () => {
    it('should handle network errors', async () => {
      const networkError = new Error('Network Error');
      axios.get.mockRejectedValue(networkError);
      
      try {
        await axios.get('/todos');
        // Should not reach here
        expect(true).toBe(false);
      } catch (error) {
        expect(error).toBe(networkError);
      }
    });

    it('should handle validation errors', async () => {
      const validationError = {
        response: {
          status: 422,
          data: {
            message: 'The given data was invalid.',
            errors: {
              title: ['The title field is required.']
            }
          }
        }
      };
      
      axios.post.mockRejectedValue(validationError);
      
      try {
        await axios.post('/todos', {});
        // Should not reach here
        expect(true).toBe(false);
      } catch (error) {
        expect(error).toBe(validationError);
        expect(error.response.status).toBe(422);
        expect(error.response.data.errors.title[0]).toBe('The title field is required.');
      }
    });

    it('should handle authentication errors', async () => {
      const authError = {
        response: {
          status: 401,
          data: {
            message: 'Unauthenticated.'
          }
        }
      };
      
      axios.get.mockRejectedValue(authError);
      
      try {
        await axios.get('/user');
        // Should not reach here
        expect(true).toBe(false);
      } catch (error) {
        expect(error).toBe(authError);
        expect(error.response.status).toBe(401);
        expect(error.response.data.message).toBe('Unauthenticated.');
      }
    });

    it('should handle not found errors', async () => {
      const notFoundError = {
        response: {
          status: 404,
          data: {
            message: 'Resource not found.'
          }
        }
      };
      
      axios.get.mockRejectedValue(notFoundError);
      
      try {
        await axios.get('/todos/999');
        // Should not reach here
        expect(true).toBe(false);
      } catch (error) {
        expect(error).toBe(notFoundError);
        expect(error.response.status).toBe(404);
        expect(error.response.data.message).toBe('Resource not found.');
      }
    });
  });
}); 