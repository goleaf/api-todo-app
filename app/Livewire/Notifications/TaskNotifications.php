<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskNotifications extends Component
{
    /**
     * The notifications list.
     *
     * @var array
     */
    public $notifications = [];

    /**
     * The maximum number of notifications to show.
     *
     * @var int
     */
    public $maxNotifications = 5;

    /**
     * Initialize the component.
     */
    public function mount()
    {
        // Initialize with empty notifications
        $this->notifications = [];
    }

    /**
     * Handle the task.created event.
     */
    #[On('echo:private:user.' . '*,task.created')]
    public function handleTaskCreated($event)
    {
        if (isset($event['task']) && $event['task']['user_id'] == Auth::id()) {
            $this->addNotification([
                'type' => 'created',
                'message' => "Task \"{$event['task']['title']}\" was created",
                'time' => now()->toDateTimeString(),
                'task' => $event['task'],
            ]);
        }
    }

    /**
     * Handle the task.updated event.
     */
    #[On('echo:private:user.' . '*,task.updated')]
    public function handleTaskUpdated($event)
    {
        if (isset($event['task']) && $event['task']['user_id'] == Auth::id()) {
            $this->addNotification([
                'type' => 'updated',
                'message' => "Task \"{$event['task']['title']}\" was updated",
                'time' => now()->toDateTimeString(),
                'task' => $event['task'],
            ]);
        }
    }

    /**
     * Handle the task.completed event.
     */
    #[On('echo:private:user.' . '*,task.completed')]
    public function handleTaskCompleted($event)
    {
        if (isset($event['task']) && $event['task']['user_id'] == Auth::id()) {
            $this->addNotification([
                'type' => 'completed',
                'message' => "Task \"{$event['task']['title']}\" was completed",
                'time' => now()->toDateTimeString(),
                'task' => $event['task'],
            ]);
        }
    }

    /**
     * Add a notification to the list.
     */
    private function addNotification($notification)
    {
        // Add the new notification to the beginning of the array
        array_unshift($this->notifications, $notification);
        
        // Trim the array to max length
        if (count($this->notifications) > $this->maxNotifications) {
            $this->notifications = array_slice($this->notifications, 0, $this->maxNotifications);
        }
    }

    /**
     * Clear all notifications.
     */
    public function clearAllNotifications()
    {
        $this->notifications = [];
    }

    /**
     * Remove a specific notification.
     */
    public function removeNotification($index)
    {
        if (isset($this->notifications[$index])) {
            unset($this->notifications[$index]);
            // Reindex the array
            $this->notifications = array_values($this->notifications);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.notifications.task-notifications');
    }
}
