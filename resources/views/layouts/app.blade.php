
<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title>Document</title>

     @livewireStyles
     @vite(['resources/css/app.css'])
     <link rel="shortcut icon" href="{{asset('img/Fixed.ico')}}" type="image/x-icon">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="preconnect" href="https://fonts.googleapis.com" />
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
     <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
     <link rel="stylesheet" href="{{asset('css/global.css')}}" />
</head>
<body>


     <nav>
          <div class="logo">
               <p class="font-bold flex gap-1 items-center"><img src="{{asset('img/Fixed.png')}}" width="25" alt=""> <span>FIXED ASSET</span></p>
               <button id="toggle-btn" class="text-lg hover:scale-125"><i class="fa-solid fa-bars"></i></button>
          </div>

          <aside class="sidebar">
               <a href="/dashboard" class="{{ request()->is('dashboard*') ? 'active' : '' }}"
                    ><span><i class="fa-solid fa-house-chimney"></i></span>
                    <p>Dashboard</p></a
               >
               <a href="/assetmanagement" class="{{ request()->is('assetmanagement*') ? 'active' : '' }}"
                    ><span><i class="fa-solid fa-boxes-stacked"></i></span>
                    <p>Asset Management</p></a
               >
               <a href="/employees" class="{{ request()->is('employees*') ? 'active' : '' }}"
                    ><span><i class="fa-solid fa-users"></i></span>
                    <p>Employees</p></a
               >
               <a href="/systemrecords" class="{{ request()->is('systemrecords*') ? 'active' : '' }}"
                    ><span><i class="fa-solid fa-file"></i></span>
                    <p>System Records</p></a
               >
               <a href="/settings" class="{{ request()->is('settings*') ? 'active' : '' }}"
                    ><span><i class="fa-solid fa-user-gear"></i></span>
                    <p>Settings</p></a
               >
          </aside>
     </nav>

     <!-- Subject to tailwind -->
     <main class="size-full flex flex-col gap-5 min-h-0">
          <header class="flex justify-between">
               <div>
                    <div class="text-sm text-gray-400">

                         @switch(true)
                              @case(request()->is('dashboard*'))
                                   Dashboard
                                   @break

                              @case(request()->is('assetmanagement*'))
                                   Asset Management

                                   @if(request()->is('assetmanagement/create'))
                                        / <span>Create</span>
                                   @elseif(request()->is('assetmanagement/edit'))
                                        / <span>Edit</span>
                                   @elseif(request()->is('assetmanagement/view'))
                                        / <span>View</span>
                                   @elseif(request()->is('assetmanagement/audit'))
                                        / <span>Audit</span>
                                   @endif
                                   @break

                              @case(request()->is('employees*'))
                                   Employees

                                   @if(request()->is('employees/view'))
                                        / <span>View</span>
                                   @endif
                                   @break

                              @case(request()->is('systemrecords*'))
                                   System Records
                                   @break

                              @case(request()->is('settings*'))
                                   Settings
                                   @break
                         @endswitch

                    </div>
                    
                    <div class="font-bold">
                         @if(request()->is('dashboard*'))
                              Dashboard
                         @elseif(request()->is('assetmanagement*'))
                              Asset Management
                         @elseif(request()->is('employees*'))
                              Employees
                         @elseif(request()->is('systemrecords*'))
                              System Records
                         @elseif(request()->is('settings*'))
                              Settings
                         @endif
                    </div>
               </div>

               <div>Hi, <span class="font-semibold">Iverson</span></div>
          </header>


          @yield('content')

     </main>

     <div x-data="{ show: false, type: '', header: '', message: '' }" 
          x-init="
               @if(session('notif'))
                    // Handle reload flash notification
                    type = '{{ session('notif.type') }}';
                    header = '{{ session('notif.header') }}';
                    message = '{{ session('notif.message') }}';
                    setTimeout(() => {
                         show = true;
                         setTimeout(() => show = false, 4000);
                    }, 1000);
               @endif

               // Handle SPA/Livewire notification
               window.addEventListener('notif', (event) => {
                    console.log(event.detail); // 👈 check what you actually receive
                    let data = event.detail
                    type = data.type
                    header = data.header
                    message = data.message
                    show = true
                    setTimeout(() => {
                         setTimeout(() => show = false, 4000)
                    }, 1000)
               })
          "
          class="absolute top-10 right-10 z-50">

          <div x-show="show"
               x-transition:enter="transition transform ease-out duration-500"
               x-transition:enter-start="-translate-y-5 opacity-0"
               x-transition:enter-end="translate-y-0 opacity-100"
               x-transition:leave="transition transform ease-in duration-500"
               x-transition:leave-start="translate-y-0 opacity-100"
               x-transition:leave-end="-translate-y-5 opacity-0"
               class="notification w-auto bg-white flex flex-col px-15 py-7 whitespace-nowrap rounded-lg border-solid shadow-xl z-50">

               <div class="notif-header font-bold text-lg flex items-center relative">
                    <i x-show="type == 'success'" class="fa-regular fa-circle-check absolute -left-8 text-green-500 text-xl"></i>
                    <i x-show="type == 'failed'" class="fa-regular fa-circle-xmark absolute -left-8 text-red-500 text-xl"></i>
                    <span x-text="header"></span>
                    <i class="fa-solid fa-xmark absolute -right-8 text-gray-500 hover:text-gray-800 text-xl cursor-pointer" @click="show = false"></i>
               </div>
               
               <div class="notif-body text-md text-gray-500" x-text="message">
               </div>
          </div>
     </div>

     <script src="{{ asset('js/global.js') }}" defer></script>
     @livewireScripts
</body>
</html>
