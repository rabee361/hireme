@props([
    'placeholder',
    'model' => 'search',
])

<label {{ $attributes->class(['relative block w-full md:max-w-xs']) }}>
    <span class="pointer-events-none absolute inset-y-0 right-40 flex items-center text-brand-400">
        <x-dashboard.icon name="search" class="size-5" />
    </span>
    <input type="search" wire:model.live.debounce.300ms="{{ $model }}" class="dashboard-input pe-14" placeholder="{{ $placeholder }}">
</label>