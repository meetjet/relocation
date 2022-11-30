<div id="events" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">{{ __('Total events') }}</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    <ul id="events-list" class="masonry-grid">
        @foreach($items as $item)
            <li id="events-item" class="masonry-grid-item p-2">
                <div
                    tabindex="1"
                    class="border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300 outline-none"
                >
                    <div class="space-y-2">
                        <a href="{{ route('events.show', $item->uuid) }}" class="flex outline-none">
                            @if($item->cover_picture)
                                <img
                                    src="{{ $item->cover_picture->thumbnail_square }}"
                                    alt="{{ $item->cover_picture->caption }}"
                                    loading="lazy"
                                >
                            @else
                                <img
                                    src="{{ asset('images/no-image.jpg') }}"
                                    alt="event cover picture placeholder"
                                    width="400"
                                    height="400"
                                    loading="lazy"
                                >
                            @endif
                        </a>
                        <a class="flex text-blue-600 font-bold"
                           href="{{ route('events.show', $item->uuid) }}" tabindex="-1">
                            {{ $item->title }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        <div class="text-[20px] leading-none font-bold">{{ $item->frontend_price }}</div>
                        <a
                            href="{{ route('events.show', $item->uuid) }}"
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

@push('page-header-scripts')
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script>
        const grid = document.querySelector('.masonry-grid');

        new Masonry(grid, {
            itemSelector: '.masonry-grid-item',
        });
    </script>
@endpush

@push('page-scripts')
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script>
        const grid = document.querySelector('.masonry-grid');

        new Masonry(grid, {
            itemSelector: '.masonry-grid-item',
        });
    </script>
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

