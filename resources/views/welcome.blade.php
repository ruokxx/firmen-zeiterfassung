<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::where('key', 'page_title')->value('value') ?: 'Asendorf-Elektrotechnik - Zeiterfassung' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes redWhiteAnim {
            0% { color: #ef4444; } /* red-500 */
            50% { color: white; }
            100% { color: #ef4444; } /* red-500 */
        }
        .animated-text {
            animation: redWhiteAnim 3s infinite;
            font-weight: bold;
            font-size: 1.5rem; /* text-2xl */
            margin-bottom: 2rem;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="antialiased font-sans bg-gray-50 text-gray-900" 
      x-data="{ 
          loginOpen: {{ $errors->has('email') ? 'true' : 'false' }}, 
          registerOpen: {{ $errors->has('first_name') || $errors->has('last_name') ? 'true' : 'false' }},
          successOpen: {{ session('status') === 'verification-pending' ? 'true' : 'false' }}
      }">

    <!-- Success Modal -->
    <div x-show="successOpen" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative text-center" @click.away="successOpen = false">
            <button @click="successOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Registrierung erfolgreich</h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    Ihr Account wurde erstellt und wird nun von der Firma geprüft. 
                    Sie erhalten eine E-Mail, sobald Ihr Account freigeschaltet wurde.
                </p>
            </div>
            
            <div class="mt-4">
                <button @click="successOpen = false" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                    Verstanden
                </button>
            </div>
        </div>
    </div>

    <div class="min-h-screen flex flex-col justify-center items-center relative bg-gray-900 px-4">
        
        <div class="animated-text">
            {{ \App\Models\Setting::where('key', 'page_title')->value('value') ?: 'Asendorf-Elektrotechnik - Zeiterfassung' }}
        </div>

        {{-- Custom Logo --}}
        <div class="mb-6">
            <img src="https://asendorf-elektrotechnik.de/wp-content/uploads/2024/11/Assendorf-Logo-CMYK_PDF-2048x716.jpg" alt="Asendorf-Elektrotechnik Logo" class="w-48 h-auto object-contain bg-white rounded-lg p-3 shadow-lg">
        </div>

        {{-- Login Box --}}
        <div class="z-10 w-full max-w-[340px] bg-gray-800 rounded-xl shadow-2xl p-6 border border-gray-700">
            <h2 class="text-xl font-light text-center text-white mb-5">Anmelden</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email Address -->
                <div class="mb-3">
                    <label for="login_email" class="block font-medium text-xs text-orange-500 mb-1">Email</label>
                    <input id="login_email" class="block w-full bg-gray-700 border-gray-600 rounded-lg shadow-sm text-white placeholder-gray-500 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 transition text-sm py-2" type="email" name="email" value="{{ old('email') }}" required autofocus />
                    @if($errors->has('email'))
                        <p class="text-xs text-red-400 mt-1">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="login_password" class="block font-medium text-xs text-orange-500 mb-1">Passwort</label>
                    <input id="login_password" class="block w-full bg-gray-700 border-gray-600 rounded-lg shadow-sm text-white placeholder-gray-500 focus:border-orange-500 focus:ring focus:ring-orange-500 focus:ring-opacity-50 transition text-sm py-2" type="password" name="password" required autocomplete="current-password" />
                    @if($errors->has('password'))
                        <p class="text-xs text-red-400 mt-1">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="block mb-4">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-600 bg-gray-700 text-orange-500 shadow-sm focus:ring-orange-500 focus:ring-offset-gray-800" name="remember">
                        <span class="ml-2 text-xs text-gray-300">Angemeldet bleiben</span>
                    </label>
                </div>

                <div class="flex flex-col gap-3">
                    <button class="w-full py-2 bg-orange-600 text-white font-semibold rounded-lg shadow-md hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition duration-150 ease-in-out text-sm">
                        Login
                    </button>
                    
                    @if (Route::has('password.request'))
                        <a class="text-xs text-gray-400 hover:text-orange-400 text-center transition" href="{{ route('password.request') }}">
                            Passwort vergessen?
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Register Trigger --}}
        <div class="mt-6">
            <button @click="registerOpen = true" class="text-gray-400 hover:text-white transition duration-150 ease-in-out text-sm">
                Noch keinen Account? <span class="underline decoration-orange-500 underline-offset-4 decoration-2 text-gray-200">Hier registrieren</span>
            </button>
        </div>

        {{-- Footer --}}
        <div class="absolute bottom-4 text-center text-xs text-gray-600">
            &copy; Sebastian Thielke 2026
        </div>
    </div>

    {{-- Login Modal --}}
    <div x-show="loginOpen" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative" @click.away="loginOpen = false">
            <button @click="loginOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <h3 class="text-2xl font-bold mb-6 text-center text-gray-800">Anmelden</h3>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Email Address -->
                <div class="mb-4">
                    <label for="login_email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="login_email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="email" name="email" value="{{ old('email') }}" required autofocus />
                    @if($errors->has('email'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="login_password" class="block font-medium text-sm text-gray-700">Passwort</label>
                    <input id="login_password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="password" name="password" required autocomplete="current-password" />
                    @if($errors->has('password'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="block mb-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Angemeldet bleiben</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 mr-4" href="{{ route('password.request') }}">
                            Passwort vergessen?
                        </a>
                    @endif

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 transition">
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Register Modal --}}
    <div x-show="registerOpen" style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm px-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative" @click.away="registerOpen = false">
            <button @click="registerOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <h3 class="text-2xl font-bold mb-6 text-center text-gray-800">Registrieren</h3>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <!-- First Name -->
                <div class="mb-4">
                    <label for="first_name" class="block font-medium text-sm text-gray-700">Vorname</label>
                    <input id="first_name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus />
                    @if($errors->has('first_name'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('first_name') }}</p>
                    @endif
                </div>

                <!-- Last Name -->
                <div class="mb-4">
                    <label for="last_name" class="block font-medium text-sm text-gray-700">Nachname</label>
                    <input id="last_name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="text" name="last_name" value="{{ old('last_name') }}" required />
                    @if($errors->has('last_name'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('last_name') }}</p>
                    @endif
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="register_email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="register_email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="email" name="email" value="{{ old('email') }}" required />
                    @if($errors->has('email'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="register_password" class="block font-medium text-sm text-gray-700">Passwort</label>
                    <input id="register_password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="password" name="password" required autocomplete="new-password" />
                    @if($errors->has('password'))
                        <p class="text-sm text-red-600 mt-2">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Passwort bestätigen</label>
                    <input id="password_confirmation" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" type="password" name="password_confirmation" required />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 transition">
                        Registrieren
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
