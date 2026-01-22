<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-users-cog text-primary mr-3"></i>{{ __('Manage Users') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2 text-base">All registered users</p>
            </div>
            <div class="px-5 py-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                    Showing {{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }} users
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

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 dark:text-red-400 mr-3"></i>
                        <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-users text-primary mr-2"></i>User Management
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Meetings</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800/30 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">Joined {{ $user->created_at->diffForHumans() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-300">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-300">@ {{ $user->username }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->is_admin)
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700">
                                                    <i class="fas fa-crown mr-1"></i>Admin
                                                </span>
                                            @else
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                                    <i class="fas fa-user mr-1"></i>User
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $user->meetings_count }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">meetings</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="text-primary dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mr-4 inline-flex items-center transition-colors">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <form action="{{ route('admin.users.delete', $user) }}" 
                                                      method="POST" 
                                                      class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 inline-flex items-center transition-colors">
                                                        <i class="fas fa-trash mr-1"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>