<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Calendar;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function calendar_component_can_be_rendered()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/calendar')
            ->assertStatus(200);
    }

    /** @test */
    public function calendar_shows_current_month_by_default()
    {
        $user = User::factory()->create();
        $today = Carbon::today();
        $currentMonth = $today->format('F Y'); // e.g., "April 2023"

        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->assertSet('currentMonth', $today->month)
            ->assertSet('currentYear', $today->year)
            ->assertSee($currentMonth);
    }

    /** @test */
    public function calendar_can_navigate_to_previous_month()
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(Calendar::class);

        // Store the current month
        $initialMonth = $component->get('currentMonth');
        $initialYear = $component->get('currentYear');

        // Navigate to previous month
        $component->call('previousMonth');

        // Check that we've moved to the previous month
        $newMonth = $component->get('currentMonth');
        $newYear = $component->get('currentYear');

        // If we were in January, we should now be in December of the previous year
        if ($initialMonth == 1) {
            $this->assertEquals(12, $newMonth);
            $this->assertEquals($initialYear - 1, $newYear);
        } else {
            $this->assertEquals($initialMonth - 1, $newMonth);
            $this->assertEquals($initialYear, $newYear);
        }
    }

    /** @test */
    public function calendar_can_navigate_to_next_month()
    {
        $user = User::factory()->create();
        $today = Carbon::today();
        $nextMonth = $today->copy()->addMonth();

        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->call('nextMonth')
            ->assertSet('currentMonth', $nextMonth->month)
            ->assertSet('currentYear', $nextMonth->year)
            ->assertSee($nextMonth->format('F Y'));
    }

    /** @test */
    public function calendar_can_select_a_date()
    {
        $user = User::factory()->create();
        $date = Carbon::today()->format('Y-m-d');

        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->call('selectDate', $date)
            ->assertSet('selectedDate', function ($selectedDate) use ($date) {
                return $selectedDate && $selectedDate->format('Y-m-d') === $date;
            });
    }

    /** @test */
    public function calendar_shows_tasks_for_selected_date()
    {
        $user = User::factory()->create();
        $today = Carbon::today();

        // Create a task for today
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Task For Today',
            'due_date' => $today,
        ]);

        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->call('selectDate', $today->format('Y-m-d'))
            ->assertSee('Test Task For Today');
    }

    /** @test */
    public function calendar_can_toggle_task_status()
    {
        $user = User::factory()->create();
        $today = Carbon::today();

        // Create a task for today (not completed)
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Toggle Test Task',
            'completed' => false,
            'due_date' => $today,
        ]);

        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->call('selectDate', $today->format('Y-m-d'))
            ->call('toggleTaskStatus', $task->id);

        // Verify task was toggled to completed
        $this->assertTrue($task->fresh()->completed);

        // Toggle back to incomplete
        Livewire::actingAs($user)
            ->test(Calendar::class)
            ->call('selectDate', $today->format('Y-m-d'))
            ->call('toggleTaskStatus', $task->id);

        $this->assertFalse($task->fresh()->completed);
    }
}
