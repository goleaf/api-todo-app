<?php

use App\Http\Controllers\Api\V1\AsyncApiController;
use App\Http\Controllers\Api\V1\AuthApiController;
use App\Http\Controllers\Api\V1\CategoryApiController;
use App\Http\Controllers\Api\V1\DashboardApiController;
use App\Http\Controllers\Api\V1\ProfileApiController;
use App\Http\Controllers\Api\V1\TaskApiController;
use App\Http\Controllers\Api\V1\UserApiController;
use App\Http\Controllers\Api\V1\TagApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\V1\TaskAnalyticsController;
use Lomkit\Rest\Facades\Rest;
use App\Rest\Controllers\TasksController;
use App\Rest\Controllers\CategoriesController;
use App\Rest\Controllers\TagsController;
use App\Rest\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Documentation route
Route::get('/documentation', function () {
    return response()->json([
        'success' => true,
        'message' => 'API documentation available at /api/docs',
        'data' => [
            'version' => '1.0',
            'endpoints' => [
                'auth' => ['/register', '/login', '/logout', '/me', '/refresh'],
                'users' => ['/users', '/users/{id}', '/users/statistics'],
                'tasks' => ['/tasks', '/tasks/{id}', '/tasks/statistics', '/tasks/{id}/tags', '/tasks/{id}/tags/bulk', '/tasks/by-tag/{tagName}'],
                'categories' => ['/categories', '/categories/{id}', '/categories/task-counts'],
                'tags' => ['/tags', '/tags/{id}', '/tags/popular', '/tags/task-counts', '/tags/{id}/tasks', '/tags/merge', '/tags/suggestions', '/tags/batch'],
                'profile' => ['/profile', '/profile/password', '/profile/photo'],
                'dashboard' => ['/dashboard'],
                'async' => ['/async/dashboard-stats', '/async/external-apis', '/async/process-tasks', '/async/batch-tag-operation'],
                'rest' => ['/rest/docs', '/rest/tasks', '/rest/categories', '/rest/tags', '/rest/users'],
            ],
        ],
    ])->name('api.documentation');
});

