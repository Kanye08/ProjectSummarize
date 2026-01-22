<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Verify your email</h2>
        <p class="text-gray-600 mt-2">Almost there! Just one more step</p>
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-gray-700">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">
                A new verification link has been sent to the email address you provided during registration.
            </p>
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" 
                    class="bg-primary hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <i class="fas fa-envelope mr-2"></i>Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" 
                    class="text-gray-600 hover:text-gray-800 font-medium py-3 px-6 rounded-lg transition duration-200 hover:bg-gray-100">
                <i class="fas fa-sign-out-alt mr-2"></i>Log Out
            </button>
        </form>
    </div>
</x-guest-layout>