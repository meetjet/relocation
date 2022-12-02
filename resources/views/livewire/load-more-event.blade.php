<div id="events" data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">{{ __('Total events') }}</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    <ul id="events-list" class="flex flex-wrap -m-2">
        @foreach($items as $item)
            <li
                id="events-item"
                class="w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 p-2"
            >
                <div
                    tabindex="1"
                    class="h-full flex flex-col justify-between border rounded p-3 space-y-2 hover:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] focus-within:shadow-[3px_3px_8px_rgb(161,161,161,0.3)] transition-all duration-300 outline-none"
                >
                    <div class="space-y-2">
                        <a href="{{ route('events.show', $item->uuid) }}" class="flex outline-none">
                            @if($item->cover_picture)
                                <img
                                    src="{{ $item->cover_picture->thumbnail_square }}"
                                    alt="{{ $item->cover_picture->caption }}"
                                    width="400"
                                    height="400"
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
                        <div>{{ $item->frontend_start_datetime }}</div>
                        <a class="flex text-blue-600 font-bold"
                           href="{{ route('events.show', $item->uuid) }}" tabindex="-1">
                            {{ $item->title }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        <div class="text-[20px] leading-none font-bold">{{ $item->frontend_price }}</div>
                        @if($item->tags->count())
                            <div>
                                <div class="flex space-x-1.5 mt-4">
                                    @foreach($item->tags->take(3) as $_tag)
                                        <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded"
                                           href="{{ route('events-by-tag.index', $_tag->slug) }}">{{ $_tag->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
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
            const events = document.getElementById('events');
            const hasMore = events.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('load-more-event');
            }
        };
    </script>
@endpush

