<x-app-layout>
    <h1>{{ $page->get('title') }}</h1>

    @if($page->get('image') !== null)
        <img src="{{ $page->get('image') }}" alt="{{ $page->get('title') }}">
    @endif

    {!! $page->body() !!}
</x-app-layout>
