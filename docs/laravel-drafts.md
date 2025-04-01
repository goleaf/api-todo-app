# Laravel Drafts Integration

This documentation outlines how the Laravel Drafts package has been integrated into our application to provide versioning and draft capabilities for our models.

## Overview

Laravel Drafts is a package that allows models to be saved as drafts before being published. It provides the following features:

- Save models as drafts that won't be visible to the public
- Publish drafts when they're ready
- Manage revisions of published models
- Keep a history of changes

## Installation

The package has been installed via Composer:

```bash
composer require oddvalue/laravel-drafts
```

The configuration file has been published:

```bash
php artisan vendor:publish --tag="drafts-config"
```

## Configuration

The package is configured in `config/drafts.php` with the following options:

```php
return [
    'revisions' => [
        'keep' => 10, // Maximum number of revisions to keep
    ],

    'column_names' => [
        // Boolean column that marks a row as the current version of the data for editing
        'is_current' => 'is_current',

        // Boolean column that marks a row as live and displayable to the public
        'is_published' => 'is_published',

        // Timestamp column that stores the date and time when the row was published
        'published_at' => 'published_at',

        // UUID column that stores the unique identifier of the model drafts
        'uuid' => 'uuid',

        // Name of the morph relationship to the publishing user
        'publisher_morph_name' => 'publisher',
    ],

    'auth' => [
        // The guard to fetch the logged-in user from for the publisher relation
        'guard' => 'web',
    ],
];
```

## Database Setup

For each model that uses drafts, the following columns have been added:

- `is_current` - Boolean column that marks a row as the current version of the data for editing
- `is_published` - Boolean column that marks a row as live and displayable to the public
- `published_at` - Timestamp column that stores the date and time when the row was published
- `uuid` - UUID column that stores the unique identifier of the model drafts
- `publisher_type` - The type of the publisher (usually the User model)
- `publisher_id` - The ID of the publisher

The migration helper methods `$table->drafts()` and `$table->dropDrafts()` have been used to add and remove these columns.

Example migration:

```php
Schema::table('posts', function (Blueprint $table) {
    $table->drafts();
});

Schema::table('posts', function (Blueprint $table) {
    $table->dropDrafts();
});
```

## Model Implementation

To make a model use drafts, we've added the `HasDrafts` trait and defined the draftable relations:

```php
use Illuminate\Database\Eloquent\Model;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;

class Post extends Model
{
    use HasDrafts;

    // Define relations that should be copied when publishing drafts
    protected array $draftableRelations = [];
}
```

## Usage Examples

### Creating a New Draft

```php
// Method 1: Using createDraft
$draft = Post::createDraft([
    'title' => 'My Draft Post',
    'content' => 'This is a draft',
    'slug' => 'my-draft-post',
    'user_id' => Auth::id()
]);

// Method 2: Using saveAsDraft
$post = new Post();
$post->title = 'My Draft Post';
$post->content = 'This is a draft';
$post->slug = 'my-draft-post';
$post->user_id = Auth::id();
$post->saveAsDraft();

// Method 3: Setting is_published to false
Post::create([
    'title' => 'My Draft Post',
    'content' => 'This is a draft',
    'slug' => 'my-draft-post',
    'user_id' => Auth::id(),
    'is_published' => false
]);
```

### Updating a Draft

```php
// Method 1: Using updateAsDraft
$post->updateAsDraft([
    'title' => 'Updated Draft Title',
    'content' => 'Updated draft content',
]);

// Method 2: Using saveAsDraft
$post->title = 'Updated Draft Title';
$post->content = 'Updated draft content';
$post->saveAsDraft();
```

### Publishing a Draft

```php
// Publish a draft
$draft = Post::onlyDrafts()->find($id);
$published = $draft->publish();
```

### Querying Models

The package adds scopes to query published records or drafts:

```php
// Get only published/live records (default behavior)
$posts = Post::all();
$posts = Post::published()->get();
$posts = Post::withoutDrafts()->get();

// Include drafts in the query
$posts = Post::withDrafts()->get();

// Get only drafts
$drafts = Post::onlyDrafts()->get();
```

### Preview Mode

Drafts implementation includes a preview mode to view drafts without publishing:

```php
// Enable preview mode
\Oddvalue\LaravelDrafts\Facades\LaravelDrafts::previewMode(true);

// Disable preview mode
\Oddvalue\LaravelDrafts\Facades\LaravelDrafts::previewMode(false);
\Oddvalue\LaravelDrafts\Facades\LaravelDrafts::disablePreviewMode();
```

You can also use the `WithDraftsMiddleware` to enable preview mode for specific routes:

```php
Route::get('/posts/preview/{post}', [PostController::class, 'preview'])
    ->middleware(\Oddvalue\LaravelDrafts\Http\Middleware\WithDraftsMiddleware::class);

// Or use the helper method
Route::withDrafts(function () {
    Route::get('/posts/preview/{post}', [PostController::class, 'preview']);
});
```

## API Endpoints

The following API endpoints have been implemented for the Posts model:

### Public Endpoints

- **GET /api/posts**: Get a paginated list of published posts
  - Response: List of published posts

- **GET /api/posts/{slug}**: Get a specific published post by slug
  - Response: Post details

### Protected Endpoints (Requires Authentication)

- **GET /api/posts/drafts/list**: Get a paginated list of user's draft posts
  - Response: List of draft posts for the authenticated user

- **GET /api/posts/drafts/{id}**: Get a specific draft post by ID
  - Response: Draft post details
  - Authentication: User must own the draft

- **POST /api/posts**: Create a new post (published or draft)
  - Request body:
    - `title`: Title of the post (required)
    - `content`: Content of the post (required)
    - `publish`: Boolean flag indicating whether to publish or save as draft (optional)
  - Response: Created post or draft

- **PUT /api/posts/{id}**: Update an existing post
  - Request body:
    - `title`: Title of the post (required)
    - `content`: Content of the post (required)
    - `publish`: Boolean flag indicating whether to publish or save as draft (optional)
  - Response: Updated post or draft
  - Authentication: User must own the post

- **POST /api/posts/drafts/{id}/publish**: Publish a draft post
  - Response: Published post
  - Authentication: User must own the draft

- **DELETE /api/posts/{id}**: Delete a post or draft
  - Response: Success message
  - Authentication: User must own the post

## Best Practices

1. **Always Check Ownership**: Ensure that users can only access, modify, or publish their own drafts.

2. **Use Middleware for Preview**: Use the `WithDraftsMiddleware` when implementing preview functionality.

3. **Handle Relations Properly**: If your model has relations that should be copied when publishing, define them in the `$draftableRelations` property.

4. **Manage Revisions**: Be aware of the `keep` configuration option that limits the number of revisions kept.

5. **Clear Cache**: If you're caching queries, make sure to invalidate cache when drafts are published.

## Troubleshooting

### Common Issues

1. **Drafts not showing in queries**: Make sure you're using the appropriate scope (`withDrafts()` or `onlyDrafts()`).

2. **Can't publish draft**: Ensure the user has the necessary permissions to publish the draft.

3. **Relations not being copied**: Check that relations are properly defined in the `$draftableRelations` property.

4. **Database errors**: Verify that all required columns have been added to the table with the `$table->drafts()` method.

### Debugging

Enable query logging to see the SQL queries being executed:

```php
\DB::enableQueryLog();
// Execute your query
dd(\DB::getQueryLog());
```

## Conclusion

The Laravel Drafts package provides a flexible and powerful way to implement draft functionality in our application. By following the guidelines in this documentation, you can effectively work with drafts and revisions in your application. 