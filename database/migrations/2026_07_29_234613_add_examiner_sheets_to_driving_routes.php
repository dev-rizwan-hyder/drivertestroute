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
            $table->string('g1_examiner_sheet_path')->nullable()->after('preview_pdf_path')->comment('Path to G1 examiner sheet PDF');
            $table->string('g2_examiner_sheet_path')->nullable()->after('g1_examiner_sheet_path')->comment('Path to G2 examiner sheet PDF');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropColumn(['g1_examiner_sheet_path', 'g2_examiner_sheet_path']);
        });
    }
};
