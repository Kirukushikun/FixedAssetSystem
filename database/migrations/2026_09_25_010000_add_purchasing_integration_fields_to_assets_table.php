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
        Schema::table('assets', function (Blueprint $table) {
            $table->string('purchase_reference_id')->nullable()->unique()->after('snipe_id');
            $table->string('acquisition_source')->default('manual')->after('purchase_reference_id');
            $table->json('purchasing_meta')->nullable()->after('acquisition_source');
            $table->timestamp('purchasing_synced_at')->nullable()->after('purchasing_meta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['purchase_reference_id', 'acquisition_source', 'purchasing_meta', 'purchasing_synced_at']);
        });
    }
};
