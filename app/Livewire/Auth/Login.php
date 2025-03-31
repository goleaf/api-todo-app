<?php

namespace App\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    
    public function mount()
    {
        if (Auth::check()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
    }
    
    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if ($this->hasTooManyLoginAttempts()) {
            $this->addError('email', __('auth.throttle', [
                'seconds' => RateLimiter::availableIn($this->throttleKey()),
            ]));
            
            return;
        }
        
        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        
        RateLimiter::clear($this->throttleKey());
        
        session()->regenerate();
        $this->dispatch('authenticated');
        
        return redirect()->intended(RouteServiceProvider::HOME);
    }
    
    public function throttleKey()
    {
        return Str::lower($this->email) . '|' . request()->ip();
    }
    
    public function hasTooManyLoginAttempts()
    {
        return RateLimiter::tooManyAttempts(
            $this->throttleKey(),
            5
        );
    }
    
    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.app');
    }
}
