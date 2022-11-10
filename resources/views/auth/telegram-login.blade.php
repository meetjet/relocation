<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo -->
        <a href="{{ route('welcome') }}">
            <x-logo width="180"/>
        </a>
        <!-- Login button -->
        <div class="mt-4">
            {!! Laravel\Socialite\Facades\Socialite::driver('telegram')->getButton() !!}
        </div>
    </div>
</x-guest-layout>
