{{-- Desktop sidebar --}}
<aside
    :class="collapsed ? 'w-16' : 'w-64'"
    class="hidden md:flex md:flex-col fixed inset-y-0 left-0 z-40 transition-all duration-300 ease-in-out
           bg-white/70 dark:bg-gray-900/70 backdrop-blur-xl border-r border-white/30 dark:border-gray-700/30"
>
    {{-- Logo --}}
    <div class="h-16 flex items-center px-4 border-b border-white/30 dark:border-gray-700/30 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden">
            <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-emerald-600 to-cyan-600 flex items-center justify-center shrink-0">
                <i class="fas fa-video text-white text-sm"></i>
            </div>
            <span x-show="!collapsed" x-cloak
                  class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-cyan-500 whitespace-nowrap">
                SummarAIze
            </span>
        </a>
    </div>

    {{-- Nav items --}}
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
        @php
            $navItems = [
                ['route' => 'dashboard', 'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard'],
                ['route' => 'meetings.record', 'icon' => 'fa-video', 'label' => 'Start Meeting'],
                ['route' => 'meetings.index', 'icon' => 'fa-file-alt', 'label' => 'My Meetings'],
                ['route' => 'exports.index', 'icon' => 'fa-file-export', 'label' => 'Exports'],
                ['route' => 'profile.edit', 'icon' => 'fa-user-circle', 'label' => 'Profile'],
            ];
        @endphp

        @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               :title="collapsed ? '{{ $item['label'] }}' : ''"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200 group
                      {{ request()->routeIs($item['route'] === 'meetings.index' ? 'meetings.*' : $item['route'])
                            ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                <i class="fas {{ $item['icon'] }} text-lg w-5 text-center shrink-0"></i>
                <span x-show="!collapsed" x-cloak class="whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
        @endforeach

        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               :title="collapsed ? 'Admin Panel' : ''"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                      {{ request()->routeIs('admin.*')
                            ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
                            : 'text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                <i class="fas fa-cogs text-lg w-5 text-center shrink-0"></i>
                <span x-show="!collapsed" x-cloak class="whitespace-nowrap">Admin Panel</span>
            </a>
        @endif
    </nav>

    {{-- Logout + collapse toggle --}}
    <div class="border-t border-white/30 dark:border-gray-700/30 p-2 space-y-1 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    :title="collapsed ? '{{ __('Log Out') }}' : ''"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                <i class="fas fa-sign-out-alt text-lg w-5 text-center shrink-0"></i>
                <span x-show="!collapsed" x-cloak class="whitespace-nowrap">{{ __('Log Out') }}</span>
            </button>
        </form>

        <button @click="toggleCollapse()"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-chevron-left text-lg w-5 text-center shrink-0 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''"></i>
            <span x-show="!collapsed" x-cloak class="whitespace-nowrap">Collapse</span>
        </button>
    </div>
</aside>

{{-- Mobile off-canvas sidebar --}}
<div x-show="mobileOpen" x-cloak class="fixed inset-0 z-50 md:hidden" style="display: none;">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="mobileOpen = false"></div>

    <aside x-show="mobileOpen"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-white dark:bg-gray-900 shadow-xl">

        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-100 dark:border-gray-700 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3" @click="mobileOpen = false">
                <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-emerald-600 to-cyan-600 flex items-center justify-center">
                    <i class="fas fa-video text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-cyan-500">
                    SummarAIze
                </span>
            </a>
            <button @click="mobileOpen = false" class="p-2 text-gray-500 dark:text-gray-400">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="flex items-center px-4 py-4 bg-emerald-50 dark:bg-gray-800 mx-3 mt-3 rounded-xl">
            <div class="h-11 w-11 rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white font-semibold text-lg mr-3 shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <div class="font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   @click="mobileOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                          {{ request()->routeIs($item['route'] === 'meetings.index' ? 'meetings.*' : $item['route'])
                                ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }}">
                    <i class="fas {{ $item['icon'] }} text-lg w-5 text-center shrink-0"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   @click="mobileOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition-all duration-200
                          {{ request()->routeIs('admin.*')
                                ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md'
                                : 'text-gray-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/20' }}">
                    <i class="fas fa-cogs text-lg w-5 text-center shrink-0"></i>
                    <span>Admin Panel</span>
                </a>
            @endif
        </nav>

        <div class="border-t border-gray-100 dark:border-gray-700 p-3 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt text-lg w-5 text-center shrink-0"></i>
                    <span>{{ __('Log Out') }}</span>
                </button>
            </form>
        </div>
    </aside>
</div>
