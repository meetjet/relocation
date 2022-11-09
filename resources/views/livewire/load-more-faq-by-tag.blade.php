<div id="faqs-list"
     data-has-more="{{ $total !== count($faqs) ? 'true' : 'false' }}"
     data-loading="false"
     data-country="{{ $country }}">
    <div class="mb-6">
        <span class="text-2xl font-bold">{{ __('Total questions') }}</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    @foreach($faqs as $_faq)
        <article id="faq-component"
                 class="{{ !$loop->last ? 'mb-8 pb-8 border-b border-dashed border-[#a1a5b7]' : 'pb-8' }}">
            <header class="mb-3">
                <a href="{{ route('faqs.show', [$_faq->country, $_faq->slug]) }}"
                   class="text-xl font-bold hover:text-[#009ef7] transition-colors duration-300 mb-3"
                >{{ $_faq->title }}</a>
            </header>

            <div class="text-sm text-gray-500 mb-4">{!! $_faq->question !!}</div>

            <footer class="flex flex-wrap items-center justify-between -m-1">
                <div class="flex flex-wrap space-x-1.5 p-1">
                    @foreach($_faq->tags as $_tag)
                        @php
                            $bgColor = $_tag->slug === $currentTag ? "bg-indigo-200" : "bg-[#f5f8fa]";
                            $textColor = $_tag->slug === $currentTag ? "text-indigo-900" : "text-[#7E8299]";
                        @endphp
                        <a class="text-xs font-medium mt-1 px-2.5 py-1.5 {{ $bgColor }} {{ $textColor }} rounded"
                           href="{{ route('faqs-by-tag.index', [$_faq->country, $_tag->slug]) }}">{{ $_tag->name }}</a>
                    @endforeach
                </div>
            </footer>
        </article>
    @endforeach

    @if($total !== count($faqs))
        <div wire:loading class="h-10 w-full mb-4 flex justify-center items-center bg-gray-200 font-bold">
            {{ __('Loading...') }}
        </div>
    @endif
</div>

@push('page-scripts')
    <script type="text/javascript">
        window.onscroll = function (ev) {
            const faqsList = document.getElementById('faqs-list');
            const hasMore = faqsList.getAttribute('data-has-more') === 'true';

            if (hasMore && (window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                window.livewire.emit('faqs-load-more', faqsList.getAttribute('data-country'));
            }
        };
    </script>
@endpush
