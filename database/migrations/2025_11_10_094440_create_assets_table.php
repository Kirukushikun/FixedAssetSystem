<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('snipe_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->string('ref_id'); 
            $table->string('category_type'); // IT, NON-IT
            $table->string('category'); // it, office, appliances, audio, tools, kitchen
            $table->string('sub_category');
            $table->string('brand');
            $table->string('model');
            
            // FIXED: Swapped the meanings
            $table->string('condition'); // GOOD, DEFECTIVE, REPAIR, REPLACE
            $table->string('status')->default('Available'); // Available, Issued, Transferred, For Disposal, Disposed, Lost

            $table->date('acquisition_date');
            $table->string('item_cost')->nullable();
            $table->string('depreciated_value')->nullable();
            $table->string('usable_life')->nullable();

            $table->json('technical_data')->nullable();

            // FIXED: Changed to unsignedBigInteger and added foreign key
            $table->unsignedBigInteger('assigned_id')->nullable();
            $table->foreign('assigned_id')->references('id')->on('employees')->onDelete('set null');
            
            $table->string('farm')->nullable();
            $table->string('department')->nullable();

            $table->text('qr_code')->nullable();
            $table->string('attachment')->nullable();
            $table->text('attachment_name')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};