<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BenchmarkService;

class BenchmarkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:run 
                            {action : The action to benchmark (model-queries|file-operations|routes)} 
                            {--iterations=10 : Number of iterations to run}
                            {--report : Generate a detailed report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Benchmark different aspects of the application';

    /**
     * The benchmark service.
     */
    protected BenchmarkService $benchmarkService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BenchmarkService $benchmarkService)
    {
        parent::__construct();
        $this->benchmarkService = $benchmarkService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $iterations = (int) $this->option('iterations');
        $generateReport = $this->option('report');

        $this->info("Running benchmark for: {$action}");
        $this->info("Number of iterations: {$iterations}");

        $results = [];

        switch ($action) {
            case 'model-queries':
                $results = $this->benchmarkModelQueries($iterations);
                break;
            case 'file-operations':
                $results = $this->benchmarkFileOperations($iterations);
                break;
            case 'routes':
                $results = $this->benchmarkRoutes($iterations);
                break;
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }

        $this->displayResults($results, $generateReport);

        return 0;
    }

    /**
     * Benchmark model queries.
     *
     * @param int $iterations
     * @return array
     */
    protected function benchmarkModelQueries(int $iterations): array
    {
        $results = [];

        $this->benchmarkService->reset();

        // Benchmark User model queries
        $this->benchmarkService->start('user-find');
        for ($i = 0; $i < $iterations; $i++) {
            \App\Models\User::find(1);
        }
        $results['User::find'] = $this->benchmarkService->end('user-find');

        // Benchmark Category model queries
        $this->benchmarkService->start('category-all');
        for ($i = 0; $i < $iterations; $i++) {
            \App\Models\Category::all();
        }
        $results['Category::all'] = $this->benchmarkService->end('category-all');

        // Benchmark Task with relationships
        $this->benchmarkService->start('task-with-relations');
        for ($i = 0; $i < $iterations; $i++) {
            \App\Models\Task::with(['user', 'category', 'tags'])->take(20)->get();
        }
        $results['Task::with-relations'] = $this->benchmarkService->end('task-with-relations');

        return $results;
    }

    /**
     * Benchmark file operations.
     *
     * @param int $iterations
     * @return array
     */
    protected function benchmarkFileOperations(int $iterations): array
    {
        $results = [];
        $testFile = storage_path('benchmark_test.txt');
        $content = str_repeat('Test content for benchmarking file operations. ', 1000);

        $this->benchmarkService->reset();

        // Write file
        $this->benchmarkService->start('file-write');
        for ($i = 0; $i < $iterations; $i++) {
            file_put_contents($testFile, $content . $i);
        }
        $results['File Write'] = $this->benchmarkService->end('file-write');

        // Read file
        $this->benchmarkService->start('file-read');
        for ($i = 0; $i < $iterations; $i++) {
            $data = file_get_contents($testFile);
        }
        $results['File Read'] = $this->benchmarkService->end('file-read');

        // Append to file
        $this->benchmarkService->start('file-append');
        for ($i = 0; $i < $iterations; $i++) {
            file_put_contents($testFile, "Line {$i}\n", FILE_APPEND);
        }
        $results['File Append'] = $this->benchmarkService->end('file-append');

        // Clean up
        @unlink($testFile);

        return $results;
    }

    /**
     * Benchmark route resolution.
     *
     * @param int $iterations
     * @return array
     */
    protected function benchmarkRoutes(int $iterations): array
    {
        $results = [];
        $this->benchmarkService->reset();

        // Admin routes
        $this->benchmarkService->start('admin-routes');
        for ($i = 0; $i < $iterations; $i++) {
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.dashboard');
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.users.index');
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.categories.index');
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('admin.tasks.index');
        }
        $results['Admin Routes'] = $this->benchmarkService->end('admin-routes');

        // API routes
        $this->benchmarkService->start('api-routes');
        for ($i = 0; $i < $iterations; $i++) {
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('api.tasks.index');
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('api.categories.index');
            \Illuminate\Support\Facades\Route::getRoutes()->getByName('api.users.index');
        }
        $results['API Routes'] = $this->benchmarkService->end('api-routes');

        return $results;
    }

    /**
     * Display benchmark results.
     *
     * @param array $results
     * @param bool $generateReport
     * @return void
     */
    protected function displayResults(array $results, bool $generateReport): void
    {
        $this->info('Benchmark Results:');
        
        $headers = ['Operation', 'Time (ms)'];
        $rows = [];
        
        foreach ($results as $operation => $time) {
            $rows[] = [$operation, number_format($time, 2)];
        }
        
        $this->table($headers, $rows);
        
        if (!$generateReport){
    return;} 
            $report = "Benchmark Report\n";
            $report .= "---------------\n";
            $report .= "Generated at: " . now()->format('Y-m-d H:i:s') . "\n\n";
            
            foreach ($results as $operation => $time) {
                $report .= "{$operation}: " . number_format($time, 2) . " ms\n";
            }
            
            $reportFile = storage_path('benchmark_report_' . now()->format('Y-m-d_H-i-s') . '.txt');
            file_put_contents($reportFile, $report);
            
            $this->info("Detailed report saved to: {$reportFile}");
        
    }
} 