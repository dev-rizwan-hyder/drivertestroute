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
        Schema::create('examiner_sheets', function (Blueprint $table) {
            $table->id();
            $table->enum('package_type', ['g1', 'g2'])->unique()->comment('G1 or G2 package type');
            $table->string('file_path')->comment('Path to the examiner sheet PDF');
            $table->string('file_name')->comment('Original filename');
            $table->integer('file_size')->comment('File size in bytes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examiner_sheets');
    }
};
