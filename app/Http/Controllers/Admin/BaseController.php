<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Success response method.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendSuccess($data = null, $message = null, $statusCode = 200)
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        return response()->json($response, $statusCode);
    }

    /**
     * Error response method.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError($message = 'An error occurred', $statusCode = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $statusCode);
    }
} 