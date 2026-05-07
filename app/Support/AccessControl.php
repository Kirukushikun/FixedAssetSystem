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
                'name' => 'Accounting / Asset Custodian',
                'description' => 'Manages asset records, QR codes, disposal finalization, reports, and audit logs.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'assets.create',
                    'assets.update',
                    'assets.delete',
                    'assets.restore',
                    'assets.import',
                    'assets.export',
                    'assets.qr',
                    'employees.view',
                    'sme.insights.view',
                    'disposal.view',
                    'disposal.dispose',
                    'disposal.history',
                    'disposal.form.view',
                    'forms.view',
                    'forms.generate',
                    'forms.disposal',
                    'forms.print',
                    'reports.view',
                    'reports.export',
                    'reports.audit',
                    'reports.disposal',
                    'settings.view',
                    'settings.update',
                ],
            ],
            'hr' => [
                'name' => 'HR',
                'description' => 'Manages employees, employee asset views, accountability forms, transfer forms, and employee-facing insights.',
                'permissions' => [
                    'dashboard.view',
                    'assets.view',
                    'employees.view',
                    'employees.create',
                    'employees.update',
                    'employees.delete',
                    'employees.import',
                    'employees.export',
                    'sme.insights.view',
                    'disposal.form.view',
                    'forms.view',
                    'forms.generate',
                    'forms.accountability',
                    'forms.transfer',
                    'forms.print',
                    'reports.view',
                    'reports.export',
                ],
            ],
            'farm_manager' => [
                'name' => 'Farm Manager',
                'description' => 'Views farm-scoped assets and submits disposal requests for farm assets.',
                'permissions' => [
                    'assets.view',
                    'assets.farm_scope',
                    'sme.insights.view',
                    'disposal.view',
                    'disposal.request',
                    'disposal.history',
                    'forms.view',
                    'reports.view',
                    'reports.farm_scope',
                ],
            ],
            'sme' => [
                'name' => 'SME / Technical Evaluator',
                'description' => 'Reviews asset condition and maintains technical review history.',
                'permissions' => [
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
                'description' => 'Views assets and adds audit records.',
                'permissions' => [
                    'assets.view',
                    'assets.audit',
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
