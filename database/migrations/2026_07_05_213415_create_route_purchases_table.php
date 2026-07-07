<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('driving_route_id')
                ->constrained('driving_routes')
                ->cascadeOnDelete();

            $table->string('payment_status')->default('pending');
            $table->string('payment_id')->nullable();
            $table->timestamp('purchased_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'driving_route_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_purchases');
    }
};