<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\AssetSmeReview;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Flag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SmeWorkspace extends Component
{
    public $employeeId = '';
    public $employees = [];
    public $farms = [];
    public $departments = [];
    public $filterFarm = '';
    public $filterDepartment = '';
    public $selectedEmployee = null;
    public $assetReviews = [];
    public $categoryCodeImage;

    public function mount()
    {
        $this->employees = Employee::where('is_deleted', false)
            ->orderBy('employee_name')
            ->get(['id', 'employee_name', 'employee_id', 'farm', 'department'])
            ->toArray();

        $this->farms = collect($this->employees)->pluck('farm')->filter()->unique()->sort()->values()->toArray();
        $this->departments = collect($this->employees)->pluck('department')->filter()->unique()->sort()->values()->toArray();
        $this->categoryCodeImage = Category::all()->keyBy('code');
    }

    public function updatedFilterFarm(): void
    {
        $this->employeeId = '';
        $this->selectedEmployee = null;
        $this->assetReviews = [];
    }

    public function updatedFilterDepartment(): void
    {
        $this->employeeId = '';
        $this->selectedEmployee = null;
        $this->assetReviews = [];
    }

    public function updatedEmployeeId($value)
    {
        $this->selectedEmployee = Employee::find($value);
        $this->loadAssetReviews();
    }

    public function loadAssetReviews()
    {
        $this->assetReviews = [];

        if (!$this->selectedEmployee) {
            return;
        }

        $assets = Asset::where('is_deleted', false)
            ->where('assigned_id', $this->selectedEmployee->id)
            ->orderBy('ref_id')
            ->get();

        foreach ($assets as $asset) {
            $review = AssetSmeReview::where('employee_id', $this->selectedEmployee->id)
                ->where('asset_id', $asset->id)
                ->latest()
                ->first();

            $this->assetReviews[$asset->id] = [
                'condition_note' => $review?->condition_note ?? $asset->condition,
                'remarks' => $review?->remarks ?? '',
                'recommended_flag' => $review?->recommended_flag ?? '',
                'flagged_employee' => (bool) ($review?->flagged_employee ?? false),
            ];
        }
    }

    public function saveReview($assetId)
    {
        if (!Auth::user()?->hasPermission('sme.review')) {
            $this->dispatch('notif', type: 'failed', header: 'Access Denied', message: 'You do not have permission to save SME reviews.');
            return;
        }

        if (!$this->selectedEmployee) {
            return;
        }

        $asset = Asset::find($assetId);

        if (!$asset) {
            $this->dispatch('notif', type: 'failed', header: 'Asset Not Found', message: 'The selected asset could not be found.');
            return;
        }

        $payload = $this->assetReviews[$assetId] ?? [];

        $this->validate([
            "assetReviews.$assetId.condition_note" => 'nullable|string|max:255',
            "assetReviews.$assetId.remarks" => 'nullable|string|max:2000',
            "assetReviews.$assetId.recommended_flag" => 'nullable|string|max:255',
            "assetReviews.$assetId.flagged_employee" => 'boolean',
        ]);

        try {
            $createdFlagId = null;

            if (!empty($payload['flagged_employee']) && !empty($payload['recommended_flag'])) {
                $existingFlag = Flag::where('target_id', $this->selectedEmployee->id)
                    ->where('flag_type', $payload['recommended_flag'])
                    ->where('asset', $asset->brand . ' ' . $asset->model . ' (' . $asset->sub_category . ')')
                    ->latest()
                    ->first();

                if (!$existingFlag) {
                    $existingFlag = Flag::create([
                        'target_id' => $this->selectedEmployee->id,
                        'flag_type' => $payload['recommended_flag'],
                        'asset' => $asset->brand . ' ' . $asset->model . ' (' . $asset->sub_category . ')',
                    ]);
                }

                $createdFlagId = $existingFlag->id;
            }

            AssetSmeReview::create([
                'employee_id' => $this->selectedEmployee->id,
                'asset_id' => $asset->id,
                'reviewed_by_user_id' => Auth::id(),
                'reviewed_by_name' => Auth::user()?->name,
                'condition_note' => $payload['condition_note'] ?: null,
                'remarks' => $payload['remarks'] ?: null,
                'recommended_flag' => $payload['recommended_flag'] ?: null,
                'created_flag_id' => $createdFlagId,
                'flagged_employee' => !empty($payload['flagged_employee']),
            ]);

            Cache::flush();

            $this->dispatch('notif', type: 'success', header: 'SME Review Saved', message: 'Review and flag details were saved successfully.');
            $this->loadAssetReviews();
        } catch (\Exception $e) {
            Log::error('SME review save failed', [
                'error' => $e->getMessage(),
                'employee_id' => $this->selectedEmployee->id,
                'asset_id' => $assetId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('notif', type: 'failed', header: 'Save Failed', message: 'Unable to save SME review. Please try again.');
        }
    }

    public function render()
    {
        $assets = collect();

        if ($this->selectedEmployee) {
            $assets = Asset::where('is_deleted', false)
                ->where('assigned_id', $this->selectedEmployee->id)
                ->orderBy('ref_id')
                ->get();
        }

        $filteredEmployees = collect($this->employees)
            ->when($this->filterFarm, fn ($employees) => $employees->where('farm', $this->filterFarm))
            ->when($this->filterDepartment, fn ($employees) => $employees->where('department', $this->filterDepartment))
            ->values();

        return view('livewire.sme-workspace', [
            'assets' => $assets,
            'filteredEmployees' => $filteredEmployees,
        ]);
    }
}
