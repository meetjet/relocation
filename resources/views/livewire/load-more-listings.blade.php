<div id="listings-list" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">Всего товаров</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    <ul id="listings" class="flex flex-wrap -m-2">
        @foreach($items as $item)
            <li id="listings-component" class="w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 p-2">
                <div
                    tabindex="1"
                    class="h-full flex flex-col justify-between border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300 outline-none"
                >
                    <div class="space-y-2">
                        @if($item->cover_image)
                            <a href="/listings/{{ $item->slug }}"
                               class="w-full max-w-[200px] mx-auto flex items-center justify-center outline-none">
                                <img
                                    src="{{ $item->cover_image['url'] }}"
                                    alt="{{ $item->cover_image['caption'] }}"
                                    class="w-[200px] aspect-square object-contain"
                                    width="2000"
                                    height="1160"
                                    loading="lazy"
                                >
                            </a>
                        @endif
                        <a href="/listings/{{ $item->slug }}" tabindex="-1">{{ $item->title }}</a>
                    </div>
                    <div class="space-y-2">
                        <div class="text-[20px] leading-none font-bold">{{ $item->price }} ₽</div>
                        <a
                            href="/listings/{{ $item->slug }}"
                            class="flex justify-center bg-[#306BC9] hover:bg-opacity-90 transition-colors duration-300 text-white text-sm font-bold leading-none rounded p-3"
                            tabindex="-1"
                        >Подробнее</a>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    @if($total !== count($items))
        <div wire:loading class="h-10 w-full mb-4 flex justify-center items-center bg-gray-200 font-bold">
            {{ __('Loading...') }}
        </div>
    @endif
</div>

@push('page-scripts')
    <script type="text/javascript">
        window.onscroll = function (ev) {
            const faqsList = document.getElementById('listings-list');
            const hasMore = faqsList.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('listings-load-more');
            }
        };
    </script>
@endpush
