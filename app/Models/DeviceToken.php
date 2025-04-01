<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'player_id',
        'device_type',
    ];

    /**
     * Get the user that owns the device token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include tokens for a specific device type.
     */
    public function scopeForDeviceType(Builder $query, string $deviceType): Builder
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope a query to only include active tokens.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotNull('player_id');
    }
}
