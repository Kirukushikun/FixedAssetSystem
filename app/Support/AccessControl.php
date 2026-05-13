<?php

namespace App\Support;

class AccessControl
{
    public static function permissions(): array
    {
        return [
            'dashboard' => [
                'dashboard.view' => 'View dashboard',
            ],
            'assets' => [
                'assets.view' => 'View assets',
                'assets.create' => 'Create assets',
                'assets.update' => 'Update assets',
                'assets.delete' => 'Delete assets',
                'assets.restore' => 'Restore deleted assets',
                'assets.import' => 'Import assets',
                'assets.export' => 'Export assets',
                'assets.qr' => 'Manage asset QR codes',
                'assets.farm_scope' => 'Limit asset access to assigned farm',
                'assets.repair' => 'Add repair records',
                'assets.audit' => 'Add audit records',
            ],
            'employees' => [
                'employees.view' => 'View employees',
                'employees.create' => 'Create employees',
                'employees.update' => 'Update employees',
                'employees.delete' => 'Delete employees',
                'employees.import' => 'Import employees',
                'employees.export' => 'Export employees',
            ],
            'sme' => [
                'sme.view' => 'View SME workspace',
                'sme.review' => 'Create SME reviews',
                'sme.history' => 'View SME review history',
                'sme.insights.view' => 'View SME insights',
            ],
            'transfer' => [
                'transfer.request' => 'Request asset transfers',
                'transfer.view' => 'View transfer workspace',
                'transfer.approve' => 'Approve transfer requests',
            ],
            'disposal' => [
                'disposal.view' => 'View disposal workspace',
                'disposal.request' => 'Request asset disposal',
                'disposal.approve' => 'Approve disposal requests',
                'disposal.dispose' => 'Mark assets as disposed',
                'disposal.history' => 'View disposal history',
                'disposal.form.view' => 'View disposal forms',
            ],
            'forms' => [
                'forms.view' => 'View forms',
                'forms.generate' => 'Generate forms',
                'forms.accountability' => 'Generate accountability forms',
                'forms.transfer' => 'Generate transfer forms',
                'forms.disposal' => 'Generate disposal forms',
                'forms.print' => 'Print forms',
            ],
            'reports' => [
                'reports.view' => 'View reports',
                'reports.export' => 'Export reports',
                'reports.audit' => 'View audit reports',
                'reports.disposal' => 'View disposal reports',
                'reports.sme' => 'View SME reports',
                'reports.farm_scope' => 'Limit reports to assigned farm',
            ],
            'analytics' => [
                'analytics.view' => 'View IT analytics dashboard',
            ],
            'system' => [
                'audit.view' => 'View audit logs',
                'audit.export' => 'Export audit logs',
                'activity.view' => 'View activity logs',
                'settings.view' => 'View settings',
                'settings.update' => 'Update settings',
                'users.view' => 'View user access',
                'users.create' => 'Create system users',
                'users.update' => 'Update system users',
                'users.delete' => 'Delete system users',
                'roles.manage' => 'Manage roles',
                'permissions.manage' => 'Manage permissions',
                'system.backup' => 'Perform data backup',
            ],
        ];
    }

    public static function roles(): array
    {
        return [
            'admin' => [
                'name' => 'Admin',
                'description' => 'Full system access and configuration control.',
                'permissions' => self::allPermissionKeys(),
            ],
            'accounting' => [
                'name' => 'Accounting',
                'description' => 'Manages maintenance tables, updates fixed assets, prints inventory, generates QR codes, handles issuance/transfer/disposal.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.repair',
                    'assets.update',
                    'assets.export',
                    'assets.qr',
                    'disposal.view',
                    'disposal.dispose',
                    'settings.view',
                ],
            ],
            'hr' => [
                'name' => 'HR',
                'description' => 'Processes exiting employees, unassigns assets, issues accountability forms, and manages employee details.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'employees.view',
                    'employees.create',
                    'employees.update',
                    'forms.accountability',
                ],
            ],
            'farm_manager' => [
                'name' => 'Farm Manager',
                'description' => 'Views farm-scoped assets and submits disposal requests for farm assets.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.farm_scope',
                    'sme.insights.view',
                    'disposal.view',
                    'disposal.request',
                    'disposal.history',
                    'transfer.view',
                    'transfer.request',
                    'forms.view',
                    'reports.view',
                    'reports.farm_scope',
                ],
            ],
            'sme' => [
                'name' => 'SME / Technical Evaluator',
                'description' => 'Reviews asset condition and maintains technical review history.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.repair',
                    'sme.view',
                    'sme.review',
                    'sme.history',
                    'sme.insights.view',
                ],
            ],
            'vp' => [
                'name' => 'VP / Approver',
                'description' => 'Reviews SME insights and approves disposal requests.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'sme.insights.view',
                    'disposal.view',
                    'disposal.approve',
                    'disposal.history',
                    'disposal.form.view',
                    'reports.view',
                    'reports.disposal',
                ],
            ],
            'auditor' => [
                'name' => 'Auditor',
                'description' => 'Prints FA inventory and performs audits/inventory.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.audit',
                    'assets.export',
                ],
            ],
            'purchasing' => [
                'name' => 'Purchasing',
                'description' => 'Encodes new fixed assets, manages PMS/scheduling, updates maintenance tables, updates fixed assets, prints inventory, generates QR codes.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.create',
                    'assets.repair',
                    'assets.update',
                    'assets.export',
                    'assets.qr',
                    'settings.view',
                ],
            ],
            'it' => [
                'name' => 'IT',
                'description' => 'Manages IT assets and views IT analytics dashboard.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'analytics.view',
                ],
            ],
            'division_head' => [
                'name' => 'Division Head / Manager',
                'description' => 'Manages PMS updates/scheduling and views/confirms FA assignments. Approves disposal and transfer requests.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.farm_scope',
                    'assets.repair',
                    'disposal.approve',
                    'transfer.approve',
                ],
            ],
        ];
    }

    public static function allPermissionKeys(): array
    {
        return collect(self::permissions())
            ->flatMap(fn ($permissions) => array_keys($permissions))
            ->values()
            ->all();
    }
}
