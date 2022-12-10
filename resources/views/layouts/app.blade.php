<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if($model)
            {!! seo($model) !!}
        @else
            <title>{{ config('app.name', 'Laravel') }}</title>
        @endif

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Inter:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Analytics -->
        <script defer data-domain="{{ request()->getHost() }}" src="//plausible.io/js/script.js"></script>
    </head>
    <body
        class="font-sans antialiased pt-16"
        :class="{ 'overflow-y-hidden': open }"
        x-data="{ open: false }"
        @resize.window="open = false"
    >
        <x-jet-banner />

        <div class="min-h-screen transition-all duration-300">
            <x-navigation-menu
                :activeCountry="$activeCountry"
                :countries="$countries"
                :menu="$menu"
                :listingCategories="$listingCategories"
            />

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
