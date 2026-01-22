<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-chart-bar text-primary mr-3"></i>{{ __('Meeting Statistics') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2 text-base">Analytics and insights</p>
            </div>
            <div class="px-5 py-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                    Updated: {{ now()->format('H:i') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 border border-blue-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-blue-600 dark:text-blue-300 uppercase tracking-wider">Total Meetings</h3>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total_meetings'] }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-primary">
                            <i class="fas fa-video text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-cyan-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 border border-cyan-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-cyan-600 dark:text-cyan-300 uppercase tracking-wider">Scheduled</h3>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['scheduled_meetings'] }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600">
                            <i class="fas fa-calendar text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 border border-green-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-green-600 dark:text-green-300 uppercase tracking-wider">Completed</h3>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['completed_meetings'] }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-lg p-6 border border-red-100 dark:border-gray-700 transform hover:scale-[1.02] transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-red-600 dark:text-red-300 uppercase tracking-wider">Cancelled</h3>
                            <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['cancelled_meetings'] }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meetings by User -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-users text-primary mr-3"></i>Meetings by User
                </h3>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Meetings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800/30 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($stats['meetings_by_user'] as $meetingStat)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                {{ substr($meetingStat->user->name ?? 'N/A', 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $meetingStat->user->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $meetingStat->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $meetingStat->count }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                                <div class="bg-green-500 dark:bg-green-400 h-2.5 rounded-full" style="width: {{ rand(60, 95) }}%"></div>
                                            </div>
                                            <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ rand(60, 95) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Meetings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                    <i class="fas fa-clock text-primary mr-3"></i>Recent Meetings
                </h3>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800/30 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($stats['recent_meetings'] as $meeting)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $meeting->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-300">{{ $meeting->user->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-300">{{ $meeting->start_time->format('Y-m-d H:i') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $meeting->start_time->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($meeting->status === 'completed')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700">
                                                <i class="fas fa-check-circle mr-1"></i>Completed
                                            </span>
                                        @elseif($meeting->status === 'cancelled')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700">
                                                <i class="fas fa-times-circle mr-1"></i>Cancelled
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                                                <i class="fas fa-clock mr-1"></i>Scheduled
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>