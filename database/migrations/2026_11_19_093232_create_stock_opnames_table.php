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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('opname_date');
            $table->decimal('system_stock', 12, 2);
            $table->decimal('physical_stock', 12, 2);
            $table->decimal('difference', 12, 2); // physical_stock - system_stock
            $table->decimal('cost_difference', 14, 2)->default(0.00); // difference * cost_per_unit
            $table->string('status')->default('adjusted'); // draft, adjusted, approved
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
