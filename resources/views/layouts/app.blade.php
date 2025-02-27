<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js"></script>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Styles -->
        <style>[x-cloak] { display: none !important; }</style>
        @stack('scripts')
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col">
        <div class="flex-grow bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 shadow py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <span class="text-sm text-gray-600 dark:text-gray-300 flex items-center justify-center gap-2">
                    © {{ date('Y') }} PT. Internet Pratama Indonesia. All rights reserved. Developed by 
                    <a href="https://fahrezifauzan.vercel.app/" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1">    
                        <img src="{{ asset('images/frz_sign.png') }}" alt="FRZ Logo" class="h-4 w-auto"> 
                    </a>
                </span>
            </div>
        </footer>
    </body>
</html>
