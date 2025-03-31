<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileManager extends Component
{
    public $user;
    public $name;
    public $email;
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;

    protected $listeners = [
        'profile-photo-updated' => '$refresh',
    ];

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
        ]);

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->save();

        $this->dispatch('profile-updated');
        session()->flash('success', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ],
            'newPassword' => ['required', 'min:8', 'different:currentPassword'],
            'newPasswordConfirmation' => ['required', 'same:newPassword'],
        ]);

        $this->user->password = Hash::make($this->newPassword);
        $this->user->save();

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->dispatch('password-updated');
        session()->flash('success', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.profile.profile-manager')
            ->layout('layouts.app', ['title' => 'Profile']);
    }
} 