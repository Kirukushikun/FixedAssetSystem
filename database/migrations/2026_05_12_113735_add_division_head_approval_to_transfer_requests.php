<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('division_head_approved_by_user_id')->nullable()->after('status');
            $table->string('division_head_approved_by_name')->nullable()->after('division_head_approved_by_user_id');
            $table->timestamp('division_head_approved_at')->nullable()->after('division_head_approved_by_name');
        });

        // Update existing records to change status from 'pending' to 'Pending Division Head Approval'
        DB::table('transfer_requests')
            ->where('status', 'pending')
            ->update(['status' => 'Pending Division Head Approval']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_requests', function (Blueprint $table) {
            $table->dropColumn(['division_head_approved_by_user_id', 'division_head_approved_by_name', 'division_head_approved_at']);
        });

        // Revert status changes
        DB::table('transfer_requests')
            ->where('status', 'Pending Division Head Approval')
            ->update(['status' => 'pending']);
    }
};