// Public routes
Route::post('/register', [AuthApiController::class, 'register'])->name('api.auth.register');
Route::post('/login', [AuthApiController::class, 'login'])->name('api.auth.login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.auth.logout');
    Route::post('/refresh', [AuthApiController::class, 'refresh'])->name('api.auth.refresh');
    Route::get('/me', [AuthApiController::class, 'me'])->name('api.auth.me');

    // User routes - full CRUD (using REST API controllers)
    Route::prefix('users')->group(function () {
        Route::get('/', function() {
            return app()->make(UsersController::class)->details();
        })->name('api.users.index');
        
        Route::post('/', function(Illuminate\Http\Request $request) {
            return app()->make(UsersController::class)->mutate($request);
        })->name('api.users.store');
        
        Route::get('/statistics', function() {
            // Special route, kept from original implementation
            return app()->make(UsersController::class)->operation('statistics');
        })->name('api.users.statistics');
        
        Route::get('/{id}', function($id) {
            return app()->make(UsersController::class)->details(['id' => $id]);
        })->name('api.users.show');
        
        Route::put('/{id}', function(Illuminate\Http\Request $request, $id) {
            $request->merge(['update' => [['id' => $id] + $request->all()]]);
            return app()->make(UsersController::class)->mutate($request);
        })->name('api.users.update');
        
        Route::delete('/{id}', function($id) {
            return app()->make(UsersController::class)->destroy(['primaryKeys' => [$id]]);
        })->name('api.users.destroy');
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileApiController::class, 'show'])->name('api.profile.show');
        Route::put('/', [ProfileApiController::class, 'update'])->name('api.profile.update');
        Route::put('/password', [ProfileApiController::class, 'updatePassword'])->name('api.profile.update-password');
        Route::post('/photo', [ProfileApiController::class, 'uploadPhoto'])->name('api.profile.upload-photo');
        Route::delete('/photo', [ProfileApiController::class, 'deletePhoto'])->name('api.profile.delete-photo');
    });

    // Task routes - full CRUD (using REST API controllers)
    Route::prefix('tasks')->group(function () {
        Route::get('/', function() {
            return app()->make(TasksController::class)->details();
        })->name('api.tasks.index');
        
        Route::post('/', function(Illuminate\Http\Request $request) {
            return app()->make(TasksController::class)->mutate($request);
        })->name('api.tasks.store');
        
        Route::get('/statistics', function() {
            // Special route, kept from original implementation
            return app()->make(TasksController::class)->operation('statistics');
        })->name('api.tasks.statistics');
        
        Route::get('/due-today', function(Illuminate\Http\Request $request) {
            $request->merge(['scopes' => [['name' => 'dueToday']]]);
            return app()->make(TasksController::class)->search($request);
        })->name('api.tasks.due-today');
        
        Route::get('/overdue', function(Illuminate\Http\Request $request) {
            $request->merge(['scopes' => [['name' => 'overdue']]]);
            return app()->make(TasksController::class)->search($request);
        })->name('api.tasks.overdue');
        
        Route::get('/upcoming', function(Illuminate\Http\Request $request) {
            $request->merge(['scopes' => [['name' => 'upcoming']]]);
            return app()->make(TasksController::class)->search($request);
        })->name('api.tasks.upcoming');
        
        Route::get('/by-tag/{tagName}', function(Illuminate\Http\Request $request, $tagName) {
            $request->merge(['scopes' => [['name' => 'withTag', 'parameters' => [$tagName]]]]);
            return app()->make(TasksController::class)->search($request);
        })->name('api.tasks.by-tag');
        
        Route::get('/{id}', function($id) {
            return app()->make(TasksController::class)->details(['id' => $id]);
        })->name('api.tasks.show');
        
        Route::put('/{id}', function(Illuminate\Http\Request $request, $id) {
            $request->merge(['update' => [['id' => $id] + $request->all()]]);
            return app()->make(TasksController::class)->mutate($request);
        })->name('api.tasks.update');
        
        Route::delete('/{id}', function($id) {
            return app()->make(TasksController::class)->destroy(['primaryKeys' => [$id]]);
        })->name('api.tasks.destroy');
        
        Route::patch('/{id}/toggle', function($id) {
            return app()->make(TasksController::class)->operation('toggle', ['id' => $id]);
        })->name('api.tasks.toggle');
        
        Route::get('/{id}/tags', function($id) {
            return app()->make(TasksController::class)->relation('tags', ['id' => $id]);
        })->name('api.tasks.tags');
        
        Route::put('/{id}/tags', function(Illuminate\Http\Request $request, $id) {
            return app()->make(TasksController::class)->operation('updateTags', ['id' => $id, 'tags' => $request->all()]);
        })->name('api.tasks.update-tags');
        
        Route::post('/{id}/tags', function(Illuminate\Http\Request $request, $id) {
            return app()->make(TasksController::class)->operation('bulkTagOperation', ['id' => $id] + $request->all());
        })->name('api.tasks.bulk-tag-operation');
    });

    // Category routes - full CRUD (using REST API controllers)
    Route::prefix('categories')->group(function () {
        Route::get('/', function() {
            return app()->make(CategoriesController::class)->details();
        })->name('api.categories.index');
        
        Route::post('/', function(Illuminate\Http\Request $request) {
            return app()->make(CategoriesController::class)->mutate($request);
        })->name('api.categories.store');
        
        Route::get('/task-counts', function() {
            return app()->make(CategoriesController::class)->operation('taskCounts');
        })->name('api.categories.task-counts');
        
        Route::get('/{id}', function($id) {
            return app()->make(CategoriesController::class)->details(['id' => $id]);
        })->name('api.categories.show');
        
        Route::put('/{id}', function(Illuminate\Http\Request $request, $id) {
            $request->merge(['update' => [['id' => $id] + $request->all()]]);
            return app()->make(CategoriesController::class)->mutate($request);
        })->name('api.categories.update');
        
        Route::delete('/{id}', function($id) {
            return app()->make(CategoriesController::class)->destroy(['primaryKeys' => [$id]]);
        })->name('api.categories.destroy');
    });

    // Tag routes - full CRUD (using REST API controllers)
    Route::prefix('tags')->group(function () {
        Route::get('/', function() {
            return app()->make(TagsController::class)->details();
        })->name('api.tags.index');
        
        Route::post('/', function(Illuminate\Http\Request $request) {
            return app()->make(TagsController::class)->mutate($request);
        })->name('api.tags.store');
        
        Route::get('/popular', function(Illuminate\Http\Request $request) {
            $request->merge(['sorts' => [['field' => 'usage_count', 'direction' => 'desc']], 'limit' => 10]);
            return app()->make(TagsController::class)->search($request);
        })->name('api.tags.popular');
        
        Route::get('/task-counts', function() {
            return app()->make(TagsController::class)->operation('taskCounts');
        })->name('api.tags.task-counts');
        
        Route::post('/merge', function(Illuminate\Http\Request $request) {
            return app()->make(TagsController::class)->operation('merge', $request->all());
        })->name('api.tags.merge');
        
        Route::get('/suggestions', function(Illuminate\Http\Request $request) {
            return app()->make(TagsController::class)->operation('suggestions', ['query' => $request->get('query')]);
        })->name('api.tags.suggestions');
        
        Route::post('/batch', function(Illuminate\Http\Request $request) {
            return app()->make(TagsController::class)->operation('batchCreate', $request->all());
        })->name('api.tags.batch-create');
        
        Route::get('/{id}', function($id) {
            return app()->make(TagsController::class)->details(['id' => $id]);
        })->name('api.tags.show');
        
        Route::put('/{id}', function(Illuminate\Http\Request $request, $id) {
            $request->merge(['update' => [['id' => $id] + $request->all()]]);
            return app()->make(TagsController::class)->mutate($request);
        })->name('api.tags.update');
        
        Route::delete('/{id}', function($id) {
            return app()->make(TagsController::class)->destroy(['primaryKeys' => [$id]]);
        })->name('api.tags.destroy');
        
        Route::get('/{id}/tasks', function($id) {
            return app()->make(TagsController::class)->relation('tasks', ['id' => $id]);
        })->name('api.tags.tasks');
    });

    // Dashboard routes
    Route::get('/dashboard', [DashboardApiController::class, 'index'])->name('api.dashboard.index');
});

