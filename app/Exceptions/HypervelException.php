<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class HypervelException extends Exception
{
    /**
     * Additional context for the exception
     *
     * @var array
     */
    protected $context;

    /**
     * Create a new Hypervel exception instance.
     *
     * @return void
     */
    public function __construct(string $message, int $code = 0, ?Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get the additional context for the exception.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Create an exception for a timeout error
     *
     * @param  int  $timeout  Timeout in seconds
     * @param  Throwable|null  $previous  Previous exception
     */
    public static function timeout(int $timeout, ?Throwable $previous = null): static
    {
        return new static(
            "Hypervel operation timed out after {$timeout} seconds",
            408,
            $previous,
            ['timeout' => $timeout]
        );
    }

    /**
     * Create an exception for a concurrency limit error
     *
     * @param  int  $limit  Concurrency limit
     * @param  Throwable|null  $previous  Previous exception
     */
    public static function concurrencyLimitExceeded(int $limit, ?Throwable $previous = null): static
    {
        return new static(
            "Hypervel concurrency limit of {$limit} exceeded",
            429,
            $previous,
            ['concurrency_limit' => $limit]
        );
    }

    /**
     * Create an exception for an operation error
     *
     * @param  string  $operation  Name of the operation that failed
     * @param  Throwable  $previous  Previous exception
     */
    public static function operationFailed(string $operation, Throwable $previous): static
    {
        return new static(
            "Hypervel operation '{$operation}' failed: {$previous->getMessage()}",
            500,
            $previous,
            ['operation' => $operation]
        );
    }

    /**
     * Report the exception
     */
    public function report(): bool
    {
        // Custom reporting logic can be added here
        return true;
    }

    /**
     * Render the exception into an HTTP response
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Hypervel Operation Failed',
                'message' => $this->getMessage(),
            ], 500);
        }

        // For web requests, you might want to add a flash message
        if ($request->session()) {
            $request->session()->flash('error', 'A processing error occurred: '.$this->getMessage());
        }

        return redirect()->back()->withInput();
    }
}
