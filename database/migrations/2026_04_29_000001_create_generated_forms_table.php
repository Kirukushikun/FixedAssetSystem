<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('asset_id')->nullable();
            $table->string('form_type');
            $table->string('title');
            $table->unsignedBigInteger('generated_by_user_id')->nullable();
            $table->json('snapshot');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->nullOnDelete();
            $table->foreign('generated_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['employee_id', 'form_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_forms');
    }
};
