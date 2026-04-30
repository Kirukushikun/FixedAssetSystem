<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_sme_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('reviewed_by_user_id')->nullable();
            $table->string('reviewed_by_name')->nullable();
            $table->string('condition_note')->nullable();
            $table->text('remarks')->nullable();
            $table->string('recommended_flag')->nullable();
            $table->unsignedBigInteger('created_flag_id')->nullable();
            $table->boolean('flagged_employee')->default(false);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->foreign('reviewed_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['employee_id', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_sme_reviews');
    }
};
