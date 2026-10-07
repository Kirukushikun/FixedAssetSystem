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

        // Sub-category names must be unique across categories: AssetMigrationImport
        // routes each imported row to its category by sub-category name.
        $it    = fn (array $names) => array_map(fn ($n) => ['name' => $n, 'category_type' => 'IT'], $names);
        $nonIt = fn (array $names) => array_map(fn ($n) => ['name' => $n, 'category_type' => 'NON-IT'], $names);

        $data = [
            'IT Equipment' => [
                'code' => 'itequipment',
                'icon' => 'desktop',
                'subcategories' => $it([
                    'Desktop', 'Laptop', 'Server', 'Tablet', 'All-in-One PC', 'Router', 'Switch',
                    'Firewall', 'Access Point', 'CCTV Camera', 'Monitor', 'Photocopier', 'Scanner',
                    'Printer', 'UPS', 'Biometric Devices', 'Modem', 'Keyboard', 'AVR', 'Camera',
                ]),
            ],
            'Software & Apps' => [
                'code' => 'software',
                'icon' => 'folder',
                'subcategories' => $it([
                    'Software License', 'Subscription', 'Application',
                ]),
            ],
            'Communication Devices' => [
                'code' => 'commdevices',
                'icon' => 'folder',
                'subcategories' => $nonIt([
                    'Telephone', 'Mobile Phones', 'PABX', 'Two-way Radio', 'Intercom',
                ]),
            ],
            'Audio Visual' => [
                'code' => 'audiovisual',
                'icon' => 'speaker',
                'subcategories' => $nonIt([
                    'Television', 'Projector', 'Speaker System', 'Amplifier', 'Microphone',
                    'Mixer Console', 'PA System',
                ]),
            ],
            'Office Furniture' => [
                'code' => 'officefurniture',
                'icon' => 'furniture',
                'subcategories' => $nonIt([
                    'Table', 'Chair', 'Conference Table', 'Filing Cabinet', 'Bookshelf', 'Whiteboard',
                    'Partition', 'Bedroom Equipment', 'Storage', 'Other Furniture',
                ]),
            ],
            'Office Equipment' => [
                'code' => 'officeequipment',
                'icon' => 'projector',
                'subcategories' => $nonIt([
                    'Office Machine', 'Other Office Equipment',
                ]),
            ],
            'Appliances' => [
                'code' => 'appliances',
                'icon' => 'appliances',
                'subcategories' => $nonIt([
                    'Air Conditioner', 'Refrigerator', 'Water Dispenser', 'Washing Machine',
                    'Electric Fan', 'Microwave',
                ]),
            ],
            'Kitchen Equipment' => [
                'code' => 'kitchen',
                'icon' => 'kitchen',
                'subcategories' => $nonIt([
                    'Stove', 'Rice Cooker', 'Oven', 'Blender', 'Steamer', 'Cooking Pot',
                ]),
            ],
            'Vehicles' => [
                'code' => 'vehicles',
                'icon' => 'vehicle',
                'subcategories' => $nonIt([
                    'Motorcycle', 'Service Vehicle', 'Delivery Truck', 'Utility Vehicle', 'Forklift',
                ]),
            ],
            'Machinery & Equipment' => [
                'code' => 'machinery',
                'icon' => 'tools',
                'subcategories' => $nonIt([
                    'Generator', 'Air Compressor', 'Water Pump', 'Welding Machine',
                    'Feedmill Equipment', 'Biogas Equipment',
                ]),
            ],
            'Farm Equipment' => [
                'code' => 'farmequip',
                'icon' => 'tools',
                'subcategories' => $nonIt([
                    'Tractor', 'Sprayer', 'Irrigation Equipment', 'Weighing Scale', 'Incubator',
                    'Submersible Pump', 'Wet-stand Pipe', 'Industrial Fan', 'Hatcher Equipment', 'Silo',
                    'Control Panel', 'Laboratory Equipment', 'Heating & Cooling Equipment', 'Tank',
                    'Other Farm Equipment',
                ]),
            ],
            'Tools & Safety' => [
                'code' => 'tools',
                'icon' => 'tools',
                'subcategories' => $nonIt([
                    'Hand Tools', 'Power Tools', 'Safety Equipment', 'Measuring Instruments', 'Scaffolding',
                ]),
            ],
            'Land & Improvements' => [
                'code' => 'land',
                'icon' => 'land',
                'subcategories' => $nonIt([
                    'Land', 'Road/Pavement', 'Drainage', 'Fencing', 'Land Improvements',
                ]),
            ],
            'Buildings & Structures' => [
                'code' => 'buildings',
                'icon' => 'building',
                'subcategories' => $nonIt([
                    'Office Building', 'Warehouse', 'Staff Housing', 'Swine House', 'Poultry House',
                    'Feed Mill', 'Biogas Plant', 'Hatchery Building', 'Other Building',
                ]),
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
