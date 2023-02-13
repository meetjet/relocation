<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo -->
        <a href="{{ route('welcome') }}">
            <x-logo width="180"/>
        </a>
        <!-- Facebook login button -->
        <div class="mt-4">
            <a href="{{ route('oauth.redirect', ['provider' => JoelButcher\Socialstream\Providers::facebook()]) }}">
                <x-socialstream-icons.facebook class="h-8 w-8 mx-2" />
                <span class="sr-only">Facebook</span>
            </a>
        </div>
        <!-- Telegram login button -->
        <div class="mt-4">
            {!! Laravel\Socialite\Facades\Socialite::driver('telegram')->getButton() !!}
        </div>
    </div>
</x-guest-layout>
