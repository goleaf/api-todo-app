<?php

namespace Tests\Feature\Livewire\Calendar;

use App\Livewire\Calendar\Calendar;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\Feature\Livewire\LivewireTestCase;

class CalendarTest extends LivewireTestCase
{
    /** @test */
    public function calendar_component_can_render()
    {
        $this->assertLivewireCanSee(Calendar::class, 'Task Calendar');
    }

    /** @test */
    public function calendar_shows_current_month_by_default()
    {
        $currentMonth = Carbon::now()->format('F Y');

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSee($currentMonth);
    }

    /** @test */
    public function calendar_can_navigate_to_next_month()
    {
        $nextMonth = Carbon::now()->addMonth()->format('F Y');

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('nextMonth')
            ->assertSee($nextMonth);
    }

    /** @test */
    public function calendar_can_navigate_to_previous_month()
    {
        $previousMonth = Carbon::now()->subMonth()->format('F Y');

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('previousMonth')
            ->assertSee($previousMonth);
    }

    /** @test */
    public function calendar_shows_tasks_for_specific_dates()
    {
        // Create tasks with specific due dates
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $yesterday = Carbon::yesterday();

        $todayTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Today Task',
            'due_date' => $today,
        ]);

        $tomorrowTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Tomorrow Task',
            'due_date' => $tomorrow,
        ]);

        $yesterdayTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Yesterday Task',
            'due_date' => $yesterday,
        ]);

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSee('Today Task')
            ->assertSee('Tomorrow Task')
            ->assertSee('Yesterday Task');
    }

    /** @test */
    public function calendar_does_not_show_tasks_from_other_users()
    {
        // Create a task for another user with today's due date
        $otherUser = $this->createUser();
        $otherUserTask = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Task',
            'due_date' => Carbon::today(),
        ]);

        // Create a task for the current user with today's due date
        $userTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'My Task',
            'due_date' => Carbon::today(),
        ]);

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSee('My Task')
            ->assertDontSee('Other User Task');
    }

    /** @test */
    public function calendar_shows_task_counts_for_each_day()
    {
        // Create multiple tasks for the same day
        $today = Carbon::today();

        Task::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'due_date' => $today,
        ]);

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSee('3 tasks');
    }

    /** @test */
    public function calendar_can_filter_completed_tasks()
    {
        $today = Carbon::today();

        // Create completed and incomplete tasks
        $completedTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Completed Task',
            'due_date' => $today,
            'completed' => true,
        ]);

        $incompleteTask = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Incomplete Task',
            'due_date' => $today,
            'completed' => false,
        ]);

        // Test showing all tasks (default)
        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSee('Completed Task')
            ->assertSee('Incomplete Task');

        // Test hiding completed tasks
        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('toggleShowCompleted')
            ->assertDontSee('Completed Task')
            ->assertSee('Incomplete Task');
    }

    /** @test */
    public function calendar_can_jump_to_specific_month()
    {
        $december2023 = Carbon::createFromDate(2023, 12, 1)->format('F Y');

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('jumpToMonth', 12, 2023)
            ->assertSee($december2023);
    }

    /** @test */
    public function calendar_highlights_today()
    {
        $today = Carbon::today()->format('j'); // Day of month without leading zeros

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->assertSeeHtml('class="calendar-today"')
            ->assertSeeHtml('>'.$today.'<');
    }

    /** @test */
    public function calendar_can_jump_to_today()
    {
        $currentMonth = Carbon::now()->format('F Y');

        // First navigate to a different month
        $component = Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('nextMonth')
            ->call('nextMonth');

        // Then jump back to today
        $component->call('jumpToToday')
            ->assertSee($currentMonth);
    }

    /** @test */
    public function clicking_on_date_shows_task_details()
    {
        $today = Carbon::today();

        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Important Task',
            'description' => 'This is an important task',
            'due_date' => $today,
        ]);

        Livewire::actingAs($this->user)
            ->test(Calendar::class)
            ->call('selectDate', $today->format('Y-m-d'))
            ->assertSet('selectedDate', $today->format('Y-m-d'))
            ->assertSee('Important Task')
            ->assertSee('This is an important task');
    }
}
