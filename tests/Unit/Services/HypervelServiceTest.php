<?php

namespace Tests\Unit\Services;

use App\Services\HypervelService;
use App\Exceptions\HypervelException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Mockery;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class HypervelServiceTest extends TestCase
{
    protected $hypervelService;
    protected $container = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->hypervelService = new HypervelService();
    }

    /** @test */
    public function it_runs_operations_concurrently()
    {
        // Define test operations
        $operations = [
            'operation1' => fn() => $this->simulateOperation(100, 'result1'),
            'operation2' => fn() => $this->simulateOperation(100, 'result2'),
            'operation3' => fn() => $this->simulateOperation(100, 'result3'),
        ];

        // Measure time without concurrency
        $startSequential = microtime(true);
        $sequentialResults = [];
        foreach ($operations as $key => $operation) {
            $sequentialResults[$key] = $operation();
        }
        $sequentialTime = microtime(true) - $startSequential;

        // Measure time with concurrency
        $startConcurrent = microtime(true);
        $concurrentResults = $this->hypervelService->runConcurrently($operations);
        $concurrentTime = microtime(true) - $startConcurrent;

        // Assertions
        $this->assertEquals($sequentialResults, $concurrentResults, 'Results should be identical');
        $this->assertLessThan($sequentialTime, $concurrentTime * 1.5, 'Concurrent execution should be faster');
        $this->assertCount(3, $concurrentResults, 'Should have 3 results');
    }

    /** @test */
    public function it_handles_batch_processing()
    {
        // Create a collection of items
        $items = collect(range(1, 10));

        // Process them in batches
        $results = $this->hypervelService->runBatch($items, function ($item) {
            return $this->simulateOperation(50, "processed-{$item}");
        }, 3);

        // Assertions
        $this->assertCount(10, $results, 'Should have processed all 10 items');
        $this->assertEquals('processed-1', $results[0], 'First item should be processed correctly');
        $this->assertEquals('processed-10', $results[9], 'Last item should be processed correctly');
    }

    /** @test */
    public function it_retries_failed_operations()
    {
        $attemptCount = 0;

        // Operation that fails the first 2 times
        $operation = function () use (&$attemptCount) {
            $attemptCount++;
            if ($attemptCount < 3) {
                throw new \Exception("Attempt {$attemptCount} failed");
            }
            return 'success';
        };

        // Run with retry
        $result = $this->hypervelService->runWithRetry($operation, 3, 10);

        // Assertions
        $this->assertEquals('success', $result, 'Operation should eventually succeed');
        $this->assertEquals(3, $attemptCount, 'Should have made 3 attempts');
    }

    /** @test */
    public function it_throws_exception_after_max_retries()
    {
        // Operation that always fails
        $operation = function () {
            throw new \Exception("Operation failed");
        };

        // Assert exception is thrown
        $this->expectException(HypervelException::class);
        $this->hypervelService->runWithRetry($operation, 2, 10);
    }

    /** @test */
    public function it_handles_concurrent_http_requests()
    {
        // Setup mock responses
        $mock = new MockHandler([
            new Response(200, [], json_encode(['data' => 'response1'])),
            new Response(200, [], json_encode(['data' => 'response2'])),
            new Response(404, [], 'Not Found'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $this->container = [];
        $history = Middleware::history($this->container);
        $handlerStack->push($history);

        // Replace HTTP client with mock
        $client = new Client(['handler' => $handlerStack]);
        $this->setPrivateProperty($this->hypervelService, 'httpClient', $client);

        // Test the method
        $urls = [
            'url1' => 'https://example.com/api1',
            'url2' => 'https://example.com/api2',
            'url3' => 'https://example.com/api3',
        ];

        $results = $this->hypervelService->runConcurrentHttpRequests($urls);

        // Assertions
        $this->assertCount(3, $results, 'Should have 3 results');
        $this->assertEquals(200, $results['url1']['status'], 'First request should succeed');
        $this->assertEquals(200, $results['url2']['status'], 'Second request should succeed');
        $this->assertEquals(404, $results['url3']['status'], 'Third request should return 404');
        $this->assertCount(3, $this->container, '3 requests should have been made');
    }

    /** @test */
    public function it_handles_http_request_errors()
    {
        // Setup mock that throws exceptions
        $mock = new MockHandler([
            new RequestException("Connection error", new Request('GET', 'https://example.com')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->setPrivateProperty($this->hypervelService, 'httpClient', $client);

        // Test the method
        $urls = ['url1' => 'https://example.com'];
        $results = $this->hypervelService->runConcurrentHttpRequests($urls);

        // Assertions
        $this->assertArrayHasKey('url1', $results, 'Should have a result entry');
        $this->assertArrayHasKey('error', $results['url1'], 'Should have an error message');
        $this->assertStringContainsString('Connection error', $results['url1']['error']);
    }

    /** @test */
    public function it_processes_collections_concurrently()
    {
        $collection = collect(['item1', 'item2', 'item3', 'item4', 'item5']);
        
        $processedCollection = $this->hypervelService->processCollection($collection, function ($item) {
            return $this->simulateOperation(50, "processed-{$item}");
        }, 2);
        
        $this->assertCount(5, $processedCollection);
        $this->assertEquals('processed-item1', $processedCollection[0]);
        $this->assertEquals('processed-item5', $processedCollection[4]);
    }

    /**
     * Helper to simulate an operation with delay
     */
    private function simulateOperation(int $delayMs, mixed $result): mixed
    {
        usleep($delayMs * 1000);
        return $result;
    }

    /**
     * Set a private property value using reflection
     */
    private function setPrivateProperty($object, string $propertyName, $value): void
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
} 