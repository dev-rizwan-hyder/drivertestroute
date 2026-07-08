<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('address');
            $table->timestamps();
        });

        Schema::table('driving_routes', function (Blueprint $table) {
            $table->foreignId('city_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });

        Schema::dropIfExists('cities');
    }
};
