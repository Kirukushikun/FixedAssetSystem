<?php

namespace Database\Seeders;

use App\Models\DynamicField;
use Illuminate\Database\Seeder;

class DynamicFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            'brand' => [
                'Acer',
                'Apple',
                'Asus',
                'Brother',
                'Canon',
                'Dell',
                'Epson',
                'HP',
                'Lenovo',
                'LG',
                'Microsoft',
                'MSI',
                'Samsung',
                'Sony',
                'Toshiba',
            ],
            'processor' => [
                'Intel Core i3',
                'Intel Core i5',
                'Intel Core i7',
                'Intel Core i9',
                'Intel Pentium',
                'Intel Celeron',
                'AMD Ryzen 3',
                'AMD Ryzen 5',
                'AMD Ryzen 7',
                'AMD Ryzen 9',
                'Apple M1',
                'Apple M2',
                'Apple M3',
            ],
            'RAM' => [
                '4GB',
                '8GB',
                '12GB',
                '16GB',
                '24GB',
                '32GB',
                '64GB',
            ],
            'Storage' => [
                '128GB SSD',
                '256GB SSD',
                '512GB SSD',
                '1TB SSD',
                '2TB SSD',
                '500GB HDD',
                '1TB HDD',
                '2TB HDD',
            ],
        ];

        foreach ($fields as $field => $values) {
            foreach ($values as $value) {
                DynamicField::updateOrCreate(
                    [
                        'field' => $field,
                        'value' => $value,
                    ],
                    []
                );
            }
        }
    }
}
