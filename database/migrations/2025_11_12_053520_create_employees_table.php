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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_deleted')->default(false);
            $table->string('employee_id');
            $table->string('employee_name');
            $table->string('position');
            $table->string('farm');
            $table->string('department');
            $table->integer('assigned_assets')->default(0);
            $table->json('flags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