// API fallback - 404 for invalid routes
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'errors' => ['endpoint' => 'The requested endpoint does not exist'],
    ], 404);
});

/*
|--------------------------------------------------------------------------
| Hypervel Async API Routes
|--------------------------------------------------------------------------
|
| These routes demonstrate the use of Hypervel for asynchronous API operations.
| They use the AsyncApiController which leverages coroutines for concurrent
| processing and improved performance.
|
*/

Route::middleware('auth:sanctum')->prefix('async')->group(function () {
    Route::get('/dashboard-stats', [AsyncApiController::class, 'getDashboardStats'])->name('api.async.dashboard-stats');
    Route::get('/external-apis', [AsyncApiController::class, 'fetchExternalApis'])->name('api.async.external-apis');
    Route::post('/process-tasks', [AsyncApiController::class, 'bulkProcessTasks'])->name('api.async.process-tasks');
    Route::post('/batch-tag-operation', [AsyncApiController::class, 'batchTagOperation'])->name('api.async.batch-tag-operation');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'login'])->name('login');
    
    Route::middleware(['auth:sanctum', 'admin.api'])->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'logoutAll'])->name('logout.all');
        Route::get('/user', [\App\Http\Controllers\Api\Admin\AdminAuthController::class, 'user'])->name('user');
    });
});

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'admin.api'])->group(function () {
    // Users management
    Route::apiResource('users', \App\Http\Controllers\Api\Admin\UsersApiController::class);
    Route::post('users/{user}/toggle-active', [\App\Http\Controllers\Api\Admin\UsersApiController::class, 'toggleActive']);
    Route::get('users/{user}/statistics', [\App\Http\Controllers\Api\Admin\UsersApiController::class, 'statistics']);
    
    // Dashboard stats
    Route::get('/dashboard/chart-data', [\App\Http\Controllers\Api\Admin\DashboardApiController::class, 'getChartData']);
});

// Comments API routes
Route::middleware('auth:sanctum')->prefix('comments')->name('comments.')->group(function () {
    Route::get('/', [CommentController::class, 'index'])->name('index');
    Route::post('/', [CommentController::class, 'store'])->name('store');
    Route::get('/{comment}', [CommentController::class, 'show'])->name('show');
    Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
});

// Regex Helper Routes
Route::prefix('regex')->group(function () {
    Route::post('/validate-email', [App\Http\Controllers\Api\RegexController::class, 'validateEmail']);
    Route::post('/extract-data', [App\Http\Controllers\Api\RegexController::class, 'extractData']);
    Route::post('/validate', [App\Http\Controllers\Api\RegexController::class, 'validateValue']);
    Route::post('/transform', [App\Http\Controllers\Api\RegexController::class, 'transform']);
    Route::post('/extract-custom', [App\Http\Controllers\Api\RegexController::class, 'extractCustom']);
});

// Model Info Routes
Route::prefix('models')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\ModelInfoController::class, 'index'])->name('api.models.index');
    Route::get('/{model}', [App\Http\Controllers\Api\ModelInfoController::class, 'show'])->name('api.models.show');
    Route::get('/{model}/attributes', [App\Http\Controllers\Api\ModelInfoController::class, 'attributes'])->name('api.models.attributes');
    Route::get('/{model}/relations', [App\Http\Controllers\Api\ModelInfoController::class, 'relations'])->name('api.models.relations');
});

