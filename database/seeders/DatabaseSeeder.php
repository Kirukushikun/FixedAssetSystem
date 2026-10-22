<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        $this->call(CategorySeeder::class);

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
