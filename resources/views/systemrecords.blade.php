@extends('layouts.app')

@section('content')

<div class="flex-1 flex flex-col">
     <div class="flex space-x-1 rounded-t-lg overflow-hidden" id="tabs">
          <button data-tab="audit" class="tab-btn active px-5 py-2 bg-white text-gray-800 font-medium rounded-t-lg border border-b-0 border-gray-200">Audit Trail</button>
          <button data-tab="logs" class="tab-btn px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium border border-transparent rounded-t-lg">User Logs</button>
          <button data-tab="trash" class="tab-btn px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium border border-transparent rounded-t-lg">Trash</button>
     </div>

     <div class="p-7 bg-white content flex-1 flex flex-col rounded-lg !rounded-tl-none">
          <!-- <div class="table-header flex justify-between items-center">
               <h1 class="text-xl font-bold">System Records</h1>
               <div class="flex items-center gap-3">
                    <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-sm">ADD NEW EMPLOYEE</button>
                    <i class="fa-solid fa-arrow-down-wide-short cursor-pointer"></i>
                    <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
               </div>
          </div> -->

          <!-- TAB CONTENTS -->
          <div class="bg-white content flex-1 flex flex-col rounded-lg !rounded-tl-none">
          <!-- AUDIT TRAIL -->
          <div class="table-container tab-content" id="audit">
               <table>
                    <thead>
                         <tr>
                              <th>USER ID</th>
                              <th>USER NAME</th>
                              <th>ACTION</th>
                              <th>DATE</th>
                              <th>TIME</th>
                         </tr>
                    </thead>
                    <tbody>
                         <tr>
                              <td>#1553 <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                              <td>Chris Bacon</td>
                              <td>Asset Created</td>
                              <td>Oct 31, 2025</td>
                              <td>12:00 NN</td>
                         </tr>
                    </tbody>
               </table>
          </div>

          <!-- USER LOGS -->
          <div class="tab-content hidden" id="logs">
               <p class="text-gray-500">This is the <b>User Logs</b> section.</p>
          </div>

          <!-- TRASH -->
          <div class="tab-content hidden" id="trash">
               <p class="text-gray-500">This is the <b>Trash</b> section.</p>
          </div>
          </div>

          <div class="pagination-container flex items-center justify-end gap-3 mt-auto">
          <div class="text-xs text-gray-400">Showing 1 to 10 of 50 results</div>

          <!-- Previous Button -->
          <button class="px-2 py-2 rounded-md hover:scale-110 cursor-pointer bg-teal-100 text-sm">
               <i class="fa-solid fa-caret-left text-teal-500"></i>
          </button>

          <!-- Page Numbers -->
          <button class="bg-teal-400 text-white px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">1</button>
          <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">2</button>
          <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">3</button>
          <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">4</button>
          <button class="bg-teal-100 text-teal-500 px-4 py-2 rounded-md hover:scale-110 cursor-pointer text-sm">5</button>

          <!-- Next Button -->
          <button class="px-2 py-2 rounded-md hover:scale-110 cursor-pointer bg-teal-100 text-sm">
               <i class="fa-solid fa-caret-right text-teal-500"></i>
          </button>
          </div>
     </div>     
</div>


@endsection