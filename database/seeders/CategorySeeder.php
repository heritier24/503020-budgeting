<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            // Needs (50%)
            [
                'name' => 'Housing',
                'type' => 'needs',
                'color' => '#ef4444',
                'icon' => 'home',
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Utilities',
                'type' => 'needs',
                'color' => '#f59e0b',
                'icon' => 'bolt',
                'is_default' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Groceries',
                'type' => 'needs',
                'color' => '#10b981',
                'icon' => 'shopping-cart',
                'is_default' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Transportation',
                'type' => 'needs',
                'color' => '#3b82f6',
                'icon' => 'car',
                'is_default' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Healthcare',
                'type' => 'needs',
                'color' => '#8b5cf6',
                'icon' => 'heart',
                'is_default' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Insurance',
                'type' => 'needs',
                'color' => '#06b6d4',
                'icon' => 'shield-check',
                'is_default' => true,
                'sort_order' => 6,
            ],

            // Wants (30%)
            [
                'name' => 'Entertainment',
                'type' => 'wants',
                'color' => '#ec4899',
                'icon' => 'film',
                'is_default' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Dining Out',
                'type' => 'wants',
                'color' => '#f97316',
                'icon' => 'utensils',
                'is_default' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'Shopping',
                'type' => 'wants',
                'color' => '#84cc16',
                'icon' => 'shopping-bag',
                'is_default' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Hobbies',
                'type' => 'wants',
                'color' => '#6366f1',
                'icon' => 'puzzle-piece',
                'is_default' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Travel',
                'type' => 'wants',
                'color' => '#14b8a6',
                'icon' => 'plane',
                'is_default' => true,
                'sort_order' => 11,
            ],

            // Savings (20%)
            [
                'name' => 'Emergency Fund',
                'type' => 'savings',
                'color' => '#059669',
                'icon' => 'piggy-bank',
                'is_default' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Retirement',
                'type' => 'savings',
                'color' => '#0d9488',
                'icon' => 'chart-line',
                'is_default' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Investments',
                'type' => 'savings',
                'color' => '#0891b2',
                'icon' => 'trending-up',
                'is_default' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Education',
                'type' => 'savings',
                'color' => '#7c3aed',
                'icon' => 'graduation-cap',
                'is_default' => true,
                'sort_order' => 15,
            ],

            // Income Categories
            [
                'name' => 'Salary',
                'type' => 'income',
                'color' => '#10b981',
                'icon' => 'briefcase',
                'is_default' => true,
                'sort_order' => 16,
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'color' => '#3b82f6',
                'icon' => 'laptop',
                'is_default' => true,
                'sort_order' => 17,
            ],
            [
                'name' => 'Business',
                'type' => 'income',
                'color' => '#8b5cf6',
                'icon' => 'store',
                'is_default' => true,
                'sort_order' => 18,
            ],
            [
                'name' => 'Investments',
                'type' => 'income',
                'color' => '#f59e0b',
                'icon' => 'trending-up',
                'is_default' => true,
                'sort_order' => 19,
            ],
            [
                'name' => 'Gifts',
                'type' => 'income',
                'color' => '#ec4899',
                'icon' => 'gift',
                'is_default' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Other Income',
                'type' => 'income',
                'color' => '#6b7280',
                'icon' => 'plus-circle',
                'is_default' => true,
                'sort_order' => 21,
            ],
        ];

        foreach ($defaultCategories as $category) {
            Category::create($category);
        }
    }
}
