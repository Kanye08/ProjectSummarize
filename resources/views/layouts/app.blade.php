<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SummarAIze') }}</title>
        
        <!-- Fonts -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: '#059669',
                            secondary: '#0d9488',
                            accent: '#22d3ee',
                        }
                    }
                }
            }
        </script>
        <style>
            * {
                font-family: 'Figtree', sans-serif;
            }

            [x-cloak] { display: none !important; }
            
            .gradient-bg {
                background: linear-gradient(135deg, #059669 0%, #0d9488 50%, #22d3ee 100%);
            }

            .gradient-bg-dark {
                background: linear-gradient(135deg, #065f46 0%, #115e59 50%, #0e7490 100%);
            }

            .text-primary {
                color: #059669;
            }

            .bg-primary {
                background-color: #059669;
            }

            .hover\:bg-primary:hover {
                background-color: #047857;
            }

            .border-primary {
                border-color: #059669;
            }

            .bg-secondary {
                background-color: #0d9488;
            }

            .bg-accent {
                background-color: #22d3ee;
            }
            
            /* Smooth transitions for theme switching */
            body, header, main, .admin-card, .admin-stat-card {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }
            
            /* Custom scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
            
            /* Custom scrollbar for dark mode */
            .dark .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(31, 41, 55, 0.5);
            }
            
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(5, 150, 105, 0.5);
            }

            .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(5, 150, 105, 0.7);
            }

            /* Pagination dark mode */
            .dark .pagination a {
                background-color: rgba(31, 41, 55, 0.8);
                color: #d1d5db;
                border-color: rgba(75, 85, 99, 0.5);
            }

            .dark .pagination a:hover {
                background-color: rgba(5, 150, 105, 0.2);
                color: #34d399;
            }

            .dark .pagination .active span {
                background-color: rgba(5, 150, 105, 0.3);
                color: #34d399;
            }
        </style>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-900">
        <div x-data="sidebarState()" x-init="init()" class="min-h-screen flex">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out" :class="collapsed ? 'md:ml-16' : 'md:ml-64'">
                @include('layouts.topbar')

                <!-- Page Heading -->
                @isset($header)
                    <header class="header-separator">
                        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                            <div class="bg-white dark:bg-gray-800 card-elevation rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                                {{ $header }}
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-grow py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Minimal Dashboard Footer -->
                <footer class="mt-auto bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <!-- Left Section -->
                        <div class="flex items-center space-x-6">
                            <!-- Logo/Icon -->
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-emerald-600 to-cyan-600 flex items-center justify-center">
                                    <i class="fas fa-video text-white text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Powered by Tritek Academy</span>
                            </div>
                            
                            <!-- Separator -->
                            <div class="hidden md:block h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
                            
                            <!-- Copyright -->
                            <div class="hidden md:block text-sm text-gray-500 dark:text-gray-400">
                                © {{ date('Y') }} Project SummarAIze
                            </div>
                        </div>
                        
                        <!-- Center Section - Status -->
                        <div class="hidden lg:flex items-center space-x-3">
                            <div class="flex items-center space-x-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                                <span class="text-xs font-medium text-green-700 dark:text-green-400">All systems normal</span>
                                <span class="text-xs text-green-600 dark:text-green-500">• Updated now</span>
                            </div>
                        </div>
                        
                        <!-- Right Section -->
                        <div class="flex items-center space-x-4">
                            <!-- Quick Links -->
                            <!-- <div class="hidden md:flex items-center space-x-4">
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-life-ring mr-1"></i> Support
                                </a>
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-shield-alt mr-1"></i> Privacy
                                </a>
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400 transition-colors">
                                    <i class="fas fa-file-contract mr-1"></i> Terms
                                </a>
                            </div> -->
                            
                            <!-- Version Badge -->
                            <div class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-gray-600 dark:text-gray-400">
                                v1.0.0
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile View - Stacked -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 lg:hidden">
                        <div class="flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
                            <!-- Status for mobile -->
                            <div class="flex items-center space-x-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-300">All systems normal</span>
                            </div>
                            
                            <!-- Copyright for mobile -->
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                © {{ date('Y') }} SummarAIze
                            </div>
                            
                            <!-- Quick Links for mobile -->
                            <!-- <div class="flex items-center space-x-3">
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400">
                                    <i class="fas fa-life-ring"></i>
                                </a>
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400">
                                    <i class="fas fa-shield-alt"></i>
                                </a>
                                <a href="#" class="text-xs text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-blue-400">
                                    <i class="fas fa-file-contract"></i>
                                </a>
                            </div> -->
                        </div>
                    </div>
                </div>
                </footer>
            </div>
        </div>

        <script>
            // Check for saved theme preference or default to light
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @stack('scripts')
    </body>
</html>