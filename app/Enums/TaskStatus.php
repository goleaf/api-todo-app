<?php

namespace App\Enums;

enum TaskStatus: bool
{
    case INCOMPLETE = false;
    case COMPLETE = true;

    /**
     * Get the label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::INCOMPLETE => 'Incomplete',
            self::COMPLETE => 'Complete',
        };
    }

    /**
     * Get the color for the status.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::INCOMPLETE => 'warning',
            self::COMPLETE => 'success',
        };
    }

    /**
     * Check if the status is complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this === self::COMPLETE;
    }

    /**
     * Check if the status is incomplete.
     *
     * @return bool
     */
    public function isIncomplete(): bool
    {
        return $this === self::INCOMPLETE;
    }

    /**
     * Get all statuses as an array for select inputs.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::INCOMPLETE->value => self::INCOMPLETE->label(),
            self::COMPLETE->value => self::COMPLETE->label(),
        ];
    }

    /**
     * Try to get enum from value or return default.
     *
     * @param bool|null $value
     * @return static
     */
    public static function tryFrom($value): ?self
    {
        if ($value === null) {
            return self::INCOMPLETE;
        }

        return parent::tryFrom($value) ?? self::INCOMPLETE;
    }
} 