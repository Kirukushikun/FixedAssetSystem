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
            $table->unsignedBigInteger('snipe_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_archived')->default(false);

            $table->string('ref_id'); 
            $table->string('category_type'); // e.g. IT, NON-IT
            $table->string('category'); // e.g. it, office, appliances, audio, tools, kitchen 
            $table->string('sub_category'); 
            // it is a category and category type is (IT, NON-IT)
            // it: Desktop, Laptop, Router, non-it: Printer, Photocopy Machine, Dogital Camera
            // office: Table. Office Chair, Office table, Conference Table, Monoblock Chair
            // Aapliances: Aircon, Refrigerator, Washing Machine, Microwave Oven, Water Dispenser, Washing , wallf fan
            // audio: Amplifier, Speaker, Microphone,   
            // tools: Drill, Grinder, Saw, Sander, Pipe Wrench, Hammer, Screwdriver
            // kitchen: Stove, Oven, Mixer, Toaster, Blender



            $table->string('brand'); // e.g. Dell, HP, Canon, Nikon, Samsung, LG, Asus, Acer
            $table->string('model'); // e.g. Inspiron, Pavilion, EOS, Galaxy, ThinkPad
            $table->string('status'); // GOOD, DEFECTIVE, REPAIR, REPLACE
            $table->string('condition');// Available, Issued, Transferred, For Disposal, Disposed, Lost

            $table->date('acquisition_date');
            $table->string('item_cost')->nullable();
            $table->string('depreciated_value')->nullable();
            $table->string('usable_life')->nullable();

            // Ignore below for now

            $table->json('technical_data')->nullable();

            $table->string('assigned_name')->nullable();
            $table->string('assigned_id')->nullable();
            $table->string('farm')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();

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
