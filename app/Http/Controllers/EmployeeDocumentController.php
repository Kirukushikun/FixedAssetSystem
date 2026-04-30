<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\GeneratedForm;
use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeDocumentController extends Controller
{
    public function accountability(Request $request)
    {
        $employee = Employee::with(['assets' => function ($query) {
            $query->where('is_deleted', false);
        }])->find($request->targetID);

        if (!$employee) {
            return redirect('/employees')->with('error', 'Employee not found');
        }

        $assets = $employee->assets;

        $snapshot = [
            'generated_at' => now()->toDateTimeString(),
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'position' => $employee->position,
                'farm' => $employee->farm,
                'department' => $employee->department,
            ],
            'assets' => $assets->map(function ($asset) {
                return [
                    'id' => $asset->id,
                    'ref_id' => $asset->ref_id,
                    'brand' => $asset->brand,
                    'model' => $asset->model,
                    'sub_category' => $asset->sub_category,
                    'status' => $asset->status,
                    'condition' => $asset->condition,
                ];
            })->values()->toArray(),
        ];

        $generatedForm = GeneratedForm::create([
            'employee_id' => $employee->id,
            'form_type' => 'accountability',
            'title' => 'Accountability Form - ' . $employee->employee_name . ' - ' . now()->format('Y-m-d H:i'),
            'generated_by_user_id' => Auth::id(),
            'snapshot' => $snapshot,
        ]);

        return view('accountability-form', [
            'employee' => $employee,
            'assets' => $assets,
            'generatedForm' => $generatedForm,
        ]);
    }

    public function transfer(Request $request)
    {
        $employee = Employee::find($request->targetID);

        if (!$employee) {
            return redirect('/employees')->with('error', 'Employee not found');
        }

        $assets = $employee->assets()->where('is_deleted', false)->get();

        $transferAssets = $assets->map(function ($asset) use ($employee) {
            $historyRecords = History::where('asset_id', $asset->id)
                ->orderByDesc('created_at')
                ->get();

            $latestTransfer = $historyRecords->first(function ($history) use ($employee) {
                return $history->action === 'Transfer' && $history->assignee_name === $employee->employee_name;
            });

            if (!$latestTransfer) {
                return null;
            }

            $previousOwner = $historyRecords
                ->skipUntil(function ($history) use ($latestTransfer) {
                    return $history->id === $latestTransfer->id;
                })
                ->skip(1)
                ->first();

            return [
                'asset_id' => $asset->id,
                'ref_id' => $asset->ref_id,
                'brand' => $asset->brand,
                'model' => $asset->model,
                'sub_category' => $asset->sub_category,
                'from_name' => $previousOwner?->assignee_name ?? 'N/A',
                'from_department' => $previousOwner?->department,
                'from_farm' => $previousOwner?->farm,
                'to_name' => $employee->employee_name,
                'to_department' => $employee->department,
                'to_farm' => $employee->farm,
                'transferred_at' => optional($latestTransfer->created_at)->toDateTimeString(),
            ];
        })->filter()->values();

        if ($transferAssets->isEmpty()) {
            return redirect('/employees/view?targetID=' . $employee->id)
                ->with('error', 'No transfer records found for this employee.');
        }

        $snapshot = [
            'generated_at' => now()->toDateTimeString(),
            'employee' => [
                'id' => $employee->id,
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->employee_name,
                'position' => $employee->position,
                'farm' => $employee->farm,
                'department' => $employee->department,
            ],
            'assets' => $transferAssets->toArray(),
        ];

        $generatedForm = GeneratedForm::create([
            'employee_id' => $employee->id,
            'form_type' => 'transfer',
            'title' => 'Transfer Form - ' . $employee->employee_name . ' - ' . now()->format('Y-m-d H:i'),
            'generated_by_user_id' => Auth::id(),
            'snapshot' => $snapshot,
        ]);

        return view('transfer-form', [
            'employee' => $employee,
            'transferAssets' => $transferAssets,
            'generatedForm' => $generatedForm,
        ]);
    }

    public function library(Request $request)
    {
        $employee = Employee::find($request->targetID);

        if (!$employee) {
            return redirect('/employees')->with('error', 'Employee not found');
        }

        $forms = GeneratedForm::where('employee_id', $employee->id)
            ->latest()
            ->get();

        return view('employee-form-library', compact('employee', 'forms'));
    }

    public function show(GeneratedForm $form)
    {
        abort_if($form->employee_id === null, 404);

        if ($form->form_type === 'accountability') {
            return view('accountability-form-archive', ['form' => $form]);
        }

        if ($form->form_type === 'transfer') {
            return view('transfer-form-archive', ['form' => $form]);
        }

        abort(404);
    }
}
