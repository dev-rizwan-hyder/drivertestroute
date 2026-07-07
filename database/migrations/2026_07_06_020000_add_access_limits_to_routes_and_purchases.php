<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driving_routes', function (Blueprint $table) {
            $table->unsignedInteger('access_limit')->default(1)->after('price');
        });

        Schema::table('route_purchases', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('payment_id');
            $table->unsignedInteger('access_limit')->default(1)->after('amount_paid');
            $table->unsignedInteger('access_used')->default(0)->after('access_limit');
            $table->timestamp('last_accessed_at')->nullable()->after('purchased_at');
        });
    }

    public function down(): void
    {
        Schema::table('route_purchases', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'access_limit', 'access_used', 'last_accessed_at']);
        });

        Schema::table('driving_routes', function (Blueprint $table) {
            $table->dropColumn('access_limit');
        });
    }
};
