<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Create your account</h2>
        <p class="text-gray-600 mt-2">Join thousands of teams using SummarAIze</p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input id="name" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Enter your full name">
            @error('name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Username -->
        <div class="mb-6">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input id="username" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                   type="text" 
                   name="username" 
                   value="{{ old('username') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Choose a username">
            @error('username')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input id="email" 
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition duration-200" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="email"
                   placeholder="Enter your email address">
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
                   autocomplete="new-password"
                   placeholder="Create a strong password">
            
            <!-- Password Requirements -->
            <div class="mt-3 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Password must contain:</p>
                <ul id="passwordRequirements" class="text-sm space-y-1">
                    <li id="req-length" class="flex items-center">
                        <i class="fas fa-circle text-xs mr-2 text-red-500"></i>
                        <span class="text-gray-600">At least 8 characters</span>
                    </li>
                    <li id="req-uppercase" class="flex items-center">
                        <i class="fas fa-circle text-xs mr-2 text-red-500"></i>
                        <span class="text-gray-600">One uppercase letter</span>
                    </li>
                    <li id="req-lowercase" class="flex items-center">
                        <i class="fas fa-circle text-xs mr-2 text-red-500"></i>
                        <span class="text-gray-600">One lowercase letter</span>
                    </li>
                    <li id="req-number" class="flex items-center">
                        <i class="fas fa-circle text-xs mr-2 text-red-500"></i>
                        <span class="text-gray-600">One number</span>
                    </li>
                    <li id="req-special" class="flex items-center">
                        <i class="fas fa-circle text-xs mr-2 text-red-500"></i>
                        <span class="text-gray-600">One special character</span>
                    </li>
                </ul>
            </div>

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
                   placeholder="Confirm your password">
            
            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-6">
            <label class="flex items-start">
                <input type="checkbox" 
                       name="terms" 
                       value="1" 
                       required 
                       class="mt-1 mr-3 rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm text-gray-600">
                    I agree to the <a href="#" class="text-primary font-medium hover:underline">Terms and Conditions</a> and <a href="#" class="text-primary font-medium hover:underline">Privacy Policy</a>
                </span>
            </label>
            @error('terms')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" 
                class="w-full bg-primary hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            <i class="fas fa-user-plus mr-2"></i>Create Account
        </button>

        <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary font-medium hover:underline ml-1">
                    Sign in here
                </a>
            </p>
        </div>
    </form>

    <script>
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const requirements = {
                'req-length': password.length >= 8,
                'req-uppercase': /[A-Z]/.test(password),
                'req-lowercase': /[a-z]/.test(password),
                'req-number': /[0-9]/.test(password),
                'req-special': /[^A-Za-z0-9]/.test(password)
            };

            Object.keys(requirements).forEach(reqId => {
                const element = document.getElementById(reqId);
                const icon = element.querySelector('i');
                const text = element.querySelector('span');
                
                if (requirements[reqId]) {
                    icon.classList.remove('text-red-500');
                    icon.classList.add('text-green-500');
                    text.classList.add('text-green-700');
                } else {
                    icon.classList.remove('text-green-500');
                    icon.classList.add('text-red-500');
                    text.classList.remove('text-green-700');
                }
            });
        });
    </script>
</x-guest-layout>