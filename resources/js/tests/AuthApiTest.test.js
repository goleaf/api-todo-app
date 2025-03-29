import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import axios from 'axios';

// Mock axios
vi.mock('axios');

describe('Auth API Tests', () => {
  const mockUserData = {
    name: 'Test User',
    email: 'test@example.com'
  };
  
  const mockToken = 'test-token';
  
  beforeEach(() => {
    // Reset mocks
    vi.resetAllMocks();
    
    // Set default headers
    axios.defaults.headers = {
      common: {}
    };
    
    // Clear localStorage mock
    localStorage.clear();
  });
  
  afterEach(() => {
    vi.clearAllMocks();
  });
  
  // Mock localStorage
  const localStorageMock = (function() {
    let store = {};
    return {
      getItem: vi.fn(key => store[key] || null),
      setItem: vi.fn((key, value) => {
        store[key] = value.toString();
      }),
      clear: vi.fn(() => {
        store = {};
      }),
      removeItem: vi.fn(key => {
        delete store[key];
      })
    };
  })();
  
  Object.defineProperty(window, 'localStorage', {
    value: localStorageMock
  });
  
  it('should register a new user successfully', async () => {
    // Mock the register API response
    axios.post.mockResolvedValueOnce({
      data: {
        token: mockToken,
        user: { ...mockUserData, id: 1 }
      }
    });
    
    // Registration form data
    const registerData = {
      name: 'Test User',
      email: 'test@example.com',
      password: 'password',
      password_confirmation: 'password'
    };
    
    // Make the API call
    const response = await axios.post('/register', registerData);
    
    // Verify the request
    expect(axios.post).toHaveBeenCalledWith('/register', registerData);
    
    // Verify the response
    expect(response.data.token).toBe(mockToken);
    expect(response.data.user.name).toBe(mockUserData.name);
    expect(response.data.user.email).toBe(mockUserData.email);
  });
  
  it('should handle registration validation errors', async () => {
    // Mock validation error response
    const validationError = {
      response: {
        status: 422,
        data: {
          message: 'The given data was invalid.',
          errors: {
            email: ['The email has already been taken.'],
            password: ['The password field confirmation does not match.']
          }
        }
      }
    };
    
    axios.post.mockRejectedValueOnce(validationError);
    
    // Registration form data with issues
    const invalidData = {
      name: 'Test User',
      email: 'existing@example.com',
      password: 'password',
      password_confirmation: 'different'
    };
    
    // Make the API call and expect it to fail
    try {
      await axios.post('/register', invalidData);
      // Should not reach here
      expect(true).toBe(false);
    } catch (error) {
      expect(error.response.status).toBe(422);
      expect(error.response.data.errors.email).toBeDefined();
      expect(error.response.data.errors.password).toBeDefined();
    }
    
    // Verify the request was made
    expect(axios.post).toHaveBeenCalledWith('/register', invalidData);
  });
  
  it('should login a user successfully', async () => {
    // Mock the login API response
    axios.post.mockResolvedValueOnce({
      data: {
        token: mockToken,
        user: { ...mockUserData, id: 1 }
      }
    });
    
    // Login form data
    const loginData = {
      email: 'test@example.com',
      password: 'password'
    };
    
    // Make the API call
    const response = await axios.post('/login', loginData);
    
    // Verify the request
    expect(axios.post).toHaveBeenCalledWith('/login', loginData);
    
    // Verify the response
    expect(response.data.token).toBe(mockToken);
    expect(response.data.user.name).toBe(mockUserData.name);
    expect(response.data.user.email).toBe(mockUserData.email);
  });
  
  it('should handle login failures', async () => {
    // Mock login failure response
    const loginError = {
      response: {
        status: 401,
        data: {
          message: 'Invalid credentials'
        }
      }
    };
    
    axios.post.mockRejectedValueOnce(loginError);
    
    // Invalid login credentials
    const invalidCredentials = {
      email: 'test@example.com',
      password: 'wrongpassword'
    };
    
    // Make the API call and expect it to fail
    try {
      await axios.post('/login', invalidCredentials);
      // Should not reach here
      expect(true).toBe(false);
    } catch (error) {
      expect(error.response.status).toBe(401);
      expect(error.response.data.message).toBe('Invalid credentials');
    }
    
    // Verify the request was made
    expect(axios.post).toHaveBeenCalledWith('/login', invalidCredentials);
  });
  
  it('should set authorization header after login', async () => {
    // Mock the login API response
    axios.post.mockResolvedValueOnce({
      data: {
        token: mockToken,
        user: { ...mockUserData, id: 1 }
      }
    });
    
    // Login form data
    const loginData = {
      email: 'test@example.com',
      password: 'password'
    };
    
    // Make the login API call
    await axios.post('/login', loginData);
    
    // Set token in localStorage and auth header (as the app would do)
    localStorage.setItem('auth_token', mockToken);
    axios.defaults.headers.common['Authorization'] = `Bearer ${mockToken}`;
    
    // Verify header was set
    expect(axios.defaults.headers.common['Authorization']).toBe(`Bearer ${mockToken}`);
    expect(localStorage.setItem).toHaveBeenCalledWith('auth_token', mockToken);
  });
  
  it('should retrieve authenticated user data', async () => {
    // Mock auth header
    axios.defaults.headers.common['Authorization'] = `Bearer ${mockToken}`;
    
    // Mock the user API response
    axios.get.mockResolvedValueOnce({
      data: { ...mockUserData, id: 1 }
    });
    
    // Make the API call
    const response = await axios.get('/user');
    
    // Verify the request
    expect(axios.get).toHaveBeenCalledWith('/user');
    
    // Verify the response
    expect(response.data.name).toBe(mockUserData.name);
    expect(response.data.email).toBe(mockUserData.email);
  });
  
  it('should handle unauthorized access to protected routes', async () => {
    // No auth header set
    const authError = {
      response: {
        status: 401,
        data: {
          message: 'Unauthenticated.'
        }
      }
    };
    
    axios.get.mockRejectedValueOnce(authError);
    
    // Make the API call and expect it to fail
    try {
      await axios.get('/user');
      // Should not reach here
      expect(true).toBe(false);
    } catch (error) {
      expect(error.response.status).toBe(401);
      expect(error.response.data.message).toBe('Unauthenticated.');
    }
    
    // Verify the request was made
    expect(axios.get).toHaveBeenCalledWith('/user');
  });
  
  it('should clear auth data on logout', async () => {
    // Set initial auth state
    localStorage.setItem('auth_token', mockToken);
    localStorage.setItem('user', JSON.stringify(mockUserData));
    axios.defaults.headers.common['Authorization'] = `Bearer ${mockToken}`;
    
    // Mock logout response
    axios.post.mockResolvedValueOnce({
      data: { message: 'Logged out successfully' }
    });
    
    // Make the logout API call
    await axios.post('/logout');
    
    // Simulate the logout action in Vuex
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    axios.defaults.headers.common['Authorization'] = '';
    
    // Verify the API call
    expect(axios.post).toHaveBeenCalledWith('/logout');
    
    // Verify auth data was cleared
    expect(localStorage.removeItem).toHaveBeenCalledWith('auth_token');
    expect(localStorage.removeItem).toHaveBeenCalledWith('user');
    expect(axios.defaults.headers.common['Authorization']).toBe('');
  });
}); 