<?php

namespace App\Notifications;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TodoReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $todo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $todo)
    {
        $this->todo = $todo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = $this->todo->due_date ? Carbon::parse($this->todo->due_date)->format('M d, Y') : 'No due date';
        $todoUrl = url('/todomvc/' . ($this->todo->is_overdue ? 'overdue' : ($this->todo->is_due_today ? 'due-today' : 'upcoming')));
        
        return (new MailMessage)
            ->subject('Reminder: ' . $this->todo->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder for your task:')
            ->line('**' . $this->todo->title . '**')
            ->line('Due Date: ' . $dueDate)
            ->line($this->getReminderMessage())
            ->action('View Todo', $todoUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'todo_id' => $this->todo->id,
            'title' => $this->todo->title,
            'due_date' => $this->todo->due_date,
            'message' => $this->getReminderMessage(),
            'type' => $this->getReminderType(),
        ];
    }
    
    /**
     * Get the reminder message based on due date status.
     */
    protected function getReminderMessage(): string
    {
        if (!$this->todo->due_date) {
            return 'This task has no due date set, but was marked for a reminder.';
        }
        
        if ($this->todo->is_overdue) {
            return 'This task is overdue! It was due on ' . Carbon::parse($this->todo->due_date)->format('M d, Y') . '.';
        }
        
        if ($this->todo->is_due_today) {
            return 'This task is due today!';
        }
        
        $daysUntilDue = Carbon::today()->diffInDays(Carbon::parse($this->todo->due_date), false);
        
        return "This task is due in {$daysUntilDue} days.";
    }
    
    /**
     * Get the reminder type based on due date status.
     */
    protected function getReminderType(): string
    {
        if ($this->todo->is_overdue) {
            return 'overdue';
        }
        
        if ($this->todo->is_due_today) {
            return 'due-today';
        }
        
        return 'upcoming';
    }
}
