<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->unsignedInteger('route_duration_minutes')->nullable()->after('description');
            $table->decimal('route_length_km', 8, 2)->nullable()->after('route_duration_minutes');
            $table->decimal('start_lat', 10, 7)->nullable()->change();
            $table->decimal('start_lng', 10, 7)->nullable()->change();
            $table->decimal('end_lat', 10, 7)->nullable()->change();
            $table->decimal('end_lng', 10, 7)->nullable()->change();
        });

        Schema::table('driving_route_points', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->change();
            $table->decimal('lng', 10, 7)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('driving_route_points', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable(false)->change();
            $table->decimal('lng', 10, 7)->nullable(false)->change();
        });

        Schema::table('driving_routes', function (Blueprint $table) {
            $table->decimal('start_lat', 10, 7)->nullable(false)->change();
            $table->decimal('start_lng', 10, 7)->nullable(false)->change();
            $table->decimal('end_lat', 10, 7)->nullable(false)->change();
            $table->decimal('end_lng', 10, 7)->nullable(false)->change();
            $table->dropColumn(['route_duration_minutes', 'route_length_km']);
        });
    }
};
