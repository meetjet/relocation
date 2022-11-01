<article>
    <div class="mb-8 pb-8 border-b border-dashed border-[#a1a5b7]">
        <div class="mb-3">
            <h1 class="text-3xl font-bold mb-4">{{ $entity->title }}</h1>
        </div>

        <div class="mb-4">{!! $entity->question !!}</div>

        <div class="flex flex-wrap items-center justify-between -m-1">
            <div class="flex space-x-1.5 p-1">
                @foreach($entity->tags as $tag)
                    <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded"
                       href="#">{{ $tag->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div>
        <div class="text-xl font-bold mb-4">{{ __('Answer') }}</div>
        <div class="mb-4 text-xl p-4 rounded bg-gray-100">{!! $entity->answer !!}</div>
    </div>
</article>
