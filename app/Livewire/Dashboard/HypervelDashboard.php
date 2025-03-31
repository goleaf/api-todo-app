<?php

namespace App\Livewire\Dashboard;

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HypervelDashboard extends Component
{
    public $dashboardData = [];
    public $isLoading = true;
    public $loadTime = 0;
    public $comparisonTime = 0;
    public $errorMessage = null;
    public $showComparison = false;

    protected $hypervelService;

    public function boot(HypervelService $hypervelService)
    {
        $this->hypervelService = $hypervelService;
    }

    public function mount()
    {
        $this->loadDashboard();
    }

    public function loadDashboard()
    {
        $this->isLoading = true;
        $this->errorMessage = null;
        
        try {
            // Load dashboard data concurrently using Hypervel
            $startTime = microtime(true);
            
            $this->dashboardData = $this->hypervelService->runConcurrently([
                'tasks' => fn() => $this->getTasks(),
                'taskStats' => fn() => $this->getTaskStats(),
                'recentActivity' => fn() => $this->getRecentActivity(),
                'upcomingTasks' => fn() => $this->getUpcomingTasks(),
                'popularCategories' => fn() => $this->getPopularCategories(),
            ]);
            
            $this->loadTime = round((microtime(true) - $startTime) * 1000);
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->errorMessage = "Error loading dashboard: " . $e->getMessage();
            $this->isLoading = false;
        }
    }

    public function compareSequentialLoading()
    {
        $this->showComparison = true;
        
        try {
            // Load the same data sequentially to compare performance
            $startTime = microtime(true);
            
            $sequentialData = [
                'tasks' => $this->getTasks(),
                'taskStats' => $this->getTaskStats(),
                'recentActivity' => $this->getRecentActivity(),
                'upcomingTasks' => $this->getUpcomingTasks(),
                'popularCategories' => $this->getPopularCategories(),
            ];
            
            $this->comparisonTime = round((microtime(true) - $startTime) * 1000);
        } catch (\Exception $e) {
            $this->errorMessage = "Error during comparison: " . $e->getMessage();
        }
    }

    public function toggleComparison()
    {
        if (!$this->showComparison) {
            $this->compareSequentialLoading();
        } else {
            $this->showComparison = false;
        }
    }

    public function refreshDashboard()
    {
        $this->loadDashboard();
    }

    protected function getTasks()
    {
        // Simulate a delay to represent a complex query or API call
        usleep(100000); // 100ms delay
        return Task::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();
    }

    protected function getTaskStats()
    {
        usleep(150000); // 150ms delay
        return [
            'total' => Task::where('user_id', Auth::id())->count(),
            'completed' => Task::where('user_id', Auth::id())->where('status', 'completed')->count(),
            'pending' => Task::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'overdue' => Task::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->count(),
        ];
    }

    protected function getRecentActivity()
    {
        usleep(120000); // 120ms delay
        return Task::where('user_id', Auth::id())
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
    }

    protected function getUpcomingTasks()
    {
        usleep(130000); // 130ms delay
        return Task::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();
    }

    protected function getPopularCategories()
    {
        usleep(180000); // 180ms delay
        // This would be a more complex query in a real app
        return [
            ['name' => 'Work', 'count' => 12],
            ['name' => 'Personal', 'count' => 8],
            ['name' => 'Shopping', 'count' => 5],
            ['name' => 'Health', 'count' => 3],
        ];
    }

    public function getImprovementPercentage()
    {
        if ($this->comparisonTime > 0 && $this->loadTime > 0) {
            $improvement = (($this->comparisonTime - $this->loadTime) / $this->comparisonTime) * 100;
            return round($improvement);
        }
        return 0;
    }

    public function render()
    {
        return view('livewire.dashboard.hypervel-dashboard');
    }
} 