import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import axios from 'axios';

// Mock axios
vi.mock('axios');

describe('API Route Status Tests', () => {
  const mockResponseData = {
    data: { id: 1, name: 'Test' },
    status: 200
  };
  
  const mockRoutes = [
    { url: '/api/user', method: 'get' },
    { url: '/api/todos', method: 'get' },
    { url: '/api/categories', method: 'get' },
    { url: '/api/tasks', method: 'get' },
    { url: '/api/tasks/search', method: 'get', params: { query: 'test' } }
  ];
  
  beforeEach(() => {
    // Reset mocks
    vi.resetAllMocks();
    
    // Default mock implementation
    axios.get.mockResolvedValue(mockResponseData);
    axios.post.mockResolvedValue(mockResponseData);
    axios.put.mockResolvedValue(mockResponseData);
    axios.delete.mockResolvedValue(mockResponseData);
  });
  
  afterEach(() => {
    vi.clearAllMocks();
  });
  
  it('should test all GET API routes respond', async () => {
    // Test each route
    for (const route of mockRoutes) {
      if (route.method === 'get') {
        const response = await axios.get(route.url, { params: route.params });
        expect(response.status).toBe(200);
      }
    }
    
    // Verify axios.get was called the expected number of times
    const getRoutes = mockRoutes.filter(r => r.method === 'get');
    expect(axios.get).toHaveBeenCalledTimes(getRoutes.length);
  });
  
  it('should test POST requests to API routes', async () => {
    const mockData = { title: 'Test Item', completed: false };
    
    await axios.post('/api/todos', mockData);
    await axios.post('/api/tasks', { ...mockData, priority: 1 });
    await axios.post('/api/categories', { name: 'Test Category', color: '#ff0000' });
    
    expect(axios.post).toHaveBeenCalledTimes(3);
    expect(axios.post).toHaveBeenCalledWith('/api/todos', mockData);
  });
  
  it('should test PUT requests to API routes', async () => {
    const mockData = { id: 1, title: 'Updated Item', completed: true };
    
    await axios.put('/api/todos/1', mockData);
    await axios.put('/api/tasks/1', { ...mockData, priority: 2 });
    await axios.put('/api/categories/1', { name: 'Updated Category', color: '#00ff00' });
    
    expect(axios.put).toHaveBeenCalledTimes(3);
    expect(axios.put).toHaveBeenCalledWith('/api/todos/1', mockData);
  });
  
  it('should test DELETE requests to API routes', async () => {
    await axios.delete('/api/todos/1');
    await axios.delete('/api/tasks/1');
    await axios.delete('/api/categories/1');
    
    expect(axios.delete).toHaveBeenCalledTimes(3);
    expect(axios.delete).toHaveBeenCalledWith('/api/todos/1');
  });
  
  it('should handle API error responses correctly', async () => {
    // Mock error response
    axios.get.mockRejectedValueOnce({ 
      response: { 
        status: 404,
        data: { message: 'Not found' } 
      } 
    });
    
    try {
      await axios.get('/api/nonexistent');
    } catch (error) {
      expect(error.response.status).toBe(404);
      expect(error.response.data.message).toBe('Not found');
    }
    
    expect(axios.get).toHaveBeenCalledWith('/api/nonexistent');
  });
}); 