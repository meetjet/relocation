<article>
    <div class="mb-8 pb-8 border-b border-dashed border-[#a1a5b7]">
        <div class="mb-3">
            <h1 class="text-3xl font-bold mb-4">{{ $entity->title }}</h1>
        </div>

        <div class="mb-4">{!! $entity->description !!}</div>

        {{-- Pictures --}}
        @if($entity->pictures->count())
            <ul class="sm:flex sm:flex-wrap -m-2">
                @foreach($entity->pictures as $_picture)
                    <li class="p-2 sm:w-1/2 md:w-1/3 lg:w-1/4">
                        <img
                            src="{{ $_picture->thumbnail_square }}"
                            alt="{{ $_picture->caption }}"
                            class="w-full aspect-square object-contain"
                            width="400"
                            height="400"
                        />
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Date/Time --}}
        <div class="inline-flex items-center text-xl font-bold my-4"><x-ri-calendar-event-line class="w-8 h-8 mr-2" />{{ $entity->frontend_start_datetime }}</div>

        {{-- Price --}}
        <div class="text-xl font-bold my-4">{{ $entity->frontend_price }}</div>

        {{-- Location --}}
        @if($entity->country)
            <div class="flex flex-wrap mt-4">
                <div class="mr-2">{{ __('Location') }}:</div>
                <div>{{ countries()->getDescription($entity->country) }}</div>
                @if($entity->location)
                    <div>, {{ locations()->getDescription($entity->country, $entity->location) }}</div>
                @endif
            </div>
        @endif

        {{-- Owner --}}
        <div class="flex flex-wrap mt-4">
            <div class="mr-2">{{ __("Contact") }}:</div>
            @if(Auth::user())
                @if($entity->custom_nickname)
                    {{-- Custom nickname --}}
                    <a href="https://t.me/{{ $entity->custom_nickname }}"
                       target="_blank"
                       class="text-blue-600">{{ "@" . $entity->custom_nickname }}</a>
                @elseif($entity->contact && $entity->contact->nickname)
                    {{-- Real owner nickname --}}
                    <a href="https://t.me/{{ $entity->contact->nickname }}"
                       target="_blank"
                       class="text-blue-600">{{ "@" . $entity->contact->nickname }}</a>
                @elseif($entity->email)
                    {{-- Owner email --}}
                    {{ $entity->email }}
                @elseif($entity->phone)
                    {{-- Owner phone --}}
                    {{ $entity->phone }}
                @else
                    {{-- Something went wrong! --}}
                    {{ str(__('Not found'))->lower() }}
                @endif
            @else
                <div>
                    @php
                        $country = str(request()->getHost())->replace(('.' . config('app.domain')), "");
                        $route = str(route('auth.login'))->replace("{$country}.", "");
                        $url = "{$route}?return_url=" . url()->current();
                    @endphp
                    <a href="{{ $url }}" class="text-blue-600 underline">Войдите через Telegram</a>, чтобы связаться с
                    автором
                </div>
            @endif
        </div>

        @if($entity->tags->count())
            <div class="flex flex-wrap items-center justify-between -m-1 mt-4">
                <div class="flex space-x-1.5 p-1">
                    @foreach($entity->tags as $_tag)
                        <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded"
                           href="{{ route('events-by-tag.index', $_tag->slug) }}">{{ $_tag->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</article>
