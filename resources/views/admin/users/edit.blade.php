<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-user-edit text-primary mr-3"></i>{{ __('Edit User') }}
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mt-2 text-base">Update user information</p>
            </div>
            <a href="{{ route('admin.users') }}" class="px-5 py-2.5 bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center shadow-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back to Users
            </a>
        </div>
    </x-slot>

    <div>
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                        <input id="name" 
                               class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                               type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               placeholder="Enter full name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 dark:text-gray-400">@</span>
                            <input id="username" 
                                   class="w-full pl-10 px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                                   type="text" 
                                   name="username" 
                                   value="{{ old('username', $user->username) }}" 
                                   required 
                                   placeholder="username">
                        </div>
                        @error('username')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                        <input id="email" 
                               class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               placeholder="user@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-8">
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700">
                            <input type="checkbox" 
                                   id="is_admin" 
                                   name="is_admin" 
                                   value="1" 
                                   {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} 
                                   class="h-5 w-5 text-primary focus:ring-primary border-gray-300 dark:border-gray-600 rounded">
                            <label for="is_admin" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700 mr-2">
                                    <i class="fas fa-crown mr-1"></i>Admin
                                </span>
                                Grant administrator privileges
                            </label>
                        </div>
                        @error('is_admin')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.users') }}" 
                           class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-primary hover:bg-blue-600 text-white font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>