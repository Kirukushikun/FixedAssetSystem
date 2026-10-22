<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Categories and sub-categories. Safe to re-run on a database that already has data:
 *   php artisan db:seed --class=CategorySeeder --force
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->renameDeskToTable();

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

        $repaired = $this->repairAssetCategories();

        Cache::forget('categories_with_subcategories');
        Cache::forget('categories_by_code');

        $this->command->info('Categories and Sub Categories seeded successfully!');
        if ($repaired) {
            $this->command->info("Repaired the category of {$repaired} assets.");
        }
    }

    /** "Desk" became "Table"; rename in place so existing databases don't keep both. */
    private function renameDeskToTable(): void
    {
        $furniture = Category::where('code', 'officefurniture')->first();
        if (!$furniture) {
            return;
        }

        $desk = SubCategory::where('category_id', $furniture->id)->where('name', 'Desk')->first();
        if ($desk && !SubCategory::where('category_id', $furniture->id)->where('name', 'Table')->exists()) {
            $desk->update(['name' => 'Table']);
        }

        Asset::where('category', 'officefurniture')->where('sub_category', 'Desk')->update(['sub_category' => 'Table']);
    }

    /**
     * Assets must store a category code. Older imports could store a category name (e.g.
     * "Office Equipment") when the sub-category was missing; re-point those by sub-category.
     */
    private function repairAssetCategories(): int
    {
        $codes = Category::pluck('code')->all();
        $codeByName = Category::pluck('code', 'name');
        $codeBySub = SubCategory::with('category:id,code')->get()
            ->mapWithKeys(fn ($s) => [$s->name => $s->category?->code]);

        $repaired = 0;

        Asset::whereNotIn('category', $codes)->each(function (Asset $asset) use ($codeBySub, $codeByName, &$repaired) {
            $code = $codeBySub->get($asset->sub_category) ?? $codeByName->get($asset->category);
            if ($code) {
                $asset->update(['category' => $code]);
                $repaired++;
            }
        });

        return $repaired;
    }
}
