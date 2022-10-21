<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Inter:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen">
            @livewire('navigation-menu')

            <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex">
                {{--<div class="hidden md:block w-60">test</div>--}}
                <!-- Content -->
                <div class="flex-1">
                    <div class="pt-4"></div>
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header>
                            {{ $header }}
                        </header>
                    @endif
                    <main>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @stack('modals')

        @livewireScripts

        @stack('page-scripts')
    </body>
</html>
