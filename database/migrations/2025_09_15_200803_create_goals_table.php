<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Emergency Fund", "Vacation to Europe"
            $table->text('description')->nullable();
            $table->enum('type', ['savings', 'debt_payoff', 'income', 'expense_reduction', 'investment', 'other'])->default('savings');
            $table->decimal('target_amount', 15, 2); // Target amount to achieve
            $table->decimal('current_amount', 15, 2)->default(0); // Current progress
            $table->date('target_date'); // When to achieve the goal
            $table->date('start_date'); // When the goal started
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])->default('active');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->string('color', 7)->default('#3b82f6'); // Color for UI display
            $table->string('icon', 50)->default('🎯'); // Icon for UI display
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'target_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
