<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Welcome back</h2>
        <p class="text-gray-600 mt-2">Sign in to your SummarAIze account</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                   autocomplete="email"
                   placeholder="Enter your email">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                   type="password"
                   name="password"
                   required 
                   autocomplete="current-password"
                   placeholder="Enter your password">
            
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       id="remember_me" 
                       name="remember" 
                       class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>
            
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" 
                class="w-full bg-primary hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>

        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary font-medium hover:underline ml-1">
                    Create account
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>