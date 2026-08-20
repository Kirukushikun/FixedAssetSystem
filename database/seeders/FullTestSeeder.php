<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FullTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AnalyticsTestSeeder::class);
        $this->call(AvailableAssetsSeeder::class);
    }
}
