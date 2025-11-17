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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->string('ref_id');
            $table->string('category_type');
            $table->string('category');
            $table->string('sub_category');

            $table->string('brand');
            $table->string('model');
            $table->string('status');
            $table->string('condition');

            $table->date('acquisition_date');
            $table->string('item_cost')->nullable();
            $table->string('depreciated_value')->nullable();
            $table->string('usable_life')->nullable();

            $table->json('technical_data')->nullable();

            $table->string('assigned_name')->nullable();
            $table->string('assigned_id')->nullable();
            $table->string('farm')->nullable();
            $table->string('department')->nullable();

            $table->text('qr_code')->nullable();
            $table->string('attachment')->nullable();
            $table->text('attachment_name')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
