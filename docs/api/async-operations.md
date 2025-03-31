# Asynchronous API Operations

This document describes the asynchronous API operations available in the Todo application.

## General Information

The async endpoints are designed for operations that may take longer to process or that benefit from parallel execution. All endpoints are prefixed with `/api/async/` and require authentication.

## Available Endpoints

### Dashboard Statistics

```
GET /api/async/dashboard-stats
```

Returns statistics for the dashboard display.

**Response:**

```json
{
  "success": true,
  "data": {
    "tasks_count": 45,
    "completed_tasks": 20,
    "overdue_tasks": 5,
    "categories_count": 8,
    "users_count": 15
  }
}
```

### External APIs

```
GET /api/async/external-apis
```

Fetches data from external APIs (simulated).

**Response:**

```json
{
  "success": true,
  "data": {
    "weather": {
      "temp": 28,
      "condition": "Sunny"
    },
    "news": {
      "items": 7,
      "source": "BBC"
    },
    "stock": {
      "value": 32.5,
      "change": "2.5%"
    }
  }
}
```

### Bulk Process Tasks

```
POST /api/async/process-tasks
```

Process multiple tasks in a single operation.

**Request:**

```json
{
  "task_ids": [1, 2, 3, 4, 5],
  "action": "complete"
}
```

Supported actions:
- `complete` - Mark tasks as complete
- `delete` - Delete tasks
- `archive` - Archive tasks

**Response:**

```json
{
  "success": true,
  "data": {
    "processed": [
      {
        "id": 1,
        "success": true,
        "action": "complete",
        "message": "Successfully processed"
      },
      {
        "id": 2,
        "success": true,
        "action": "complete",
        "message": "Successfully processed"
      }
    ],
    "total": 5,
    "success_count": 5
  }
}
```

### Batch Tag Operation

```
POST /api/async/batch-tag-operation
```

Add or remove tags from multiple tasks at once.

**Request:**

```json
{
  "task_ids": [1, 2, 3, 4, 5],
  "tags": ["important", "work", "project-x"],
  "operation": "add"
}
```

Supported operations:
- `add` - Add the specified tags to all tasks
- `remove` - Remove the specified tags from all tasks

**Response:**

```json
{
  "success": true,
  "data": {
    "processed": [
      {
        "id": 1,
        "success": true,
        "message": "Tags added successfully"
      },
      {
        "id": 2,
        "success": true,
        "message": "Tags added successfully"
      }
    ],
    "total": 5,
    "success_count": 5,
    "operation": "add",
    "tags": ["important", "work", "project-x"]
  }
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
- `422` - Validation error (e.g., invalid task IDs, missing required fields)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Resource not found
- `500` - Server error

## Usage Notes

1. All async endpoints require a valid authentication token
2. The batch operations accept a maximum of 100 tasks at once
3. For optimal performance, keep tag names reasonably short (max 50 characters)
4. Non-existent tasks in bulk operations will result in a validation error
5. Use these endpoints for operations that affect multiple resources at once 