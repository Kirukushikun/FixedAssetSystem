<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Print</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: white; }

        .page-header {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .page-header h1 { font-size: 18px; font-weight: bold; }
        .page-header p { font-size: 12px; color: #666; margin-top: 4px; }

        .qr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            padding: 20px;
        }

        .qr-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            page-break-inside: avoid;
        }

        .qr-card img {
            width: 120px;
            height: 120px;
            margin: 0 auto 8px;
            display: block;
        }

        .qr-card .ref-id {
            font-size: 12px;
            font-weight: bold;
            color: #111;
        }

        .qr-card .brand-model {
            font-size: 11px;
            color: #555;
            margin-top: 2px;
        }

        .qr-card .farm {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }

        .print-actions {
            text-align: center;
            padding: 20px;
        }

        .print-actions button {
            padding: 10px 30px;
            background: #4fd1c5;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            margin-right: 10px;
        }

        .print-actions a {
            padding: 10px 30px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            color: #555;
            text-decoration: none;
        }

        @media print {
            .print-actions { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-actions">
        <button onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print
        </button>
        <a href="javascript:history.back()">Back</a>
    </div>

    <div class="page-header">
        <h1>QR Code Sheet</h1>
        <p>Generated {{ now()->format('F d, Y h:i A') }} &mdash; {{ $assets->count() }} asset(s)</p>
    </div>

    <div class="qr-grid">
        @foreach($assets as $asset)
        <div class="qr-card">
            <img src="{{ asset('storage/' . $asset->qr_code) }}" alt="QR Code">
            <div class="ref-id">{{ $asset->ref_id }}</div>
            <div class="brand-model">{{ $asset->brand }} {{ $asset->model }}</div>
            <div class="farm">{{ $asset->farm ?? 'Unassigned' }}</div>
        </div>
        @endforeach
    </div>

</body>
</html>