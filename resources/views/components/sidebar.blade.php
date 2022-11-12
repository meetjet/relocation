<aside
    class="w-60 h-screen flex flex-col bg-white fixed top-0 transition-transform duration-300 shadow -translate-x-full"
    :class="{'-translate-x-full': !open }"
    style="visibility: hidden"
    x-init="() => { $el.removeAttribute('style') }"
>
    <header class="flex items-center justify-between p-3 border-b">
        <div></div>
        <button
            @click="open = false"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
        >
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </header>

    <div class="h-full scrollbar overflow-y-auto py-3">
        <ul>
            @foreach(json_decode($menu) as $item)
                <li>
                    <a
                        href="{{ route($item->route_name) }}"
                        class="{{ request()->routeIs($item->route_name_regex) ? 'font-bold bg-indigo-200 text-indigo-900 ' : '' }}cursor-pointer w-full flex py-2 px-6 items-center font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                    >{{ $item->title }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <footer class="p-4"></footer>
</aside>
