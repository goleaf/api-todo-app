# Category API Endpoints

This document provides information about the Category API endpoints available in the Todo application.

## Base URL

All endpoints are relative to the base API URL:

```
/api
```

## Authentication

All Category endpoints require authentication. Include the authentication token in the request header:

```
Authorization: Bearer {your_api_token}
```

## Endpoints

### List Categories

Retrieves all categories for the authenticated user.

- **URL**: `/categories`
- **Method**: `GET`
- **URL Parameters**:
  - `sort_by` (optional): Field to sort by (`name` or `created_at`). Default: `name`
  - `sort_direction` (optional): Sort direction (`asc` or `desc`). Default: `asc`
  - `with_task_count` (optional): Include task count for each category (`true` or `false`). Default: `false`

#### Example Request

```bash
curl -X GET "https://todo.example.com/api/categories?sort_by=name&sort_direction=asc&with_task_count=true" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Work",
      "color": "#ff5722",
      "icon": "briefcase",
      "user_id": 1,
      "created_at": "2023-05-01T12:00:00.000000Z",
      "updated_at": "2023-05-01T12:00:00.000000Z",
      "tasks_count": 5
    },
    {
      "id": 2,
      "name": "Personal",
      "color": "#2196f3",
      "icon": "user",
      "user_id": 1,
      "created_at": "2023-05-01T12:00:00.000000Z",
      "updated_at": "2023-05-01T12:00:00.000000Z",
      "tasks_count": 3
    }
  ]
}
```

### Create Category

Creates a new category.

- **URL**: `/categories`
- **Method**: `POST`
- **Request Body**:
  - `name` (required): Category name (string, max 255 characters)
  - `color` (optional): Hexadecimal color code (string, max 7 characters). Default: `#6b7280`
  - `icon` (optional): Icon name or identifier (string, max 255 characters)

#### Example Request

```bash
curl -X POST "https://todo.example.com/api/categories" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Shopping",
    "color": "#9c27b0",
    "icon": "shopping-cart"
  }'
```

#### Success Response

```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 3,
    "name": "Shopping",
    "color": "#9c27b0",
    "icon": "shopping-cart",
    "user_id": 1,
    "created_at": "2023-05-02T10:30:00.000000Z",
    "updated_at": "2023-05-02T10:30:00.000000Z"
  }
}
```

#### Error Responses

**Validation Error**:

```json
{
  "message": "The name field is required.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```

**Duplicate Name Error**:

```json
{
  "message": "The name has already been taken.",
  "errors": {
    "name": [
      "The name has already been taken."
    ]
  }
}
```

### Get Category

Retrieves a specific category by ID.

- **URL**: `/categories/{category_id}`
- **Method**: `GET`
- **URL Parameters**:
  - `with_task_count` (optional): Include task count (`true` or `false`). Default: `false`

#### Example Request

```bash
curl -X GET "https://todo.example.com/api/categories/1?with_task_count=true" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Work",
    "color": "#ff5722",
    "icon": "briefcase",
    "user_id": 1,
    "created_at": "2023-05-01T12:00:00.000000Z",
    "updated_at": "2023-05-01T12:00:00.000000Z",
    "tasks_count": 5
  }
}
```

#### Error Response

**Not Found or Unauthorized**:

```json
{
  "message": "This action is unauthorized."
}
```

### Update Category

Updates an existing category.

- **URL**: `/categories/{category_id}`
- **Method**: `PUT` or `PATCH`
- **Request Body**:
  - `name` (optional): Category name (string, max 255 characters)
  - `color` (optional): Hexadecimal color code (string, max 7 characters)
  - `icon` (optional): Icon name or identifier (string, max 255 characters)

#### Example Request

```bash
curl -X PUT "https://todo.example.com/api/categories/1" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Work Projects",
    "color": "#e91e63"
  }'
```

#### Success Response

```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": 1,
    "name": "Work Projects",
    "color": "#e91e63",
    "icon": "briefcase",
    "user_id": 1,
    "created_at": "2023-05-01T12:00:00.000000Z",
    "updated_at": "2023-05-02T11:15:00.000000Z"
  }
}
```

#### Error Responses

**Validation Error**:

```json
{
  "message": "The name has already been taken.",
  "errors": {
    "name": [
      "The name has already been taken."
    ]
  }
}
```

**Not Found or Unauthorized**:

```json
{
  "message": "This action is unauthorized."
}
```

### Delete Category

Deletes a category.

- **URL**: `/categories/{category_id}`
- **Method**: `DELETE`

#### Example Request

```bash
curl -X DELETE "https://todo.example.com/api/categories/3" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "message": "Category deleted successfully"
}
```

#### Error Response

**Not Found or Unauthorized**:

```json
{
  "message": "This action is unauthorized."
}
```

## Error Handling

All API endpoints return appropriate HTTP status codes:

- `200 OK`: The request was successful
- `201 Created`: A new resource was created successfully
- `400 Bad Request`: The request was invalid or could not be processed
- `401 Unauthorized`: Authentication failed or token expired
- `403 Forbidden`: The authenticated user does not have permission to access the resource
- `404 Not Found`: The requested resource was not found
- `422 Unprocessable Entity`: Validation errors occurred

## Notes

- When a category is deleted, tasks associated with that category are not deleted. Instead, their `category_id` is set to `null`.
- Category names must be unique for each user.
- The color field should be a valid hexadecimal color code (e.g., `#ff5722`). 