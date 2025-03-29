<?php

namespace App\Components;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserHeader extends Component
{
    public function render()
    {
        $user = Auth::user();
        $taskCount = 0;

        if ($user) {
            // Count tasks that are not completed
            $taskCount = Task::where('user_id', $user->id)
                ->where('completed', false)
                ->count();
        }

        return view('components.user-header', [
            'user' => $user,
            'taskCount' => $taskCount,
        ]);
    }
}
