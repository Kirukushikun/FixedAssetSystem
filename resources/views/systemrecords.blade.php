@extends('layouts.app')

@section('content')

<div class="flex-1 flex flex-col">
     <div class="flex space-x-1 rounded-t-lg overflow-hidden" id="tabs">
          <button data-tab="audit" class="tab-btn active px-5 py-2 bg-white text-gray-800 font-medium rounded-t-lg border border-b-0 border-gray-200">Audit Trail</button>
          <button data-tab="logs" class="tab-btn px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium border border-transparent rounded-t-lg">User Logs</button>
          <button data-tab="access" class="tab-btn px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium border border-transparent rounded-t-lg">User Access</button>
          <button data-tab="trash" class="tab-btn px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium border border-transparent rounded-t-lg">Trash</button>
     </div>

     <div class="p-7 bg-white content flex-1 flex flex-col rounded-lg !rounded-tl-none">

          <!-- TAB CONTENTS -->
          <div class="bg-white content flex-1 flex flex-col rounded-lg !rounded-tl-none">
               <!-- AUDIT TRAIL -->
               <livewire:audit-trail />

               <!-- USER LOGS -->
               <livewire:user-log />
               
               <!-- USER ACCESS -->
               <livewire:user-access />

               <!-- TRASH -->
               <livewire:trash />
          </div>

     </div>     
</div>


@endsection