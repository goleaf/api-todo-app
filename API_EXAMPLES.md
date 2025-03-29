# Todo Application API Examples

This document provides code examples for interacting with the Todo Application API from external applications.

## Authentication

### Register a New User

```javascript
// Using Fetch API
const registerUser = async (userData) => {
  try {
    const response = await fetch('https://todo.prus.dev/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(userData)
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Registration failed');
    }
    
    // Save the token to local storage
    localStorage.setItem('token', data.token);
    
    return data;
  } catch (error) {
    console.error('Registration error:', error);
    throw error;
  }
};

// Example usage
const newUser = {
  name: 'John Doe',
  email: 'john@example.com',
  password: 'secure-password',
  password_confirmation: 'secure-password',
  device_name: navigator.userAgent
};

registerUser(newUser)
  .then(data => console.log('Registration successful:', data))
  .catch(error => console.error('Registration failed:', error));
```

### Login

```javascript
// Using Fetch API
const loginUser = async (credentials) => {
  try {
    const response = await fetch('https://todo.prus.dev/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(credentials)
    });
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.message || 'Login failed');
    }
    
    // Save the token to local storage
    localStorage.setItem('token', data.token);
    
    return data;
  } catch (error) {
    console.error('Login error:', error);
    throw error;
  }
};

// Example usage
const credentials = {
  email: 'john@example.com',
  password: 'secure-password',
  remember: true,
  device_name: navigator.userAgent
};

loginUser(credentials)
  .then(data => console.log('Login successful:', data))
  .catch(error => console.error('Login failed:', error));
```

### Logout

```javascript
// Using Fetch API
const logoutUser = async () => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch('https://todo.prus.dev/api/logout', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const data = await response.json();
      throw new Error(data.message || 'Logout failed');
    }
    
    // Remove the token from local storage
    localStorage.removeItem('token');
    
    return true;
  } catch (error) {
    console.error('Logout error:', error);
    throw error;
  }
};

// Example usage
logoutUser()
  .then(() => console.log('Logout successful'))
  .catch(error => console.error('Logout failed:', error));
```

## Task Management

### Get All Tasks

```javascript
// Using Fetch API
const getTasks = async (filters = {}) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    // Build query parameters
    const queryParams = new URLSearchParams();
    
    if (filters.status) queryParams.append('status', filters.status);
    if (filters.category) queryParams.append('category', filters.category);
    if (filters.search) queryParams.append('search', filters.search);
    if (filters.dueDate) queryParams.append('due_date', filters.dueDate);
    if (filters.sort) queryParams.append('sort', filters.sort);
    if (filters.order) queryParams.append('order', filters.order);
    if (filters.page) queryParams.append('page', filters.page);
    
    const url = `https://todo.prus.dev/api/tasks?${queryParams.toString()}`;
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to fetch tasks');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error fetching tasks:', error);
    throw error;
  }
};

// Example usage
const filters = {
  status: 'pending',
  sort: 'due_date',
  order: 'asc',
  page: 1
};

getTasks(filters)
  .then(data => console.log('Tasks:', data))
  .catch(error => console.error('Failed to fetch tasks:', error));
```

### Create a New Task

```javascript
// Using Fetch API
const createTask = async (taskData) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch('https://todo.prus.dev/api/tasks', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(taskData)
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to create task');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error creating task:', error);
    throw error;
  }
};

// Example usage
const newTask = {
  title: 'Complete project documentation',
  description: 'Write comprehensive documentation for the project',
  category_id: 1,
  due_date: '2023-07-15',
  priority: 'high'
};

createTask(newTask)
  .then(data => console.log('Task created:', data))
  .catch(error => console.error('Failed to create task:', error));
```

### Get a Specific Task

```javascript
// Using Fetch API
const getTask = async (taskId) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch(`https://todo.prus.dev/api/tasks/${taskId}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to fetch task');
    }
    
    return await response.json();
  } catch (error) {
    console.error(`Error fetching task ${taskId}:`, error);
    throw error;
  }
};

// Example usage
const taskId = 1;

getTask(taskId)
  .then(data => console.log('Task details:', data))
  .catch(error => console.error('Failed to fetch task:', error));
```

### Update a Task

```javascript
// Using Fetch API
const updateTask = async (taskId, taskData) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch(`https://todo.prus.dev/api/tasks/${taskId}`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(taskData)
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to update task');
    }
    
    return await response.json();
  } catch (error) {
    console.error(`Error updating task ${taskId}:`, error);
    throw error;
  }
};

// Example usage
const taskId = 1;
const taskUpdate = {
  title: 'Updated task title',
  description: 'Updated task description',
  due_date: '2023-08-01',
  priority: 'medium',
  completed: true
};

updateTask(taskId, taskUpdate)
  .then(data => console.log('Task updated:', data))
  .catch(error => console.error('Failed to update task:', error));
