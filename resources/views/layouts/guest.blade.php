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
        
        <style>
            * {
                font-family: 'Figtree', sans-serif;
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
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
            
            .focus\:ring-primary:focus {
                --tw-ring-color: rgba(14, 165, 233, 0.5);
            }
        </style>
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-10 sm:pt-0 bg-gradient-to-br from-blue-50 to-gray-100">
            <div class="mb-8 mt-4 text-center">
                <a href="/" class="flex items-center justify-center space-x-2">
                    <i class="fas fa-video text-primary text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">SummarAIze</span>
                </a>
                <p class="text-gray-600 mt-2">Smart Meeting Platform</p>
            </div>

            <div class="w-full sm:max-w-xl px-6 py-8 bg-white shadow-xl rounded-2xl">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} SummarAIze. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
