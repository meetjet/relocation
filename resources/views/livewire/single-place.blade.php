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

        {{-- Location --}}
        @if($entity->country)
            <div class="flex flex-wrap mt-4">
                <div class="mr-2">{{ __('Location') }}:</div>
                <div>{{ countries()->getDescription($entity->country) }}</div>
                @if($entity->location)
                    <div>, {{ locations()->getDescription($entity->country, $entity->location) }}</div>
                @endif
                @if($entity->address)
                    <div>, {{ $entity->address }}</div>
                @endif
            </div>
        @endif

        {{-- Owner --}}
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
                    автором
                </div>
            @endif
        </div>

        {{-- Phones --}}
        @if($entity->phones)
            <div class="flex flex-wrap mt-4">
                <div class="mr-2">
                    {{ count($entity->phones) === 1 ? __('Phone number') : __('Phone numbers') }}:
                </div>
                <div>
                    @foreach($entity->phones as $_phone)
                        <div>{{ $_phone['number'] }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sites --}}
        @if($entity->sites)
            <div class="flex flex-wrap mt-4">
                <div class="mr-2">
                    {{ count($entity->sites) === 1 ? __('Website') : __('Websites') }}:
                </div>
                <div>
                    @foreach($entity->sites as $_site)
                        <a class="block py-0.5"
                           href="{{ $_site['url'] }}"
                           target="_blank"
                           rel="nofollow noopener noreferrer">{{ $_site['url'] }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Social media --}}
        @if($entity->social_media)
            <div class="flex flex-wrap mt-4">
                <div class="mr-2">
                    {{ count($entity->social_media) === 1 ? __('Social network') : __('Social media') }}:
                </div>
                <div>
                    @foreach($entity->social_media as $_media)
                        <a class="block py-0.5"
                           href="{{ $_media['url'] }}"
                           target="_blank"
                           rel="nofollow noopener noreferrer">{{ \App\Enums\PlaceSocialNetwork::getDescription($_media['network']) }}</a>
                    @endforeach
                </div>
            </div>
        @endif

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
    @if (Auth::user() && Auth::user()->is_admin)
        <x-shared.edit-entity-button :url="route('filament.resources.places.edit', $entity->slug)"/>
    @endif
</article>
