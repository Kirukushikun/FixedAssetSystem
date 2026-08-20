<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title>FIXED Asset</title>

     @livewireStyles
     @vite(['resources/css/app.css'])
     <link rel="shortcut icon" href="{{asset('img/Fixed.ico')}}" type="image/x-icon">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="preconnect" href="https://fonts.googleapis.com" />
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
     <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
     <link rel="stylesheet" href="{{asset('css/global.css')}}" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    {{-- Choice modal — shown only to unauthenticated visitors --}}
    @if($showModal)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50"
        style="display: flex"
    >
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="mb-6 text-center">
                <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5z" />
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-800">What are you here for?</h2>
                <p class="text-sm text-gray-500 mt-1">
                    You scanned asset <span class="font-semibold text-gray-700">{{ $asset->ref_id }}</span>
                </p>
            </div>

            <div class="flex flex-col gap-3">
                <button
                    @click="show = false"
                    class="w-full bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl p-4 text-left transition-colors"
                >
                    <div class="font-semibold text-gray-800 text-sm">View Asset Details</div>
                    <div class="text-xs text-gray-500 mt-0.5">See basic information about this asset</div>
                </button>

                <a
                    href="/login?redirect_to={{ urlencode('/assetmanagement/audit?targetID=' . $encryptedId) }}"
                    class="w-full bg-teal-500 hover:bg-teal-600 rounded-xl p-4 text-left transition-colors block"
                >
                    <div class="font-semibold text-white text-sm">I'm an Auditor</div>
                    <div class="text-xs text-teal-100 mt-0.5">Sign in to submit an audit entry for this asset</div>
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="min-h-screen flex items-center justify-center py-6 px-4 sm:py-10 sm:px-6">
        <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg p-6 sm:p-8">
            
            <!-- Header with QR Code -->
            <div class="flex flex-col sm:flex-row items-start sm:items-start justify-between gap-4 mb-6">
                <div class="flex-1">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Asset Information</h1>
                    <p class="text-gray-500 text-xs sm:text-sm mt-1">Reference ID: {{ $asset->ref_id }}</p>
                </div>
                @if($asset->qr_code)
                    <img src="{{ asset('storage/' . $asset->qr_code) }}" class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0" alt="QR Code">
                @endif
            </div>

            <hr class="mb-6">

            <!-- Asset Details -->
            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Category Type</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->category_type }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Category</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $categoryDetails->name ?? $asset->category }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Sub-category</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->sub_category }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Brand</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->brand }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Model</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->model }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Status</p>
                        <span class="inline-block px-3 py-1 text-xs sm:text-sm font-semibold text-white rounded
                            @if($asset->status == 'Pending Acquisition') bg-blue-300
                            @elseif($asset->status == 'Available') bg-green-500
                            @elseif($asset->status == 'Issued') bg-yellow-500
                            @elseif($asset->status == 'Transferred') bg-blue-500
                            @elseif($asset->status == 'For Disposal') bg-orange-500
                            @elseif($asset->status == 'Disposed') bg-gray-800
                            @else bg-gray-500
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Condition</p>
                        <p class="font-semibold text-sm sm:text-base
                            @if($asset->condition == 'Good') text-green-600
                            @elseif($asset->condition == 'Repair') text-yellow-600
                            @else text-red-600
                            @endif">
                            {{ $asset->condition }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-500 mb-1">Acquisition Date</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-800">
                            {{ \Carbon\Carbon::parse($asset->acquisition_date)->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                @if($asset->assigned_name)
                    <hr class="my-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3">Assignment Information</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Assigned To</p>
                            <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->assigned_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Farm</p>
                            <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->farm ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Department</p>
                            <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Location</p>
                            <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif

                @if($asset->latestDisposalRequest)
                    <hr class="my-4">
                    <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-3">Disposal Information</h3>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1">Disposal Status</p>
                                <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->latestDisposalRequest->status }}</p>
                            </div>
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500 mb-1">Requested By</p>
                                <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->latestDisposalRequest->requested_by_name ?: 'System' }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-xs sm:text-sm text-gray-500 mb-1">Reason</p>
                            <p class="text-sm text-gray-700">{{ $asset->latestDisposalRequest->reason }}</p>
                        </div>
                        @if($asset->latestDisposalRequest->vp_approved_by_name)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Approved By</p>
                                    <p class="font-semibold text-sm sm:text-base text-gray-800">{{ $asset->latestDisposalRequest->vp_approved_by_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500 mb-1">Approved At</p>
                                    <p class="font-semibold text-sm sm:text-base text-gray-800">{{ optional($asset->latestDisposalRequest->vp_approved_at)->format('m/d/Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t text-center text-xs sm:text-sm text-gray-500">
                <p>This is a public asset information page</p>
                <p class="mt-1">For management access, please log in to the system</p>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>