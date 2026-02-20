
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
<body>
    <div class="min-h-screen bg-gray-100 py-10 px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            
            <!-- Header with QR Code -->
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Asset Information</h1>
                    <p class="text-gray-500 text-sm">Reference ID: {{ $asset->ref_id }}</p>
                </div>
                @if($asset->qr_code)
                    <img src="{{ asset('storage/' . $asset->qr_code) }}" class="w-24 h-24" alt="QR Code">
                @endif
            </div>

            <hr class="mb-6">

            <!-- Asset Details -->
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Category Type</p>
                        <p class="font-semibold text-gray-800">{{ $asset->category_type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Category</p>
                        <p class="font-semibold text-gray-800">{{ $categoryDetails->name ?? $asset->category }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Sub-category</p>
                        <p class="font-semibold text-gray-800">{{ $asset->sub_category }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Brand</p>
                        <p class="font-semibold text-gray-800">{{ $asset->brand }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Model</p>
                        <p class="font-semibold text-gray-800">{{ $asset->model }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block px-3 py-1 text-sm font-semibold text-white rounded 
                            @if($asset->status == 'Available') bg-green-500
                            @elseif($asset->status == 'Issued') bg-yellow-500
                            @elseif($asset->status == 'Transferred') bg-blue-500
                            @else bg-gray-500
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Condition</p>
                        <p class="font-semibold 
                            @if($asset->condition == 'Good') text-green-600
                            @elseif($asset->condition == 'Repair') text-yellow-600
                            @else text-red-600
                            @endif">
                            {{ $asset->condition }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Acquisition Date</p>
                        <p class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($asset->acquisition_date)->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                @if($asset->assigned_name)
                    <hr class="my-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">Assignment Information</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Assigned To</p>
                            <p class="font-semibold text-gray-800">{{ $asset->assigned_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Farm</p>
                            <p class="font-semibold text-gray-800">{{ $asset->farm ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <p class="text-sm text-gray-500">Department</p>
                            <p class="font-semibold text-gray-800">{{ $asset->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="font-semibold text-gray-800">{{ $asset->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t text-center text-sm text-gray-500">
                <p>This is a public asset information page</p>
                <p class="mt-1">For management access, please log in to the system</p>
            </div>
        </div>
    </div>
    @livewireScripts
</body>
</html>
