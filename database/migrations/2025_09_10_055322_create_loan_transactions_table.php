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
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['payment', 'disbursement', 'interest_charge', 'fee', 'adjustment'])->default('payment');
            $table->decimal('amount', 15, 2); // Total transaction amount
            $table->decimal('principal_amount', 15, 2)->nullable(); // Principal portion (for payments)
            $table->decimal('interest_amount', 15, 2)->nullable(); // Interest portion (for payments)
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['loan_id', 'transaction_date']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};
