<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIXED Asset</title>
    <link rel="shortcut icon" href="{{ asset('img/Fixed.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-clean { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 py-12 px-4">
    <div class="max-w-5xl mx-auto bg-white shadow-2xl print-clean relative" style="box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.05);">
        <div class="no-print absolute top-4 -right-[120px] z-50 flex flex-col gap-4">
            <button type="button" class="bg-teal-500 text-white px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2" onclick="window.print()">
                <i class="fa-solid fa-print"></i>
                Print
            </button>
            <button type="button" class="px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2" onclick="history.back()">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                Back
            </button>
        </div>

        <div class="px-12 pt-10 pb-6 flex flex-col items-center gap-3">
            <img src="{{ asset('img/BGC.png') }}" width="200" alt="">
            <h1 class="text-center text-2xl font-bold text-gray-900">TRANSFER FORM</h1>
        </div>

        <div class="px-12 py-8">
            <div class="mb-8 text-sm space-y-1">
                <div class="flex"><span class="font-bold text-gray-900 w-24">Date:</span><span class="text-gray-900">{{ now()->format('d/m/Y') }}</span></div>
                <div class="flex"><span class="font-bold text-gray-900 w-24">Employee:</span><span class="text-gray-900">{{ $employee->employee_name }}</span></div>
                <div class="flex"><span class="font-bold text-gray-900 w-24">Position:</span><span class="text-gray-900">{{ $employee->position }}</span></div>
            </div>

            <div class="mb-8">
                <p class="text-sm text-gray-900 leading-relaxed">
                    This form documents the most recent asset transfer records assigned to
                    <span class="font-bold uppercase">{{ $employee->employee_name }}</span>.
                </p>
            </div>

            <div class="mb-8 overflow-hidden">
                <table class="w-full border-2 border-gray-400">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400 border-r border-gray-400">Asset</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400 border-r border-gray-400">Previous Owner</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400 border-r border-gray-400">Current Owner</th>
                            <th class="px-4 py-3 text-left text-sm font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-400">Transfer Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transferAssets as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 border-r border-t border-gray-400">
                                    <div class="font-semibold">{{ $item['brand'] }} {{ $item['model'] }}</div>
                                    <div class="text-xs italic text-gray-500">{{ $item['sub_category'] }} • {{ $item['ref_id'] }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 border-r border-t border-gray-400">
                                    <div class="font-semibold">{{ $item['from_name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['from_farm'] ?? '—' }}{{ $item['from_department'] ? ' / ' . $item['from_department'] : '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 border-r border-t border-gray-400">
                                    <div class="font-semibold">{{ $item['to_name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $item['to_farm'] ?? '—' }}{{ $item['to_department'] ? ' / ' . $item['to_department'] : '' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900 border-t border-gray-400">{{ \Carbon\Carbon::parse($item['transferred_at'])->format('d/m/Y h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mb-10 space-y-5 text-sm text-gray-900 leading-relaxed">
                <p class="text-justify">
                    This document serves as a formal record that the listed asset or assets were transferred from the previous owner to the current owner indicated above.
                    The current owner acknowledges responsibility for proper use, safekeeping, and return of the company property when required.
                </p>
                <p class="text-justify">
                    All succeeding transfers should be documented accordingly, and this form reflects only the latest confirmed transfer record for each listed asset.
                </p>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-8 pt-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Owner Signature:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issued / Witnessed By:</label>
                        <div class="border-b-2 border-gray-400 pb-1 min-h-[40px]"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-100 px-8 py-4 text-center">
            <p class="text-xs text-gray-600">Document Ref: {{ $generatedForm->id }} • This is an official company document.</p>
        </div>
    </div>
</body>
</html>
