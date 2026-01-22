<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark-mode">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SummarAIze') }}</title>
        
        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
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
                            primary: '#0ea5e9',
                            secondary: '#3b82f6',
                            accent: '#06b6d4',
                        }
                    }
                }
            }
        </script>
            <style>
                    * {
                        font-family: 'Poppins', sans-serif;
                    }
                    
                    .gradient-bg {
                        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
                    }
                    
                    .gradient-bg-dark {
                        background: linear-gradient(135deg, #0369a1 0%, #1d4ed8 100%);
                    }
                    
                    .text-primary {
                        color: #0ea5e9;
                    }
                    
                    .bg-primary {
                        background-color: #0ea5e9;
                    }
                    
                    .hover\:bg-primary:hover {
                        background-color: #0284c7;
                    }
                    
                    .border-primary {
                        border-color: #0ea5e9;
                    }
                    
                    .bg-secondary {
                        background-color: #3b82f6;
                    }
                    
                    .bg-accent {
                        background-color: #06b6d4;
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
                        background: rgba(59, 130, 246, 0.5);
                    }
                    
                    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                        background: rgba(59, 130, 246, 0.7);
                    }
                    
                    /* Pagination dark mode */
                    .dark .pagination a {
                        background-color: rgba(31, 41, 55, 0.8);
                        color: #d1d5db;
                        border-color: rgba(75, 85, 99, 0.5);
                    }
                    
                    .dark .pagination a:hover {
                        background-color: rgba(59, 130, 246, 0.2);
                        color: #60a5fa;
                    }
                    
                    .dark .pagination .active span {
                        background-color: rgba(59, 130, 246, 0.3);
                        color: #60a5fa;
                    }
            </style>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

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
        </div>
        
        <script>
            // Check for saved theme preference or default to light
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </body>
</html>