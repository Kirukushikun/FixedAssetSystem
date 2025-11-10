
<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title>Document</title>

     @livewireStyles
     @vite(['resources/css/app.css'])
     <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
     
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="preconnect" href="https://fonts.googleapis.com" />
     <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
     <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
     <link rel="stylesheet" href="{{asset('css/global.css')}}" />
</head>
<body>
     <nav>
          <div class="logo">
               <p class="font-bold">FIXED ASSET</p>
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
               <a href="/employees.html"
                    ><span><i class="fa-solid fa-users"></i></span>
                    <p>Employees</p></a
               >
               <a href="/system-records.html"
                    ><span><i class="fa-solid fa-file"></i></span>
                    <p>System Records</p></a
               >
               <a href="settings.html"
                    ><span><i class="fa-solid fa-user-gear"></i></span>
                    <p>Settings</p></a
               >
          </aside>
     </nav>

     <!-- Subject to tailwind -->
     <main class="size-full flex flex-col">
          <header class="flex justify-between">
               <div>
                    <div class="text-sm text-gray-400">Pages / <span>Header</span></div>
                    
                    <div class="font-bold">
                         @if(request()->is('dashboard*'))
                              Dashboard
                         @elseif(request()->is('assetmanagement*'))
                              Asset Management
                         @endif
                    </div>
               </div>

               <div>Hi, <span class="font-semibold">Iverson</span></div>
          </header>

          <br />

          @yield('content')

     </main>
     <script src="{{ asset('js/global.js') }}" defer></script>
     @livewireScripts
</body>
</html>
