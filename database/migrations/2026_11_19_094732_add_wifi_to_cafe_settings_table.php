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
        Schema::table('cafe_settings', function (Blueprint $table) {
            $table->string('wifi_name')->nullable()->after('address');
            $table->string('wifi_password')->nullable()->after('wifi_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafe_settings', function (Blueprint $table) {
            $table->dropColumn(['wifi_name', 'wifi_password']);
        });
    }
};
