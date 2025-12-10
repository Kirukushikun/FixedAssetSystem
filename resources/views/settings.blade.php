@extends('layouts.app')

@section('content')
<div class="card">
     <div class="flex items-center justify-between mb-2">
          <h1 class="text-lg font-bold">Profile Information</h1>
          <div class="flex gap-3">
               <button class="px-5 py-2 bg-red-500 rounded-lg font-bold text-white text-xs hover:bg-red-600" onclick="window.location.href='/logout'">LOGOUT</button>
          </div>
     </div>
     
     <div class="flex flex-col gap-2">
          <p class="text-sm text-gray-500"><b>Full Name: </b> {{Auth::user()->name}}</p>
          <p class="text-sm text-gray-500"><b>Position: </b> {{Auth::user()->position}}</p>
          <p class="text-sm text-gray-500"><b>Farm: </b> BFC</p>
          <p class="text-sm text-gray-500"><b>Department: </b> IT & Security</p>
     </div>
</div>

<div class="flex-1 flex gap-5 overflow-hidden">
     <livewire:dynamic-values.department-management />

     <livewire:dynamic-values.category-management />

     <livewire:dynamic-values.subcategory-management />
</div>
@endsection