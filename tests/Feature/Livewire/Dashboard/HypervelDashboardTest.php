<?php

namespace Tests\Feature\Livewire\Dashboard;

use App\Livewire\Dashboard\HypervelDashboard;
use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Mockery;

class HypervelDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_render_dashboard()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(HypervelDashboard::class)
            ->assertStatus(200)
            ->assertSee('Hypervel Dashboard Demo')
            ->assertSee('Task Stats');
    }

    /** @test */
    public function it_loads_dashboard_data_concurrently()
    {
        $user = User::factory()->create();
        Task::factory()->count(5)->create(['user_id' => $user->id]);

        // Mock the HypervelService
        $this->mock(HypervelService::class, function ($mock) {
            $mock->shouldReceive('runConcurrently')
                ->once()
                ->andReturn([
                    'tasks' => Task::factory()->count(3)->make(),
                    'taskStats' => [
                        'total' => 10,
                        'completed' => 5,
                        'pending' => 5,
                        'overdue' => 2,
                    ],
                    'recentActivity' => [],
                    'upcomingTasks' => [],
                    'popularCategories' => [
                        ['name' => 'Work', 'count' => 5],
                        ['name' => 'Personal', 'count' => 3],
                    ],
                ]);
        });

        Livewire::actingAs($user)
            ->test(HypervelDashboard::class)
            ->assertSet('isLoading', false)
            ->assertSee('Total Tasks')
            ->assertSee('10'); // Assert we see the mocked data
    }

    /** @test */
    public function it_can_refresh_dashboard_data()
    {
        $user = User::factory()->create();

        // Mock the HypervelService
        $this->mock(HypervelService::class, function ($mock) {
            $mock->shouldReceive('runConcurrently')
                ->twice() // Once for initial load and once for refresh
                ->andReturn([
                    'tasks' => [],
                    'taskStats' => [
                        'total' => 0,
                        'completed' => 0,
                        'pending' => 0,
                        'overdue' => 0,
                    ],
                    'recentActivity' => [],
                    'upcomingTasks' => [],
                    'popularCategories' => [],
                ]);
        });

        Livewire::actingAs($user)
            ->test(HypervelDashboard::class)
            ->call('refreshDashboard')
            ->assertSet('isLoading', false);
    }

    /** @test */
    public function it_can_toggle_performance_comparison()
    {
        $user = User::factory()->create();

        // Mock the HypervelService
        $this->mock(HypervelService::class, function ($mock) {
            $mock->shouldReceive('runConcurrently')
                ->once()
                ->andReturn([
                    'tasks' => [],
                    'taskStats' => [
                        'total' => 0,
                        'completed' => 0,
                        'pending' => 0,
                        'overdue' => 0,
                    ],
                    'recentActivity' => [],
                    'upcomingTasks' => [],
                    'popularCategories' => [],
                ]);
        });

        Livewire::actingAs($user)
            ->test(HypervelDashboard::class)
            ->assertSet('showComparison', false)
            ->call('toggleComparison')
            ->assertSet('showComparison', true)
            ->assertSee('Performance Improvement')
            ->call('toggleComparison')
            ->assertSet('showComparison', false);
    }

    /** @test */
    public function it_handles_errors_gracefully()
    {
        $user = User::factory()->create();

        // Mock the HypervelService to throw an exception
        $this->mock(HypervelService::class, function ($mock) {
            $mock->shouldReceive('runConcurrently')
                ->once()
                ->andThrow(new \Exception('Test error'));
        });

        Livewire::actingAs($user)
            ->test(HypervelDashboard::class)
            ->assertSet('isLoading', false)
            ->assertSet('errorMessage', 'Error loading dashboard: Test error')
            ->assertSee('Error loading dashboard: Test error');
    }

    /** @test */
    public function it_calculates_improvement_percentage_correctly()
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)->test(HypervelDashboard::class);
        
        // Set the load times manually
        $component->set('loadTime', 100);
        $component->set('comparisonTime', 400);
        
        // Calculate improvement percentage (should be 75%)
        $result = $component->instance()->getImprovementPercentage();
        
        $this->assertEquals(75, $result);
    }
} 