# Task API Endpoints

This document provides information about the Task API endpoints available in the Todo application.

## Base URL

All endpoints are relative to the base API URL:

```
/api
```

## Authentication

All Task endpoints require authentication. Include the authentication token in the request header:

```
Authorization: Bearer {your_api_token}
```

## Endpoints

### List Tasks

Retrieves tasks for the authenticated user with optional filtering and sorting.

- **URL**: `/tasks`
- **Method**: `GET`
- **URL Parameters**:
  - `completed` (optional): Filter by completion status (`true` or `false`)
  - `category_id` (optional): Filter by category ID
  - `priority` (optional): Filter by priority level (1-3)
  - `due_date` (optional): Filter by due date. Values: `today`, `week`, `overdue`, `no_date`
  - `tag_id` (optional): Filter by tag ID
  - `search` (optional): Search in title and description
  - `sort_by` (optional): Field to sort by. Options: `title`, `due_date`, `priority`, `created_at`
  - `sort_direction` (optional): Sort direction (`asc` or `desc`). Default: `asc`
  - `per_page` (optional): Number of items per page. Default: 15, Maximum: 100
  - `page` (optional): Page number. Default: 1

#### Example Request

```bash
curl -X GET "https://todo.example.com/api/tasks?completed=false&category_id=1&sort_by=due_date&sort_direction=asc" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "title": "Complete project proposal",
        "description": "Write and submit the project proposal for client review",
        "due_date": "2023-05-05",
        "priority": 3,
        "completed": false,
        "category_id": 1,
        "user_id": 1,
        "created_at": "2023-05-01T10:00:00.000000Z",
        "updated_at": "2023-05-01T10:00:00.000000Z",
        "category": {
          "id": 1,
          "name": "Work",
          "color": "#ff5722",
          "icon": "briefcase",
          "user_id": 1,
          "created_at": "2023-05-01T09:00:00.000000Z",
          "updated_at": "2023-05-01T09:00:00.000000Z"
        },
        "tags": [
          {
            "id": 1,
            "name": "Urgent",
            "color": "#f44336",
            "user_id": 1,
            "created_at": "2023-05-01T09:00:00.000000Z",
            "updated_at": "2023-05-01T09:00:00.000000Z",
            "pivot": {
              "task_id": 1,
              "tag_id": 1
            }
          }
        ]
      },
      // ... more tasks
    ],
    "first_page_url": "https://todo.example.com/api/tasks?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "https://todo.example.com/api/tasks?page=3",
    "next_page_url": "https://todo.example.com/api/tasks?page=2",
    "path": "https://todo.example.com/api/tasks",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 42
  }
}
```

### Create Task

Creates a new task.

- **URL**: `/tasks`
- **Method**: `POST`
- **Request Body**:
  - `title` (required): Task title (string, max 255 characters)
  - `description` (optional): Task description (string)
  - `due_date` (optional): Due date (YYYY-MM-DD)
  - `priority` (optional): Priority level (integer: 1=Low, 2=Medium, 3=High). Default: 2
  - `category_id` (optional): Category ID (integer)
  - `completed` (optional): Completion status (boolean). Default: false
  - `tags` (optional): Array of tag IDs

#### Example Request

```bash
curl -X POST "https://todo.example.com/api/tasks" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Prepare presentation slides",
    "description": "Create slides for the upcoming client meeting",
    "due_date": "2023-05-10",
    "priority": 2,
    "category_id": 1,
    "tags": [2, 3]
  }'
```

#### Success Response

```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 5,
    "title": "Prepare presentation slides",
    "description": "Create slides for the upcoming client meeting",
    "due_date": "2023-05-10",
    "priority": 2,
    "completed": false,
    "category_id": 1,
    "user_id": 1,
    "created_at": "2023-05-03T14:30:00.000000Z",
    "updated_at": "2023-05-03T14:30:00.000000Z",
    "category": {
      "id": 1,
      "name": "Work",
      "color": "#ff5722",
      "icon": "briefcase",
      "user_id": 1,
      "created_at": "2023-05-01T09:00:00.000000Z",
      "updated_at": "2023-05-01T09:00:00.000000Z"
    },
    "tags": [
      {
        "id": 2,
        "name": "Meeting",
        "color": "#4caf50",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 2
        }
      },
      {
        "id": 3,
        "name": "Client",
        "color": "#2196f3",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 3
        }
      }
    ]
  }
}
```

### Get Task

Retrieves a specific task by ID.

- **URL**: `/tasks/{task_id}`
- **Method**: `GET`

#### Example Request

