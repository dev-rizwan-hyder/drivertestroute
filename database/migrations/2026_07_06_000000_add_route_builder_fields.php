<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });

        Schema::table('driving_routes', function (Blueprint $table) {
            $table->string('start_label')->nullable();
            $table->string('destination_label')->nullable();
            $table->string('preview_pdf_path')->nullable();
        });

        Schema::table('driving_route_points', function (Blueprint $table) {
            $table->string('maneuver')->default('continue');
        });
    }

    public function down(): void
    {
        Schema::table('driving_route_points', function (Blueprint $table) {
            $table->dropColumn('maneuver');
        });

        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropColumn(['start_label', 'destination_label', 'preview_pdf_path']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
