<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class RunApiTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-api-tests {--filter= : Optional test filter} {--coverage : Generate code coverage report}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the API test suite';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running API Tests...');

        // Base command using vendor/bin/phpunit
        $testCommand = ['vendor/bin/phpunit', '--testdox'];

        // Add filter for API tests if no specific filter is provided
        if (! $this->option('filter')) {
            $testCommand[] = '--filter';
            $testCommand[] = 'Api';
        } else {
            $testCommand[] = '--filter';
            $testCommand[] = $this->option('filter');
        }

        // Add coverage option if requested
        if ($this->option('coverage')) {
            $testCommand[] = '--coverage-text';
        }

        $process = new Process($testCommand);
        $process->setTimeout(300); // 5 minutes timeout
        $process->setTty(false);

        $this->info('Executing: '.implode(' ', $testCommand));

        // Show output in real-time
        $process->start();

        foreach ($process as $type => $data) {
            echo $data;
        }

        $exitCode = $process->getExitCode();

        if ($exitCode === 0) {
            $this->info('All API tests passed successfully!');

            // List the files tested
            $this->info('Test files executed:');
            $testFiles = glob('tests/Feature/Api/*.php');
            $fileCount = 0;

            foreach ($testFiles as $file) {
                if (basename($file) !== 'ApiTestSuite.php') {
                    $this->line(' - '.basename($file));
                    $fileCount++;
                }
            }

            $this->info("Ran tests from {$fileCount} API test files.");

        } else {
            $this->error('API tests failed with exit code: '.$exitCode);
        }

        return $exitCode;
    }
}
