<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_purchases', function (Blueprint $table) {
            $table->string('payment_provider')->default('local')->after('payment_status');
            $table->string('student_name')->nullable()->after('payment_id');
            $table->string('student_email')->nullable()->after('student_name');
            $table->string('student_phone')->nullable()->after('student_email');
            $table->string('student_city')->nullable()->after('student_phone');
            $table->date('student_test_date')->nullable()->after('student_city');
            $table->text('student_notes')->nullable()->after('student_test_date');
            $table->string('billing_name')->nullable()->after('student_notes');
            $table->string('billing_email')->nullable()->after('billing_name');
        });
    }

    public function down(): void
    {
        Schema::table('route_purchases', function (Blueprint $table) {
            $table->dropColumn([
                'payment_provider',
                'student_name',
                'student_email',
                'student_phone',
                'student_city',
                'student_test_date',
                'student_notes',
                'billing_name',
                'billing_email',
            ]);
        });
    }
};
