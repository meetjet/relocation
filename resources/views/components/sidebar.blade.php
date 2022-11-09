<aside
    class="w-80 h-screen flex flex-col bg-white fixed top-0 -translate-x-full transition-transform duration-300 shadow"
    :class="{'translate-x-0': open }"
>
    <header class="p-4"></header>

    <div class="h-full scrollbar overflow-y-auto">
        <ul>
            @foreach($menu as $item)
                <li>
                    <a
                        href="{{ route($item['route_name'], $item['route_params']) }}"
                        class="cursor-pointer w-full flex py-2 px-6 rounded-lg items-center font-medium leading-5 transition duration-150 ease-in-out focus:outline-none"
                    >
                        <span class="ml-2 ml-3">{{ $item['title'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <footer class="p-4"></footer>
</aside>
