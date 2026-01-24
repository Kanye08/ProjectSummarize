<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user-circle text-primary mr-3 text-3xl"></i>
                    <div>
                        {{ __('Profile Management') }}
                        <p class="text-sm text-gray-600 dark:text-gray-300 font-normal mt-1">
                            Manage your personal information and account settings
                        </p>
                    </div>
                </h2>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="px-5 py-2.5 bg-white dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center shadow-sm font-medium group">
                <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Overview Card -->
            <div class="mb-8">
                <div class="relative overflow-hidden rounded-2xl">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-primary/10 dark:from-blue-600/20 dark:to-primary/20"></div>
                    
                    <div class="relative bg-white dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl border border-gray-200 dark:border-gray-700 p-8">
                        <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                            <!-- Profile Photo Section -->
                            <div class="text-center">
                                <div class="relative inline-block">
                                    <div class="h-36 w-36 rounded-full border-4 border-white dark:border-gray-700 shadow-xl overflow-hidden bg-gradient-to-br from-blue-100 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                                        <img id="profileImageDisplay" 
                                             src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0ea5e9&color=fff&size=256' }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="h-full w-full object-cover">
                                    </div>
                                    
                                    <!-- Upload Button -->
                                    <label for="profile_photo" 
                                           class="absolute bottom-2 right-2 bg-primary hover:bg-blue-600 text-white p-3 rounded-full cursor-pointer shadow-lg transition-all duration-200 hover:scale-110 active:scale-95 group">
                                        <i class="fas fa-camera text-sm"></i>
                                        <input id="profile_photo" 
                                               name="profile_photo" 
                                               type="file" 
                                               class="hidden" 
                                               accept="image/*">
                                    </label>
                                </div>
                                
                                <div id="imagePreviewContainer" class="mt-4 hidden">
                                    <div class="flex flex-col items-center gap-3">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Preview</h4>
                                        <div class="h-20 w-20 rounded-full border-2 border-dashed border-primary/50 overflow-hidden">
                                            <img id="imagePreview" class="h-full w-full object-cover" alt="Preview">
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" id="cancelUpload" 
                                                    class="px-3 py-1.5 text-xs bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                                Cancel
                                            </button>
                                            <button type="submit" form="profilePhotoForm" 
                                                    class="px-3 py-1.5 text-xs bg-primary text-white rounded-lg hover:bg-blue-600 transition flex items-center">
                                                <i class="fas fa-check mr-1"></i> Apply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <form id="profilePhotoForm" method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-4">
                                    @csrf
                                    @method('PATCH')
                                </form>
                            </div>
                            
                            <!-- User Info -->
                            <div class="flex-1">
                                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h1>
                                        <p class="text-gray-600 dark:text-gray-300 mt-2 flex items-center">
                                            <i class="fas fa-envelope text-primary mr-2"></i>
                                            {{ auth()->user()->email }}
                                        </p>
                                    </div>
                                    <div class="mt-4 md:mt-0">
                                        @if(auth()->user()->is_admin)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700">
                                                <i class="fas fa-crown mr-1.5"></i> Administrator
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700">
                                                <i class="fas fa-user mr-1.5"></i> User
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="p-4 bg-blue-50 dark:bg-gray-700/50 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mr-3">
                                                <i class="fas fa-user-tag text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Username</p>
                                                <p class="font-medium text-gray-900 dark:text-white">@ {{ auth()->user()->username ?? 'Not set' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-green-50 dark:bg-gray-700/50 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center mr-3">
                                                <i class="fas fa-calendar-alt text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Member Since</p>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 bg-purple-50 dark:bg-gray-700/50 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mr-3">
                                                <i class="fas fa-clock text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Last Updated</p>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ auth()->user()->updated_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Navigation & Content -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Settings Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-1">
                            <button onclick="switchTab('personal')" 
                                    id="personal-tab" 
                                    class="w-full text-left px-4 py-3 rounded-lg mb-1 flex items-center transition-all duration-200 bg-primary text-white">
                                <i class="fas fa-user-edit mr-3"></i>
                                <span class="font-medium">Personal Information</span>
                            </button>
                            
                            <button onclick="switchTab('security')" 
                                    id="security-tab" 
                                    class="w-full text-left px-4 py-3 rounded-lg mb-1 flex items-center transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-lock mr-3"></i>
                                <span class="font-medium">Password Settings</span>
                            </button>
                            
                            <button onclick="switchTab('danger')" 
                                    id="danger-tab" 
                                    class="w-full text-left px-4 py-3 rounded-lg flex items-center transition-all duration-200 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-exclamation-triangle mr-3"></i>
                                <span class="font-medium">Delete Account</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Quick Help -->
                    <div class="mt-6 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-blue-100 dark:border-gray-700">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-3 flex items-center">
                            <i class="fas fa-question-circle text-primary mr-2"></i> Need Help?
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            Contact support if you encounter any issues with your account.
                        </p>
                        <a href="#" class="inline-flex items-center text-sm text-primary hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                            <i class="fas fa-headset mr-2"></i> Contact Support
                        </a>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3">
                    <!-- Personal Information Tab -->
                    <div id="personal-content" class="tab-content">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white mr-4">
                                    <i class="fas fa-user-edit text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Personal Information</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update your personal details and contact information</p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('profile.update') }}" class="space-y-8">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                            <i class="fas fa-user text-primary mr-2 text-sm"></i> Full Name
                                        </label>
                                        <div class="relative">
                                            <input id="name" 
                                                   name="name" 
                                                   type="text" 
                                                   value="{{ old('name', auth()->user()->name) }}" 
                                                   required 
                                                   autocomplete="name" 
                                                   class="w-full pl-10 pr-4 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user text-gray-400"></i>
                                            </div>
                                        </div>
                                        @error('name')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                            <i class="fas fa-at text-primary mr-2 text-sm"></i> Username
                                        </label>
                                        <div class="relative">
                                            <input id="username" 
                                                   name="username" 
                                                   type="text" 
                                                   value="{{ old('username', auth()->user()->username) }}" 
                                                   required 
                                                   autocomplete="username" 
                                                   class="w-full pl-10 pr-4 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-at text-gray-400"></i>
                                            </div>
                                        </div>
                                        @error('username')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                        <i class="fas fa-envelope text-primary mr-2 text-sm"></i> Email Address
                                    </label>
                                    <div class="relative">
                                        <input id="email" 
                                               name="email" 
                                               type="email" 
                                               value="{{ old('email', auth()->user()->email) }}" 
                                               required 
                                               autocomplete="email" 
                                               class="w-full pl-10 pr-4 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                    <button type="submit" 
                                            class="px-8 py-3.5 bg-gradient-to-r from-primary to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg flex items-center group">
                                        <i class="fas fa-save mr-2 group-hover:rotate-12 transition-transform"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div id="security-content" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white mr-4">
                                    <i class="fas fa-lock text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Security Settings</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Update your password and enhance account security</p>
                                </div>
                            </div>
                            
                            <form method="POST" action="{{ route('password.update') }}" class="space-y-8">
                                @csrf
                                @method('PUT')

                                <!-- Current Password -->
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                        <i class="fas fa-key text-primary mr-2 text-sm"></i> Current Password
                                    </label>
                                    <div class="relative">
                                        <input id="current_password" 
                                               name="current_password" 
                                               type="password" 
                                               autocomplete="current-password" 
                                               class="w-full pl-10 pr-12 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-key text-gray-400"></i>
                                        </div>
                                        <button type="button" 
                                                onclick="togglePasswordVisibility('current_password')" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                            <i class="fas fa-lock text-green-600 mr-2 text-sm"></i> New Password
                                        </label>
                                        <div class="relative">
                                            <input id="password" 
                                                   name="password" 
                                                   type="password" 
                                                   autocomplete="new-password" 
                                                   class="w-full pl-10 pr-12 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-gray-400"></i>
                                            </div>
                                            <button type="button" 
                                                    onclick="togglePasswordVisibility('password')" 
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                                <i class="fas fa-exclamation-circle mr-2"></i>{{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                            <i class="fas fa-lock text-green-600 mr-2 text-sm"></i> Confirm Password
                                        </label>
                                        <div class="relative">
                                            <input id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   type="password" 
                                                   autocomplete="new-password" 
                                                   class="w-full pl-10 pr-12 py-3.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-primary transition duration-200">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-gray-400"></i>
                                            </div>
                                            <button type="button" 
                                                    onclick="togglePasswordVisibility('password_confirmation')" 
                                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Requirements -->
                                <div class="p-5 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-green-100 dark:border-gray-700">
                                    <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-3 flex items-center">
                                        <i class="fas fa-shield-alt text-green-600 mr-2"></i> Password Requirements
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="flex items-center">
                                            <span id="lengthCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">At least 8 characters</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span id="uppercaseCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">One uppercase letter</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span id="lowercaseCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">One lowercase letter</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span id="numberCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">One number</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span id="specialCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">One special character</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span id="matchCheck" class="h-5 w-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mr-2">
                                                <i class="fas fa-times text-xs text-gray-400"></i>
                                            </span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Passwords match</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                                    <button type="submit" 
                                            class="px-8 py-3.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-xl transition duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg flex items-center group">
                                        <i class="fas fa-key mr-2 group-hover:rotate-12 transition-transform"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danger Zone Tab -->
                    <div id="danger-content" class="tab-content hidden">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-red-200 dark:border-red-900/50">
                            <div class="flex items-center mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white mr-4">
                                    <i class="fas fa-exclamation-triangle text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Danger Zone</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Permanent actions that cannot be undone</p>
                                </div>
                            </div>
                            
                            <div class="space-y-6">
                                <div class="p-5 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 rounded-xl border border-red-200 dark:border-red-800">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-skull-crossbones text-red-500 text-xl mt-1"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Delete Account</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                                                Once you delete your account, there is no going back. This will permanently remove:
                                            </p>
                                            <ul class="mt-3 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                                <li class="flex items-center">
                                                    <i class="fas fa-times-circle text-red-400 mr-2 text-xs"></i>
                                                    All your personal information
                                                </li>
                                                <li class="flex items-center">
                                                    <i class="fas fa-times-circle text-red-400 mr-2 text-xs"></i>
                                                    Meeting records and transcripts
                                                </li>
                                                <li class="flex items-center">
                                                    <i class="fas fa-times-circle text-red-400 mr-2 text-xs"></i>
                                                    Uploaded files and recordings
                                                </li>
                                                <li class="flex items-center">
                                                    <i class="fas fa-times-circle text-red-400 mr-2 text-xs"></i>
                                                    Account preferences and settings
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                                        <div class="text-center sm:text-left">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Ready to say goodbye?
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                This action is permanent and cannot be reversed.
                                            </p>
                                        </div>
                                        
                                        <button type="button" 
                                                onclick="showDeleteConfirmation()" 
                                                class="px-8 py-3.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg flex items-center group">
                                            <i class="fas fa-trash mr-2 group-hover:shake"></i> Delete Account
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Debug log to confirm script is loading
        console.log('Profile page scripts loaded');
        
        // Simple Tab Switching Function
        window.switchTab = function(tabName) {
            console.log('Attempting to switch to tab:', tabName);
            
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => {
                content.style.display = 'none';
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('[id$="-tab"]');
            tabs.forEach(tab => {
                tab.classList.remove('bg-primary', 'text-white');
                tab.classList.add('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            });
            
            // Show selected content
            const contentElement = document.getElementById(tabName + '-content');
            if (contentElement) {
                contentElement.style.display = 'block';
                contentElement.classList.remove('hidden');
                console.log('Tab content shown:', tabName + '-content');
            }
            
            // Activate selected tab
            const tabElement = document.getElementById(tabName + '-tab');
            if (tabElement) {
                tabElement.classList.remove('text-gray-700', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
                tabElement.classList.add('bg-primary', 'text-white');
                console.log('Tab activated:', tabName + '-tab');
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            
            // Initialize with personal tab active
            try {
                switchTab('personal');
            } catch (e) {
                console.error('Error initializing tabs:', e);
            }
            
            // Test buttons - add click event listeners as backup
            const personalTab = document.getElementById('personal-tab');
            const securityTab = document.getElementById('security-tab');
            const dangerTab = document.getElementById('danger-tab');
            
            if (personalTab) {
                personalTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    switchTab('personal');
                });
            }
            
            if (securityTab) {
                securityTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    switchTab('security');
                });
            }
            
            if (dangerTab) {
                dangerTab.addEventListener('click', function(e) {
                    e.preventDefault();
                    switchTab('danger');
                });
            }
            
            // Profile photo upload functionality
            const profilePhotoInput = document.getElementById('profile_photo');
            const imagePreview = document.getElementById('imagePreview');
            const currentProfileImage = document.getElementById('profileImageDisplay');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const cancelUploadBtn = document.getElementById('cancelUpload');
            const profilePhotoForm = document.getElementById('profilePhotoForm');

            if (profilePhotoInput) {
                profilePhotoInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    
                    if (file) {
                        // Validate file type
                        if (!file.type.match('image.*')) {
                            alert('Please select an image file.');
                            return;
                        }
                        
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('File size must be less than 2MB.');
                            return;
                        }
                        
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            if (imagePreview) imagePreview.src = e.target.result;
                            if (imagePreviewContainer) imagePreviewContainer.classList.remove('hidden');
                            if (currentProfileImage) currentProfileImage.classList.add('opacity-50');
                        }
                        
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (cancelUploadBtn) {
                cancelUploadBtn.addEventListener('click', function() {
                    if (profilePhotoInput) profilePhotoInput.value = '';
                    if (imagePreviewContainer) imagePreviewContainer.classList.add('hidden');
                    if (currentProfileImage) currentProfileImage.classList.remove('opacity-50');
                });
            }
        });

        // Toggle Password Visibility
        window.togglePasswordVisibility = function(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            const button = input.parentElement.querySelector('button');
            const icon = button ? button.querySelector('i') : null;
            
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        // Simple Delete Confirmation
        window.showDeleteConfirmation = function() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone!')) {
                document.getElementById('deleteAccountForm').submit();
            }
        }
    </script>
    @endpush
</x-app-layout>