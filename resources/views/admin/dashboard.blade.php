<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center mb-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center mr-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ __('Admin Dashboard') }}
                        </h2>
                        <p class="mt-1 flex items-center">
                            <i class="fas fa-user-shield text-primary mr-2"></i>
                            <span class="text-gray-600 dark:text-gray-300">
                                Welcome back,
                            </span>
                            <span class="font-semibold ml-1 text-gray-900 dark:text-white">
                                {{ Auth::user()->name }}
                            </span>
                        </p>

                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="px-4 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ now()->format('F j, Y') }}</p>
                    </div>
                </div>
                
                <div class="px-4 py-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                        <i class="fas fa-circle text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <p class="font-semibold text-green-700 dark:text-green-400">System Online</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl p-6 border border-blue-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wider">Total Users</p>
                            <p class="text-4xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['total_users'] }}</p>
                            <div class="flex items-center mt-4">
                                <i class="fas fa-arrow-up text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">+12% from last month</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-cyan-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl p-6 border border-cyan-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-cyan-600 dark:text-cyan-300 uppercase tracking-wider">Total Meetings</p>
                            <p class="text-4xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['total_meetings'] }}</p>
                            <div class="flex items-center mt-4">
                                <i class="fas fa-arrow-up text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">+24% from last month</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-600 text-white">
                            <i class="fas fa-video text-2xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-indigo-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl p-6 border border-indigo-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-300 uppercase tracking-wider">Active Users</p>
                            <p class="text-4xl font-bold text-gray-800 dark:text-white mt-2">{{ $stats['active_users'] }}</p>
                            <div class="flex items-center mt-4">
                                <i class="fas fa-arrow-up text-green-500 mr-2"></i>
                                <span class="text-sm text-gray-600 dark:text-gray-300">+8% from last month</span>
                            </div>
                        </div>
                        <div class="p-4 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="mb-10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-bolt text-primary mr-3"></i>Quick Actions
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Access frequently used features
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('admin.users') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start mb-4">
                            <div class="p-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white mr-4 group-hover:shadow-lg transition-shadow">
                                <i class="fas fa-user-cog text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Manage Users</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">View and manage all user accounts</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Active now: {{ rand(5, 20) }}</span>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.activities') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start mb-4">
                            <div class="p-3 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white mr-4 group-hover:shadow-lg transition-shadow">
                                <i class="fas fa-history text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Activity Logs</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Monitor real-time user activities</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ rand(50, 200) }} logs today</span>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.meetings.stats') }}" 
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl p-6 border border-gray-200 dark:border-gray-700 hover:border-primary transition-all duration-300 transform hover:-translate-y-1">
                        <div class="flex items-start mb-4">
                            <div class="p-3 rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white mr-4 group-hover:shadow-lg transition-shadow">
                                <i class="fas fa-chart-bar text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white group-hover:text-primary">Analytics</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Detailed meeting statistics & insights</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ rand(10, 50) }} meetings today</span>
                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-primary transform group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activities & System Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activities -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                                <i class="fas fa-stream text-primary mr-3"></i>Recent Activities
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Latest user actions in the system</p>
                        </div>
                        <a href="{{ route('admin.activities') }}" class="text-sm text-primary hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            View All
                        </a>
                    </div>
                    
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($stats['recent_activities'] as $activity)
                            <div class="flex items-start p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold mr-4">
                                    {{ substr($activity->user->name ?? 'N/A', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity->user->name ?? 'N/A' }}
                                        </p>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $activity->action }}</p>
                                    @if($activity->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">{{ $activity->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="p-4 rounded-full bg-blue-50 dark:bg-blue-900/30 inline-block mb-4">
                                    <i class="fas fa-inbox text-3xl text-blue-500"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Activities will appear here as they happen</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- System Overview -->
                <div class="space-y-6">
                    <!-- System Health -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center mb-6">
                            <i class="fas fa-heartbeat text-primary mr-3"></i>System Health
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Server Uptime</span>
                                    <span class="text-sm font-bold text-green-600 dark:text-green-400">99.8%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 99.8%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Storage Usage</span>
                                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">65%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">API Response Time</span>
                                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">142ms</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl shadow-lg p-6 text-white">
                        <h3 class="text-xl font-bold mb-4">Today's Snapshot</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-white/15 rounded-xl hover:bg-white/25 transition-all duration-200 hover:scale-[1.02]">
                                <div class="text-2xl font-bold mb-1">{{ rand(5, 20) }}</div>
                                <div class="text-sm font-medium opacity-90">New Users</div>
                            </div>
                            <div class="text-center p-4 bg-white/15 rounded-xl hover:bg-white/25 transition-all duration-200 hover:scale-[1.02]">
                                <div class="text-2xl font-bold mb-1">{{ rand(10, 50) }}</div>
                                <div class="text-sm font-medium opacity-90">Meetings</div>
                            </div>
                            <div class="text-center p-4 bg-white/15 rounded-xl hover:bg-white/25 transition-all duration-200 hover:scale-[1.02]">
                                <div class="text-2xl font-bold mb-1">{{ rand(100, 500) }}</div>
                                <div class="text-sm font-medium opacity-90">Transcriptions</div>
                            </div>
                            <div class="text-center p-4 bg-white/15 rounded-xl hover:bg-white/25 transition-all duration-200 hover:scale-[1.02]">
                                <div class="text-2xl font-bold mb-1">{{ rand(50, 200) }}</div>
                                <div class="text-sm font-medium opacity-90">Exports</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>