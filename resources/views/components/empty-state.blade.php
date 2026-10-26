@props(['title', 'description' => ''])
<div {{ $attributes->class(['rk-empty']) }} role="status">
    <i data-lucide="{{ $icon ?? 'file-search' }}" aria-hidden="true"></i>
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>
    {{ $slot }}
</div>
