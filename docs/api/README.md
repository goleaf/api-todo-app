# Todo Application API Documentation

This directory contains documentation for the Todo Application API endpoints.

## Authentication

All API endpoints (except for registration and login) require authentication using a Bearer token.

To authenticate your requests, include the following header:

```
Authorization: Bearer {your_api_token}
```

## Available Endpoints

- [Category Endpoints](./category-endpoints.md) - Endpoints for managing task categories
- [Task Endpoints](./task-endpoints.md) - Endpoints for managing tasks
- Tag Endpoints - Endpoints for managing tags
- Smart Tag Endpoints - Endpoints for managing smart tags
- Time Entry Endpoints - Endpoints for tracking time on tasks
- Attachment Endpoints - Endpoints for managing task attachments
- Dashboard Endpoints - Endpoints for fetching dashboard statistics

## API Response Format

All API responses follow a consistent format:

### Success Response

```json
{
  "success": true,
  "message": "Optional success message",
  "data": {
    // Response data
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message"
}
```

### Validation Error Response

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message for field"
    ]
  }
}
```

## HTTP Status Codes

- `200 OK`: The request was successful
- `201 Created`: A new resource was created successfully
- `400 Bad Request`: The request was invalid or could not be processed
- `401 Unauthorized`: Authentication failed or token expired
- `403 Forbidden`: The authenticated user does not have permission to access the resource
- `404 Not Found`: The requested resource was not found
- `422 Unprocessable Entity`: Validation errors occurred
- `500 Internal Server Error`: An error occurred on the server

## Authentication Endpoints

### Registration

- **URL**: `/api/register`
- **Method**: `POST`
- **Request Body**:
  - `name` (required): User's name
  - `email` (required): User's email address
  - `password` (required): User's password
  - `password_confirmation` (required): Password confirmation

### Login

- **URL**: `/api/login`
- **Method**: `POST`
- **Request Body**:
  - `email` (required): User's email address
  - `password` (required): User's password
  - `revoke_all` (optional): Whether to revoke all existing tokens (boolean)

### Logout

- **URL**: `/api/logout`
- **Method**: `POST`
- **Authentication**: Required

## Pagination

Endpoints that return collections of resources support pagination with the following query parameters:

- `per_page`: Number of items per page (default varies by endpoint, maximum 100)
- `page`: Page number (default: 1)

Example pagination response:

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      // Array of items
    ],
    "first_page_url": "http://todo.example.com/api/resource?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://todo.example.com/api/resource?page=5",
    "next_page_url": "http://todo.example.com/api/resource?page=2",
    "path": "http://todo.example.com/api/resource",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 70
  }
}
``` 