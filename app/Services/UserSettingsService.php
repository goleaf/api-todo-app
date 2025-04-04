<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;

class UserSettingsService
{
    /**
     * Get a user setting value.
     *
     * @param string $key
     * @param mixed $default
     * @param User|null $user
     * @return mixed
     */
    public function get(string $key, $default = null, User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return $default;
        }
        
        $setting = $user->settings()->where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Set a user setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param User|null $user
     * @return UserSetting
     */
    public function set(string $key, $value, User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        return $user->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
    
    /**
     * Set multiple user settings at once.
     *
     * @param array $settings
     * @param User|null $user
     * @return void
     */
    public function setMultiple(array $settings, User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        foreach ($settings as $key => $value) {
            $this->set($key, $value, $user);
        }
    }
    
    /**
     * Check if a user setting exists.
     *
     * @param string $key
     * @param User|null $user
     * @return bool
     */
    public function has(string $key, User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->settings()->where('key', $key)->exists();
    }
    
    /**
     * Remove a user setting.
     *
     * @param string $key
     * @param User|null $user
     * @return bool
     */
    public function remove(string $key, User $user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $user->settings()->where('key', $key)->delete() > 0;
    }
} 