```bash
curl -X GET "https://todo.example.com/api/tasks/5" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "data": {
    "id": 5,
    "title": "Prepare presentation slides",
    "description": "Create slides for the upcoming client meeting",
    "due_date": "2023-05-10",
    "priority": 2,
    "completed": false,
    "category_id": 1,
    "user_id": 1,
    "created_at": "2023-05-03T14:30:00.000000Z",
    "updated_at": "2023-05-03T14:30:00.000000Z",
    "category": {
      "id": 1,
      "name": "Work",
      "color": "#ff5722",
      "icon": "briefcase",
      "user_id": 1,
      "created_at": "2023-05-01T09:00:00.000000Z",
      "updated_at": "2023-05-01T09:00:00.000000Z"
    },
    "tags": [
      {
        "id": 2,
        "name": "Meeting",
        "color": "#4caf50",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 2
        }
      },
      {
        "id": 3,
        "name": "Client",
        "color": "#2196f3",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 3
        }
      }
    ]
  }
}
```

### Update Task

Updates an existing task.

- **URL**: `/tasks/{task_id}`
- **Method**: `PUT` or `PATCH`
- **Request Body**:
  - `title` (optional): Task title (string, max 255 characters)
  - `description` (optional): Task description (string)
  - `due_date` (optional): Due date (YYYY-MM-DD)
  - `priority` (optional): Priority level (integer: 1=Low, 2=Medium, 3=High)
  - `category_id` (optional): Category ID (integer)
  - `completed` (optional): Completion status (boolean)
  - `tags` (optional): Array of tag IDs

#### Example Request

```bash
curl -X PUT "https://todo.example.com/api/tasks/5" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Prepare presentation slides for client meeting",
    "priority": 3,
    "tags": [2, 3, 4]
  }'
```

#### Success Response

```json
{
  "success": true,
  "message": "Task updated successfully",
  "data": {
    "id": 5,
    "title": "Prepare presentation slides for client meeting",
    "description": "Create slides for the upcoming client meeting",
    "due_date": "2023-05-10",
    "priority": 3,
    "completed": false,
    "category_id": 1,
    "user_id": 1,
    "created_at": "2023-05-03T14:30:00.000000Z",
    "updated_at": "2023-05-03T15:00:00.000000Z",
    "category": {
      "id": 1,
      "name": "Work",
      "color": "#ff5722",
      "icon": "briefcase",
      "user_id": 1,
      "created_at": "2023-05-01T09:00:00.000000Z",
      "updated_at": "2023-05-01T09:00:00.000000Z"
    },
    "tags": [
      {
        "id": 2,
        "name": "Meeting",
        "color": "#4caf50",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 2
        }
      },
      {
        "id": 3,
        "name": "Client",
        "color": "#2196f3",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 3
        }
      },
      {
        "id": 4,
        "name": "Important",
        "color": "#ff9800",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 5,
          "tag_id": 4
        }
      }
    ]
  }
}
```

### Delete Task

Deletes a task.

- **URL**: `/tasks/{task_id}`
- **Method**: `DELETE`

#### Example Request

```bash
curl -X DELETE "https://todo.example.com/api/tasks/5" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "message": "Task deleted successfully"
}
```

### Toggle Task Completion

Toggles the completion status of a task.

- **URL**: `/tasks/{task_id}/toggle`
- **Method**: `PATCH`

#### Example Request

```bash
curl -X PATCH "https://todo.example.com/api/tasks/1/toggle" \
  -H "Authorization: Bearer {your_api_token}" \
  -H "Accept: application/json"
```

#### Success Response

```json
{
  "success": true,
  "message": "Task status toggled successfully",
  "data": {
    "id": 1,
    "title": "Complete project proposal",
    "description": "Write and submit the project proposal for client review",
    "due_date": "2023-05-05",
    "priority": 3,
    "completed": true,
    "category_id": 1,
    "user_id": 1,
    "created_at": "2023-05-01T10:00:00.000000Z",
    "updated_at": "2023-05-03T16:30:00.000000Z",
    "category": {
      "id": 1,
      "name": "Work",
      "color": "#ff5722",
      "icon": "briefcase",
      "user_id": 1,
      "created_at": "2023-05-01T09:00:00.000000Z",
      "updated_at": "2023-05-01T09:00:00.000000Z"
    },
    "tags": [
      {
        "id": 1,
        "name": "Urgent",
        "color": "#f44336",
        "user_id": 1,
        "created_at": "2023-05-01T09:00:00.000000Z",
        "updated_at": "2023-05-01T09:00:00.000000Z",
        "pivot": {
          "task_id": 1,
          "tag_id": 1
        }
      }
    ]
  }
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

- Priority levels: 1 = Low, 2 = Medium, 3 = High
- When updating a task's tags, the provided tag IDs will replace any existing tags
- Tasks are automatically loaded with their associated category and tags 