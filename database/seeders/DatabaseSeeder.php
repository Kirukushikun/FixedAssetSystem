<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
        $this->call(DynamicFieldSeeder::class);

        // User::factory(10)->create();

        User::factory()->create(
            [
                'id' => 1,
                'name' => 'Adam Trinidad',
                'is_admin' => true,
            ]
        );
        User::factory()->create(
            [
                'id' => 61,
                'name' => 'Iverson Craig',
                'is_admin' => true,
            ]
        );
        User::factory()->create(
            [
                'id' => 5,
                'name' => 'Jeffrey Montiano',
                'is_admin' => true,
            ]
        );

        // Test accounts (password: 1234) — local/testing only, never production
        if (!app()->isProduction()) {
            $this->call(LocalRoleAccountSeeder::class);
        }

        $data = [
            // ── IT ──────────────────────────────────────────────────────────────
            'IT Equipment' => [
                'code' => 'itequipment',
                'icon' => 'desktop',
                'subcategories' => [
                    ['name' => 'Desktop',           'category_type' => 'IT'],
                    ['name' => 'Laptop',            'category_type' => 'IT'],
                    ['name' => 'Server',            'category_type' => 'IT'],
                    ['name' => 'Tablet',            'category_type' => 'IT'],
                    ['name' => 'All-in-One PC',     'category_type' => 'IT'],
                    ['name' => 'Router',            'category_type' => 'IT'],
                    ['name' => 'Switch',            'category_type' => 'IT'],
                    ['name' => 'Firewall',          'category_type' => 'IT'],
                    ['name' => 'Access Point',      'category_type' => 'IT'],
                    ['name' => 'CCTV Camera',       'category_type' => 'IT'],
                    ['name' => 'Monitor',           'category_type' => 'IT'],
                    ['name' => 'Photocopier',       'category_type' => 'IT'],
                    ['name' => 'Scanner',           'category_type' => 'IT'],
                    ['name' => 'Printer',           'category_type' => 'IT'],
                    ['name' => 'UPS',               'category_type' => 'IT'],
                    ['name' => 'Biometric Devices', 'category_type' => 'IT'],
                ],
            ],
            'Software & Apps' => [
                'code' => 'software',
                'icon' => 'folder',
                'subcategories' => [
                    ['name' => 'Software License', 'category_type' => 'IT'],
                    ['name' => 'Subscription',     'category_type' => 'IT'],
                    ['name' => 'Application',      'category_type' => 'IT'],
                ],
            ],

            // ── NON-IT ──────────────────────────────────────────────────────────
            'Communication Devices' => [
                'code' => 'commdevices',
                'icon' => 'folder',
                'subcategories' => [
                    ['name' => 'Telephone',    'category_type' => 'NON-IT'],
                    ['name' => 'Mobile Phones','category_type' => 'NON-IT'],
                    ['name' => 'PABX',         'category_type' => 'NON-IT'],
                    ['name' => 'Two-way Radio','category_type' => 'NON-IT'],
                    ['name' => 'Intercom',     'category_type' => 'NON-IT'],
                ],
            ],
            'Audio Visual' => [
                'code' => 'audiovisual',
                'icon' => 'speaker',
                'subcategories' => [
                    ['name' => 'Television',    'category_type' => 'NON-IT'],
                    ['name' => 'Projector',     'category_type' => 'NON-IT'],
                    ['name' => 'Speaker System','category_type' => 'NON-IT'],
                    ['name' => 'Amplifier',     'category_type' => 'NON-IT'],
                    ['name' => 'Microphone',    'category_type' => 'NON-IT'],
                    ['name' => 'Mixer Console', 'category_type' => 'NON-IT'],
                    ['name' => 'PA System',     'category_type' => 'NON-IT'],
                ],
            ],
            'Office Furniture' => [
                'code' => 'officefurniture',
                'icon' => 'furniture',
                'subcategories' => [
                    ['name' => 'Desk',             'category_type' => 'NON-IT'],
                    ['name' => 'Chair',            'category_type' => 'NON-IT'],
                    ['name' => 'Conference Table', 'category_type' => 'NON-IT'],
                    ['name' => 'Filing Cabinet',   'category_type' => 'NON-IT'],
                    ['name' => 'Bookshelf',        'category_type' => 'NON-IT'],
                    ['name' => 'Whiteboard',       'category_type' => 'NON-IT'],
                    ['name' => 'Partition',        'category_type' => 'NON-IT'],
                ],
            ],
            'Appliances' => [
                'code' => 'appliances',
                'icon' => 'appliances',
                'subcategories' => [
                    ['name' => 'Air Conditioner', 'category_type' => 'NON-IT'],
                    ['name' => 'Refrigerator',    'category_type' => 'NON-IT'],
                    ['name' => 'Water Dispenser', 'category_type' => 'NON-IT'],
                    ['name' => 'Washing Machine', 'category_type' => 'NON-IT'],
                    ['name' => 'Electric Fan',    'category_type' => 'NON-IT'],
                    ['name' => 'Microwave',       'category_type' => 'NON-IT'],
                ],
            ],
            'Kitchen Equipment' => [
                'code' => 'kitchen',
                'icon' => 'kitchen',
                'subcategories' => [
                    ['name' => 'Stove',       'category_type' => 'NON-IT'],
                    ['name' => 'Rice Cooker', 'category_type' => 'NON-IT'],
                    ['name' => 'Oven',        'category_type' => 'NON-IT'],
                    ['name' => 'Blender',     'category_type' => 'NON-IT'],
                    ['name' => 'Steamer',     'category_type' => 'NON-IT'],
                    ['name' => 'Cooking Pot', 'category_type' => 'NON-IT'],
                ],
            ],
            'Vehicles' => [
                'code' => 'vehicles',
                'icon' => 'vehicle',
                'subcategories' => [
                    ['name' => 'Motorcycle',      'category_type' => 'NON-IT'],
                    ['name' => 'Service Vehicle', 'category_type' => 'NON-IT'],
                    ['name' => 'Delivery Truck',  'category_type' => 'NON-IT'],
                    ['name' => 'Utility Vehicle', 'category_type' => 'NON-IT'],
                    ['name' => 'Forklift',        'category_type' => 'NON-IT'],
                ],
            ],
            'Machinery & Equipment' => [
                'code' => 'machinery',
                'icon' => 'tools',
                'subcategories' => [
                    ['name' => 'Generator',        'category_type' => 'NON-IT'],
                    ['name' => 'Air Compressor',   'category_type' => 'NON-IT'],
                    ['name' => 'Water Pump',       'category_type' => 'NON-IT'],
                    ['name' => 'Welding Machine',  'category_type' => 'NON-IT'],
                    ['name' => 'Feedmill Equipment','category_type' => 'NON-IT'],
                    ['name' => 'Biogas Equipment', 'category_type' => 'NON-IT'],
                ],
            ],
            'Farm Equipment' => [
                'code' => 'farmequip',
                'icon' => 'tools',
                'subcategories' => [
                    ['name' => 'Tractor',              'category_type' => 'NON-IT'],
                    ['name' => 'Sprayer',              'category_type' => 'NON-IT'],
                    ['name' => 'Irrigation Equipment', 'category_type' => 'NON-IT'],
                    ['name' => 'Weighing Scale',       'category_type' => 'NON-IT'],
                    ['name' => 'Incubator',            'category_type' => 'NON-IT'],
                ],
            ],
            'Tools & Safety' => [
                'code' => 'tools',
                'icon' => 'tools',
                'subcategories' => [
                    ['name' => 'Hand Tools',           'category_type' => 'NON-IT'],
                    ['name' => 'Power Tools',          'category_type' => 'NON-IT'],
                    ['name' => 'Safety Equipment',     'category_type' => 'NON-IT'],
                    ['name' => 'Measuring Instruments','category_type' => 'NON-IT'],
                ],
            ],
            'Land & Improvements' => [
                'code' => 'land',
                'icon' => 'land',
                'subcategories' => [
                    ['name' => 'Land',             'category_type' => 'NON-IT'],
                    ['name' => 'Road/Pavement',    'category_type' => 'NON-IT'],
                    ['name' => 'Drainage',         'category_type' => 'NON-IT'],
                    ['name' => 'Fencing',          'category_type' => 'NON-IT'],
                    ['name' => 'Land Improvements','category_type' => 'NON-IT'],
                ],
            ],
            'Buildings & Structures' => [
                'code' => 'buildings',
                'icon' => 'building',
                'subcategories' => [
                    ['name' => 'Office Building', 'category_type' => 'NON-IT'],
                    ['name' => 'Warehouse',       'category_type' => 'NON-IT'],
                    ['name' => 'Staff Housing',   'category_type' => 'NON-IT'],
                    ['name' => 'Swine House',     'category_type' => 'NON-IT'],
                    ['name' => 'Poultry House',   'category_type' => 'NON-IT'],
                    ['name' => 'Feed Mill',       'category_type' => 'NON-IT'],
                    ['name' => 'Biogas Plant',    'category_type' => 'NON-IT'],
                    ['name' => 'Hatchery Building','category_type' => 'NON-IT'],
                ],
            ],
        ];

        foreach ($data as $categoryName => $categoryData) {
            $category = Category::updateOrCreate(
                ['name' => $categoryName],
                [
                    'code' => $categoryData['code'],
                    'icon' => $categoryData['icon'],
                ]
            );

            foreach ($categoryData['subcategories'] as $subcat) {
                SubCategory::updateOrCreate(
                    [
                        'name' => $subcat['name'],
                        'category_id' => $category->id,
                    ],
                    [
                        'category_type' => $subcat['category_type']
                    ]
                );
            }
        }

        Cache::forget('categories_with_subcategories');
        Cache::forget('categories_by_code');

        $this->command->info('Categories and Sub Categories seeded successfully!');

        $department = [
            'FEEDMILL',
            'FOC',
            'GENERAL SERVICES',
            'IT & SECURITY',
            'POULTRY',
            'PURCHASING',
            'SALES & ANALYTICS',
            'SWINE',
        ];

        foreach ($department as $dept) {
            DB::table('departments')->insert([
                'name' => $dept,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Department seeded successfully!');
    }
}
