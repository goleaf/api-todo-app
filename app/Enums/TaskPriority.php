<?php

namespace App\Enums;

enum TaskPriority: int
{
    case LOW = 0;
    case MEDIUM = 1;
    case HIGH = 2;

    /**
     * Get the label for the priority.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }

    /**
     * Get the color for the priority.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::LOW => 'success',
            self::MEDIUM => 'warning',
            self::HIGH => 'danger',
        };
    }

    /**
     * Get all priorities as an array for select inputs.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::LOW->value => self::LOW->label(),
            self::MEDIUM->value => self::MEDIUM->label(),
            self::HIGH->value => self::HIGH->label(),
        ];
    }

    /**
     * Try to get enum from value or return default.
     *
     * @param int|null $value
     * @return static
     */
    public static function fromValueOrDefault($value): self
    {
        if ($value === null) {
            return self::LOW;
        }

        // Manually check against known values
        foreach ([self::LOW, self::MEDIUM, self::HIGH] as $priority) {
            if ($priority->value === (int) $value) {
                return $priority;
            }
        }
        
        return self::LOW; // Default fallback
    }
} 