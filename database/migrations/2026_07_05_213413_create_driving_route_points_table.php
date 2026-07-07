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
        Schema::create('driving_route_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driving_route_id')->constrained()->cascadeOnDelete();

            $table->integer('sort_order');
            $table->string('instruction')->nullable();
            // Example: Turn right onto Daniel Street North

            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            $table->decimal('distance_km', 8, 2)->nullable();
            $table->string('duration')->nullable(); // 1 min
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driving_route_points');
    }
};
