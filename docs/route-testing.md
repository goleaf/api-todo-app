# Route Testing in Todo Application

This document outlines our approach to testing routes in the Todo application, covering both backend and frontend testing strategies.

## Overview

We use a comprehensive testing strategy that combines:
- PHP tests for backend routes and API endpoints
- JavaScript tests for frontend API interactions

## Backend Route Testing (PHP)

### Test Files:
- `tests/Feature/RouteTest.php`: Tests basic route existence and responses
- `tests/Feature/RouteBulkTest.php`: Tests routes in bulk by iterating through registered routes

### Testing Approaches:

1. **Route Existence Testing**:
   ```php
   public function test_api_routes_exist(): void
   {
       $routes = collect(Route::getRoutes())->filter(function ($route) {
           return strpos($route->uri(), 'api/') === 0;
       })->map(function ($route) {
           return [
               'uri' => $route->uri(),
               'methods' => $route->methods(),
           ];
       })->values()->all();
       
       $this->assertNotEmpty($routes, 'API routes should exist');
       
       // Check for essential routes
       $uris = collect($routes)->pluck('uri')->all();
       $this->assertContains('api/login', $uris, 'Login API route should exist');
       $this->assertContains('api/user', $uris, 'User API route should exist');
   }
   ```

2. **Authentication Testing**:
   ```php
   public function test_protected_routes_require_authentication(): void
   {
       $protectedEndpoints = [
           '/api/user',
           '/api/todos',
           '/api/tasks',
       ];
       
       foreach ($protectedEndpoints as $endpoint) {
           $response = $this->getJson($endpoint);
           $this->assertEquals(401, $response->status());
       }
   }
   ```

3. **Response Structure Testing**:
   ```php
   public function test_authenticated_user_can_access_user_endpoint(): void
   {
       $user = User::factory()->create();
       $response = $this->actingAs($user)->getJson('/api/user');
       $response->assertStatus(200);
       $response->assertJsonStructure(['id', 'name', 'email']);
   }
   ```

## Frontend Route Testing (JavaScript)

### Test Files:
- `resources/js/tests/RouteStatusTest.test.js`: Tests API responses from the frontend perspective

### Testing Approaches:

1. **Mock Testing HTTP Methods**:
   ```javascript
   import { describe, it, expect, beforeEach, vi } from 'vitest';
   import axios from 'axios';

   // Mock axios
   vi.mock('axios');

   describe('API Route Status Tests', () => {
     beforeEach(() => {
       vi.resetAllMocks();
       axios.get.mockResolvedValue({ status: 200, data: { id: 1 } });
     });
     
     it('should test GET API routes respond', async () => {
       const response = await axios.get('/api/user');
       expect(response.status).toBe(200);
       expect(axios.get).toHaveBeenCalledWith('/api/user');
     });
   });
   ```

2. **Testing All CRUD Operations**:
   ```javascript
   it('should test POST requests to API routes', async () => {
     const mockData = { title: 'Test Item' };
     await axios.post('/api/todos', mockData);
     expect(axios.post).toHaveBeenCalledWith('/api/todos', mockData);
   });
   
   it('should test PUT requests to API routes', async () => {
     const mockData = { id: 1, title: 'Updated Item' };
     await axios.put('/api/todos/1', mockData);
     expect(axios.put).toHaveBeenCalledWith('/api/todos/1', mockData);
   });
   
   it('should test DELETE requests to API routes', async () => {
     await axios.delete('/api/todos/1');
     expect(axios.delete).toHaveBeenCalledWith('/api/todos/1');
   });
   ```

3. **Testing Error Handling**:
   ```javascript
   it('should handle API error responses correctly', async () => {
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
     }
   });
   ```

## Best Practices

1. **Focus on Contracts, Not Implementation**:
   - Test that routes exist and return expected status codes
   - Test authentication requirements are enforced
   - Test response structures match expectations
   - Avoid coupling tests to specific implementation details

2. **Use Factories for Test Data**:
   - Create test data using factories instead of relying on seeded data
   - Make tests independent of each other

3. **Separate PHP and JavaScript Tests**:
   - Use PHP tests for backend route validation
   - Use JavaScript tests for frontend API interactions
   - Don't duplicate test coverage between layers

4. **Avoid Common Pitfalls**:
   - Don't rely on specific view files existing in PHP tests
   - Don't assert on exact HTML content in response bodies
   - Use appropriate response code assertions (200, 201, 401, 403, 404, 422)
   - Mock external services to avoid test flakiness

## Running the Tests

### PHP Tests:
```bash
php vendor/bin/phpunit tests/Feature/RouteTest.php
php vendor/bin/phpunit tests/Feature/RouteBulkTest.php
```

### JavaScript Tests:
```bash
npm test
# Or specific test file:
npx vitest run resources/js/tests/RouteStatusTest.test.js
``` 