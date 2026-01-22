<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Set New Password</h2>
        <p class="text-gray-600 mt-2">Create a new password for your account</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                   type="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}" 
                   required 
                   autofocus
                   placeholder="Enter your email address">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input id="password" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                   type="password"
                   name="password"
                   required 
                   autocomplete="new-password"
                   placeholder="Enter new password">
            
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input id="password_confirmation" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200"
                   type="password"
                   name="password_confirmation"
                   required 
                   autocomplete="new-password"
                   placeholder="Confirm new password">
            
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" 
                class="w-full bg-primary hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <i class="fas fa-redo-alt mr-2"></i>Reset Password
        </button>
    </form>
</x-guest-layout>