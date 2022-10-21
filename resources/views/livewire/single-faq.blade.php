<article class="mb-8 pb-8 border-b border-dashed border-[#a1a5b7]">
    <div class="mb-3">
        <h1 class="text-xl font-bold mb-4">{{ $entity->title }}</h1>
    </div>

    <div class="mb-4">{!! $entity->question !!}</div>

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
            @foreach($entity->tags as $tag)
                <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded" href="#">{{ $tag->name }}</a>
            @endforeach
        </div>
    </footer>
</article>
