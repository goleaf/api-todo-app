<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($search = $request->input('search')) {
            $query->search($search);
        }
        
        // Apply sorting using the Sortable trait
        if ($request->has('sort') || $request->has('direction')) {
            $query = $query->sortable($request->only(['sort', 'direction']));
        } else {
            // Default sorting if no sort parameters
            $query->latest();
        }
        
        $users = $query->fastPaginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = \App\Enums\UserRole::cases();
        $isEdit = false;
        return view('admin.users.form', compact('roles', 'isEdit'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        User::create($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $taskStats = $user->getTaskStatistics();
        
        return view('admin.users.show', compact('user', 'taskStats'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = \App\Enums\UserRole::cases();
        $isEdit = true;
        return view('admin.users.form', compact('user', 'roles', 'isEdit'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
} 