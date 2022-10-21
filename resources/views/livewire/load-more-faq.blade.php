<div id="faqs-list" data-has-more="{{ $total !== count($faqs) ? 'true' : 'false' }}" data-loading="false">
    <div class="mb-6">
        <span class="text-2xl font-bold">Всего вопросов</span>
        <span class="text-sm font-bold text-[#a1a5b7]">({{ $total }})</span>
    </div>

    @foreach($faqs as $faq)
        <article id="faq-component" class="{{ !$loop->last ? 'mb-8 pb-8 border-b border-dashed border-[#a1a5b7]' : 'pb-8' }}">
            <header class="mb-3">
                <a
                    href="/faqs/{{$faq->slug}}"
                    class="text-xl font-bold hover:text-[#009ef7] transition-colors duration-300 mb-3"
                >{{ $faq->title }}</a>
            </header>

            <div class="text-sm text-gray-500 mb-4">{!! $faq->question !!}</div>

            <footer class="flex flex-wrap items-center justify-between -m-1">
                <div class="flex items-center p-1">
                    <div
                        class="shrink-0 bg-[#e8fff3] w-[35px] h-[35px] flex items-center justify-center rounded-[6px] mr-1.5">
                        <div class="text-[#50cd89] text-lg font-medium">J</div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-gray-900 text-xs">James Hunt</span>
                        <span class="text-[#a1a5b7] text-[11px]">24 minutes ago</span>
                    </div>
                </div>

                <div class="flex space-x-1.5 p-1">
                    @foreach($faq->tags as $tag)
                        <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded" href="#">{{ $tag->name }}</a>
                    @endforeach
                </div>
            </footer>
        </article>
    @endforeach

    @if($total !== count($faqs))
        <div wire:loading class="h-10 w-full flex justify-center items-center bg-gray-200 font-bold">
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
                window.livewire.emit('faqs-load-more');
            }
        };
    </script>
@endpush
