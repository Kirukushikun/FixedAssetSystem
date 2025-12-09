<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create(
            [
                'id' => 1,
                'name' => 'Adam Trinidad',
            ]
        );
        User::factory()->create(
            [
                'id' => 61,
                'name' => 'Iverson Craig',
            ]
        );
        User::factory()->create(
            [
                'id' => 5,
                'name' => 'Jeffrey Montiano',
            ]
        );

        $categories = [
            'IT' => [
                'it' => [
                    'Desktop', 'Laptop', 'Router', 'Switch',
                    'Firewall', 'Server', 'Monitor', 'Keyboard'
                ],
            ],
            'NON-IT' => [
                'it' => [
                    'Printer', 'Photocopy Machine', 'Digital Camera', 'Scanner',
                    'Projector', 'CCTV Camera'
                ],
                'office' => [
                    'Table', 'Office Chair', 'Office Table', 'Conference Table',
                    'Monoblock Chair', 'Filing Cabinet', 'Bookshelf', 'Whiteboard'
                ],
                'appliances' => [
                    'Aircon', 'Refrigerator', 'Washing Machine', 'Microwave Oven',
                    'Water Dispenser', 'Wall Fan', 'Ceiling Fan', 'Rice Cooker'
                ],
                'audio' => [
                    'Amplifier', 'Speaker', 'Microphone', 'Mixer Console',
                    'Headset', 'PA System'
                ],
                'tools' => [
                    'Drill', 'Grinder', 'Saw', 'Sander', 'Pipe Wrench',
                    'Hammer', 'Screwdriver', 'Pliers', 'Measuring Tape'
                ],
                'kitchen' => [
                    'Stove', 'Oven', 'Mixer', 'Toaster', 'Blender',
                    'Fryer', 'Rice Cooker', 'Steamer'
                ],
            ],
        ];

        $brands = ['Dell', 'HP', 'Canon', 'Nikon', 'Samsung', 'LG', 'Asus', 'Acer', 'Lenovo', 'Sony'];
        $models = ['Inspiron', 'Pavilion', 'EOS', 'Galaxy', 'ThinkPad', 'VivoBook', 'IdeaPad', 'ProBook'];
        $conditions = ['Good', 'Defective', 'Repair', 'Replace'];
        $statuses = ['Available', 'Issued', 'Transferred', 'For Disposal', 'Disposed', 'Lost'];
        $farms = ['BFC', 'BDL', 'PFC', 'RH'];

        $counter = 1;
        $year = date('Y');
        $totalInserted = 0;

        // --- Generate from categories ---
        foreach ($categories as $categoryType => $categoryGroup) {
            foreach ($categoryGroup as $category => $subCategories) {
                foreach ($subCategories as $subCategory) {

                    // Generate 5–20 per subcategory
                    $assetCount = rand(5, 20);

                    for ($i = 0; $i < $assetCount; $i++) {

                        DB::table('assets')->insert([
                            'is_deleted' => false,
                            'is_archived' => rand(0, 10) > 8,
                            'ref_id' => sprintf('FA-%s-%05d', $year, $counter++),
                            'category_type' => $categoryType,
                            'category' => $category,
                            'sub_category' => $subCategory,
                            'brand' => $brands[array_rand($brands)],
                            'model' => $models[array_rand($models)],
                            'status' => $statuses[array_rand($statuses)],
                            'condition' => $conditions[array_rand($conditions)],
                            'acquisition_date' => Carbon::now()->subDays(rand(1, 1825))->format('Y-m-d'),
                            'item_cost' => rand(5000, 100000),
                            'depreciated_value' => rand(1000, 50000),
                            'usable_life' => rand(3, 10) . ' years',
                            'farm' => $farms[array_rand($farms)],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $totalInserted++;
                    }
                }
            }
        }

        // --- Ensure minimum 3000 assets ---
        while ($totalInserted < 3000) {
            DB::table('assets')->insert([
                'is_deleted' => false,
                'is_archived' => rand(0, 10) > 8,
                'ref_id' => sprintf('FA-%s-%05d', $year, $counter++),
                'category_type' => 'NON-IT',
                'category' => 'office',
                'sub_category' => 'Table',
                'brand' => $brands[array_rand($brands)],
                'model' => $models[array_rand($models)],
                'status' => $statuses[array_rand($statuses)],
                'condition' => $conditions[array_rand($conditions)],
                'acquisition_date' => Carbon::now()->subDays(rand(1, 1825))->format('Y-m-d'),
                'item_cost' => rand(5000, 100000),
                'depreciated_value' => rand(1000, 50000),
                'usable_life' => rand(3, 10) . ' years',
                'farm' => $farms[array_rand($farms)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalInserted++;
        }
        $this->command->info('Assets seeded successfully! Total assets: ' . ($counter - 1));
    }
}
