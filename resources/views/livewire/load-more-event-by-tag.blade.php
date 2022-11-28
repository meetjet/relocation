<div id="events" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">{{ __('Total events') }}</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    <ul id="events-list" class="flex flex-wrap -m-2">
        @foreach($items as $_item)
            <li id="events-item" class="w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 p-2">
                <div
                    tabindex="1"
                    class="h-full flex flex-col justify-between border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300 outline-none"
                >
                    <div class="space-y-2">
                        <a href="{{ route('events.show', $_item->uuid) }}"
                           class="flex outline-none">
                            @if($_item->cover_picture)
                                <img src="{{ $_item->cover_picture->thumbnail_square }}"
                                     alt="{{ $_item->cover_picture->caption }}"
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
                           href="{{ route('events.show', $_item->uuid) }}" tabindex="-1">
                            {{ $_item->title }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        <div
                            class="text-[20px] leading-none font-bold">{{ $_item->frontend_price }}</div>
                        <a
                            href="{{ route('events.show', $_item->uuid) }}"
                            class="flex justify-center bg-blue-500 hover:bg-blue-600 transition-colors duration-300 text-white text-sm font-bold leading-none rounded p-3"
                            tabindex="-1"
                        >{{ __('More') }}</a>
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
            const events = document.getElementById('events');
            const hasMore = events.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('load-more-event');
            }
        };
    </script>
@endpush
