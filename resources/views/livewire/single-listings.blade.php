<article>
    <div class="mb-8 pb-8 border-b border-dashed border-[#a1a5b7]">
        <div class="mb-3">
            <h1 class="text-3xl font-bold mb-4">{{ $entity->title }}</h1>
        </div>

        <div class="mb-4">{!! $entity->description !!}</div>

        @if($entity->pictures->count())
            <ul class="sm:flex sm:flex-wrap -m-2">
                @foreach($entity->pictures as $picture)
                    <li class="p-2 sm:w-1/2 md:w-1/3 lg:w-1/4">
                        <img
                            src="{{ $picture->thumbnail_square }}"
                            alt="{{ $picture->caption }}"
                            width="400"
                            height="400"
                            class="w-full aspect-square object-contain"
                        />
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="text-2xl font-bold my-4">{{ $entity->price }} {{ currencies()->getSign($entity->currency) }}</div>

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

        {{-- Announcement owner --}}
        <div class="flex flex-wrap mt-4">
            <div class="mr-2">{{ __("Contact") }}:</div>
            @if(Auth::user())
                @if($entity->email)
                    {{-- Owner email --}}
                    {{ $entity->email }}
                @elseif($entity->phone)
                    {{-- Owner phone --}}
                    {{ $entity->phone }}
                @elseif($entity->custom_nickname)
                    {{-- Custom nickname --}}
                    <a href="https://t.me/{{ $entity->custom_nickname }}"
                       target="_blank"
                       class="text-blue-600">{{ "@" . $entity->custom_nickname }}</a>
                @elseif($entity->contact && $entity->contact->nickname)
                    {{-- Real owner nickname --}}
                    <a href="https://t.me/{{ $entity->contact->nickname }}"
                       target="_blank"
                       class="text-blue-600">{{ "@" . $entity->contact->nickname }}</a>
                @else
                    {{-- Something went wrong! --}}
                    {{ str(__('Not found'))->lower()->value() }}
                @endif
            @else
                <div>
                    @php
                        $country = str(request()->getHost())->replace(('.' . config('app.domain')), "");
                        $route = str(route('auth.login'))->replace("{$country}.", "");
                        $url = "{$route}?return_url=" . url()->current();
                    @endphp
                    <a href="{{ $url }}" class="text-blue-600 underline">Войдите на сайт</a>, чтобы связаться с
                    продавцом
                </div>
            @endif
        </div>

        @if($entity->tags->count())
            <div class="flex flex-wrap items-center justify-between -m-1 mt-4">
                <div class="flex space-x-1.5 p-1">
                    @foreach($entity->tags as $_tag)
                        <a class="text-xs font-medium px-2.5 py-1.5 bg-[#f5f8fa] text-[#7E8299] rounded"
                           href="{{ route('listings-by-tag.index', $_tag->slug) }}">{{ $_tag->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @if (Auth::user() && Auth::user()->is_admin)
        <x-shared.edit-entity-button :url="route('filament.resources.listings.edit', $entity->uuid)"/>
    @endif
</article>
