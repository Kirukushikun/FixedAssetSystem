@extends('layouts.app')

@section('content')
@php
    $label = fn ($e) => $e->employee_name . ' (' . $e->employee_id . ')';
    $badge = [
        'exact'     => ['Exact match',   'bg-green-100 text-green-700'],
        'close'     => ['Close match',   'bg-teal-100 text-teal-700'],
        'partial'   => ['Partial match', 'bg-amber-100 text-amber-700'],
        'ambiguous' => ['Several possible', 'bg-orange-100 text-orange-700'],
        'none'      => ['No match found', 'bg-gray-100 text-gray-500'],
    ];
@endphp

<div class="card content h-full flex flex-col gap-4 overflow-y-auto">
    <div>
        <h1 class="text-lg font-bold text-[#2d3748]">Match Employees</h1>
        <p class="text-sm text-gray-400">
            <span class="font-semibold text-gray-600">{{ $fileName }}</span> · {{ $rowCount }} assets ·
            {{ $names->count() }} names in the Assigned To column
        </p>
        <p class="text-sm text-gray-500 mt-2">
            Confirm which employee each name refers to. Suggestions are only a starting point — change or clear any
            that are wrong. Cleared names are imported as text only, without an employee link.
        </p>
    </div>

    @if($errors->any())
        <div class="border border-red-200 bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3">
            Some selections don't match an employee. Fix the highlighted rows below.
        </div>
    @endif

    @if($employees->isEmpty())
        <div class="border border-amber-200 bg-amber-50 text-amber-700 text-sm rounded-lg px-4 py-3">
            There are no employees in the system yet, so nothing can be matched. Sync or add employees first,
            or continue to import all names as text only.
        </div>
    @endif

    <form method="POST" action="{{ route('assets.migration-confirm', $token) }}" class="flex flex-col gap-4">
        @csrf

        <datalist id="employee-options">
            @foreach($employees as $e)
                <option value="{{ $label($e) }}">{{ $e->farm }} · {{ $e->department }}</option>
            @endforeach
        </datalist>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-400 uppercase tracking-wide border-b">
                        <th class="py-2 pr-4">Name in file</th>
                        <th class="py-2 pr-4 text-right">Assets</th>
                        <th class="py-2 pr-4 text-right">Issued</th>
                        <th class="py-2 pr-4">Suggestion</th>
                        <th class="py-2">Employee</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($names as $i => $row)
                        @php
                            [$text, $class] = $badge[$row['confidence']];
                            $default = $row['employee'] ? $label($row['employee']) : '';
                        @endphp
                        <tr class="border-b last:border-0 align-middle">
                            <td class="py-2 pr-4 font-semibold text-gray-700">
                                {{ $row['name'] }}
                                <input type="hidden" name="names[{{ $i }}]" value="{{ $row['name'] }}">
                            </td>
                            <td class="py-2 pr-4 text-right text-gray-600">{{ $row['total'] }}</td>
                            <td class="py-2 pr-4 text-right text-gray-600">{{ $row['issued'] }}</td>
                            <td class="py-2 pr-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $class }}">
                                    {{ $text }}@if($row['confidence'] === 'ambiguous') ({{ $row['candidates'] }})@endif
                                </span>
                            </td>
                            <td class="py-2 min-w-[320px]">
                                <div class="flex items-center gap-2">
                                    <input
                                        type="text"
                                        name="matches[{{ $i }}]"
                                        list="employee-options"
                                        value="{{ old("matches.$i", $default) }}"
                                        placeholder="Type to search, or leave blank"
                                        class="w-full text-sm rounded-lg border px-2 py-1.5 outline-none focus:border-teal-400 {{ $errors->has("matches.$i") ? 'border-red-400' : 'border-gray-200' }}"
                                    >
                                    <button type="button" title="Clear"
                                        class="text-gray-400 hover:text-gray-700 text-xs"
                                        onclick="this.previousElementSibling.value = ''">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                @error("matches.$i")
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="link_available" value="1" class="mt-0.5" @checked(old('link_available'))>
            <span>
                Also link <strong>Available</strong> assets to the selected employee (for example, a department custodian).
                <span class="text-gray-400">Unchecked: only <strong>Issued</strong> assets are linked; other rows keep the name as text.</span>
            </span>
        </label>

        <div class="flex items-center justify-end gap-2">
            <button type="submit" form="cancel-migration"
                class="px-4 py-2 rounded-lg text-xs font-bold text-gray-500 hover:bg-gray-100">
                Cancel
            </button>
            <button type="submit"
                class="px-4 py-2 bg-[#4fd1c5] hover:bg-teal-500 text-white rounded-lg text-xs font-bold transition-colors"
                onclick="this.disabled = true; this.innerText = 'Importing...'; this.form.submit();">
                Import {{ $rowCount }} Assets
            </button>
        </div>
    </form>

    <form id="cancel-migration" method="POST" action="{{ route('assets.migration-cancel', $token) }}" class="hidden">
        @csrf
    </form>
</div>
@endsection
