<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Category $category)
    {
        return $user->id === $category->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Category $category)
    {
        return $user->id === $category->user_id;
    }

    public function delete(User $user, Category $category)
    {
        return $user->id === $category->user_id;
    }
} 