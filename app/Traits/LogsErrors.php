<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait LogsErrors
{
    /**
     * Log an error with context
     */
    protected function logError(Throwable $exception, array $context = []): void
    {
        $defaultContext = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
        ];

        Log::error($exception->getMessage(), array_merge($defaultContext, $context));
    }
}
