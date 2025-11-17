<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <link rel="shortcut icon" href="{{asset('img/Fixed.ico')}}" type="image/x-icon">
     <title>Login</title>
     
     @vite(['resources/css/app.css'])
     <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">
     <main class="w-full max-w-sm bg-white p-8 rounded-md shadow">
          <div class="text-center mb-6">
               <img src="{{ asset('img/BGC.png') }}" class="w-48 mx-auto border-b pb-3 mb-3" alt="" />
               <h1 class="font-bold text-lg">FIXED ASSET SYSTEM</h1>
          </div>

          @if ($errors->any())
          <p class="text-red-500 text-center mb-3">{{ $errors->first('login') }}</p>
          @endif

          <form action="{{ route('login.post') }}" method="POST" class="space-y-3">
               @csrf

               <input name="email" type="email" placeholder="Email" value="{{ old('email') }}" required class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror" />

               <div x-data="{ show: false }" class="relative">
                    <input name="password" :type="show ? 'text' : 'password'" placeholder="Password" required class="w-full border rounded px-3 py-2 pr-10 @error('password') border-red-500 @enderror" />

                    <button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500" @click="show = !show">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                    </button>
               </div>

               <button class="w-full bg-teal-500 text-white py-2 rounded hover:bg-teal-600 cursor-pointer">Login</button>
          </form>

          <div class="mt-4 text-center text-sm text-gray-500">
               Forgot your password? Contact
               <span class="text-teal-600 font-medium">IT Department</span>.
          </div>
     </main>
</body>
</html>
