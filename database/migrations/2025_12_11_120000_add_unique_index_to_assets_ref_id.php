<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will abort if there are duplicate `ref_id` values.
     * Resolve duplicates first (see instructions) and re-run.
     *
     * @return void
     */
    public function up()
    {
        // Check for duplicates first
        $duplicates = DB::table('assets')
            ->select('ref_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('ref_id')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $list = $duplicates->pluck('ref_id')->implode(', ');
            throw new \Exception("Cannot add unique index: duplicate ref_id values found: {$list}. Please resolve duplicates before running this migration.");
        }

        Schema::table('assets', function (Blueprint $table) {
            $table->unique('ref_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['ref_id']);
        });
    }
};
