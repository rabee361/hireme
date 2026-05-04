@props([
    'title',
    'description',
    'eyebrow' => 'لوحة التحكم',
])

<div class="space-y-6">

    @isset($stats)
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            {{ $stats }}
        </div>
    @endisset

    <section class="dashboard-panel overflow-hidden">
        @isset($toolbar)
            <div class="border-b border-brand-100/80 px-5 py-4 sm:px-6">
                {{ $toolbar }}
            </div>
        @endisset

        <div class="overflow-x-auto">
            {{ $slot }}
        </div>
    </section>
</div>