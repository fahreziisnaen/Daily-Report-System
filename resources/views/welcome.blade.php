<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Daily Report') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gradient-to-br from-indigo-100 via-white to-indigo-50">
            <!-- Navigation -->
            <div class="absolute top-0 right-0 p-6 text-right">
                    @auth
                    <a href="{{ route('dashboard') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Masuk</a>
                    @endauth
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <!-- Logo or Icon -->
                    <div class="mb-8">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mx-auto h-24 w-auto">
                    </div>

                    <!-- Welcome Message -->
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl mb-4">
                        Daily Report Application
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Hi Project Engineering Team, selamat datang di platform laporan pekerjaan kita! 🚀
                    </p>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="text-indigo-600 mb-4">
                                <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold mb-2">Laporan Mudah</h3>
                            <p class="text-gray-600">Buat dan kelola laporan pekerjaan dengan mudah dan efisien</p>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="text-indigo-600 mb-4">
                                <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold mb-2">Real-time Tracking</h3>
                            <p class="text-gray-600">Pantau progress pekerjaan secara real-time dengan mudah</p>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <div class="text-indigo-600 mb-4">
                                <svg class="h-12 w-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold mb-2">Daily Reminder</h3>
                            <p class="text-gray-600">Dapatkan notifikasi untuk memastikan laporan terisi tepat waktu</p>
                        </div>
                    </div>

                    <!-- Motivational Message -->
                    <div class="mt-12">
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white p-8 rounded-lg shadow-lg transform hover:scale-[1.02] transition-transform">
                            <div class="relative">
                                <!-- Decorative Elements -->
                                <div class="absolute -top-6 -left-6">
                                    <svg class="w-12 h-12 text-indigo-400 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0L1.5 6v12L12 24l10.5-6V6L12 0zm0 2.8l8.4 4.8L12 12.4l-8.4-4.8L12 2.8z"/>
                                    </svg>
                                </div>
                                <div class="absolute -bottom-6 -right-6 transform rotate-180">
                                    <svg class="w-12 h-12 text-blue-400 opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0L1.5 6v12L12 24l10.5-6V6L12 0zm0 2.8l8.4 4.8L12 12.4l-8.4-4.8L12 2.8z"/>
                                    </svg>
                                </div>

                                <!-- Main Content -->
                                <div class="text-center space-y-6">
                                    <h2 class="text-3xl font-bold mb-4 leading-tight">
                                        <span class="block transform hover:scale-105 transition-transform">
                                            🌟 Teamwork Makes Dream Work 🌟
                                </span>
                                    </h2>
                                    
                                    <div class="space-y-4 text-lg">
                                        <p class="text-indigo-100">
                                            Setiap laporan yang kita buat adalah langkah menuju kesempurnaan project.
                                            <span class="block mt-2 font-semibold text-yellow-300">
                                                "Dokumentasi Rapi, Project Terkawal, Hasil Optimal!"
                                    </span>
                                        </p>
                                        
                                        <div class="flex justify-center space-x-2 text-xl">
                                            <span>🔧</span>
                                            <span>📊</span>
                                            <span>💪</span>
                                            <span>🎯</span>
                                            <span>🚀</span>
                                        </div>
                                    </div>

                                    @guest
                                        <div class="mt-8">
                                            <a href="{{ route('login') }}" 
                                                class="inline-flex items-center px-8 py-4 border-2 border-white text-lg font-bold rounded-full text-indigo-600 bg-white hover:bg-indigo-50 hover:scale-105 transform transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white">
                                                Masuk Sekarang
                                                <svg class="ml-2 -mr-1 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </a>
                                        </div>
                                    @endguest

                                    <!-- Animated Tags -->
                                    <div class="flex flex-wrap justify-center gap-2 mt-6 text-sm">
                                        <span class="px-3 py-1 bg-white/20 rounded-full">#TeamWork</span>
                                        <span class="px-3 py-1 bg-white/20 rounded-full">#Engineering</span>
                                        <span class="px-3 py-1 bg-white/20 rounded-full">#Innovation</span>
                                        <span class="px-3 py-1 bg-white/20 rounded-full">#Excellence</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="absolute bottom-0 w-full bg-white/80 backdrop-blur-sm py-4 shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <span class="text-sm text-gray-600 dark:text-gray-300 flex items-center justify-center gap-2">
                        © {{ date('Y') }} PT. Internet Pratama Indonesia. All rights reserved. Developed by 
                        <a href="https://fahrezifauzan.vercel.app/" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1">    
                            <img src="{{ asset('images/frz_sign.png') }}" alt="FRZ Logo" class="h-4 w-auto"> 
                        </a>
                    </span>
                </div>
            </footer>
        </div>
    </body>
</html>
