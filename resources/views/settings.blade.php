@extends('layouts.app')

@section('content')

<div class="flex flex-col gap-5 h-full">

    {{-- ── Profile Card ── --}}
    <div class="card">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-bold text-[#2d3748]">Profile Information</h1>
            <livewire:settings-action />
        </div>
        <div class="flex flex-col gap-1.5">
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-700">Full Name:</span> {{ Auth::user()->name }}</p>
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-700">Position:</span> {{ Auth::user()->position }}</p>
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-700">Farm:</span> BFC</p>
            <p class="text-sm text-gray-500"><span class="font-semibold text-gray-700">Department:</span> IT & Security</p>
        </div>
    </div>

    {{-- ── Row 1: Categories (1fr) + Subcategories (2fr) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-5">
        <livewire:dynamic-values.category-management />
        <livewire:dynamic-values.subcategory-management />
    </div>

    {{-- ── Row 2: Department + Input managers ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 pb-5 auto-rows-fr">
        <livewire:dynamic-values.department-management />
        <livewire:dynamic-values.input-management inputType="brand" />
        <livewire:dynamic-values.input-management inputType="processor" />
        <livewire:dynamic-values.input-management inputType="RAM" />
        <livewire:dynamic-values.input-management inputType="storage" />
    </div>

</div>

@endsection