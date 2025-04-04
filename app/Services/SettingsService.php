<?php

namespace App\Services;

use App\Repositories\SettingsRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class SettingsService
{
    /**
     * The settings repository instance.
     *
     * @var \App\Repositories\SettingsRepository
     */
    protected $repository;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\SettingsRepository  $repository
     * @return void
     */
    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all application settings.
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        return Cache::remember('app_settings', 3600, function () {
            $dbSettings = $this->repository->getAll();
            
            return array_merge([
                'site_name' => config('app.name'),
                'site_description' => config('app.description'),
                'site_url' => config('app.url'),
                'timezone' => config('app.timezone'),
                'date_format' => config('app.date_format'),
                'time_format' => config('app.time_format'),
                'registration_enabled' => config('app.registration_enabled'),
                'maintenance_mode' => app()->isDownForMaintenance(),
                'version' => config('app.version'),
            ], $dbSettings);
        });
    }

    /**
     * Update application settings.
     *
     * @param array $settings
     * @return array
     */
    public function updateSettings(array $settings): array
    {
        // Update database settings
        $dbSettings = $this->repository->update($settings);

        // Update config settings
        foreach ($settings as $key => $value) {
            Config::set("app.{$key}", $value);
        }

        // Clear the settings cache
        Cache::forget('app_settings');

        return array_merge($this->getAllSettings(), $dbSettings);
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        // Try to get from database first
        $value = $this->repository->get($key);
        if ($value !== null) {
            return $value;
        }

        // Fall back to config
        return config("app.{$key}", $default);
    }

    /**
     * Set a specific setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setSetting(string $key, $value): void
    {
        // Update database
        $this->repository->set($key, $value);

        // Update config
        Config::set("app.{$key}", $value);

        // Clear cache
        Cache::forget('app_settings');
    }

    /**
     * Check if maintenance mode is enabled.
     *
     * @return bool
     */
    public function isMaintenanceMode(): bool
    {
        return app()->isDownForMaintenance();
    }

    /**
     * Enable maintenance mode.
     *
     * @return void
     */
    public function enableMaintenanceMode(): void
    {
        app()->down();
    }

    /**
     * Disable maintenance mode.
     *
     * @return void
     */
    public function disableMaintenanceMode(): void
    {
        app()->up();
    }
} 