<?php

namespace App\Soap\Responses;

class ExampleResponse
{
    /**
     * @var bool
     */
    protected $success;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $data;

    /**
     * Parse the SOAP response.
     *
     * @param mixed $response
     * @return self
     */
    public static function parse($response): self
    {
        $instance = new self();
        
        // If response is an object with properties, use them
        if (is_object($response)) {
            $instance->success = isset($response->Success) ? (bool) $response->Success : false;
            $instance->message = $response->Message ?? '';
            $instance->data = $response->Data ?? [];
        } 
        // If response is an array, get the values
        elseif (is_array($response)) {
            $instance->success = isset($response['Success']) ? (bool) $response['Success'] : false;
            $instance->message = $response['Message'] ?? '';
            $instance->data = $response['Data'] ?? [];
        } 
        // Otherwise set default values
        else {
            $instance->success = false;
            $instance->message = 'Invalid response format';
            $instance->data = [];
        }

        return $instance;
    }

    /**
     * Get success status.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get response message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get response data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Convert response to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
} 