@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-3 my-3 bg-indigo-200 text-sm font-bold leading-none text-indigo-900 focus:outline-none focus:border-indigo-700 transition rounded'
            : 'inline-flex items-center px-4 py-3 my-3 text-sm font-bold leading-none text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:bg-gray-300 transition rounded';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
