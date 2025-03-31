# Hypervel API Endpoints

This document provides documentation for the Hypervel-powered API endpoints that enable concurrent data fetching and processing.

## Authentication

All endpoints require authentication using Laravel Sanctum. Include a bearer token in the `Authorization` header.

```
Authorization: Bearer {your_token}
```

## Endpoints

### Get Dashboard Data

Concurrently fetches dashboard data for the authenticated user.

```
GET /api/todos/dashboard
```

#### Response

```json
{
  "success": true,
  "data": {
    "stats": {
      "total": 42,
      "completed": 18,
      "pending": 24,
      "overdue": 3,
      "due_today": 5,
      "high_priority": 7
    },
    "recent": [
      {
        "id": 42,
        "title": "Finish project proposal",
        "completed": false,
        "created_at": "2023-12-01T14:30:00.000000Z",
        "priority": "high",
        "category": "work"
      },
      // ... more todos
    ],
    "upcoming": [
      {
        "id": 45,
        "title": "Team meeting",
        "due_date": "2023-12-02T10:00:00.000000Z",
        "priority": "medium",
        "category": "work"
      },
      // ... more todos
    ]
  }
}
```

### Batch Process Todos

Processes multiple operations on todos concurrently.

```
POST /api/todos/batch
```

#### Request Body

```json
{
  "operations": [
    {
      "id": 1,
      "action": "complete"
    },
    {
      "id": 2,
      "action": "prioritize",
      "data": {
        "priority": "high"
      }
    },
    {
      "id": 3,
      "action": "categorize",
      "data": {
        "category": "work"
      }
    },
    {
      "id": 4,
      "action": "schedule",
      "data": {
        "due_date": "2023-12-15"
      }
    },
    {
      "id": 5,
      "action": "delete"
    }
  ]
}
```

#### Response

```json
{
  "success": true,
  "results": {
    "1": {
      "success": true,
      "message": "Todo marked as completed"
    },
    "2": {
      "success": true,
      "message": "Todo priority set to high"
    },
    "3": {
      "success": true,
      "message": "Todo category updated"
    },
    "4": {
      "success": true,
      "message": "Todo due date updated"
    },
    "5": {
      "success": true,
      "message": "Todo deleted"
    }
  }
}
```

### Fetch Multiple Todos

Fetches data for multiple todos concurrently.

```
POST /api/todos/fetch-multiple
```

#### Request Body

```json
{
  "ids": [1, 2, 3, 4, 5]
}
```

#### Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Complete project",
      "description": "Finish the project by end of month",
      "completed": false,
      "priority": "high",
      "category": "work",
      "due_date": "2023-12-31T23:59:59.000000Z",
      "created_at": "2023-12-01T10:00:00.000000Z",
      "updated_at": "2023-12-01T10:00:00.000000Z"
    },
    // ... more todos
  ]
}
```

## Error Handling

All endpoints return a standardized error response format:

```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error description"
}
```

Common error status codes:

- `400 Bad Request`: Invalid request format
- `401 Unauthorized`: Missing or invalid authentication
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Todo not found
- `500 Internal Server Error`: Server error during processing

## Performance Notes

These endpoints use Hypervel to process operations concurrently, resulting in significant performance improvements:

1. The dashboard endpoint runs 6 database queries in parallel
2. Batch processing handles up to 10 operations concurrently per batch
3. Fetching multiple todos processes all requests in parallel

For large batch operations (more than 10 items), the system automatically chunks the requests to maintain optimal performance. 