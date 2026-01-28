<nav class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <!-- Logo - Left Side -->
            <div class="flex items-center">
                <a href="{{route('dashboard') }}" class="flex items-center space-x-3">
                    <i class="fas fa-video text-primary text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800 dark:text-white">SummarAIze</span>
                </a>
            </div>

            <!-- User Menu - Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" 
                        class="p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-yellow-300 transition-all duration-200 relative group"
                        title="Toggle Dark Mode">
                    <i class="fas fa-moon text-xl" id="theme-icon"></i>
                    <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-800 dark:bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                        Toggle Theme
                    </span>
                </button>
                
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <!-- Trigger Button -->
                    <button @click="open = !open" 
                            class="flex items-center space-x-2 px-4 py-2 text-lg font-medium text-gray-700 dark:text-gray-200 hover:text-primary dark:hover:text-blue-400 transition-colors duration-200 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg">
                        <span>Hi, {{ Auth::user()->username }}</span>
                        <i class="fas fa-chevron-down text-sm text-gray-400" :class="{'rotate-180': open}"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black dark:ring-gray-700 ring-opacity-5 z-50"
                         style="display: none;">
                        <div class="py-2">
                            <!-- Profile -->
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-blue-400 transition-colors duration-200">
                                <i class="fas fa-user-circle mr-3 text-primary"></i>
                                <span class="font-medium">{{ __('Profile') }}</span>
                            </a>

                            <!-- Admin Panel -->
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-primary dark:hover:text-blue-400 transition-colors duration-200">
                                    <i class="fas fa-cogs mr-3 text-primary"></i>
                                    <span class="font-medium">{{ __('Admin Panel') }}</span>
                                </a>
                            @endif

                            <!-- Divider -->
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full text-left flex items-center px-4 py-3 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-700 dark:hover:text-red-300 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    <span class="font-medium">{{ __('Log Out') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button x-data="{ mobileOpen: false }" 
                        @click="mobileOpen = !mobileOpen" 
                        class="ml-4 md:hidden inline-flex items-center justify-center p-3 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition duration-200">
                    <i class="fas fa-bars text-xl" x-show="!mobileOpen"></i>
                    <i class="fas fa-times text-xl" x-show="mobileOpen" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div x-data="{ mobileOpen: false }" 
         :class="{'block': mobileOpen, 'hidden': !mobileOpen}" 
         class="hidden md:hidden bg-white dark:bg-gray-800 shadow-lg transition-colors duration-300">
        <div class="px-4 py-6 space-y-3">
            <div class="flex items-center px-3 py-4 bg-blue-50 dark:bg-gray-700 rounded-xl">
                <div class="h-12 w-12 rounded-full bg-primary flex items-center justify-center text-white font-semibold text-lg mr-4">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition duration-200">
                    <i class="fas fa-user-circle mr-3 text-primary text-lg"></i>
                    <span class="font-medium">{{ __('Profile') }}</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition duration-200">
                        <i class="fas fa-cogs mr-3 text-primary text-lg"></i>
                        <span class="font-medium">{{ __('Admin Panel') }}</span>
                    </a>
                @endif

                <div class="border-t border-gray-100 dark:border-gray-700 pt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full text-left flex items-center px-4 py-3 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-3 text-lg"></i>
                            <span class="font-medium">{{ __('Log Out') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>

<script>
    // Dark Mode Toggle with Tailwind compatibility
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        
        // Check for saved theme preference or respect OS preference
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        const currentTheme = localStorage.getItem('theme');
        
        // Function to set theme
        function setTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                themeIcon.classList.remove('text-gray-600');
                themeIcon.classList.add('text-yellow-300');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                themeIcon.classList.remove('text-yellow-300');
                themeIcon.classList.add('text-gray-600');
            }
        }
        
        // Initialize theme based on localStorage or OS preference
        if (currentTheme) {
            setTheme(currentTheme);
        } else if (prefersDarkScheme.matches) {
            setTheme('dark');
        } else {
            setTheme('light');
        }
        
        // Toggle theme on button click
        if (themeToggle && themeIcon) {
            themeToggle.addEventListener('click', function() {
                const isDark = document.documentElement.classList.contains('dark');
                setTheme(isDark ? 'light' : 'dark');
            });
        }
        
        // Listen for OS theme changes
        prefersDarkScheme.addEventListener('change', (e) => {
            // Only change if user hasn't set a preference
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    });
</script>