<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FIXED Asset</title>

    @livewireStyles
    @vite(['resources/css/app.css'])
    <link rel="shortcut icon" href="{{ asset('img/Fixed.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
</head>
<body>

    {{-- Backdrop: visible on mobile when sidebar is open, tap to close --}}
    <div id="nav-backdrop" onclick="closeMobileNav()"></div>

    <nav id="main-nav">
        <div class="logo">
            <p>
                <img src="{{ asset('img/Fixed.png') }}" width="24" alt="">
                <span>FIXED ASSET</span>
            </p>
            <button id="toggle-btn" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>

        <aside class="sidebar">
            <a href="/dashboard"
               data-tooltip="Dashboard"
               class="{{ request()->is('dashboard*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-house-chimney"></i></span>
                <p>Dashboard</p>
            </a>
            <a href="/assetmanagement"
               data-tooltip="Asset Management"
               class="{{ request()->is('assetmanagement*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-boxes-stacked"></i></span>
                <p>Asset Management</p>
            </a>
            <a href="/employees"
               data-tooltip="Employees"
               class="{{ request()->is('employees*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-users"></i></span>
                <p>Employees</p>
            </a>
            <a href="/systemrecords"
               data-tooltip="System Records"
               class="{{ request()->is('systemrecords*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-file"></i></span>
                <p>System Records</p>
            </a>
            <a href="/settings"
               data-tooltip="Settings"
               class="{{ request()->is('settings*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-user-gear"></i></span>
                <p>Settings</p>
            </a>
        </aside>
    </nav>

    <main>
        <header>
            {{-- Burger: only rendered and visible on mobile --}}
            <button class="mobile-menu-btn" onclick="openMobileNav()" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="page-info">
                <div class="breadcrumb">
                    @switch(true)
                        @case(request()->is('dashboard*'))
                            Dashboard
                            @break
                        @case(request()->is('assetmanagement*'))
                            Asset Management
                            @if(request()->is('assetmanagement/create'))
                                &rsaquo; <span>Create</span>
                            @elseif(request()->is('assetmanagement/edit'))
                                &rsaquo; <span>Edit</span>
                            @elseif(request()->is('assetmanagement/view'))
                                &rsaquo; <span>View</span>
                            @elseif(request()->is('assetmanagement/audit'))
                                &rsaquo; <span>Audit</span>
                            @endif
                            @break
                        @case(request()->is('employees*'))
                            Employees
                            @if(request()->is('employees/view'))
                                &rsaquo; <span>View</span>
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

                <div class="page-title">
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

            <div class="user-info">
                Hi, <span>{{ Auth::user()->name }}</span>
            </div>
        </header>

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    {{-- Notification toast --}}
    <div x-data="{ show: false, type: '', header: '', message: '' }"
         x-init="
             @if(session('notif'))
                 type    = '{{ session('notif.type') }}';
                 header  = '{{ session('notif.header') }}';
                 message = '{{ session('notif.message') }}';
                 setTimeout(() => {
                     show = true;
                     setTimeout(() => show = false, 4000);
                 }, 600);
             @endif

             window.addEventListener('notif', (event) => {
                 let data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                 type    = data.type;
                 header  = data.header;
                 message = data.message;
                 show    = true;
                 setTimeout(() => show = false, 4000);
             });
         "
         style="position: fixed; top: 20px; right: 20px; z-index: 50;">

        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="notification">

            <div class="notif-header">
                <i x-show="type === 'success'"
                   class="fa-regular fa-circle-check"
                   style="color: #48bb78; font-size: 16px;"></i>
                <i x-show="type === 'failed'"
                   class="fa-regular fa-circle-xmark"
                   style="color: #fc8181; font-size: 16px;"></i>
                <span x-text="header"></span>
                <i class="fa-solid fa-xmark"
                   style="margin-left: auto; color: #a0aec0; cursor: pointer; font-size: 14px;"
                   @click="show = false"></i>
            </div>

            <div class="notif-body" x-text="message"></div>
        </div>
    </div>

    <script src="{{ asset('js/global.js') }}" defer></script>
    @livewireScripts

</body>
</html>