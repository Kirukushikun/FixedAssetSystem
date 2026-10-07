<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeSyncService
{
    // PandaSystem-v2 farm code => FAMS farm code. BRD and BDL are both Brookdale Farms.
    // Nothing maps to BBGC or HATCHERY; those employees are maintained in FAMS by hand.
    private const FARM_MAP = [
        'BFC' => 'BFC',
        'PFC' => 'PFC',
        'RH'  => 'RH',
        'BDL' => 'BDL',
        'BRD' => 'BDL',
    ];

    public function __construct(private PandaService $panda) {}

    /**
     * Pull employees from PandaSystem-v2 and upsert them by employee number.
     * Employees that exist only in FAMS are left untouched.
     *
     * @return array{created: int, updated: int, deactivated: int, skipped: int, unmapped_farms: string[]}
     */
    public function sync(): array
    {
        $remote = $this->panda->getEmployees();

        $result = ['created' => 0, 'updated' => 0, 'deactivated' => 0, 'skipped' => 0, 'unmapped_farms' => []];

        DB::transaction(function () use ($remote, &$result) {
            $existing = Employee::all()->keyBy('employee_id');

            foreach ($remote as $row) {
                $employeeNo = trim((string) ($row['employee_no'] ?? ''));
                $farmCode   = strtoupper(trim((string) ($row['farm'] ?? '')));
                $farm       = self::FARM_MAP[$farmCode] ?? null;
                $separated  = ($row['status'] ?? 'active') !== 'active';
                $employee   = $existing->get($employeeNo);

                if ($employeeNo === '') {
                    $result['skipped']++;
                    continue;
                }

                if ($separated) {
                    if ($employee && !$employee->is_deleted) {
                        $employee->update(['is_deleted' => true]);
                        $result['deactivated']++;
                    }
                    continue;
                }

                if ($farm === null) {
                    $result['skipped']++;
                    $result['unmapped_farms'][$farmCode] = true;
                    continue;
                }

                $attributes = [
                    'employee_name' => trim((string) ($row['name'] ?? '')),
                    'position'      => trim((string) ($row['position'] ?? '')),
                    'farm'          => $farm,
                    'department'    => trim((string) ($row['department'] ?? '')),
                    'is_deleted'    => false,
                ];

                if (!$employee) {
                    Employee::create(['employee_id' => $employeeNo] + $attributes);
                    $result['created']++;
                    continue;
                }

                $employee->fill($attributes);
                if ($employee->isDirty()) {
                    $employee->save();
                    $result['updated']++;
                }
            }
        });

        $result['unmapped_farms'] = array_keys($result['unmapped_farms']);

        return $result;
    }
}
