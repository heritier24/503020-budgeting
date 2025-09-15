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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Bank ABC Loan", "John's Personal Loan"
            $table->string('lender_name'); // Bank, Person, Institution
            $table->enum('lender_type', ['bank', 'personal', 'credit_card', 'business', 'other'])->default('personal');
            $table->enum('loan_type', ['personal', 'business', 'education', 'mortgage', 'auto', 'other'])->default('personal');
            $table->decimal('original_amount', 15, 2); // Original loan amount
            $table->decimal('current_balance', 15, 2); // Current remaining balance
            $table->decimal('interest_rate', 5, 2)->nullable(); // Annual interest rate (optional for now)
            $table->decimal('monthly_payment', 15, 2); // Monthly payment amount
            $table->enum('payment_frequency', ['weekly', 'bi-weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date')->nullable(); // When loan should be paid off
            $table->enum('status', ['active', 'paid_off', 'defaulted', 'paused'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'lender_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
