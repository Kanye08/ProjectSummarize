<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-history text-primary mr-3"></i>{{ __('User Activities') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2 text-base">Monitor all user activities</p>
            </div>
            <div class="px-5 py-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                    Total: {{ $activities->total() }} records
                </span>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 dark:text-green-400 mr-3"></i>
                        <p class="text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-history text-primary mr-2"></i>Activity Log
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800/30 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($activities as $activity)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                    {{ substr($activity->user->name ?? 'N/A', 0, 1) }}
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->user->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                                                {{ $activity->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-300 max-w-xs truncate">{{ $activity->description }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $activity->ip_address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-300">{{ $activity->created_at->format('Y-m-d H:i:s') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>