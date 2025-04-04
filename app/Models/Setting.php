<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'type',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
    ];
    
    /**
     * The types of settings.
     */
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_JSON = 'json';
    
    /**
     * Get the user that owns the setting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the setting value with proper casting.
     */
    public function getValueAttribute($value)
    {
        if ($this->type === null) {
            return $value;
        }
        
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
                return (bool) $value;
            case self::TYPE_INTEGER:
                return (int) $value;
            case self::TYPE_FLOAT:
                return (float) $value;
            case self::TYPE_JSON:
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    /**
     * Set the setting value with proper casting.
     */
    public function setValueAttribute($value)
    {
        if ($this->type === self::TYPE_JSON && !is_string($value)) {
            $this->attributes['value'] = json_encode($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
} 