<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Appstract\Options\Option as OptionModel;
use Appstract\Options\OptionFacade as Option;

class OptionsController extends Controller
{
    /**
     * Get all options or a specific option by key.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!$request->has('key')){
        
        // Get all options
        $options = OptionModel::all()->pluck('value', 'key')->toArray();
        
        return response()->json($options);
    } 
            $key = $request->input('key');
            $defaultValue = $request->input('default');
            
            return response()->json([
                'key' => $key,
                'value' => Option::get($key, $defaultValue)
            ]);
        }
    
    /**
     * Set an option value.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);
        
        $key = $request->input('key');
        $value = $request->input('value');
        
        Option::set([$key => $value]);
        
        return response()->json([
            'message' => 'Option saved successfully',
            'key' => $key,
            'value' => $value
        ], 201);
    }
    
    /**
     * Update an option value.
     *
     * @param Request $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
        ]);
        
        $value = $request->input('value');
        
        Option::set([$key => $value]);
        
        return response()->json([
            'message' => 'Option updated successfully',
            'key' => $key,
            'value' => $value
        ]);
    }
    
    /**
     * Delete an option.
     *
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($key)
    {
        Option::remove($key);
        
        return response()->json([
            'message' => 'Option removed successfully',
            'key' => $key
        ]);
    }
    
    /**
     * Check if an option exists.
     *
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists($key)
    {
        $exists = Option::exists($key);
        
        return response()->json([
            'key' => $key,
            'exists' => $exists
        ]);
    }
} 