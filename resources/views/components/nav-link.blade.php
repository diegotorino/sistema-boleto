@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-accent-color text-sm font-medium leading-5 text-accent-color focus:outline-none focus:border-accent-color transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 dark:text-gray-400 hover:text-accent-color hover:border-accent-color focus:outline-none focus:text-accent-color focus:border-accent-color transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
