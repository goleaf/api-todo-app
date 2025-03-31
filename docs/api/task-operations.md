# Task API Operations

This document describes the task-related API operations available in the Todo application.

## General Information

All task endpoints are prefixed with `/api/tasks/` and require authentication.

## Available Endpoints

### List All Tasks

```
GET /api/tasks
```

Retrieves all tasks for the authenticated user.

**Query Parameters:**
- `completed` - Filter by completion status (true/false)
- `category_id` - Filter by category ID
- `priority` - Filter by priority (1-3)
- `search` - Search term to filter tasks by title/description
- `due_date` - Filter by due date (YYYY-MM-DD)
- `tag` - Filter by tag name
- `sort_by` - Field to sort by (default: 'created_at')
- `sort_dir` - Sort direction ('asc' or 'desc', default: 'desc')
- `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Complete project proposal",
      "description": "Draft the proposal for client review",
      "due_date": "2023-01-20",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 2,
      "progress": 25,
      "created_at": "2023-01-15T09:00:00.000000Z",
      "updated_at": "2023-01-15T09:00:00.000000Z",
      "category": {
        "id": 2,
        "name": "Work",
        "color": "#ff0000"
      }
    },
    {
      "id": 2,
      "title": "Buy groceries",
      "description": "Get items for dinner",
      "due_date": "2023-01-16",
      "completed": true,
      "user_id": 1,
      "category_id": 3,
      "priority": 1,
      "progress": 100,
      "created_at": "2023-01-15T09:15:00.000000Z",
      "updated_at": "2023-01-15T14:30:00.000000Z",
      "category": {
        "id": 3,
        "name": "Personal",
        "color": "#00ff00"
      }
    }
  ]
}
```

### Create New Task

```
POST /api/tasks
```

Creates a new task.

**Request:**

```json
{
  "title": "Call team meeting",
  "description": "Discuss project timeline",
  "due_date": "2023-01-22",
  "priority": 2,
  "category_id": 2,
  "tags": ["work", "meeting"]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 3,
    "title": "Call team meeting",
    "description": "Discuss project timeline",
    "due_date": "2023-01-22",
    "completed": false,
    "user_id": 1,
    "category_id": 2,
    "priority": 2,
    "progress": 0,
    "created_at": "2023-01-15T15:00:00.000000Z",
    "updated_at": "2023-01-15T15:00:00.000000Z",
    "category": {
      "id": 2,
      "name": "Work",
      "color": "#ff0000"
    },
    "tags": [
      {
        "id": 1,
        "name": "work",
        "color": "#ff0000"
      },
      {
        "id": 5,
        "name": "meeting",
        "color": "#0000ff"
      }
    ]
  }
}
```

### Get Task Statistics

```
GET /api/tasks/statistics
```

Retrieves task statistics for the authenticated user.

**Response:**

```json
{
  "success": true,
  "data": {
    "total": 10,
    "completed": 4,
    "incomplete": 6,
    "overdue": 2,
    "due_today": 1,
    "upcoming": 3,
    "completion_rate": 40,
    "by_priority": {
      "1": 3,
      "2": 5,
      "3": 2
    },
    "by_category": {
      "2": 6,
      "3": 4
    }
  }
}
```

### Get Tasks Due Today

```
GET /api/tasks/due-today
```

Retrieves all tasks due today.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 4,
      "title": "Submit expense report",
      "description": "Include receipts",
      "due_date": "2023-01-16",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 1,
      "created_at": "2023-01-15T10:00:00.000000Z",
      "updated_at": "2023-01-15T10:00:00.000000Z"
    }
  ]
}
```

### Get Overdue Tasks

```
GET /api/tasks/overdue
```

Retrieves all overdue tasks.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 5,
      "title": "Call client",
      "description": "Discuss project requirements",
      "due_date": "2023-01-14",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 3,
      "created_at": "2023-01-12T11:00:00.000000Z",
      "updated_at": "2023-01-12T11:00:00.000000Z"
    }
  ]
}
```

### Get Upcoming Tasks

```
GET /api/tasks/upcoming?days=5
```

Retrieves all upcoming tasks within the specified number of days.

**Query Parameters:**
- `days` - Number of days to look ahead (default: 7)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 6,
      "title": "Team retrospective",
      "description": "Review sprint accomplishments",
      "due_date": "2023-01-18",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 2,
      "created_at": "2023-01-15T11:30:00.000000Z",
      "updated_at": "2023-01-15T11:30:00.000000Z"
    }
  ]
}
```

### Get Task by ID

```
GET /api/tasks/{id}
```

Retrieves a specific task by ID.

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Complete project proposal",
    "description": "Draft the proposal for client review",
    "due_date": "2023-01-20",
    "completed": false,
    "user_id": 1,
    "category_id": 2,
    "priority": 2,
    "progress": 25,
    "created_at": "2023-01-15T09:00:00.000000Z",
    "updated_at": "2023-01-15T09:00:00.000000Z",
    "category": {
      "id": 2,
      "name": "Work",
      "color": "#ff0000"
    },
    "tags": [
      {
        "id": 1,
        "name": "work",
        "color": "#ff0000"
      }
    ]
  }
}
```

