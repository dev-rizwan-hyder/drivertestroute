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
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropColumn(['g1_examiner_sheet_path', 'g2_examiner_sheet_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->string('g1_examiner_sheet_path')->nullable();
            $table->string('g2_examiner_sheet_path')->nullable();
        });
    }
};
