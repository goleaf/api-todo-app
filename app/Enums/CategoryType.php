<?php

namespace App\Enums;

enum CategoryType: string
{
    case WORK = 'work';
    case PERSONAL = 'personal';
    case EDUCATION = 'education';
    case HEALTH = 'health';
    case FINANCE = 'finance';
    case HOME = 'home';
    case OTHER = 'other';

    /**
     * Get the label for the category type.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::WORK => 'Work',
            self::PERSONAL => 'Personal',
            self::EDUCATION => 'Education',
            self::HEALTH => 'Health',
            self::FINANCE => 'Finance',
            self::HOME => 'Home',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get the color for the category type.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::WORK => 'blue',
            self::PERSONAL => 'green',
            self::EDUCATION => 'purple',
            self::HEALTH => 'red',
            self::FINANCE => 'yellow',
            self::HOME => 'indigo',
            self::OTHER => 'gray',
        };
    }

    /**
     * Get the icon for the category type.
     *
     * @return string
     */
    public function icon(): string
    {
        return match($this) {
            self::WORK => 'briefcase',
            self::PERSONAL => 'user',
            self::EDUCATION => 'book',
            self::HEALTH => 'heart',
            self::FINANCE => 'dollar-sign',
            self::HOME => 'home',
            self::OTHER => 'tag',
        };
    }

    /**
     * Get all category types as an array for select inputs.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::WORK->value => self::WORK->label(),
            self::PERSONAL->value => self::PERSONAL->label(),
            self::EDUCATION->value => self::EDUCATION->label(),
            self::HEALTH->value => self::HEALTH->label(),
            self::FINANCE->value => self::FINANCE->label(),
            self::HOME->value => self::HOME->label(),
            self::OTHER->value => self::OTHER->label(),
        ];
    }

    /**
     * Try to get enum from value or return default.
     *
     * @param string|null $value
     * @return static
     */
    public static function tryFrom($value): ?self
    {
        if ($value === null) {
            return self::OTHER;
        }

        return parent::tryFrom($value) ?? self::OTHER;
    }
} 