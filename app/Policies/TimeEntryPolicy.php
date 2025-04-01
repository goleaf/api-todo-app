<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TimeEntryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any time entries.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the time entry.
     */
    public function view(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    /**
     * Determine whether the user can create time entries.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the time entry.
     */
    public function update(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    /**
     * Determine whether the user can delete the time entry.
     */
    public function delete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    /**
     * Determine whether the user can restore the time entry.
     */
    public function restore(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }

    /**
     * Determine whether the user can permanently delete the time entry.
     */
    public function forceDelete(User $user, TimeEntry $timeEntry): bool
    {
        return $user->id === $timeEntry->user_id;
    }
}
