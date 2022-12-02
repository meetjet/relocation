<div id="listings-list" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="flex items-center space-x-4 mb-6">
        <div>
            <span class="text-2xl font-bold">{{ __('Total announcements') }}</span>
            <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
        </div>
        @if($botUsername)
            <div>
                <a class="flex justify-center bg-blue-500 hover:bg-blue-600 transition-colors duration-300 text-white text-sm font-bold leading-none rounded px-3 py-2"
                   href="https://t.me/{{ $botUsername }}" target="_blank"
                   tabindex="-1">{{ __("Create announcement") }}</a>
            </div>
        @endif
    </div>

    <ul id="listings" class="flex flex-wrap -m-2">
        @foreach($items as $item)
            <li
                id="listings-component"
                class="w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 p-2"
            >
                <div
                    tabindex="1"
                    class="h-full flex flex-col justify-between border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300 outline-none"
                >
                    <div class="space-y-2">
                        <a href="{{ route('listings.show', [$item->category->slug, $item->uuid]) }}"
                           class="flex outline-none">
                            @if($item->cover_picture)
                                <img src="{{ $item->cover_picture->thumbnail_square }}"
                                     alt="{{ $item->cover_picture->caption }}"
                                     class="aspect-square object-cover max-w-auto w-full"
                                     width="400"
                                     height="400"
                                     loading="lazy"
                                >
                            @else
                                <img
                                    src="{{ asset('images/no-image.jpg') }}"
                                    class="aspect-square object-cover max-w-auto w-full"
                                    width="400"
                                    height="400"
                                    loading="lazy"
                                >
                            @endif
                        </a>
                        <a class="flex text-blue-600 font-bold"
                           href="{{ route('listings.show', [$item->category->slug, $item->uuid]) }}" tabindex="-1">
                            {{ $item->title }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        <div
                            class="text-[20px] leading-none font-bold">{{ $item->price }} {{ currencies()->getSign($item->currency) }}</div>
                        <a
                            href="{{ route('listings.show', [$item->category->slug, $item->uuid]) }}"
                            class="flex justify-center bg-blue-500 hover:bg-blue-600 transition-colors duration-300 text-white text-sm font-bold leading-none rounded p-3"
                            tabindex="-1"
                        >{{ __('More') }}</a>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    @if($total !== count($items))
        <div wire:loading:flex class="h-10 w-full my-4 flex justify-center items-center bg-gray-200 font-bold">
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
