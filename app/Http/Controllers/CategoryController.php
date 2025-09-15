<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();
        
        // Get user's custom categories and default categories
        $categories = Category::where('user_id', $user->id)
            ->orWhereNull('user_id')
            ->orderBy('type')
            ->orderBy('sort_order')
            ->get();

        // Group categories by type
        $groupedCategories = $categories->groupBy('type');

        return Inertia::render('Categories/Index', [
            'data' => [
                'user' => $user,
                'categories' => $categories,
                'groupedCategories' => $groupedCategories,
            ]
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): Response
    {
        return Inertia::render('Categories/Create', [
            'data' => [
                'user' => Auth::user()
            ]
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:needs,wants,savings,income',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        // Get the next sort order for this type
        $maxSortOrder = Category::where('user_id', $user->id)
            ->where('type', $request->type)
            ->max('sort_order') ?? 0;

        $category = Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? '#6b7280',
            'icon' => $request->icon ?? 'tag',
            'is_default' => false,
            'is_active' => true,
            'sort_order' => $maxSortOrder + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): Response
    {
        // Ensure category belongs to user or is a default category
        if ($category->user_id !== Auth::id() && $category->user_id !== null) {
            abort(403);
        }

        return Inertia::render('Categories/Edit', [
            'data' => [
                'user' => Auth::user(),
                'category' => $category
            ]
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        // Ensure category belongs to user or is a default category
        if ($category->user_id !== Auth::id() && $category->user_id !== null) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:needs,wants,savings,income',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color ?? $category->color,
            'icon' => $request->icon ?? $category->icon,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Ensure category belongs to user (can't delete default categories)
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if category is being used in transactions
        $transactionCount = $category->transactions()->count();
        if ($transactionCount > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete category. It's being used in {$transactionCount} transaction(s)."
                ], 422);
            }
            return redirect()->back()->with('error', "Cannot delete category. It's being used in {$transactionCount} transaction(s).");
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}