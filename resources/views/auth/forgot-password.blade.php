<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Reset your password</h2>
        <p class="text-gray-600 mt-2">Enter your email to receive a reset link</p>
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-gray-700">
            Enter your registered email address and we will email you a password reset link.
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   placeholder="Enter your email address">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-primary hover:underline">
                <i class="fas fa-arrow-left mr-1"></i>Back to login
            </a>
            
            <button type="submit" 
                    class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
            </button>
        </div>
    </form>
</x-guest-layout>