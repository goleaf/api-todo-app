<?php

namespace App\Enums;

enum TaskProgressStatus: int
{
    case NOT_STARTED = 0;
    case IN_PROGRESS = 50;
    case COMPLETED = 100;

    /**
     * Get the label for the progress status.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not Started',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Get the color for the progress status.
     *
     * @return string
     */
    public function color(): string
    {
        return match($this) {
            self::NOT_STARTED => 'danger',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED => 'success',
        };
    }

    /**
     * Check if the task is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Check if the task is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    /**
     * Check if the task is not started.
     *
     * @return bool
     */
    public function isNotStarted(): bool
    {
        return $this === self::NOT_STARTED;
    }

    /**
     * Get progress status from percentage.
     *
     * @param int $percentage
     * @return static
     */
    public static function fromPercentage(int $percentage): self
    {
        if ($percentage >= 100) {
            return self::COMPLETED;
        }

        if ($percentage > 0) {
            return self::IN_PROGRESS;
        }

        return self::NOT_STARTED;
    }

    /**
     * Get all progress statuses as an array for select inputs.
     *
     * @return array
     */
    public static function toArray(): array
    {
        return [
            self::NOT_STARTED->value => self::NOT_STARTED->label(),
            self::IN_PROGRESS->value => self::IN_PROGRESS->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
        ];
    }

    /**
     * Try to get enum from value or return default.
     *
     * @param int|null $value
     * @return static
     */
    public static function tryFrom($value): ?self
    {
        if ($value === null) {
            return self::NOT_STARTED;
        }

        return parent::tryFrom($value) ?? self::fromPercentage((int) $value);
    }
} 