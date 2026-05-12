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
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('status');
            $table->string('external_farm')->nullable()->after('is_external');
            $table->string('external_department')->nullable()->after('external_farm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'external_farm', 'external_department']);
        });
    }
};
