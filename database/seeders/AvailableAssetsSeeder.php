<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvailableAssetsSeeder extends Seeder
{
    public function run(): void
    {
        $year  = now()->year;
        $now   = now();

        $brands = ['Dell', 'HP', 'Asus', 'Lenovo', 'Acer', 'Samsung', 'Canon', 'Epson', 'Panasonic'];
        $models = ['ProBook', 'ThinkPad', 'VivoBook', 'IdeaPad', 'Pavilion', 'Inspiron', 'Aspire', 'EliteBook'];

        $categoryPool = [
            ['IT',     'itequipment',     'Desktop'],
            ['IT',     'itequipment',     'Laptop'],
            ['IT',     'itequipment',     'Monitor'],
            ['IT',     'itequipment',     'Printer'],
            ['IT',     'itequipment',     'Router'],
            ['NON-IT', 'officefurniture', 'Chair'],
            ['NON-IT', 'officefurniture', 'Desk'],
            ['NON-IT', 'appliances',      'Air Conditioner'],
            ['NON-IT', 'appliances',      'Water Dispenser'],
            ['NON-IT', 'vehicles',        'Service Vehicle'],
        ];

        $conditionPool = array_merge(
            array_fill(0, 70, 'Good'),
            array_fill(0, 15, 'Defective'),
            array_fill(0, 10, 'Repair'),
            array_fill(0, 5,  'Replace')
        );

        $farms = [
            'BFC' => 20, 'BDL' => 12, 'PFC' => 10,
            'RH'  => 8,  'BBGC' => 6, 'HATCHERY' => 4,
        ];

        $departments = [
            'IT & SECURITY', 'SWINE', 'POULTRY',
            'GENERAL SERVICES', 'PURCHASING', 'FEEDMILL',
        ];

        // Start counter after existing assets to avoid ref_id collision
        $lastRef = DB::table('assets')
            ->where('ref_id', 'LIKE', "FA-{$year}-%")
            ->orderByDesc('ref_id')
            ->value('ref_id');

        $counter = $lastRef
            ? ((int) substr($lastRef, -5)) + 1
            : 1;

        // ~75% Available, ~25% Pending Acquisition
        $statusPool = array_merge(
            array_fill(0, 75, 'Available'),
            array_fill(0, 25, 'Pending Acquisition')
        );

        $inserted = 0;

        foreach ($farms as $farm => $count) {
            for ($i = 0; $i < $count; $i++) {
                $cat       = $categoryPool[array_rand($categoryPool)];
                $condition = $conditionPool[array_rand($conditionPool)];
                $status    = $statusPool[array_rand($statusPool)];

                DB::table('assets')->insert([
                    'is_deleted'        => false,
                    'is_archived'       => false,
                    'ref_id'            => sprintf('FA-%s-%05d', $year, $counter++),
                    'category_type'     => $cat[0],
                    'category'          => $cat[1],
                    'sub_category'      => $cat[2],
                    'brand'             => $brands[array_rand($brands)],
                    'model'             => $models[array_rand($models)],
                    'status'            => $status,
                    'condition'         => $condition,
                    'acquisition_date'  => $now->copy()->subDays(rand(30, 1500))->format('Y-m-d'),
                    'item_cost'         => rand(5000, 120000),
                    'depreciated_value' => rand(1000, 60000),
                    'usable_life'       => (string) rand(3, 10),
                    'assigned_id'       => null,
                    'assigned_name'     => null,
                    'farm'              => $farm,
                    'department'        => $departments[array_rand($departments)],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $inserted++;
            }
        }

        $this->command->info("Available assets seeded: {$inserted}");
    }
}
