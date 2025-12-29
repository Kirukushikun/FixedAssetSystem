<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIXED Asset</title>
    <link rel="shortcut icon" href="{{asset('img/Fixed.ico')}}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        @media print {
            /* hide print button */
            .no-print { display: none !important; }

            /* remove page background */
            body {
            background: white !important;
            padding: 0 !important;
            }

            /* remove shadows and borders from the main document */
            .print-clean {
            box-shadow: none !important;
            border: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 py-12 px-4">

    <div class="max-w-4xl mx-auto bg-white shadow-2xl print-clean relative" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.05);">
    
    <div class="no-print absolute top-4 -right-[120px] z-50 flex flex-col gap-4">
        <button
            type="button"
            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2"
            onclick="window.print()"
            aria-label="Print document"
            title="Print">
            <i class="fa-solid fa-print"></i>
            Print
        </button> 
        
        <button
            type="button"
            class="px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2"
            onclick="history.back()"
            aria-label="Go back"
            title="Go Back">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            Back
        </button>         
    </div>
    
    <!-- Header Section -->
        <div class="px-12 pt-10 pb-6 flex flex-col items-center gap-3">
            <img src="{{asset('img/BGC.png')}}" width="200" alt="">
            <h1 class="text-center text-2xl font-bold text-gray-900" >ACCOUNTABILITY FORM</h1>
        </div>

        <!-- Content Section -->
        <div class="px-12 py-8">
            <!-- Info Grid -->
            <div class="mb-8 text-sm space-y-1">
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">Date:</span>
                    <span class="text-gray-900">{{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">To:</span>
                    <span class="text-gray-900">{{$employee->employee_name}}</span>
                </div>
                <div class="flex">
                    <span class="font-bold text-gray-900 w-20">From:</span>
                    <span class="text-gray-900">N/A</span>
                </div>
            </div>

            <!-- Acknowledgment Text -->
            <div class="mb-8">
                <p class="text-sm text-gray-900 leading-relaxed">
                    I, <span class="font-bold uppercase">{{$employee->employee_name}}</span>, acknowledge the receipt of the item/s as listed below, today, <span class="font-bold">{{ now()->format('d/m/Y') }}</span>.
                </p>
            </div>

            <!-- Items Table with Grouped Quantities -->
            <div class="mb-8">
                <table class="w-full border-2 border-gray-400">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400 border-r border-gray-400">Item</th>
                            <th class="px-4 py-3 text-center text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400 w-24">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Group assets by brand and model
                            $groupedAssets = $assets->groupBy(function($asset) {
                                return $asset->brand . ' ' . $asset->model;
                            })->map(function($group) {
                                return [
                                    'name' => $group->first()->brand . ' ' . $group->first()->model,
                                    'quantity' => $group->count(),
                                    'assets' => $group
                                ];
                            });
                        @endphp
                        
                        @foreach($groupedAssets as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-400">{{ $item['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900 font-semibold">{{ $item['quantity'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Terms Section -->
            <div class="mb-10 space-y-5 text-sm text-gray-900 leading-relaxed">
                @php
                    $farmCodes = [
                        'BFC' => 'Brookside Farms',
                        'BDL' => 'Brookdale Farms',
                        'PFC' => 'Poultrypure Farms',
                        'RH' => 'RH Farms',
                    ];
                @endphp
                <p class="text-justify">
                    I understand that the item/s has/have been assigned to me as it is a requirement for my job in
                    <span class="font-bold underline uppercase">{{$farmCodes[$employee->farm]}}, {{$employee->department}} DEPARTMENT,</span> as
                    <span class="font-bold underline uppercase">{{$employee->position}}</span>. I recognize that these item/s are private properties 
                    of the Company and are only assigned to me during my employment or when no longer necessary
                    for my use due to promotion, transfer, or related situations.
                </p>

                <p class="text-justify">
                    I am responsible for the safeguarding of the listed property/asset. In case of loss or damage,
                    the replacement cost will be charged to me through salary deduction.
                </p>
            </div>

            <!-- Signature Section -->
            <div class="space-y-5">
                <!-- Employee Signature -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 pb-4 border-b border-gray-200">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Signature:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- Noted By -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4 pb-4 border-b border-gray-200">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Noted By:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- Issued By -->
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issued By:</label>
                        <div class="border-b-2 border-blue-600 pb-1 min-h-[40px] flex items-end">
                            <!-- <span class="font-bold text-gray-900">MARK LESTER DELA CRUZ</span> -->
                        </div>
                    </div>
                    <div class="sm:w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date:</label>
                        <div class="text-sm font-semibold text-gray-900">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 px-8 py-4 text-center">
            <p class="text-xs text-gray-600">This is an official company document. Please keep for your records.</p>
        </div>
    </div>
</body>
</html>