// Posts and Drafts Routes
Route::prefix('posts')->group(function () {
    // Public routes
    Route::get('/', [App\Http\Controllers\PostController::class, 'index'])->name('api.posts.index');
    Route::get('/{slug}', [App\Http\Controllers\PostController::class, 'show'])->name('api.posts.show');
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/drafts/list', [App\Http\Controllers\PostController::class, 'drafts'])->name('api.posts.drafts');
        Route::get('/drafts/{id}', [App\Http\Controllers\PostController::class, 'showDraft'])->name('api.posts.drafts.show');
        Route::post('/', [App\Http\Controllers\PostController::class, 'store'])->name('api.posts.store');
        Route::put('/{id}', [App\Http\Controllers\PostController::class, 'update'])->name('api.posts.update');
        Route::post('/drafts/{id}/publish', [App\Http\Controllers\PostController::class, 'publish'])->name('api.posts.drafts.publish');
        Route::delete('/{id}', [App\Http\Controllers\PostController::class, 'destroy'])->name('api.posts.destroy');
    });
});

// Options Routes
Route::prefix('options')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\OptionsController::class, 'index'])->name('api.options.index');
    Route::post('/', [App\Http\Controllers\Api\OptionsController::class, 'store'])->name('api.options.store');
    Route::put('/{key}', [App\Http\Controllers\Api\OptionsController::class, 'update'])->name('api.options.update');
    Route::delete('/{key}', [App\Http\Controllers\Api\OptionsController::class, 'destroy'])->name('api.options.destroy');
    Route::get('/{key}/exists', [App\Http\Controllers\Api\OptionsController::class, 'exists'])->name('api.options.exists');
});

// Onboarding Routes
Route::middleware('auth:sanctum')->prefix('onboarding')->group(function () {
    Route::get('/', [App\Http\Controllers\OnboardingController::class, 'index'])->name('api.onboarding.index');
    Route::post('/skip', [App\Http\Controllers\OnboardingController::class, 'skip'])->name('api.onboarding.skip');
});

/*
|--------------------------------------------------------------------------
| Task Analytics Routes
|--------------------------------------------------------------------------
|
| Routes for the new task analytics feature using eloquent-has-many-deep
|
*/

Route::middleware(['auth:sanctum'])->prefix('analytics')->name('analytics.')->group(function () {
    // User-specific analytics
    Route::get('/user/task-comments', [TaskAnalyticsController::class, 'getUserTaskComments'])
        ->name('user.task-comments');
    Route::get('/user/task-tags', [TaskAnalyticsController::class, 'getUserTaskTags'])
        ->name('user.task-tags');
    Route::get('/user/category-tasks', [TaskAnalyticsController::class, 'getUserCategoryTasks'])
        ->name('user.category-tasks');
    
    // Task analytics
    Route::get('/tasks/{task}/engagement', [TaskAnalyticsController::class, 'getTaskEngagementMetrics'])
        ->name('task.engagement');
    
    // Category analytics
    Route::get('/categories/{category}/task-comments', [TaskAnalyticsController::class, 'getCategoryTaskComments'])
        ->name('category.task-comments');
    Route::get('/categories/{category}/task-tags', [TaskAnalyticsController::class, 'getCategoryTaskTags'])
        ->name('category.task-tags');
});

// SOAP Routes
Route::prefix('soap')->group(function () {
    Route::post('/example', [App\Http\Controllers\SoapController::class, 'exampleMethod'])->name('api.soap.example');
    Route::get('/mock', [App\Http\Controllers\SoapController::class, 'mockResponse'])->name('api.soap.mock');
});

/*
|--------------------------------------------------------------------------
| Direct REST API Routes
|--------------------------------------------------------------------------
|
| These routes expose our models directly through the REST API package.
| They use a different prefix to avoid conflicts with the original routes.
|
*/

// Public routes
Route::prefix('rest')->name('rest.')->group(function () {
    // Documentation
    Route::get('docs', function () {
        return response()->json([
            'title' => 'Todo API Documentation',
            'description' => 'API documentation for the Todo application',
            'version' => '1.0.0',
            'resources' => [
                'users' => '/api/rest/users',
                'tasks' => '/api/rest/tasks',
                'categories' => '/api/rest/categories',
                'tags' => '/api/rest/tags',
            ]
        ]);
    })->name('docs');
});

// Protected routes with direct REST controllers
Route::middleware('auth:sanctum')->prefix('rest')->name('rest.')->group(function () {
    // Users (admin only)
    Rest::resource('users', \App\Rest\Controllers\UsersController::class)
        ->middleware('admin.api');

    // Tasks
    Rest::resource('tasks', \App\Rest\Controllers\TasksController::class);

    // Categories
    Rest::resource('categories', \App\Rest\Controllers\CategoriesController::class);

    // Tags
    Rest::resource('tags', \App\Rest\Controllers\TagsController::class);
});