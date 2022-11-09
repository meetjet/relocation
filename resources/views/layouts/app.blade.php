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
        
        <!-- Analytics -->
        <script defer data-domain="relocation.digital" src="//plausible.io/js/script.js"></script>
    </head>
    <body
        class="font-sans antialiased"
        :class="{ 'overflow-y-hidden': open }"
        x-data="{ open: false }"
        @resize.window="open = false"
    >
        <x-jet-banner />

        <div class="min-h-screen transition-all duration-300">
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

        <div
            class="fixed top-0 left-0 w-screen bg-black bg-opacity-20" :class="{ 'h-screen': open }"
            @click="open = false"
        ></div>

        <x-sidebar :menu="$menu" />

        @stack('modals')

        @livewireScripts

        @stack('page-scripts')
    </body>
</html>
