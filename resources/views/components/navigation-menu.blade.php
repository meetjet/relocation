<nav class="border-b border-gray-100 fixed w-full top-0">

    <!-- Primary Navigation Menu -->
    <div class="bg-white relative z-20">
        <div class="container mx-auto z-20 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 space-x-6">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('welcome') }}">
                            <x-jet-application-mark class="block h-9 w-auto" />
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <ul class="hidden lg:flex space-x-4 lg:-my-px lg:ml-10 ">
                        @foreach($menu as $item)
                            <li>
                                <x-jet-nav-link href="{{ route($item->route_name) }}" :active="request()->routeIs($item->route_name_regex)">
                                    {{ __($item->title) }}
                                </x-jet-nav-link>
                            </li>
                        @endforeach
                    </ul>

                </div>

                <div class="flex items-center space-x-6">

                    <div class="relative">
                        <!-- Country switch -->
                        <x-jet-dropdown align="right" width="48">
                            <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 512 512"
                                        width="24"
                                        height="24"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M256 0c-141.385 0-256 114.615-256 256s114.615 256 256 256 256-114.615 256-256-114.615-256-256-256zM256 480.001c-31.479 0-61.436-6.506-88.615-18.226l116.574-131.145c2.603-2.929 4.041-6.711 4.041-10.63v-48c0-8.837-7.163-16-16-16-56.495 0-116.102-58.731-116.687-59.313-3-3.001-7.070-4.687-11.313-4.687h-64c-8.836 0-16 7.164-16 16v96c0 6.061 3.424 11.601 8.845 14.311l55.155 27.578v93.943c-58.026-40.478-96-107.716-96-183.832 0-34.357 7.745-66.903 21.569-96h58.431c4.244 0 8.313-1.686 11.314-4.686l64-64c3-3.001 4.686-7.070 4.686-11.314v-38.706c20.281-6.037 41.759-9.294 64-9.294 35.203 0 68.502 8.13 98.141 22.6-2.072 1.751-4.088 3.582-6.023 5.518-18.133 18.132-28.118 42.239-28.118 67.882s9.985 49.75 28.118 67.882c18.217 18.216 42.609 28.132 67.817 28.13 1.583 0 3.171-0.040 4.759-0.118 6.907 25.901 19.376 93.328-4.202 186.167-0.222 0.872-0.348 1.744-0.421 2.612-40.662 41.54-97.35 67.328-160.071 67.328z"
                                        ></path>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    class="hidden lg:inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition"
                                >
                                    <span>{{ $activeCountry }}</span>
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                            </x-slot>
                            <x-slot name="content">
                                @foreach($countries as $_country)
                                    <x-jet-dropdown-link href="{{ $_country['url'] }}"
                                                         class="{{ $_country['is_active'] ? 'font-bold' : '' }}">
                                        {{ $_country['name'] }}
                                    </x-jet-dropdown-link>
                                @endforeach
                            </x-slot>
                        </x-jet-dropdown>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <div class="flex items-center">
                                <!-- Teams Dropdown -->
                                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->currentTeam)
                                    <div class="ml-3 relative">
                                        <x-jet-dropdown align="right" width="60">
                                            <x-slot name="trigger">
                                            <span class="inline-flex rounded-md">
                                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                    {{ Auth::user()->currentTeam->name }}

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                            </x-slot>

                                            <x-slot name="content">
                                                <div class="w-60">
                                                    <!-- Team Management -->
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        {{ __('Manage Team') }}
                                                    </div>

                                                    <!-- Team Settings -->
                                                    <x-jet-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                                        {{ __('Team Settings') }}
                                                    </x-jet-dropdown-link>

                                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                                        <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                                            {{ __('Create New Team') }}
                                                        </x-jet-dropdown-link>
                                                    @endcan

                                                    <div class="border-t border-gray-100"></div>

                                                    <!-- Team Switcher -->
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        {{ __('Switch Teams') }}
                                                    </div>

                                                    @foreach (Auth::user()->allTeams() as $team)
                                                        <x-jet-switchable-team :team="$team" />
                                                    @endforeach
                                                </div>
                                            </x-slot>
                                        </x-jet-dropdown>
                                    </div>
                                @endif

                                <!-- Settings Dropdown -->
                                <div class="ml-3 relative">
                                    <x-jet-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                                </button>
                                            @else
                                                <span class="inline-flex rounded-md">
                                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                                    <span class="hidden lg:block">{{ Auth::user()->name }}</span>

                                                    <svg class="ml-2 -mr-0.5 h-4 w-4 hidden lg:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>

                                                    <svg class="flex lg:hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512" fill="currentColor">
                                                        <path d="M288 353.306v-26.39c35.249-19.864 64-69.386 64-118.916 0-79.529 0-144-96-144s-96 64.471-96 144c0 49.53 28.751 99.052 64 118.916v26.39c-108.551 8.874-192 62.21-192 126.694h448c0-64.484-83.449-117.82-192-126.694z"></path>
                                                    </svg>
                                                </button>
                                            </span>
                                            @endif
                                        </x-slot>

                                        <x-slot name="content">
                                            <!-- Admin panel -->
                                            @if (Auth::user() && Auth::user()->is_admin)
                                                <x-jet-dropdown-link href="{{ route('filament.pages.dashboard') }}">
                                                    {{ __('Admin panel') }}
                                                </x-jet-dropdown-link>

                                                <div class="border-t border-gray-100"></div>
                                            @endif

                                            <!-- Account Management -->
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                {{ __('Manage Account') }}
                                            </div>

                                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                                {{ __('Profile') }}
                                            </x-jet-dropdown-link>

                                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                                <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                                    {{ __('API Tokens') }}
                                                </x-jet-dropdown-link>
                                            @endif

                                            <div class="border-t border-gray-100"></div>

                                            <!-- Authentication -->
                                            <form method="POST" action="{{ route('logout') }}" x-data>
                                                @csrf

                                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                                                     @click.prevent="$root.submit();">
                                                    {{ __('Log Out') }}
                                                </x-jet-dropdown-link>
                                            </form>
                                        </x-slot>
                                    </x-jet-dropdown>
                                </div>
                            </div>
                        @else
                            <!-- Telegram login button -->
                            <div class="flex items-center">
                                @php
                                    $country = str(request()->getHost())->replace(('.' . config('app.domain')), "");
                                    $route = str(route('auth.login'))->replace("{$country}.", "");
                                    $url = "{$route}?return_url=" . url()->current();
                                @endphp
                                <a href="{{ $url }}">
                                    <x-login-button/>
                                </a>
                            </div>
                        @endauth
                    @endif

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center lg:hidden">
                        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($listingCategories && $listingCategories->count())
        <div id="categories" class="relative z-10 bg-white transition-all duration-300">
            <div class="container mx-auto z-10 px-4 sm:px-6 lg:px-8 py-1">
                <div class="overflow-x-auto scrollbar scrollbar-sm py-1">
                    <ul class="flex space-x-2">
                        @foreach($listingCategories as $_category)
                            @php
                                $bgColor = $_category->isCurrent ? "bg-indigo-200" : "bg-gray-200";
                                $textColor = $_category->isCurrent ? "text-indigo-900" : "text-black";
                            @endphp
                            <li class="text-xs {{ $bgColor }} {{ $textColor }} py-1 px-2 rounded-full select-none whitespace-nowrap">
                                <a href="{{ route('listings.category', $_category->slug) }}">{{ $_category->title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</nav>
