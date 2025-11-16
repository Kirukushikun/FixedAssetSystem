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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->string('asset_id');
            $table->string('assignee_id')->nullable();
            $table->string('assignee_name')->nullable();
            $table->string('status')->nullable();
            $table->string('condition')->nullable();
            $table->string('farm')->nullable();
            $table->string('department')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
