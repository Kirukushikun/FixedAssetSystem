<div class="flex flex-col gap-5">
    @php
        $conditionColors = [
            'Good'      => 'bg-green-500',
            'Repair'    => 'bg-yellow-400',
            'Defective' => 'bg-red-500',
            'Replace'   => 'bg-orange-500',
        ];
        $statusColors = [
            'Available'    => 'bg-blue-500',
            'Issued'       => 'bg-teal-500',
            'Transferred'  => 'bg-purple-500',
            'For Transfer' => 'bg-indigo-400',
            'For Disposal' => 'bg-orange-400',
            'Disposed'     => 'bg-gray-400',
            'Lost'         => 'bg-red-400',
        ];
        $farms = ['BFC', 'BDL', 'PFC', 'RH', 'BBGC', 'HATCHERY'];
    @endphp

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-lg font-bold">Analytics Dashboard</h1>
            <p class="text-sm text-gray-400">Real-time metrics and AI-powered insights for overall asset health, costs, and performance across all farms.</p>
        </div>
        <div class="input-group min-w-[180px]">
            <label>Filter by Farm</label>
            <select wire:model.live="filterFarm">
                <option value="">All Farms</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm }}">{{ $farm }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Overview Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Assets</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $metrics['total'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $metrics['available'] }} available · {{ $metrics['disposed'] }} disposed</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Utilization Rate</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">{{ $metrics['utilization'] }}%</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $metrics['issued'] }} of {{ $metrics['total'] - $metrics['disposed'] }} active assets in use</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-chart-line text-teal-600"></i>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Avg Asset Cost</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">₱{{ number_format($metrics['avgCost'], 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Total value: ₱{{ number_format($metrics['totalCost'], 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-coins text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Needs Attention</p>
                    <p class="text-3xl font-bold {{ $metrics['needsAttention'] > 0 ? 'text-red-600' : 'text-gray-800' }} mt-1">
                        {{ $metrics['needsAttention'] }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Defective or flagged for replacement</p>
                </div>
                <div class="w-12 h-12 {{ $metrics['needsAttention'] > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation {{ $metrics['needsAttention'] > 0 ? 'text-red-500' : 'text-gray-400' }}"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Condition + Status Breakdowns --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card flex flex-col gap-4">
            <h2 class="text-base font-bold">Condition Breakdown</h2>
            @if(empty($metrics['conditions']))
                <p class="text-sm text-gray-400">No data available.</p>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($metrics['conditions'] as $label => $count)
                        @php($pct = $metrics['total'] > 0 ? round(($count / $metrics['total']) * 100) : 0)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $label }}</span>
                                <span class="text-gray-500">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $conditionColors[$label] ?? 'bg-gray-400' }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card flex flex-col gap-4">
            <h2 class="text-base font-bold">Status Breakdown</h2>
            @if(empty($metrics['statuses']))
                <p class="text-sm text-gray-400">No data available.</p>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($metrics['statuses'] as $label => $count)
                        @php($pct = $metrics['total'] > 0 ? round(($count / $metrics['total']) * 100) : 0)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $label }}</span>
                                <span class="text-gray-500">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $statusColors[$label] ?? 'bg-gray-400' }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Farm + Department Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card flex flex-col gap-4">
            <h2 class="text-base font-bold">Farm Distribution</h2>
            @if(empty($metrics['byFarm']))
                <p class="text-sm text-gray-400">No data available.</p>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($metrics['byFarm'] as $farm => $count)
                        @php($pct = $metrics['total'] > 0 ? round(($count / $metrics['total']) * 100) : 0)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $farm ?: '—' }}</span>
                                <span class="text-gray-500">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-teal-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card flex flex-col gap-4">
            <h2 class="text-base font-bold">Top Departments</h2>
            @if(empty($metrics['byDepartment']))
                <p class="text-sm text-gray-400">No data available.</p>
            @else
                <div class="flex flex-col gap-3">
                    @foreach(array_slice($metrics['byDepartment'], 0, 6) as $dept => $count)
                        @php($pct = $metrics['total'] > 0 ? round(($count / $metrics['total']) * 100) : 0)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ $dept ?: '—' }}</span>
                                <span class="text-gray-500">{{ $count }} <span class="text-gray-400">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-indigo-400 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Maintenance + Risk Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card flex flex-col gap-1">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Repairs This Year</p>
            <p class="text-2xl font-bold text-gray-800">{{ $metrics['repairsThisYear'] }}</p>
            <p class="text-sm text-gray-500">₱{{ number_format($metrics['repairCostThisYear'], 0) }} total repair cost</p>
        </div>

        <div class="card flex flex-col gap-3">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Risk Indicators</p>
            <div class="flex flex-col gap-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Near end of life</span>
                    <span class="{{ $metrics['nearEndOfLife'] > 0 ? 'text-orange-500 font-bold' : 'text-gray-500' }}">{{ $metrics['nearEndOfLife'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Flagged employee assets</span>
                    <span class="{{ $metrics['assetsWithFlags'] > 0 ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $metrics['assetsWithFlags'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Overdue audits</span>
                    <span class="{{ $metrics['overdueAudits'] > 0 ? 'text-red-500 font-bold' : 'text-gray-500' }}">{{ $metrics['overdueAudits'] }}</span>
                </div>
            </div>
        </div>

        <div class="card flex flex-col gap-3">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Active Workflows</p>
            <div class="flex flex-col gap-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending disposals</span>
                    <span class="{{ $metrics['activeDisposals'] > 0 ? 'text-orange-500 font-bold' : 'text-gray-500' }}">{{ $metrics['activeDisposals'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pending transfers</span>
                    <span class="{{ $metrics['activeTransfers'] > 0 ? 'text-indigo-500 font-bold' : 'text-gray-500' }}">{{ $metrics['activeTransfers'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Insights Panel --}}
    <div class="card flex flex-col gap-4">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="text-base font-bold flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-teal-500"></i>
                    AI Insights
                </h2>
                <p class="text-sm text-gray-400 mt-0.5">Powered by OpenRouter — analyzes current metrics and generates actionable recommendations.</p>
            </div>
            <div class="flex gap-2">
                @if($aiInsights)
                    <button wire:click="clearInsights"
                        class="px-3 py-2 border border-gray-200 text-sm rounded-lg text-gray-600 hover:bg-gray-50 font-medium">
                        Clear
                    </button>
                @endif
                <button wire:click="generateInsights"
                    wire:loading.attr="disabled"
                    wire:target="generateInsights,confirmRegenerate"
                    class="px-4 py-2 bg-teal-500 text-white rounded-lg text-sm font-bold hover:bg-teal-600 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                    <span wire:loading.remove wire:target="generateInsights,confirmRegenerate">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>
                        {{ $aiInsights ? 'Regenerate' : 'Generate Insights' }}
                    </span>
                    <span wire:loading wire:target="generateInsights,confirmRegenerate">
                        <i class="fa-solid fa-spinner fa-spin mr-1"></i>Analyzing...
                    </span>
                </button>
            </div>
        </div>

        @if(!config('services.openrouter.api_key'))
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                OpenRouter API key not configured. Add <code class="font-mono bg-amber-100 px-1 rounded text-xs">OPENROUTER_API_KEY=your_key_here</code> to your <code class="font-mono bg-amber-100 px-1 rounded text-xs">.env</code> file, then run <code class="font-mono bg-amber-100 px-1 rounded text-xs">php artisan config:clear</code>.
            </div>
        @elseif($aiError)
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ $aiError }}
            </div>
        @elseif($aiInsights)
            <div class="rounded-xl bg-gradient-to-br from-teal-50 to-white border border-teal-100 p-5">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <p class="text-xs text-teal-600 font-semibold uppercase tracking-wide">
                        <i class="fa-solid fa-robot mr-1"></i>
                        AI Analysis · {{ $filterFarm ?: 'All Farms' }}
                    </p>
                    <div class="flex items-center gap-2">
                        @if($insightIsFromToday)
                            <span class="text-xs bg-teal-100 text-teal-700 font-semibold px-2 py-0.5 rounded-full">
                                <i class="fa-solid fa-circle-check mr-1"></i>Today
                            </span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-500 font-semibold px-2 py-0.5 rounded-full">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i>Saved
                            </span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $insightSavedAt }} · {{ $insightSavedBy }}</span>
                    </div>
                </div>
                <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $aiInsights }}</div>
            </div>
        @else
            <div class="rounded-xl bg-gray-50 border border-dashed border-gray-200 p-10 flex flex-col items-center justify-center gap-3 text-center">
                <div class="w-14 h-14 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-wand-magic-sparkles text-teal-500 text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-gray-600">No insights generated yet</p>
                <p class="text-xs text-gray-400 max-w-xs">Click "Generate Insights" to have AI analyze the current asset data and surface key risks, recommendations, and observations.</p>
            </div>
        @endif
    </div>

    {{-- Regenerate Confirmation Modal --}}
    <div @if(! $showRegenerateConfirm) style="display:none" @endif
         class="fixed inset-0 bg-black/40 z-[70]"
         wire:click="cancelRegenerate">
    </div>
    <div @if(! $showRegenerateConfirm) style="display:none" @endif
         class="fixed inset-0 z-[80] flex items-center justify-center px-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 pointer-events-auto">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Regenerate Insights?</h2>
            </div>
            <p class="text-sm text-gray-500 mb-1">
                AI insights for <span class="font-semibold text-gray-700">{{ $filterFarm ?: 'All Farms' }}</span> were already generated today.
            </p>
            <p class="text-sm text-gray-400 mb-6">
                Regenerating will consume additional API credits. The previous insights will be replaced.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="cancelRegenerate"
                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Keep Current
                </button>
                <button type="button" wire:click="confirmRegenerate"
                    wire:loading.attr="disabled"
                    wire:target="confirmRegenerate"
                    class="px-4 py-2 bg-teal-500 text-white rounded-xl text-sm font-bold hover:bg-teal-600">
                    <span wire:loading.remove wire:target="confirmRegenerate">Yes, Regenerate</span>
                    <span wire:loading wire:target="confirmRegenerate"><i class="fa-solid fa-spinner fa-spin mr-1"></i>Analyzing...</span>
                </button>
            </div>
        </div>
    </div>
</div>
