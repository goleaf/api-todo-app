<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'bio',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * User roles.
     */
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * Get the tasks for the user.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the categories for the user.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    
    /**
     * Get the tags for the user.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
    
    /**
     * Get the smart tags for the user.
     */
    public function smartTags(): HasMany
    {
        return $this->hasMany(SmartTag::class);
    }
    
    /**
     * Get the time entries for the user.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
    
    /**
     * Get the attachments uploaded by the user.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
    
    /**
     * Get the settings for the user.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }
    
    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
    
    /**
     * Get user's setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->settings()->where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Get the avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!$this->avatar) {
            // Return default avatar or generate one based on initials
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
        }
        
        return $this->avatar;
    }
    
    /**
     * Get the user's preferred language.
     */
    public function getPreferredLanguageAttribute(): string
    {
        return $this->getSetting('language', config('app.locale'));
    }
    
    /**
     * Get the user's preferred timezone.
     */
    public function getPreferredTimezoneAttribute(): string
    {
        return $this->getSetting('timezone', config('app.timezone'));
    }
    
    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', self::ROLE_ADMIN);
    }
    
    /**
     * Get task counts statistics.
     */
    public function getTaskCountsAttribute(): array
    {
        return [
            'total' => $this->tasks()->count(),
            'completed' => $this->tasks()->where('completed', true)->count(),
            'pending' => $this->tasks()->where('completed', false)->count()
        ];
    }

    /**
     * Get all user settings as a collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }
    
    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
