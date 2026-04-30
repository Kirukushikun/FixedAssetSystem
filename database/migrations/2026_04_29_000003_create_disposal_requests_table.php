<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposal_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('requested_by_user_id')->nullable();
            $table->string('requested_by_name')->nullable();
            $table->string('requester_farm')->nullable();
            $table->string('requester_department')->nullable();
            $table->text('reason');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('status')->default('Pending VP Approval');
            $table->unsignedBigInteger('vp_approved_by_user_id')->nullable();
            $table->string('vp_approved_by_name')->nullable();
            $table->timestamp('vp_approved_at')->nullable();
            $table->unsignedBigInteger('accounting_disposed_by_user_id')->nullable();
            $table->string('accounting_disposed_by_name')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->foreign('requested_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('vp_approved_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('accounting_disposed_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['status', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_requests');
    }
};
