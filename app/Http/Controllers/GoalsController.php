<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GoalsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goals = Goal::where('user_id', $user->id)
            ->orderBy('priority', 'desc')
            ->orderBy('target_date', 'asc')
            ->get();

        // Calculate summary statistics
        $activeGoals = $goals->where('status', 'active');
        $completedGoals = $goals->where('status', 'completed');
        $totalTargetAmount = $activeGoals->sum('target_amount');
        $totalCurrentAmount = $activeGoals->sum('current_amount');
        $overallProgress = $totalTargetAmount > 0 ? ($totalCurrentAmount / $totalTargetAmount) * 100 : 0;

        return Inertia::render('Goals/Index', [
            'data' => [
                'user' => $user,
                'goals' => $goals,
                'summary' => [
                    'total_goals' => $goals->count(),
                    'active_goals' => $activeGoals->count(),
                    'completed_goals' => $completedGoals->count(),
                    'total_target_amount' => $totalTargetAmount,
                    'total_current_amount' => $totalCurrentAmount,
                    'overall_progress' => $overallProgress,
                ]
            ]
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        
        return Inertia::render('Goals/Create', [
            'data' => [
                'user' => $user,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:savings,debt_payoff,income,expense_reduction,investment,other',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date|after:today',
            'start_date' => 'required|date|before_or_equal:target_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'total_contributions' => $request->current_amount ?? 0, // Initial amount is also a contribution
            'target_date' => $request->target_date,
            'start_date' => $request->start_date,
            'status' => 'active',
            'priority' => $request->priority,
            'color' => $request->color ?? '#3b82f6',
            'icon' => $request->icon ?? '🎯',
            'notes' => $request->notes,
        ]);

        // If initial amount is provided, create a contribution record
        if ($request->current_amount && $request->current_amount > 0) {
            GoalContribution::create([
                'goal_id' => $goal->id,
                'user_id' => Auth::id(),
                'amount' => $request->current_amount,
                'contribution_date' => now()->toDateString(),
                'notes' => 'Initial contribution',
            ]);
        }

        return redirect()->route('goals.index')->with('success', 'Goal created successfully!');
    }

    public function show(Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Goals/Show', [
            'data' => [
                'user' => $user,
                'goal' => $goal,
            ]
        ]);
    }

    public function edit(Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Goals/Edit', [
            'data' => [
                'user' => $user,
                'goal' => $goal,
            ]
        ]);
    }

    public function update(Request $request, Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:savings,debt_payoff,income,expense_reduction,investment,other',
            'target_amount' => 'required|numeric|min:0',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'required|date',
            'start_date' => 'required|date|before_or_equal:target_date',
            'status' => 'required|in:active,completed,paused,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $goal->update($request->all());

        return redirect()->route('goals.index')->with('success', 'Goal updated successfully!');
    }

    public function destroy(Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Goal deleted successfully!');
    }

    public function updateProgress(Request $request, Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'current_amount' => 'required|numeric|min:0',
        ]);

        $goal->update([
            'current_amount' => $request->current_amount,
            'status' => $request->current_amount >= $goal->target_amount ? 'completed' : $goal->status,
        ]);

        return redirect()->back()->with('success', 'Goal progress updated successfully!');
    }

    public function contribute(Request $request, Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'contribution_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $goal, $user) {
            // Create contribution record
            GoalContribution::create([
                'goal_id' => $goal->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'contribution_date' => $request->contribution_date,
                'notes' => $request->notes,
            ]);

            // Update goal's current amount and total contributions
            $goal->increment('current_amount', $request->amount);
            $goal->increment('total_contributions', $request->amount);

            // Check if goal is completed
            if ($goal->fresh()->current_amount >= $goal->target_amount) {
                $goal->update(['status' => 'completed']);
            }
        });

        return redirect()->back()->with('success', 'Contribution recorded successfully!');
    }

    public function getContributions(Goal $goal)
    {
        $user = Auth::user();
        
        // Ensure user owns this goal
        if ($goal->user_id !== $user->id) {
            abort(403);
        }

        $contributions = $goal->contributions()
            ->orderBy('contribution_date', 'desc')
            ->get();

        return response()->json($contributions);
    }
}
