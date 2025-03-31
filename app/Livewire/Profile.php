<?php

namespace App\Livewire;

use App\Livewire\Profile\ProfileManager;
use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return app(ProfileManager::class)->render();
    }
}
