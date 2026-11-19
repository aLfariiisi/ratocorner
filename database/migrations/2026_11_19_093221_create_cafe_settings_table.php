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
        Schema::create('cafe_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Ratocorner');
            $table->string('tagline')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('service_charge_percentage', 5, 2)->default(0.00);
            $table->text('receipt_header')->nullable();
            $table->text('receipt_footer')->nullable();
            $table->string('currency', 10)->default('IDR');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_settings');
    }
};
