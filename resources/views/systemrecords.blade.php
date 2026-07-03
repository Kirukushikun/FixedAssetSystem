@extends('layouts.app')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden h-full">
     @php($sysUser = Auth::user())
     <div class="flex space-x-1 rounded-t-lg overflow-hidden" id="tabs">
          @if($sysUser?->hasPermission('audit.view'))
          <button data-tab="audit"
               class="tab-btn active px-5 py-2 font-medium rounded-t-lg
               bg-white text-gray-800 border border-gray-200 border-b-0">
               Audit Trail
          </button>
          @endif

          @if($sysUser?->hasPermission('activity.view'))
          <button data-tab="logs"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               User Logs
          </button>
          @endif

          @if($sysUser?->hasPermission('users.view'))
          <button data-tab="access"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               User Access
          </button>
          @endif

          @if($sysUser?->hasPermission('assets.delete') || $sysUser?->hasPermission('assets.restore'))
          <button data-tab="trash"
               class="tab-btn px-5 py-2 font-medium rounded-t-lg
               bg-gray-100 text-gray-600 hover:bg-gray-200
               border border-transparent">
               Trash
          </button>
          @endif
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
               @if($sysUser?->hasPermission('audit.view'))
               <div class="tab-content flex-1 flex flex-col min-h-0" id="audit">
                    <livewire:audit-trail />
               </div>
               @endif

               <!-- USER LOGS -->
               @if($sysUser?->hasPermission('activity.view'))
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="logs">
                    <livewire:user-log />
               </div>
               @endif

               <!-- USER ACCESS -->
               @if($sysUser?->hasPermission('users.view'))
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="access">
                    <livewire:user-access />
               </div>
               @endif

               <!-- TRASH -->
               @if($sysUser?->hasPermission('assets.delete') || $sysUser?->hasPermission('assets.restore'))
               <div class="tab-content flex-1 flex flex-col min-h-0 hidden" id="trash">
                    <livewire:trash />
               </div>
               @endif
          </div>

     </div>     
</div>

@endsection