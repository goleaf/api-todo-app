<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use HalilCosdu\Slower\Models\SlowLog;
use Illuminate\Http\Request;

class SlowQueryController extends AdminController
{
    /**
     * Display a listing of slow queries.
     */
    public function index(Request $request)
    {
        $query = SlowLog::query();
        
        if ($search = $request->input('search')) {
            $query->where('sql', 'like', "%{$search}%")
                  ->orWhere('raw_sql', 'like', "%{$search}%");
        }
        
        if ($timeFilter = $request->input('time_filter')) {
            if ($timeFilter === 'today') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($timeFilter === 'week') {
                $query->whereDate('created_at', '>=', now()->subWeek()->toDateString());
            } elseif ($timeFilter === 'month') {
                $query->whereDate('created_at', '>=', now()->subMonth()->toDateString());
            }
        }
        
        if ($minTime = $request->input('min_time')) {
            $query->where('time', '>=', $minTime);
        }
        
        if ($request->has('analyzed')) {
            $query->where('is_analyzed', $request->boolean('analyzed'));
        }
        
        $slowQueries = $query->latest()->paginate(20);
        
        return view('admin.slow-queries.index', compact('slowQueries'));
    }
    
    /**
     * Display the specified slow query details.
     */
    public function show(SlowLog $slowQuery)
    {
        return view('admin.slow-queries.show', compact('slowQuery'));
    }
    
    /**
     * Mark a slow query as analyzed.
     */
    public function markAsAnalyzed(SlowLog $slowQuery)
    {
        $slowQuery->update(['is_analyzed' => true]);
        
        return redirect()->route('admin.slow-queries.index')
            ->with('success', 'Query marked as analyzed successfully.');
    }
    
    /**
     * Delete a slow query log.
     */
    public function destroy(SlowLog $slowQuery)
    {
        $slowQuery->delete();
        
        return redirect()->route('admin.slow-queries.index')
            ->with('success', 'Slow query log deleted successfully.');
    }
    
    /**
     * Clear all slow query logs.
     */
    public function clearAll()
    {
        SlowLog::truncate();
        
        return redirect()->route('admin.slow-queries.index')
            ->with('success', 'All slow query logs cleared successfully.');
    }
} 