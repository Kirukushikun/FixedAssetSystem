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

<!-- <div class="grid grid-cols-3 gap-5">
     <div class="card flex flex-col gap-4">
          <h1 class="text-lg font-bold flex justify-between">Farms <i class="fa-solid fa-plus text-teal-400 cursor-pointer hover:scale-125"></i></h1>
          <div class="flex items-center justify-between">
               <p>BFC</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>BFC</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>BFC</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>BFC</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
     </div>
     <div class="card flex flex-col gap-4">
          <h1 class="text-lg font-bold flex justify-between">Division/Department <i class="fa-solid fa-plus text-teal-400 cursor-pointer hover:scale-125"></i></h1>
          <div class="flex items-center justify-between">
               <p>IT & Security</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Purchasing</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Human Resources</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Sales & Marketing</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
     </div>
     <div class="card flex flex-col gap-4">
          <h1 class="text-lg font-bold flex justify-between">Category <i class="fa-solid fa-plus text-teal-400 cursor-pointer hover:scale-125"></i></h1>
          <div class="flex items-center justify-between">
               <p>Desktop</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Laptop</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Chairs</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
          <div class="flex items-center justify-between">
               <p>Table</p>
               <div class="flex gap-3">
                    <button class="text-red-500 hover:scale-105">Delete</button>
                    <button class="text-blue-500 hover:scale-105">Edit</button>
               </div>
          </div>
     </div>
</div> -->
@endsection