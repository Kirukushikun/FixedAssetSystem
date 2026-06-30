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
        Schema::create('asset_investigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->enum('status', ['Investigating', 'Resolved'])->default('Investigating');
            $table->enum('resolution', ['Found', 'Written Off'])->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('opened_by_user_id')->nullable();
            $table->string('opened_by_name')->nullable();
            $table->unsignedBigInteger('resolved_by_user_id')->nullable();
            $table->string('resolved_by_name')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_investigations');
    }
};
