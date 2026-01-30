@extends('layouts.app')

@section('content')
<div class="card">
     <div class="flex items-center justify-between mb-2">
          <h1 class="text-lg font-bold">Profile Information</h1>
          <livewire:settings-action />
     </div>
     
     <div class="flex flex-col gap-2">
          <p class="text-sm text-gray-500"><b>Full Name: </b> {{Auth::user()->name}}</p>
          <p class="text-sm text-gray-500"><b>Position: </b> {{Auth::user()->position}}</p>
          <p class="text-sm text-gray-500"><b>Farm: </b> BFC</p>
          <p class="text-sm text-gray-500"><b>Department: </b> IT & Security</p>
     </div>
</div>

<div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 overflow-y-auto">
     <livewire:dynamic-values.department-management />

     <livewire:dynamic-values.category-management />

     <livewire:dynamic-values.subcategory-management />

     <livewire:dynamic-values.input-management inputType="brand" />

     <livewire:dynamic-values.input-management inputType="processor" />

     <livewire:dynamic-values.input-management inputType="RAM" />

     <livewire:dynamic-values.input-management inputType="Storage" />
</div>
@endsection