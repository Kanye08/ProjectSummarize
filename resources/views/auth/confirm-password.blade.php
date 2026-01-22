<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Confirm Password</h2>
        <p class="text-gray-600 mt-2">Secure area confirmation required</p>
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-gray-700">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

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

        <div class="flex justify-end">
            <button type="submit" 
                    class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <i class="fas fa-check mr-2"></i>Confirm Password
            </button>
        </div>
    </form>
</x-guest-layout>