<div id="listings-list"
     data-has-more="{{ $total !== count($items) ? 'true' : 'false' }}"
     data-loading="false"
     data-country="{{ $country }}">
    <div class="mb-6">
        <span class="text-2xl font-bold">{{ __('Total announcements') }}</span>
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
                        <a href="{{ route('listings.show', [$item->country, $item->uuid]) }}"
                           class="w-full max-w-[200px] mx-auto flex items-center justify-center outline-none">
                            @if($item->cover_picture)
                                <img src="{{ $item->cover_picture->thumbnail_square }}"
                                     alt="{{ $item->cover_picture->caption }}"
                                     class="w-[200px] aspect-square object-contain"
                                     width="2000"
                                     height="1160"
                                     loading="lazy"
                                >
                            @else
                                <img src="{{ asset('images/no-image.jpg') }}"
                                     class="w-[200px] aspect-square object-contain"
                                     loading="lazy"
                                >
                            @endif
                        </a>
                        <a class="text-blue-600 font-bold"
                           href="{{ route('listings.show', [$item->country, $item->uuid]) }}"
                           tabindex="-1">{{ $item->title }}</a>
                    </div>
                    <div class="space-y-2">
                        <div class="text-[20px] leading-none font-bold">{{ $item->price }} ֏</div>
                        <a
                            href="{{ route('listings.show', [$item->country, $item->uuid]) }}"
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
            const faqsList = document.getElementById('listings-list');
            const hasMore = faqsList.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('listings-load-more', faqsList.getAttribute('data-country'));
            }
        };
    </script>
@endpush
