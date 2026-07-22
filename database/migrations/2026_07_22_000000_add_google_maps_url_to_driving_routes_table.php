<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->text('google_maps_url')->nullable()->after('preview_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropColumn('google_maps_url');
        });
    }
};
