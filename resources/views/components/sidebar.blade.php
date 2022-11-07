<aside
    class="w-80 h-screen flex flex-col bg-white fixed top-0 -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow"
    :class="{'translate-x-0': open }"
>
    <header class="p-4 bg-blue-400">Header</header>

    <div class="h-full scrollbar overflow-y-auto">
        <ul>
            @foreach(json_decode($menu) as $item)
                <li>
                    <a
                        href="{{ route($item->route_name) }}"
                        class="cursor-pointer w-full flex py-2 px-6 rounded-lg items-center font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                    >
                        <div>
                            <svg
                                aria-hidden="true"
                                width="19.2"
                                height="19.2"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="ov-icon text-sm"
                            >
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M21 20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.49a1 1 0 01.386-.79l8-6.222a1 1 0 011.228 0l8 6.222a1 1 0 01.386.79V20zm-2-1V9.978l-7-5.444-7 5.444V19h14z"
                                ></path>
                            </svg>
                        </div>
                        <span class="ml-2 ml-3">{{ $item->title }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <footer class="p-4 bg-blue-400">Footer</footer>
</aside>