```

### Delete a Task

```javascript
// Using Fetch API
const deleteTask = async (taskId) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch(`https://todo.prus.dev/api/tasks/${taskId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to delete task');
    }
    
    return true;
  } catch (error) {
    console.error(`Error deleting task ${taskId}:`, error);
    throw error;
  }
};

// Example usage
const taskId = 1;

deleteTask(taskId)
  .then(() => console.log('Task deleted successfully'))
  .catch(error => console.error('Failed to delete task:', error));
```

### Toggle Task Completion

```javascript
// Using Fetch API
const toggleTaskCompletion = async (taskId) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch(`https://todo.prus.dev/api/tasks/${taskId}/toggle-complete`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to toggle task completion');
    }
    
    return await response.json();
  } catch (error) {
    console.error(`Error toggling task ${taskId} completion:`, error);
    throw error;
  }
};

// Example usage
const taskId = 1;

toggleTaskCompletion(taskId)
  .then(data => console.log('Task completion toggled:', data))
  .catch(error => console.error('Failed to toggle task completion:', error));
```

## Categories

### Get All Categories

```javascript
// Using Fetch API
const getCategories = async () => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch('https://todo.prus.dev/api/categories', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to fetch categories');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error fetching categories:', error);
    throw error;
  }
};

// Example usage
getCategories()
  .then(data => console.log('Categories:', data))
  .catch(error => console.error('Failed to fetch categories:', error));
```

### Create a New Category

```javascript
// Using Fetch API
const createCategory = async (categoryData) => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Not authenticated');
    }
    
    const response = await fetch('https://todo.prus.dev/api/categories', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(categoryData)
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || 'Failed to create category');
    }
    
    return await response.json();
  } catch (error) {
    console.error('Error creating category:', error);
    throw error;
  }
};

// Example usage
const newCategory = {
  name: 'Work',
  color: '#FF5733'
};

createCategory(newCategory)
  .then(data => console.log('Category created:', data))
  .catch(error => console.error('Failed to create category:', error));
```

## Using with React.js

Here's a simple example of a React component that fetches and displays tasks:

```jsx
import React, { useState, useEffect } from 'react';

const TaskList = () => {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchTasks = async () => {
      try {
        const token = localStorage.getItem('token');
        
        if (!token) {
          throw new Error('Not authenticated');
        }
        
        const response = await fetch('https://todo.prus.dev/api/tasks', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
          }
        });
        
        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'Failed to fetch tasks');
        }
        
        const data = await response.json();
        setTasks(data.data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchTasks();
  }, []);

  const toggleTaskCompletion = async (taskId, completed) => {
    try {
      const token = localStorage.getItem('token');
      
      if (!token) {
        throw new Error('Not authenticated');
      }
      
      const response = await fetch(`https://todo.prus.dev/api/tasks/${taskId}/toggle-complete`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        }
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to toggle task completion');
      }
      
      // Update local state
      setTasks(tasks.map(task => 
        task.id === taskId ? { ...task, completed: !completed } : task
      ));
      
    } catch (err) {
      setError(err.message);
    }
  };

  if (loading) return <div>Loading tasks...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="task-list">
      <h2>My Tasks</h2>
      {tasks.length === 0 ? (
        <p>No tasks found.</p>
      ) : (
        <ul>
          {tasks.map(task => (
            <li key={task.id} className={task.completed ? 'completed' : ''}>
              <input
                type="checkbox"
                checked={task.completed}
                onChange={() => toggleTaskCompletion(task.id, task.completed)}
              />
              <span>{task.title}</span>
              {task.due_date && (
                <span className="due-date">
                  Due: {new Date(task.due_date).toLocaleDateString()}
                </span>
              )}
            </li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default TaskList;
```

## Using with Angular

Here's a simple example of an Angular service to interact with the Todo API:

```typescript
// task.service.ts
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class TaskService {
  private apiUrl = environment.apiUrl + '/tasks';

  constructor(private http: HttpClient) { }

  // Get auth token from localStorage
  private getToken(): string {
    return localStorage.getItem('token') || '';
  }

  // Create headers with auth token
  private getHeaders(): HttpHeaders {
    return new HttpHeaders({
      'Authorization': `Bearer ${this.getToken()}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    });
  }

  // Get all tasks with optional filters
  getTasks(filters = {}): Observable<any> {
    let params = new HttpParams();
    
    Object.keys(filters).forEach(key => {
      if (filters[key]) {
        params = params.set(key, filters[key]);
      }
    });

    return this.http.get(this.apiUrl, {
      headers: this.getHeaders(),
      params
    });
  }

  // Get a specific task
  getTask(id: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/${id}`, {
      headers: this.getHeaders()
    });
  }

  // Create a new task
  createTask(task: any): Observable<any> {
    return this.http.post(this.apiUrl, task, {
      headers: this.getHeaders()
    });
  }

  // Update an existing task
  updateTask(id: number, task: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, task, {
      headers: this.getHeaders()
    });
  }

  // Delete a task
  deleteTask(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`, {
      headers: this.getHeaders()
    });
  }

  // Toggle task completion
  toggleTaskCompletion(id: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/${id}/toggle-complete`, {}, {
      headers: this.getHeaders()
    });
  }
}
``` 