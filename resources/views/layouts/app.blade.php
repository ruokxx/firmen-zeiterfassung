<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Setting::where('key', 'page_title')->value('value') ?: config('app.name', 'Asendorf-Elektrotechnik - Zeiterfassung') }}</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            @keyframes germany-blink {
                0%, 32% { background-color: #000000; color: white; border-color: #000000; }
                33%, 65% { background-color: #DD0000; color: white; border-color: #DD0000; }
                66%, 100% { background-color: #FFCE00; color: black; border-color: #FFCE00; }
            }
            .animate-germany-blink {
                animation: germany-blink 2s infinite step-end;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-900 text-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @php
                $user = auth()->user();
            @endphp
            @if(empty($user->address) || empty($user->mobile_number))
                <div class="bg-indigo-600">
                    <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between flex-wrap">
                            <div class="w-0 flex-1 flex items-center">
                                <span class="flex p-2 rounded-lg bg-indigo-800">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <p class="ml-3 font-medium text-white truncate">
                                    <span class="md:hidden">Profil unvollständig!</span>
                                    <span class="hidden md:inline">Bitte vervollständigen Sie Ihr Profil (Adresse & Handynummer).</span>
                                </p>
                            </div>
                            <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                                <a href="{{ route('profile.edit') }}" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-indigo-600 bg-white hover:bg-indigo-50">
                                    Zum Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-gray-800 shadow border-b border-gray-700">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-grow">
                {{ $slot }}
            </main>

            <footer class="bg-gray-800 text-white py-4 text-center">
                <p class="text-sm">Copyright Sebastian Thielke 2026</p>
            </footer>
            <x-reminder-popup />
        </div>
        @stack('scripts')
    </body>
</html>