### Update Task

```
PUT /api/tasks/{id}
```

Updates an existing task.

**Request:**

```json
{
  "title": "Complete project proposal [UPDATED]",
  "description": "Draft the proposal for client review and get feedback",
  "priority": 3,
  "progress": 50,
  "tags": ["work", "important", "client"]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "title": "Complete project proposal [UPDATED]",
    "description": "Draft the proposal for client review and get feedback",
    "due_date": "2023-01-20",
    "completed": false,
    "user_id": 1,
    "category_id": 2,
    "priority": 3,
    "progress": 50,
    "created_at": "2023-01-15T09:00:00.000000Z",
    "updated_at": "2023-01-15T16:00:00.000000Z"
  }
}
```

### Delete Task

```
DELETE /api/tasks/{id}
```

Deletes a task.

**Response:**

```json
{
  "success": true,
  "message": "Task deleted successfully"
}
```

### Toggle Task Completion

```
PATCH /api/tasks/{id}/toggle
```

Toggles the completion status of a task.

**Response:**

```json
{
  "success": true,
  "message": "Task marked as complete",
  "data": {
    "id": 1,
    "completed": true,
    "completed_at": "2023-01-15T16:30:00.000000Z",
    "progress": 100
  }
}
```

### Get Task Tags

```
GET /api/tasks/{id}/tags
```

Retrieves all tags associated with a specific task.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "work",
      "color": "#ff0000"
    },
    {
      "id": 3,
      "name": "important",
      "color": "#ff9900"
    },
    {
      "id": 7,
      "name": "client",
      "color": "#0099ff"
    }
  ]
}
```

### Update Task Tags

```
PUT /api/tasks/{id}/tags
```

Replaces all tags for a task.

**Request:**

```json
{
  "tags": ["work", "meeting", "presentation"]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Task tags updated successfully",
  "data": [
    {
      "id": 1,
      "name": "work",
      "color": "#ff0000"
    },
    {
      "id": 5,
      "name": "meeting",
      "color": "#0000ff"
    },
    {
      "id": 9,
      "name": "presentation",
      "color": "#9900cc"
    }
  ]
}
```

### Bulk Tag Operation

```
POST /api/tasks/{id}/tags
```

Adds or removes tags from a task.

**Request:**

```json
{
  "operation": "add",
  "tags": ["deadline", "follow-up"]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tags added successfully",
  "data": [
    {
      "id": 1,
      "name": "work",
      "color": "#ff0000"
    },
    {
      "id": 5,
      "name": "meeting",
      "color": "#0000ff"
    },
    {
      "id": 9,
      "name": "presentation",
      "color": "#9900cc"
    },
    {
      "id": 10,
      "name": "deadline",
      "color": "#cc0000"
    },
    {
      "id": 11,
      "name": "follow-up",
      "color": "#00ccff"
    }
  ]
}
```

### Find Tasks by Tag

```
GET /api/tasks/by-tag/{tagName}
```

Retrieves all tasks with a specific tag.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Complete project proposal [UPDATED]",
      "description": "Draft the proposal for client review and get feedback",
      "due_date": "2023-01-20",
      "completed": true,
      "user_id": 1,
      "category_id": 2,
      "priority": 3,
      "created_at": "2023-01-15T09:00:00.000000Z",
      "updated_at": "2023-01-15T16:30:00.000000Z"
    },
    {
      "id": 7,
      "title": "Prepare quarterly report",
      "description": "Compile data for Q4",
      "due_date": "2023-01-25",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 2,
      "created_at": "2023-01-15T13:00:00.000000Z",
      "updated_at": "2023-01-15T13:00:00.000000Z"
    }
  ]
}
```

## Error Handling

All endpoints follow the standard API error response format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message for this field"]
  }
}
```

Common error codes:
- `422` - Validation error (e.g., missing required fields)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Task not found
- `500` - Server error

## Usage Notes

1. The `priority` field accepts values 1-3 (1: Low, 2: Medium, 3: High)
2. The `progress` field accepts values 0-100
3. When a task is marked as complete, its progress is automatically set to 100
4. When specifying tags, non-existent tags will be created automatically
5. Task due dates are optional but recommended for better organization
6. Category ID must correspond to an existing category owned by the user 