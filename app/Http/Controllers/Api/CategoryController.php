<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $categories = Category::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('is_default', true);
        })
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get categories by type.
     */
    public function byType(Request $request, string $type): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:needs,wants,savings',
        ]);

        $user = $request->user();
        
        $categories = Category::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('is_default', true);
        })
        ->ofType($type)
        ->active()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Create a new category.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:needs,wants,savings',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $user = $request->user();
        
        $category = Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'color' => $request->color,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category created successfully.',
        ], 201);
    }

    /**
     * Get a specific category.
     */
    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:needs,wants,savings',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($request->only([
            'name', 'type', 'color', 'icon', 'sort_order', 'is_active'
        ]));

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Category updated successfully.',
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        // Check if category has transactions
        if ($category->transactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with existing transactions.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        $user = $request->user();

        foreach ($request->categories as $categoryData) {
            $category = Category::where('id', $categoryData['id'])
                ->where('user_id', $user->id)
                ->first();

            if ($category) {
                $category->update(['sort_order' => $categoryData['sort_order']]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories reordered successfully.',
        ]);
    }
}
