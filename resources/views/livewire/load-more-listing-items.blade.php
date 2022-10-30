<div id="listing-items-list" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">Всего товаров</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    <ul id="listing-items" class="flex flex-wrap -m-2">
        @foreach($items as $item)
            <li id="listing-items-component" class="w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 p-2" >
                <div
                    tabindex="1"
                    class="h-full flex flex-col justify-between border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300"
                >
                    <div class="space-y-2">
                        <a href="/listing-items/{{ $item->slug }}" class="w-full max-w-[200px] mx-auto flex items-center justify-center outline-none">
                            <img
                                src="{{ $images[$loop->index] }}"
                                alt="{{ $item->title }}"
                                class="w-[200px] aspect-square object-contain"
                                width="2000"
                                height="1160"
                                loading="lazy"
                            >
                        </a>
                        <a href="/listing-items/{{ $item->slug }}" tabindex="-1">{{ $item->title }}</a>
                    </div>
                    <div class="space-y-2">
                        <div class="text-[20px] leading-none font-bold">33 270 ₽</div>
                        <div class="text-xs leading-none">{{ $item->status }}</div>
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
            const faqsList = document.getElementById('listing-items-list');
            const hasMore = faqsList.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('listing-items-load-more');
            }
        };
    </script>
@endpush
