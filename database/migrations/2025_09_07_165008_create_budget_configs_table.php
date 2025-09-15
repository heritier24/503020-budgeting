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
        Schema::create('budget_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('needs_percentage', 5, 2)->default(50.00);
            $table->decimal('wants_percentage', 5, 2)->default(30.00);
            $table->decimal('savings_percentage', 5, 2)->default(20.00);
            $table->enum('calculation_method', ['fixed', 'variable', 'adaptive'])->default('adaptive');
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_configs');
    }
};
