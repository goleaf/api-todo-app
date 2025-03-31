<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';

    /**
     * Get the label for the user role.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'User',
            self::GUEST => 'Guest',
        };
    }

    /**
     * Get the color for the user role.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::ADMIN => 'danger',
            self::USER => 'primary',
            self::GUEST => 'info',
        };
    }

    /**
     * Check if the role is admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if the role is a regular user.
     *
     * @return bool
     */
    public function isUser(): bool
    {
        return $this === self::USER;
    }

    /**
     * Check if the role is a guest.
     *
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this === self::GUEST;
    }

    /**
     * Get all user roles as an array for select inputs.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::ADMIN->value => self::ADMIN->label(),
            self::USER->value => self::USER->label(),
            self::GUEST->value => self::GUEST->label(),
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
            return self::USER;
        }

        return parent::tryFrom($value) ?? self::USER;
    }
} 