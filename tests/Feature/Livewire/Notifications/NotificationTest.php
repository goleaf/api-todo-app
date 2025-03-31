<?php

namespace Tests\Feature\Livewire\Notifications;

use App\Livewire\Notifications\NotificationList;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class NotificationTest extends LivewireTestCase
{
    /** @test */
    public function notification_component_can_render()
    {
        $this->assertLivewireCanSee(NotificationList::class, 'Notifications');
    }
    
    /** @test */
    public function it_shows_user_notifications()
    {
        // Create notifications for the test user
        $notifications = $this->createNotifications($this->user, 3);
        
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->assertSee($notifications[0]->data['message'])
            ->assertSee($notifications[1]->data['message'])
            ->assertSee($notifications[2]->data['message']);
    }
    
    /** @test */
    public function it_shows_unread_notification_count()
    {
        // Create notifications for the test user
        $this->createNotifications($this->user, 5);
        
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->assertSee('5 unread')
            ->assertSeeHtml('<span class="notification-badge">5</span>');
    }
    
    /** @test */
    public function it_marks_notification_as_read()
    {
        // Create a notification
        $notification = $this->createNotifications($this->user, 1)[0];
        
        // Mark it as read
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->call('markAsRead', $notification->id)
            ->assertEmitted('notification-read');
            
        // Verify it's marked as read
        $this->assertNotNull($notification->fresh()->read_at);
    }
    
    /** @test */
    public function it_marks_all_notifications_as_read()
    {
        // Create notifications
        $this->createNotifications($this->user, 3);
        
        // Mark all as read
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->call('markAllAsRead')
            ->assertEmitted('all-notifications-read');
            
        // Verify all are marked as read
        $this->assertEquals(
            0, 
            $this->user->unreadNotifications()->count()
        );
    }
    
    /** @test */
    public function it_deletes_notification()
    {
        // Create a notification
        $notification = $this->createNotifications($this->user, 1)[0];
        
        // Delete it
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->call('delete', $notification->id)
            ->assertEmitted('notification-deleted');
            
        // Verify it's deleted
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id
        ]);
    }
    
    /** @test */
    public function it_receives_push_notifications_in_real_time()
    {
        // Setup component
        $component = Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->assertDontSee('New Task Assigned');
        
        // Create a new notification programmatically
        $newNotification = $this->user->notifications()->create([
            'type' => 'App\\Notifications\\TaskAssigned',
            'data' => ['message' => 'New Task Assigned', 'task_id' => 1],
        ]);
        
        // Emit the event that would be triggered by the broadcast
        $component->emit('notification-received', $newNotification->id)
            ->assertSee('New Task Assigned');
    }
    
    /** @test */
    public function it_filters_notifications_by_type()
    {
        // Create different types of notifications
        $taskNotification = $this->user->notifications()->create([
            'type' => 'App\\Notifications\\TaskAssigned',
            'data' => ['message' => 'Task Notification', 'type' => 'task'],
        ]);
        
        $commentNotification = $this->user->notifications()->create([
            'type' => 'App\\Notifications\\CommentAdded',
            'data' => ['message' => 'Comment Notification', 'type' => 'comment'],
        ]);
        
        // Test filtering
        $component = Livewire::actingAs($this->user)
            ->test(NotificationList::class);
            
        // Initially see all notifications
        $component->assertSee('Task Notification')
            ->assertSee('Comment Notification');
            
        // Filter to only task notifications
        $component->call('filterByType', 'task')
            ->assertSee('Task Notification')
            ->assertDontSee('Comment Notification');
            
        // Filter to only comment notifications
        $component->call('filterByType', 'comment')
            ->assertDontSee('Task Notification')
            ->assertSee('Comment Notification');
            
        // Clear filter
        $component->call('filterByType', '')
            ->assertSee('Task Notification')
            ->assertSee('Comment Notification');
    }
    
    /** @test */
    public function it_updates_notification_styles_based_on_read_status()
    {
        // Create a notification
        $notification = $this->createNotifications($this->user, 1)[0];
        
        // Test unread notification has appropriate styling
        $component = Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->assertSeeHtml('class="notification unread"');
            
        // Mark as read
        $component->call('markAsRead', $notification->id);
            
        // Test read notification has appropriate styling
        $component->assertSeeHtml('class="notification read"')
            ->assertDontSeeHtml('class="notification unread"');
    }
    
    /** @test */
    public function it_groups_notifications_by_date()
    {
        // Create notifications with different dates
        $this->travel(-3)->days(); // 3 days ago
        $oldNotification = $this->createNotifications($this->user, 1)[0];
        
        $this->travel(3)->days(); // Back to today
        $newNotification = $this->createNotifications($this->user, 1)[0];
        
        // Test date headers are shown
        Livewire::actingAs($this->user)
            ->test(NotificationList::class)
            ->assertSee('Today')
            ->assertSee('3 days ago');
    }
    
    /**
     * Helper to create test notifications
     */
    private function createNotifications(User $user, int $count = 1)
    {
        $notifications = [];
        
        for ($i = 0; $i < $count; $i++) {
            $notifications[] = $user->notifications()->create([
                'type' => 'App\\Notifications\\TestNotification',
                'data' => ['message' => "Test Notification {$i}"],
            ]);
        }
        
        return $notifications;
    }
} 