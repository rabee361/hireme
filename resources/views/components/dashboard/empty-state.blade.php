@props([
    'title',
    'description',
    'icon' => 'spark',
])

<div class="flex flex-col items-center justify-center gap-4 px-6 py-16 text-center">
    <div class="grid h-16 w-16 place-items-center rounded-[1.75rem] bg-brand-50 text-brand-600 shadow-[inset_0_0_0_1px_rgba(21,152,104,0.12)]">
        <x-dashboard.icon :name="$icon" class="size-7" />
    </div>

    <div class="space-y-2">
        <h3 class="text-xl font-black text-zinc-900">{{ $title }}</h3>
    </div>
</div>