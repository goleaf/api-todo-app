<?php

namespace App\Soap\Requests;

use Illuminate\Contracts\Support\Arrayable;

class ExampleRequest implements Arrayable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $message;

    /**
     * ExampleRequest constructor.
     *
     * @param string $name
     * @param string $email
     * @param string $message
     */
    public function __construct(string $name, string $email, string $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'message' => $this->message,
        ];
    }
} 