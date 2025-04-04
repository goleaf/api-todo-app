<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsRepository
{
    /**
     * Get all settings from the database.
     *
     * @return array
     */
    public function getAll(): array
    {
        return Cache::remember('db_settings', 3600, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->getAll();
        return $settings[$key] ?? $default;
    }

    /**
     * Set a specific setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('db_settings');
    }

    /**
     * Update multiple settings at once.
     *
     * @param array $settings
     * @return array
     */
    public function update(array $settings): array
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value);
        }

        return $this->getAll();
    }

    /**
     * Delete a setting.
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $result = Setting::where('key', $key)->delete();
        
        if ($result) {
            Cache::forget('db_settings');
        }

        return $result;
    }

    /**
     * Check if a setting exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->getAll()[$key]);
    }
} 