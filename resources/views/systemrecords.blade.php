@extends('layouts.app')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden h-full">
     <div class="flex space-x-1 rounded-t-lg overflow-hidden" id="tabs">
          <button data-tab="audit"
               class="tab-btn active px-5 py-2 font-medium rounded-t-lg
               bg-white text-gray-800 border border-gray-200 border-b-0">
               Audit Trail
          </button>

          <button data-tab="logs"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               User Logs
          </button>

          <button data-tab="access"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               User Access
          </button>

          <button data-tab="trash"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               Trash
          </button>
     </div>

     <style>
          /* Base styling for all tabs */
          .tab-btn {
               transition: all 0.15s ease;
          }

          /* Hover (only when not active) */
          .tab-btn:not(.active):hover {
               background-color: #e5e7eb; /* Tailwind gray-200 */
               color: #374151;            /* Tailwind gray-700 */
          }

          /* Active effect */
          .tab-btn.active {
               background-color: #ffffff;     /* white */
               color: #1f2937;                /* gray-800 */
               border-color: #e5e7eb;         /* gray-200 */
               border-bottom-color: transparent !important;
               z-index: 10; /* ensures active tab visually sits on top */
          }
     </style>

     <div class="p-7 bg-white flex-1 rounded-lg !rounded-tl-none overflow-hidden flex flex-col min-h-0">

          <!-- TAB CONTENTS -->
          <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
               <!-- AUDIT TRAIL -->
               <div class="tab-content flex-1 flex flex-col min-h-0" id="audit">
                    <livewire:audit-trail />
               </div>

               <!-- USER LOGS -->
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="logs">
                    <livewire:user-log />
               </div>
               
               <!-- USER ACCESS -->
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="access">
                    <livewire:user-access />
               </div>

               <!-- TRASH -->
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="trash">
                    <livewire:trash />
               </div>
          </div>

     </div>     
</div>

@endsection