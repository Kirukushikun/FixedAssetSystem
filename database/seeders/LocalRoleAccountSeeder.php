<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LocalRoleAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            'admin' => [
                'name' => 'IT Admin',
                'email' => 'admin@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'IT & SECURITY',
                'is_admin' => true,
            ],
            'accounting' => [
                'name' => 'Accounting Test User',
                'email' => 'accounting@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'ACCOUNTING',
                'is_admin' => false,
            ],
            'pfc_farm_manager' => [
                'name' => 'PFC Farm Manager',
                'email' => 'pfc.farmmanager@bfcgroup.org',
                'farm' => 'PFC',
                'department' => 'POULTRY',
                'is_admin' => false,
            ],
            'bdl_farm_manager' => [
                'name' => 'BDL Farm Manager',
                'email' => 'bdl.farmmanager@bfcgroup.org',
                'farm' => 'BDL',
                'department' => 'POULTRY',
                'is_admin' => false,
            ],
            'vp' => [
                'name' => 'VP Approver Test User',
                'email' => 'vp@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'EXECUTIVE',
                'is_admin' => false,
            ],
            'hr' => [
                'name' => 'HR Test User',
                'email' => 'hr@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'HR',
                'is_admin' => false,
            ],
            'sme' => [
                'name' => 'SME Test User',
                'email' => 'sme@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'IT & SECURITY',
                'is_admin' => false,
            ],
            'auditor' => [
                'name' => 'Auditor Test User',
                'email' => 'auditor@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'AUDIT',
                'is_admin' => false,
            ],
            'purchasing' => [
                'name' => 'Purchasing Test User',
                'email' => 'purchasing@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'PURCHASING',
                'is_admin' => false,
            ],
            'division_head' => [
                'name' => 'Division Head Test User',
                'email' => 'division.head@bfcgroup.org',
                'farm' => 'BFC',
                'department' => 'POULTRY',
                'is_admin' => false,
            ],
            'pfc_division_head' => [
                'name' => 'PFC Division Head',
                'email' => 'pfc.divisionhead@bfcgroup.org',
                'farm' => 'PFC',
                'department' => 'POULTRY',
                'is_admin' => false,
            ],
            'bdl_division_head' => [
                'name' => 'BDL Division Head',
                'email' => 'bdl.divisionhead@bfcgroup.org',
                'farm' => 'BDL',
                'department' => 'POULTRY',
                'is_admin' => false,
            ],
        ];

        foreach ($accounts as $roleKey => $account) {
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('1234'),
                    'farm' => $account['farm'],
                    'department' => $account['department'],
                    'is_admin' => $account['is_admin'],
                ]
            );

            if (! $account['is_admin']) {
                // Map role keys for new accounts
                $roleKeyToUse = $roleKey;
                if (in_array($roleKey, ['pfc_farm_manager', 'bdl_farm_manager'])) {
                    $roleKeyToUse = 'farm_manager';
                } elseif (in_array($roleKey, ['pfc_division_head', 'bdl_division_head'])) {
                    $roleKeyToUse = 'division_head';
                }

                $role = Role::where('key', $roleKeyToUse)->first();

                if ($role) {
                    $user->roles()->sync([$role->id]);
                }
            }
        }
    }
}
