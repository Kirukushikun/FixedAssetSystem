@extends('layouts.app')

@section('content')
<div class="card content flex-1 flex flex-col" x-data="{
     showModal: false,
     modalTemplate: '',
}">
     <div class="table-header flex justify-between items-center">
          <h1 class="text-lg font-bold">Employee List</h1>
          <div class="flex items-center gap-3">
               <div class="border border-2 px-3 py-1 rounded-md border-gray-300">
                    <input class="outline-none text-sm" type="text">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
               </div>
               <button class="px-5 py-2 bg-[#4fd1c5] rounded-lg font-bold text-white text-xs hover:bg-teal-500" @click="showModal = true; modalTemplate = 'create'">ADD NEW EMPLOYEE</button>
               <i class="fa-solid fa-file-import cursor-pointer"></i>
               <i class="fa-solid fa-file-export cursor-pointer"></i>
               <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
          </div>
     </div>

     <div class="table-container">
          <table>
               <thead>
                    <tr>
                         <th>EMPLOYEE ID</th>
                         <th>EMPLOYEE NAME</th>
                         <th>POSITION</th>
                         <th>FARM</th>
                         <th>DEPARTMENT/DIVISION</th>
                         <th>ASSIGNED ASSETS</th>
                         <th>FLAGS</th>
                         <th>ACTION</th>
                    </tr>
               </thead>
               <tbody>
                    <tr>
                         <td>#1553 <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                         <td>Chris Bacon</td>
                         <td>Web Developer</td>
                         <td>BFC</td>
                         <td>IT & Security</td>
                         <td>123</td>
                         <td>
                              <div class="flex gap-2 items-center">
                              <i class="fa-solid fa-flag text-[#4299E1]"></i>
                              <i class="fa-solid fa-flag text-[#C075F9]"></i>
                              <i class="fa-solid fa-flag text-[#ECC94B]"></i>
                              <p class="font-bold text-gray-400">+1</p>
                              </div>
                         </td>
                         <td x-data="{ open: false }" class="relative">
                              <i class="fa-solid fa-ellipsis-vertical cursor-pointer" @click="open = !open"></i>

                              <!-- Dropdown -->
                              <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-36 bg-white border border-gray-200 rounded-lg shadow-md z-40">
                              <ul class="text-sm text-gray-700">
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" onclick="window.location.href='employee-view.html'">View</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 hover:bg-gray-100" @click="modalTemplate='edit'; showModal=true">Edit</button>
                                   </li>
                                   <li>
                                        <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100" @click="modalTemplate='delete'; showModal=true">Delete</button>
                                   </li>
                              </ul>
                              </div>
                         </td>
                    </tr>
                    <tr>
                         <td>#1253 <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                         <td>Juan Dela Cruz</td>
                         <td>Web Developer</td>
                         <td>BFC</td>
                         <td>IT & Security</td>
                         <td>123</td>
                         <td>
                              <div class="flex gap-2 items-center">
                              <i class="fa-solid fa-flag text-[#ECC94B]"></i>
                              <i class="fa-solid fa-flag text-[#ED8936]"></i>
                              <i class="fa-solid fa-flag text-[#F56565]"></i>
                              <p class="font-bold text-gray-400">+2</p>
                              </div>
                         </td>
                         <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                    </tr>
                    <tr>
                         <td>#1173 <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                         <td>Jonathan Earth</td>
                         <td>Web Developer</td>
                         <td>BFC</td>
                         <td>IT & Security</td>
                         <td>123</td>
                         <td>
                              <div class="flex gap-2 items-center">
                              <i class="fa-solid fa-flag text-[#ECC94B]"></i>
                              <i class="fa-solid fa-flag text-[#ED8936]"></i>
                              </div>
                         </td>
                         <td><i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i></td>
                    </tr>
               </tbody>
          </table>
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

     <!-- Backdrop -->
     <div 
          x-show="showModal"
          x-transition.opacity
          class="fixed inset-0 bg-black/30 z-40"
     ></div>

     <!-- Modal -->
     <div 
          x-show="showModal"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-90"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-90"
          class="fixed inset-0 flex items-center justify-center z-50"
     >
          <div class="relative bg-white p-8 rounded-lg shadow-lg w-[26rem]">
               <div class="absolute right-7 top-7 text-gray-400 cursor-pointer hover:text-gray-800" @click="showModal = false"><i class="fa-solid fa-xmark"></i></div>

               <!-- With Input -->
               <div class="flex flex-col gap-5" x-show="modalTemplate === 'create'">
                    <h2 class="text-xl font-semibold -mb-2">Create Modal</h2>

                    <div class="input-group">
                         <label>Input Field 1: </label>
                         <input type="text" >
                    </div>

                    <div class="input-group">
                         <label>Input Field 2: </label>
                         <input type="text" >
                    </div>
                    
                    <div class="flex justify-end gap-3">
                         <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                         <button type="button" @click="showModal = false;" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    </div>
               </div>

               <div class="flex flex-col gap-5" x-show="modalTemplate === 'edit'">
                    <h2 class="text-xl font-semibold -mb-2">Edit Modal</h2>

                    <div class="input-group">
                         <label>Input Field 1: </label>
                         <input type="text" >
                    </div>

                    <div class="input-group">
                         <label>Input Field 2: </label>
                         <input type="text" >
                    </div>
                    
                    <div class="flex justify-end gap-3">
                         <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                         <button type="button" @click="showModal = false;" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    </div>
               </div>
               
               <!-- Just Confirmation -->
               <div class="flex flex-col gap-5" x-show="modalTemplate === 'delete'">
                    <h2 class="text-xl font-semibold -mb-2">Create Modal</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima, incidunt! asdadasd</p>

                    <div class="flex justify-end gap-3">
                         <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 cursor-pointer">Cancel</button>
                         <button type="button" @click="showModal = false;" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-800 cursor-pointer">Confirm</button>
                    </div>
               </div>
          </div>
     </div>
</div>
@endsection