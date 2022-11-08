<article>
    <div class="mb-8 pb-8 border-b border-dashed border-[#a1a5b7]">
        <div class="mb-3">
            <h1 class="text-3xl font-bold mb-4">{{ $entity->title }}</h1>
        </div>

        <div class="mb-4">{!! $entity->description !!}</div>

        @if($entity->pictures->count())
            <div class="flex space-x-4">
                @foreach($entity->pictures as $picture)
                    {{-- TODO: if transformation is required, then replace "$picture->url" with "$picture->transform('resize-200-crop-center')" --}}
                    <img src="{{ $picture->url }}"
                         alt="{{ $picture->caption }}"
                         class="w-[200px] aspect-square object-contain"/>
                @endforeach
            </div>
        @endif

        @if($entity->tags->count())
            <div class="flex flex-wrap items-center justify-between -m-1 mt-4">
                <div class="flex space-x-1.5 p-1">
                    @foreach($entity->tags as $tag)
                        <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded"
                           href="#">{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(Auth::user() && $entity->contact)
            <div class=" mt-4">
                {{ __("Contact") }}:
                <a href="https://t.me/{{ $entity->contact->nickname }}"
                   target="_blank"
                   class="text-blue-600">{{ $entity->contact->name }} ({{ "@" . $entity->contact->nickname }})</a>
            </div>
        @endif
    </div>
</article>
