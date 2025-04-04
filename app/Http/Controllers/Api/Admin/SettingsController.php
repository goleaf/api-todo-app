<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\SettingsRequest;
use App\Services\SettingsService;

class SettingsController extends BaseController
{
    /**
     * The settings service instance.
     *
     * @var \App\Services\SettingsService
     */
    protected $settingsService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SettingsService  $settingsService
     * @return void
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display the application settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $settings = $this->settingsService->getAllSettings();

        return $this->successResponse($settings);
    }

    /**
     * Update the application settings.
     *
     * @param  \App\Http\Requests\SettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SettingsRequest $request)
    {
        $settings = $this->settingsService->updateSettings($request->validated());

        return $this->successResponse($settings, 'Settings updated successfully');
    }

    /**
     * Toggle maintenance mode.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleMaintenanceMode()
    {
        if ($this->settingsService->isMaintenanceMode()) {
            $this->settingsService->disableMaintenanceMode();
            $message = 'Maintenance mode disabled successfully';
        } else {
            $this->settingsService->enableMaintenanceMode();
            $message = 'Maintenance mode enabled successfully';
        }

        return $this->successResponse([
            'maintenance_mode' => $this->settingsService->isMaintenanceMode()
        ], $message);
    }
} 