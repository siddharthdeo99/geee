@props(['disabled' => false])

<x-button :disabled="$disabled" {{ $attributes->merge(['type' => 'submit', 'class' => 'text-black  bg-primary-600 hover:bg-primary-600 focus:outline-none border border-black']) }}>
    {{ $slot }}
</x-button>
