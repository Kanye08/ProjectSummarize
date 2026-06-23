<nav class="sticky top-0 z-30 h-16 flex items-center bg-white/60 dark:bg-gray-900/60 backdrop-blur-xl border-b border-white/30 dark:border-gray-700/30 transition-colors duration-300">
    <div class="w-full px-4 sm:px-6 flex items-center justify-between">

        {{-- Mobile hamburger --}}
        <button @click="mobileOpen = true" class="md:hidden p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-bars text-xl"></i>
        </button>

        {{-- Desktop collapse toggle (mirrors sidebar's own, handy when collapsed) --}}
        <button @click="toggleCollapse()" class="hidden md:inline-flex p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <div class="flex-1"></div>

        <div class="flex items-center space-x-3">
            {{-- Dark Mode Toggle --}}
            <button id="theme-toggle"
                    class="p-2.5 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-emerald-600 dark:hover:text-yellow-300 transition-all duration-200 relative group"
                    title="Toggle Dark Mode">
                <i class="fas fa-moon text-xl" id="theme-icon"></i>
                <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-800 dark:bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                    Toggle Theme
                </span>
            </button>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open"
                        class="flex items-center space-x-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg">
                    <span>Hi, {{ Auth::user()->username }}</span>
                    <i class="fas fa-chevron-down text-sm text-gray-400" :class="{'rotate-180': open}"></i>
                </button>

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
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">
                            <i class="fas fa-user-circle mr-3 text-emerald-600"></i>
                            <span class="font-medium">{{ __('Profile') }}</span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">
                                <i class="fas fa-cogs mr-3 text-emerald-600"></i>
                                <span class="font-medium">{{ __('Admin Panel') }}</span>
                            </a>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

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
        </div>
    </div>
</nav>

<script>
    function sidebarState() {
        return {
            collapsed: false,
            mobileOpen: false,
            init() {
                this.collapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                this.$watch('collapsed', val => localStorage.setItem('sidebar-collapsed', val));
            },
            toggleCollapse() {
                this.collapsed = !this.collapsed;
            },
        };
    }

    // Dark Mode Toggle with Tailwind compatibility
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        const currentTheme = localStorage.getItem('theme');

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

        if (currentTheme) {
            setTheme(currentTheme);
        } else if (prefersDarkScheme.matches) {
            setTheme('dark');
        } else {
            setTheme('light');
        }

        if (themeToggle && themeIcon) {
            themeToggle.addEventListener('click', function() {
                const isDark = document.documentElement.classList.contains('dark');
                setTheme(isDark ? 'light' : 'dark');
            });
        }

        prefersDarkScheme.addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    });
</script>
