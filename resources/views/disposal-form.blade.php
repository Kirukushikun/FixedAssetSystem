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
        @media print { .no-print { display: none !important; } body { background: white !important; padding: 0 !important; } .print-clean { box-shadow: none !important; border: none !important; } }
    </style>
</head>
<body class="bg-gray-100 py-12 px-4">
    <div class="max-w-4xl mx-auto bg-white shadow-2xl print-clean relative" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.05);">
        <div class="no-print absolute top-4 -right-[120px] z-50 flex flex-col gap-4">
            <button type="button" class="bg-teal-500 text-white px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2" onclick="window.print()"><i class="fa-solid fa-print"></i>Print</button>
            <button type="button" class="px-4 py-2 rounded shadow hover:brightness-95 focus:outline-none flex items-center gap-2" onclick="history.back()"><i class="fa-solid fa-arrow-right-from-bracket"></i>Back</button>
        </div>
        <div class="px-12 pt-10 pb-6 flex flex-col items-center gap-3">
            <img src="{{ asset('img/BGC.png') }}" width="200" alt="">
            <h1 class="text-center text-2xl font-bold text-gray-900">DISPOSAL FORM</h1>
        </div>
        <div class="px-12 py-8">
            <div class="mb-8 text-sm space-y-1">
                <div class="flex"><span class="font-bold text-gray-900 w-40">Asset Reference:</span><span class="text-gray-900">{{ $asset->ref_id }}</span></div>
                <div class="flex"><span class="font-bold text-gray-900 w-40">Asset Name:</span><span class="text-gray-900">{{ $asset->brand }} {{ $asset->model }} ({{ $asset->sub_category }})</span></div>
                <div class="flex"><span class="font-bold text-gray-900 w-40">Current Status:</span><span class="text-gray-900">{{ $request->status }}</span></div>
            </div>
            <div class="space-y-4 text-sm text-gray-900">
                <div>
                    <p class="font-bold mb-1">Reason for Disposal</p>
                    <p>{{ $request->reason }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-bold mb-1">Requested By</p>
                        <p>{{ $request->requested_by_name ?: 'System' }}</p>
                        <p class="text-xs text-gray-500">{{ $request->requester_farm ?: '—' }}{{ $request->requester_department ? ' / ' . $request->requester_department : '' }}</p>
                    </div>
                    <div>
                        <p class="font-bold mb-1">Approval Date</p>
                        <p>{{ optional($request->vp_approved_at)->format('m/d/Y h:i A') ?: 'Pending approval' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-bold mb-1">Approved By</p>
                        <p>{{ $request->vp_approved_by_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="font-bold mb-1">Disposed At</p>
                        <p>{{ optional($request->disposed_at)->format('m/d/Y h:i A') ?: 'Not yet disposed' }}</p>
                    </div>
                </div>
                @if($request->attachment_path)
                    <div>
                        <p class="font-bold mb-1">Supporting Attachment</p>
                        <a href="{{ Storage::url($request->attachment_path) }}" target="_blank" class="text-blue-500 font-semibold">{{ $request->attachment_name ?: 'View attachment' }}</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-gray-100 px-8 py-4 text-center"><p class="text-xs text-gray-600">This is an official company disposal record.</p></div>
    </div>
</body>
</html>
