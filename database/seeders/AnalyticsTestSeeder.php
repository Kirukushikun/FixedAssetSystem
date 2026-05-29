<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalyticsTestSeeder extends Seeder
{
    private int $assetCounter = 1;

    public function run(): void
    {
        $this->seedEmployees();

        $employees = DB::table('employees')->where('is_deleted', false)->get();

        $this->seedAssets($employees);

        $assets = DB::table('assets')
            ->where('is_deleted', false)
            ->where('is_archived', false)
            ->get();

        $this->seedRepairs($assets);
        $this->seedAudits($assets);
        $this->seedFlags($employees, $assets);
        $this->seedDisposalRequests($assets);
        $this->seedTransferRequests($assets, $employees);

        $this->command->info('');
        $this->command->info('Analytics test data seeded successfully.');
        $this->command->info('  Employees : ' . DB::table('employees')->count());
        $this->command->info('  Assets    : ' . DB::table('assets')->count());
        $this->command->info('  Repairs   : ' . DB::table('asset_repairs')->count());
        $this->command->info('  Audits    : ' . DB::table('audits')->count());
        $this->command->info('  Flags     : ' . DB::table('flags')->count());
        $this->command->info('  Disposals : ' . DB::table('disposal_requests')->count());
        $this->command->info('  Transfers : ' . DB::table('transfer_requests')->count());
    }

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEES  (60 total, spread across all farms)
    // ─────────────────────────────────────────────────────────────
    private function seedEmployees(): void
    {
        $farmDepts = [
            'BFC'      => ['IT & SECURITY', 'SWINE', 'POULTRY', 'GENERAL SERVICES', 'PURCHASING', 'FEEDMILL'],
            'BDL'      => ['SWINE', 'POULTRY', 'GENERAL SERVICES', 'FEEDMILL'],
            'PFC'      => ['POULTRY', 'GENERAL SERVICES', 'SWINE'],
            'RH'       => ['GENERAL SERVICES', 'SWINE', 'POULTRY'],
            'BBGC'     => ['GENERAL SERVICES', 'POULTRY'],
            'HATCHERY' => ['GENERAL SERVICES', 'POULTRY'],
        ];

        $farmCounts = [
            'BFC' => 18, 'BDL' => 12, 'PFC' => 12,
            'RH' => 8, 'BBGC' => 6, 'HATCHERY' => 4,
        ];

        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Pedro', 'Rosa', 'Carlo', 'Elena',
                       'Marco', 'Liza', 'Ryan', 'Grace', 'Mark', 'Joy', 'John', 'Mary',
                       'Anton', 'Claire', 'Roel', 'Alma', 'Dennis', 'Sharon', 'Leo', 'Pia'];
        $lastNames  = ['Santos', 'Cruz', 'Reyes', 'Dela Cruz', 'Garcia', 'Torres', 'Flores',
                       'Rivera', 'Mendoza', 'Aquino', 'Villanueva', 'Lopez', 'Martinez',
                       'Ramos', 'Gomez', 'Bautista', 'Hernandez', 'Pascual'];
        $positions  = ['Staff', 'Supervisor', 'Technician', 'Operator', 'Officer'];

        $counter = 1;

        foreach ($farmCounts as $farm => $count) {
            $depts = $farmDepts[$farm];
            for ($i = 0; $i < $count; $i++) {
                DB::table('employees')->insert([
                    'employee_id'   => sprintf('EMP-%04d', $counter++),
                    'employee_name' => $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)],
                    'position'      => $positions[array_rand($positions)],
                    'farm'          => $farm,
                    'department'    => $depts[array_rand($depts)],
                    'is_deleted'    => false,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // ASSETS  (~300 total)
    //  - Realistic status + condition distribution
    //  - ~15 near-end-of-life (EOL within 12 months)
    //  - Mix of IT and NON-IT across all farms + departments
    // ─────────────────────────────────────────────────────────────
    private function seedAssets(object $employees): void
    {
        $year  = now()->year;
        $now   = now();

        $brands = ['Dell', 'HP', 'Asus', 'Lenovo', 'Acer', 'Samsung', 'LG', 'Canon',
                   'Epson', 'Panasonic', 'Sharp', 'Toshiba', 'Sony', 'Fujitsu'];
        $models = ['ProBook', 'ThinkPad', 'VivoBook', 'IdeaPad', 'Pavilion', 'Inspiron',
                   'Aspire', 'EliteBook', 'Latitude', 'A80', 'MF420', 'Pro 14', 'XR200'];

        $categoryPool = [
            ['IT',     'itequipment',     'Desktop'],
            ['IT',     'itequipment',     'Laptop'],
            ['IT',     'itequipment',     'Router'],
            ['IT',     'itequipment',     'Switch'],
            ['IT',     'computerequip',   'Equipment'],
            ['NON-IT', 'itequipment',     'Printer'],
            ['NON-IT', 'itequipment',     'Photocopy Machine'],
            ['NON-IT', 'itequipment',     'Scanner'],
            ['NON-IT', 'officefurniture', 'Office Chair'],
            ['NON-IT', 'officefurniture', 'Office Table'],
            ['NON-IT', 'appliances',      'Aircon'],
            ['NON-IT', 'appliances',      'Refrigerator'],
            ['NON-IT', 'appliances',      'Water Dispenser'],
            ['NON-IT', 'tools',           'Radio'],
            ['NON-IT', 'deliveryequip',   'Vehicles'],
            ['NON-IT', 'transportequip',  'Vehicles'],
            ['NON-IT', 'communicationequip', 'Equipment'],
        ];

        // Weighted pools — reflect a realistic fleet
        $statusPool = array_merge(
            array_fill(0, 55, 'Issued'),
            array_fill(0, 25, 'Available'),
            array_fill(0, 8,  'Disposed'),
            array_fill(0, 5,  'Transferred'),
            array_fill(0, 4,  'For Disposal'),
            array_fill(0, 3,  'Lost')
        );

        $conditionPool = array_merge(
            array_fill(0, 55, 'Good'),
            array_fill(0, 20, 'Repair'),
            array_fill(0, 15, 'Defective'),
            array_fill(0, 10, 'Replace')
        );

        $farmCounts = [
            'BFC' => 90, 'BDL' => 60, 'PFC' => 60,
            'RH'  => 40, 'BBGC' => 30, 'HATCHERY' => 20,
        ];

        $departments = [
            'IT & SECURITY', 'SWINE', 'POULTRY', 'GENERAL SERVICES',
            'PURCHASING', 'FEEDMILL', 'ACCOUNTING', 'HR',
        ];

        // Track how many near-EOL assets we've created
        $nearEolCreated = 0;
        $nearEolTarget  = 15;

        foreach ($farmCounts as $farm => $assetCount) {
            $farmEmps = collect($employees)->where('farm', $farm)->values();

            for ($i = 0; $i < $assetCount; $i++) {
                $cat       = $categoryPool[array_rand($categoryPool)];
                $status    = $statusPool[array_rand($statusPool)];
                $condition = $conditionPool[array_rand($conditionPool)];
                $dept      = $departments[array_rand($departments)];

                // Near-end-of-life: acquisition 5 years ago such that EOL = 2–10 months from now
                $isNearEol = ($nearEolCreated < $nearEolTarget && $this->assetCounter % 20 === 0);
                if ($isNearEol) {
                    $usableLife = 5;
                    $eol        = $now->copy()->addMonths(rand(2, 10));
                    $acqDate    = $eol->copy()->subYears($usableLife);
                    $nearEolCreated++;
                } else {
                    $usableLife = rand(3, 10);
                    $acqDate    = $now->copy()->subDays(rand(60, 2500));
                }

                $assignedId   = null;
                $assignedName = null;
                if ($status === 'Issued' && $farmEmps->isNotEmpty()) {
                    $emp          = $farmEmps->random();
                    $assignedId   = $emp->id;
                    $assignedName = $emp->employee_name;
                }

                DB::table('assets')->insert([
                    'is_deleted'        => false,
                    'is_archived'       => false,
                    'ref_id'            => sprintf('FA-%s-%05d', $year, $this->assetCounter++),
                    'category_type'     => $cat[0],
                    'category'          => $cat[1],
                    'sub_category'      => $cat[2],
                    'brand'             => $brands[array_rand($brands)],
                    'model'             => $models[array_rand($models)],
                    'status'            => $status,
                    'condition'         => $condition,
                    'acquisition_date'  => $acqDate->format('Y-m-d'),
                    'item_cost'         => rand(5000, 150000),
                    'depreciated_value' => rand(1000, 80000),
                    'usable_life'       => (string) $usableLife,
                    'assigned_id'       => $assignedId,
                    'assigned_name'     => $assignedName,
                    'farm'              => $farm,
                    'department'        => $dept,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    // REPAIRS  (~40 records — ~80% this year, ~20% last year)
    //  Tests: repairsThisYear count + repairCostThisYear sum
    // ─────────────────────────────────────────────────────────────
    private function seedRepairs(object $assets): void
    {
        $types   = ['Preventive', 'Corrective', 'Emergency', 'Scheduled'];
        $sources = ['In-house', 'Third-party', 'Vendor Warranty'];
        $now     = now();

        $sample = collect($assets)->random(min(40, count($assets)));

        foreach ($sample as $idx => $asset) {
            $isThisYear = ($idx % 5 !== 0); // 80% this year
            $date = $isThisYear
                ? $now->copy()->subDays(rand(1, 150))
                : $now->copy()->subDays(rand(366, 700));

            DB::table('asset_repairs')->insert([
                'asset_id'   => $asset->id,
                'date'       => $date->format('Y-m-d'),
                'type'       => $types[array_rand($types)],
                'cost'       => rand(500, 15000),
                'notes'      => 'Seeded repair record.',
                'source'     => $sources[array_rand($sources)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // AUDITS  (~30 records — ~30% overdue)
    //  Tests: overdueAudits count
    // ─────────────────────────────────────────────────────────────
    private function seedAudits(object $assets): void
    {
        $now    = now();
        $sample = collect($assets)->random(min(30, count($assets)));

        foreach ($sample as $idx => $asset) {
            $isOverdue     = ($idx % 3 === 0); // every 3rd audit is overdue
            $nextAuditDate = $isOverdue
                ? $now->copy()->subDays(rand(15, 180))
                : $now->copy()->addDays(rand(30, 365));

            DB::table('audits')->insert([
                'asset_id'        => $asset->id,
                'farm'            => $asset->farm,
                'location'        => $asset->department ?? 'Main Office',
                'next_audit_date' => $nextAuditDate->format('Y-m-d'),
                'notes'           => $isOverdue ? 'Audit overdue — needs immediate scheduling.' : 'Routine scheduled audit.',
                'finding'         => $isOverdue ? 'Minor wear observed' : 'Asset in good condition',
                'audited_at'      => $now->copy()->subDays(rand(30, 365))->format('Y-m-d H:i:s'),
                'audited_by'      => 1,
                'audited_by_name' => 'IT Admin',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // FLAGS  (8 employees flagged)
    //  Tests: assetsWithFlags count
    // ─────────────────────────────────────────────────────────────
    private function seedFlags(object $employees, object $assets): void
    {
        $flagTypes    = ['Pending Clearances', 'Damaged Asset', 'Lost Asset', 'Under Investigation', 'Unreturned Asset'];
        $flaggedEmps  = collect($employees)->random(min(8, count($employees)));
        $assetsById   = collect($assets)->keyBy('assigned_id');

        foreach ($flaggedEmps as $emp) {
            $empAsset   = $assetsById->get($emp->id);
            $assetLabel = $empAsset
                ? "{$empAsset->brand} {$empAsset->model} ({$empAsset->sub_category})"
                : 'Unknown Asset';

            DB::table('flags')->insert([
                'target_id'          => $emp->id,
                'flag_type'          => $flagTypes[array_rand($flagTypes)],
                'asset'              => $assetLabel,
                'source'             => 'System',
                'created_by_user_id' => 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // DISPOSAL REQUESTS  (12 records across 3 stages)
    //  Tests: activeDisposals count
    // ─────────────────────────────────────────────────────────────
    private function seedDisposalRequests(object $assets): void
    {
        $stages = [
            'Pending Division Head Approval',
            'Pending Division Head Approval',
            'Pending Division Head Approval',
            'Pending Division Head Approval',
            'Pending VP Approval',
            'Pending VP Approval',
            'Pending VP Approval',
            'Pending VP Approval',
            'VP Approved',
            'VP Approved',
            'VP Approved',
            'VP Approved',
        ];

        $candidates = collect($assets)
            ->whereIn('status', ['Available', 'Issued', 'For Disposal'])
            ->values()
            ->take(12);

        foreach ($candidates as $idx => $asset) {
            DB::table('disposal_requests')->insert([
                'asset_id'             => $asset->id,
                'requested_by_user_id' => 1,
                'requested_by_name'    => 'IT Admin',
                'requester_farm'       => $asset->farm,
                'requester_department' => $asset->department,
                'reason'               => 'Asset has exceeded usable life and is no longer operational.',
                'status'               => $stages[$idx],
                'created_at'           => now()->subDays(rand(1, 30)),
                'updated_at'           => now(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // TRANSFER REQUESTS  (10 records — DH Approval + For Transfer)
    //  Tests: activeTransfers count
    // ─────────────────────────────────────────────────────────────
    private function seedTransferRequests(object $assets, object $employees): void
    {
        $stages = [
            'DH Approval', 'DH Approval', 'DH Approval',
            'DH Approval', 'DH Approval',
            'For Transfer', 'For Transfer', 'For Transfer',
            'For Transfer', 'For Transfer',
        ];

        $issued    = collect($assets)->where('status', 'Issued')->values()->take(10);
        $empsByFarm = collect($employees)->groupBy('farm');

        foreach ($issued as $idx => $asset) {
            $farmEmps = $empsByFarm->get($asset->farm, collect());
            $toEmp    = $farmEmps->first(fn($e) => $e->id !== $asset->assigned_id);

            if (!$toEmp) continue;

            DB::table('transfer_requests')->insert([
                'asset_id'                => $asset->id,
                'requested_by'            => 1,
                'requested_by_name'       => 'IT Admin',
                'requested_employee_id'   => $toEmp->id,
                'requested_employee_name' => $toEmp->employee_name,
                'reason'                  => 'Reallocation due to department restructuring.',
                'status'                  => $stages[$idx],
                'is_external'             => false,
                'created_at'              => now()->subDays(rand(1, 20)),
                'updated_at'              => now(),
            ]);
        }
    }
}
