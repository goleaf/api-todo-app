# Tag API Operations

This document describes the tag-related API operations available in the Todo application.

## General Information

All tag endpoints are prefixed with `/api/tags/` and require authentication.

## Available Endpoints

### List All Tags

```
GET /api/tags
```

Retrieves all tags for the authenticated user.

**Query Parameters:**
- `sort_by` - Field to sort by (default: 'name')
- `sort_dir` - Sort direction ('asc' or 'desc', default: 'asc')
- `search` - Search term to filter tags by name
- `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Work",
      "color": "#ff0000",
      "user_id": 1,
      "usage_count": 5,
      "created_at": "2023-01-15T08:30:00.000000Z",
      "updated_at": "2023-01-15T09:45:00.000000Z"
    },
    {
      "id": 2,
      "name": "Personal",
      "color": "#00ff00",
      "user_id": 1,
      "usage_count": 3,
      "created_at": "2023-01-15T08:35:00.000000Z",
      "updated_at": "2023-01-15T09:50:00.000000Z"
    }
  ]
}
```

### Create New Tag

```
POST /api/tags
```

Creates a new tag.

**Request:**

```json
{
  "name": "Important",
  "color": "#ff9900"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tag created successfully",
  "data": {
    "id": 3,
    "name": "Important",
    "color": "#ff9900",
    "user_id": 1,
    "usage_count": 0,
    "created_at": "2023-01-15T10:00:00.000000Z",
    "updated_at": "2023-01-15T10:00:00.000000Z"
  }
}
```

### Get Popular Tags

```
GET /api/tags/popular?limit=5
```

Retrieves the most frequently used tags.

**Query Parameters:**
- `limit` - Number of tags to return (default: 10)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Work",
      "color": "#ff0000",
      "usage_count": 5
    },
    {
      "id": 2,
      "name": "Personal",
      "color": "#00ff00",
      "usage_count": 3
    }
  ]
}
```

### Get Tag by ID

```
GET /api/tags/{id}
```

Retrieves a specific tag by ID.

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Work",
    "color": "#ff0000",
    "user_id": 1,
    "usage_count": 5,
    "created_at": "2023-01-15T08:30:00.000000Z",
    "updated_at": "2023-01-15T09:45:00.000000Z"
  }
}
```

### Update Tag

```
PUT /api/tags/{id}
```

Updates an existing tag.

**Request:**

```json
{
  "name": "Work-Related",
  "color": "#ff3333"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tag updated successfully",
  "data": {
    "id": 1,
    "name": "Work-Related",
    "color": "#ff3333",
    "user_id": 1,
    "usage_count": 5,
    "created_at": "2023-01-15T08:30:00.000000Z",
    "updated_at": "2023-01-15T10:15:00.000000Z"
  }
}
```

### Delete Tag

```
DELETE /api/tags/{id}
```

Deletes a tag and removes it from all associated tasks.

**Response:**

```json
{
  "success": true,
  "message": "Tag deleted successfully"
}
```

### Get Tasks for Tag

```
GET /api/tags/{id}/tasks
```

Retrieves all tasks associated with a specific tag.

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 10,
      "title": "Complete project proposal",
      "description": "Draft the proposal for client review",
      "due_date": "2023-01-20",
      "completed": false,
      "user_id": 1,
      "category_id": 2,
      "priority": 2,
      "created_at": "2023-01-15T09:00:00.000000Z",
      "updated_at": "2023-01-15T09:00:00.000000Z"
    },
    {
      "id": 11,
      "title": "Schedule team meeting",
      "description": "Coordinate with team members for weekly sync",
      "due_date": "2023-01-18",
      "completed": true,
      "user_id": 1,
      "category_id": 2,
      "priority": 1,
      "created_at": "2023-01-15T09:05:00.000000Z",
      "updated_at": "2023-01-15T09:05:00.000000Z"
    }
  ]
}
```

### Get Task Counts by Tag

```
GET /api/tags/task-counts
```

Retrieves the number of tasks associated with each tag.

**Response:**

```json
{
  "success": true,
  "data": {
    "1": 5,
    "2": 3,
    "3": 0
  }
}
```

### Merge Tags

```
POST /api/tags/merge
```

Merges two tags, moving all tasks from the source tag to the target tag.

**Request:**

```json
{
  "source_tag_id": 2,
  "target_tag_id": 1
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tags merged successfully",
  "data": {
    "merged_task_count": 3,
    "target_tag": {
      "id": 1,
      "name": "Work-Related",
      "color": "#ff3333",
      "usage_count": 8
    }
  }
}
```

### Get Tag Suggestions

```
GET /api/tags/suggestions?query=wor&limit=5
```

Retrieves tag suggestions based on a partial name match.

**Query Parameters:**
- `query` - Partial tag name to search for
- `limit` - Maximum number of suggestions to return (default: 10)

**Response:**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Work-Related",
      "color": "#ff3333"
    },
    {
      "id": 5,
      "name": "Workout",
      "color": "#9900ff"
    }
  ]
}
```

### Batch Create Tags

```
POST /api/tags/batch
```

Creates multiple tags in a single operation.

**Request:**

```json
{
  "tags": [
    {"name": "Urgent", "color": "#ff0000"},
    {"name": "Low-Priority", "color": "#00ff00"},
    {"name": "Meeting", "color": "#0000ff"}
  ]
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tags created successfully",
  "data": [
    {
      "id": 6,
      "name": "Urgent",
      "color": "#ff0000",
      "user_id": 1,
      "usage_count": 0
    },
    {
      "id": 7,
      "name": "Low-Priority",
      "color": "#00ff00",
      "user_id": 1,
      "usage_count": 0
    },
    {
      "id": 8,
      "name": "Meeting",
      "color": "#0000ff",
      "user_id": 1,
      "usage_count": 0
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
- `422` - Validation error (e.g., missing required fields, invalid color format)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (insufficient permissions)
- `404` - Tag not found
- `409` - Conflict (e.g., attempting to create a duplicate tag)
- `500` - Server error

## Usage Notes

1. Tag names must be unique per user
2. Colors should be provided in hexadecimal format (e.g., "#ff0000")
3. The default color will be generated from the tag name if not specified
4. When deleting a tag, it is automatically removed from all associated tasks
5. Tag usage counts are automatically updated when tasks are tagged or untagged 