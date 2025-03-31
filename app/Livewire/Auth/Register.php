<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $terms = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'terms' => 'required|accepted',
    ];

    protected $messages = [
        'terms.required' => 'You must accept the terms and conditions',
        'terms.accepted' => 'You must accept the terms and conditions',
    ];

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }
    }

    public function register()
    {
        $this->validate();

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            Auth::login($user);
            
            // Use dispatch instead of emit for Livewire 3
            $this->dispatch('registered');

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred during registration. Please try again.');
            // Log the error for debugging
            \Log::error('Registration error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.app');
    }
}
