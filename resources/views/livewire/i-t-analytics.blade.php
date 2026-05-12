<div class="flex flex-col gap-5">
    <div>
        <h1 class="text-lg font-bold">IT Analytics Dashboard</h1>
        <p class="text-sm text-gray-400 mb-4">Track utilization patterns, lifecycle costs, and department/farm performance.</p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Assets</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $metrics['total_assets'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Utilization Rate</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $metrics['utilization_rate'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Avg Lifecycle Cost</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $metrics['avg_lifecycle_cost'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-coins text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Assets</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $metrics['total_assets'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card">
            <h2 class="text-lg font-bold mb-4">Department Performance</h2>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                <p class="text-gray-400">Chart placeholder - Department performance metrics</p>
            </div>
        </div>
        <div class="card">
            <h2 class="text-lg font-bold mb-4">Farm Performance</h2>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                <p class="text-gray-400">Chart placeholder - Farm performance metrics</p>
            </div>
        </div>
    </div>

    <!-- Utilization Patterns -->
    <div class="card">
        <h2 class="text-lg font-bold mb-4">Utilization Patterns</h2>
        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
            <p class="text-gray-400">Chart placeholder - Utilization trends over time</p>
        </div>
    </div>

    <!-- Lifecycle Costs -->
    <div class="card">
        <h2 class="text-lg font-bold mb-4">Lifecycle Costs Analysis</h2>
        <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
            <p class="text-gray-400">Chart placeholder - Lifecycle cost breakdown by category</p>
        </div>
    </div>
</div>
