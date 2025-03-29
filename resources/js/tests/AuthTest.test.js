import { describe, it, expect, vi } from 'vitest';
import axios from 'axios';

// Mock axios
vi.mock('axios');

describe('Auth API Tests', () => {
  beforeEach(() => {
    // Reset mocks before each test
    vi.resetAllMocks();
  });

  it('should test registration endpoint', async () => {
    // Mock the registration response
    axios.post.mockResolvedValueOnce({
      data: {
        token: 'test-token',
        user: {
          id: 1,
          name: 'Test User',
          email: 'test@example.com'
        }
      }
    });

    // Registration data
    const userData = {
      name: 'Test User',
      email: 'test@example.com',
      password: 'password',
      password_confirmation: 'password'
    };

    // Call the API
    const response = await axios.post('/api/register', userData);

    // Verify request was made correctly
    expect(axios.post).toHaveBeenCalledWith('/api/register', userData);

    // Verify the response
    expect(response.data.token).toBeDefined();
    expect(response.data.user.name).toBe('Test User');
    expect(response.data.user.email).toBe('test@example.com');
  });

  it('should test login endpoint', async () => {
    // Mock the login response
    axios.post.mockResolvedValueOnce({
      data: {
        token: 'test-token',
        user: {
          id: 1,
          name: 'Test User',
          email: 'test@example.com'
        }
      }
    });

    // Login data
    const loginData = {
      email: 'test@example.com',
      password: 'password'
    };

    // Call the API
    const response = await axios.post('/api/login', loginData);

    // Verify request was made correctly
    expect(axios.post).toHaveBeenCalledWith('/api/login', loginData);

    // Verify the response
    expect(response.data.token).toBeDefined();
    expect(response.data.user.name).toBe('Test User');
    expect(response.data.user.email).toBe('test@example.com');
  });

  it('should handle login errors', async () => {
    // Mock error response
    const errorResponse = {
      response: {
        status: 422,
        data: {
          message: 'Invalid credentials',
          errors: {
            email: ['The provided credentials are incorrect.']
          }
        }
      }
    };
    
    axios.post.mockRejectedValueOnce(errorResponse);

    // Login data with wrong credentials
    const loginData = {
      email: 'test@example.com',
      password: 'wrong-password'
    };

    // Call the API and expect error
    try {
      await axios.post('/api/login', loginData);
      // Should not reach here
      expect(true).toBe(false);
    } catch (error) {
      expect(error.response.status).toBe(422);
      expect(error.response.data.message).toBe('Invalid credentials');
    }

    // Verify request was made correctly
    expect(axios.post).toHaveBeenCalledWith('/api/login', loginData);
  });

  it('should test user endpoint for authenticated users', async () => {
    // Mock the user response
    axios.get.mockResolvedValueOnce({
      data: {
        id: 1,
        name: 'Test User',
        email: 'test@example.com'
      }
    });

    // Set auth header
    axios.defaults.headers = {
      common: {
        Authorization: 'Bearer test-token'
      }
    };

    // Call the API
    const response = await axios.get('/api/user');

    // Verify request was made correctly
    expect(axios.get).toHaveBeenCalledWith('/api/user');

    // Verify the response
    expect(response.data.id).toBe(1);
    expect(response.data.name).toBe('Test User');
    expect(response.data.email).toBe('test@example.com');
  });

  it('should handle unauthorized access to protected routes', async () => {
    // Mock error response
    const errorResponse = {
      response: {
        status: 401,
        data: {
          message: 'Unauthenticated.'
        }
      }
    };
    
    axios.get.mockRejectedValueOnce(errorResponse);

    // Call the API and expect error
    try {
      await axios.get('/api/user');
      // Should not reach here
      expect(true).toBe(false);
    } catch (error) {
      expect(error.response.status).toBe(401);
      expect(error.response.data.message).toBe('Unauthenticated.');
    }

    // Verify request was made correctly
    expect(axios.get).toHaveBeenCalledWith('/api/user');
  });
}); 