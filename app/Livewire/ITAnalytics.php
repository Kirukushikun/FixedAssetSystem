<?php

namespace App\Livewire;

use App\Models\AnalyticsInsight;
use App\Models\Asset;
use App\Models\AssetRepair;
use App\Models\Audit;
use App\Models\DisposalRequest;
use App\Models\Flag;
use App\Models\TransferRequest;
use App\Services\OpenRouterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ITAnalytics extends Component
{
    public string $filterFarm = '';
    public string $aiInsights = '';
    public string $aiError = '';
    public bool   $showRegenerateConfirm = false;

    // Metadata of the currently displayed saved insight
    public ?string $insightSavedAt     = null;
    public ?string $insightSavedBy     = null;
    public bool    $insightIsFromToday = false;

    public function mount(): void
    {
        $this->loadLatestInsight();
    }

    public function updatedFilterFarm(): void
    {
        $this->aiError = '';
        $this->showRegenerateConfirm = false;
        $this->loadLatestInsight();
    }

    // ── Triggered by the button ───────────────────────────────────
    public function generateInsights(): void
    {
        if (!config('services.openrouter.api_key')) {
            $this->aiError = 'OpenRouter API key is not configured. Add OPENROUTER_API_KEY to your .env file.';
            return;
        }

        // If an insight was already generated today for this scope, ask first
        if ($this->insightIsFromToday) {
            $this->showRegenerateConfirm = true;
            return;
        }

        $this->doGenerate();
    }

    public function confirmRegenerate(): void
    {
        $this->showRegenerateConfirm = false;
        $this->doGenerate();
    }

    public function cancelRegenerate(): void
    {
        $this->showRegenerateConfirm = false;
    }

    public function clearInsights(): void
    {
        $this->aiInsights        = '';
        $this->aiError           = '';
        $this->insightSavedAt    = null;
        $this->insightSavedBy    = null;
        $this->insightIsFromToday = false;
    }

    // ── Core generation + save ───────────────────────────────────
    private function doGenerate(): void
    {
        $this->aiInsights = '';
        $this->aiError    = '';

        try {
            $metrics = $this->computeMetrics();
            $service = new OpenRouterService();
            $result  = $service->analyze($this->systemPrompt(), $this->buildDataSummary($metrics));

            AnalyticsInsight::create([
                'farm_filter'          => $this->filterFarm,
                'insights'             => $result,
                'metrics_snapshot'     => $metrics,
                'generated_by_user_id' => Auth::id(),
                'generated_by_name'    => Auth::user()?->name,
            ]);

            $this->loadLatestInsight();
        } catch (\Exception $e) {
            Log::error('OpenRouter analysis failed', ['error' => $e->getMessage()]);
            $this->aiError = 'Unable to generate insights. Please verify your API key and try again.';
        }
    }

    // ── Load the latest saved insight for the current farm scope ─
    private function loadLatestInsight(): void
    {
        $latest = AnalyticsInsight::where('farm_filter', $this->filterFarm)
            ->latest()
            ->first();

        if ($latest) {
            $this->aiInsights         = $latest->insights;
            $this->insightSavedAt     = $latest->created_at->format('M d, Y g:i A');
            $this->insightSavedBy     = $latest->generated_by_name;
            $this->insightIsFromToday = $latest->created_at->isToday();
        } else {
            $this->aiInsights         = '';
            $this->insightSavedAt     = null;
            $this->insightSavedBy     = null;
            $this->insightIsFromToday = false;
        }
    }

    // ── Metric computation ────────────────────────────────────────
    private function computeMetrics(): array
    {
        $farm = $this->filterFarm;

        $base = fn() => Asset::where('is_deleted', false)
            ->where('is_archived', false)
            ->when($farm, fn($q) => $q->where('farm', $farm));

        $total       = $base()->count();
        $issued      = $base()->where('status', 'Issued')->count();
        $available   = $base()->where('status', 'Available')->count();
        $disposed    = $base()->where('status', 'Disposed')->count();
        $utilization = $total > $disposed
            ? round(($issued / ($total - $disposed)) * 100, 1)
            : 0;

        $conditions = $base()
            ->groupBy('condition')
            ->selectRaw('`condition`, count(*) as total')
            ->orderByDesc('total')
            ->pluck('total', 'condition')
            ->toArray();

        $statuses = $base()
            ->groupBy('status')
            ->selectRaw('`status`, count(*) as total')
            ->orderByDesc('total')
            ->pluck('total', 'status')
            ->toArray();

        $byFarm = $base()
            ->groupBy('farm')
            ->selectRaw('`farm`, count(*) as total')
            ->orderByDesc('total')
            ->pluck('total', 'farm')
            ->toArray();

        $byDepartment = $base()
            ->whereNotNull('department')
            ->groupBy('department')
            ->selectRaw('`department`, count(*) as total')
            ->orderByDesc('total')
            ->pluck('total', 'department')
            ->toArray();

        $costs = $base()
            ->whereNotNull('item_cost')
            ->where('item_cost', '!=', '')
            ->pluck('item_cost')
            ->map(fn($c) => (float) preg_replace('/[^0-9.]/', '', $c))
            ->filter();

        $avgCost   = $costs->isNotEmpty() ? round($costs->avg(), 2) : 0;
        $totalCost = $costs->isNotEmpty() ? round($costs->sum(), 2) : 0;

        $repairBase = fn() => AssetRepair::whereHas('asset', fn($q) =>
            $q->where('is_deleted', false)
              ->when($farm, fn($q) => $q->where('farm', $farm))
        );

        $repairsThisYear    = $repairBase()->whereYear('date', now()->year)->count();
        $repairCostThisYear = $repairBase()
            ->whereYear('date', now()->year)
            ->pluck('cost')
            ->map(fn($c) => (float) preg_replace('/[^0-9.]/', '', $c ?? '0'))
            ->sum();

        $nearEndOfLife = $base()
            ->whereNotNull('acquisition_date')
            ->whereNotNull('usable_life')
            ->where('usable_life', '!=', '')
            ->get(['acquisition_date', 'usable_life'])
            ->filter(function ($asset) {
                $years = (int) $asset->usable_life;
                if (!$years || !$asset->acquisition_date) return false;
                $eol  = $asset->acquisition_date->copy()->addYears($years);
                $diff = now()->diffInMonths($eol, false);
                return $diff >= 0 && $diff <= 12;
            })
            ->count();

        $needsAttention = $base()->whereIn('condition', ['Defective', 'Replace'])->count();

        $flaggedIds      = Flag::pluck('target_id')->unique()->toArray();
        $assetsWithFlags = $flaggedIds
            ? $base()->whereIn('assigned_id', $flaggedIds)->count()
            : 0;

        $overdueAudits = Audit::where('next_audit_date', '<', now())
            ->whereHas('asset', fn($q) =>
                $q->where('is_deleted', false)
                  ->when($farm, fn($q) => $q->where('farm', $farm))
            )
            ->count();

        $activeDisposals = DisposalRequest::whereHas('asset', fn($q) =>
            $q->when($farm, fn($q) => $q->where('farm', $farm))
        )
        ->whereIn('status', ['Pending Division Head Approval', 'Pending VP Approval', 'VP Approved'])
        ->count();

        $activeTransfers = TransferRequest::whereHas('asset', fn($q) =>
            $q->when($farm, fn($q) => $q->where('farm', $farm))
        )
        ->whereIn('status', ['DH Approval', 'For Transfer'])
        ->count();

        return compact(
            'total', 'issued', 'available', 'disposed', 'utilization',
            'conditions', 'statuses', 'byFarm', 'byDepartment',
            'avgCost', 'totalCost',
            'repairsThisYear', 'repairCostThisYear',
            'nearEndOfLife', 'needsAttention', 'assetsWithFlags',
            'overdueAudits', 'activeDisposals', 'activeTransfers'
        );
    }

    private function systemPrompt(): string
    {
        return "You are a fixed asset management analyst for a multi-farm agricultural company. Analyze the provided asset data and deliver a focused, practical report.

Format your response exactly as follows (use these exact section headers with no asterisks or markdown):

OVERALL HEALTH ASSESSMENT
[2-3 sentences summarizing the overall state]

KEY RISKS
- [risk 1]
- [risk 2]
- [risk 3]

RECOMMENDED ACTIONS
- [specific, actionable recommendation 1]
- [specific, actionable recommendation 2]
- [specific, actionable recommendation 3]

NOTABLE OBSERVATIONS
- [observation 1]
- [observation 2]
- [observation 3]

Keep your total response under 380 words. Be direct and practical — avoid padding and filler phrases.";
    }

    private function buildDataSummary(array $m): string
    {
        $scope   = $this->filterFarm ? "Farm: {$this->filterFarm}" : 'All Farms';
        $condStr = collect($m['conditions'])->map(fn($v, $k) => "{$k}: {$v}")->implode(' | ');
        $statStr = collect($m['statuses'])->map(fn($v, $k) => "{$k}: {$v}")->implode(' | ');
        $farmStr = collect($m['byFarm'])->map(fn($v, $k) => "{$k}: {$v}")->implode(' | ');
        $deptStr = collect($m['byDepartment'])->take(5)->map(fn($v, $k) => "{$k}: {$v}")->implode(' | ');

        return "FIXED ASSET REPORT — " . now()->format('F d, Y') . " | Scope: {$scope}

OVERVIEW
Total Assets: {$m['total']} | In Use: {$m['issued']} | Available: {$m['available']} | Disposed: {$m['disposed']}
Utilization Rate: {$m['utilization']}%
Average Asset Cost: ₱" . number_format($m['avgCost'], 2) . " | Total Portfolio Value: ₱" . number_format($m['totalCost'], 2) . "

CONDITIONS: {$condStr}
STATUSES: {$statStr}
FARM DISTRIBUTION: {$farmStr}
TOP DEPARTMENTS: {$deptStr}

MAINTENANCE — Year to Date
Repair Incidents: {$m['repairsThisYear']} | Total Repair Cost: ₱" . number_format($m['repairCostThisYear'], 2) . "

RISK INDICATORS
Assets Defective or Needing Replacement: {$m['needsAttention']}
Near End of Life (within 12 months): {$m['nearEndOfLife']}
Assets Linked to Flagged Employees: {$m['assetsWithFlags']}
Overdue Audits: {$m['overdueAudits']}
Active Disposal Requests: {$m['activeDisposals']}
Active Transfer Requests: {$m['activeTransfers']}";
    }

    public function render()
    {
        return view('livewire.i-t-analytics', [
            'metrics' => $this->computeMetrics(),
        ]);
    }
}
