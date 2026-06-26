<x-dashboard.page-shell title="سجل المشاريع" description="">
    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900">قائمة المشاريع</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن مشروع" />
        </div>
    </x-slot:toolbar>

    @if ($projects->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60 dark:border-zinc-800 dark:bg-zinc-900/40">
                <tr>
                    <th>المشروع</th>
                    <th>العميل</th>
                    <th>التفاصيل</th>
                    <th>الأدوات</th>
                    <th>التقديمات</th>
                    <th>الإنشاء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80 dark:divide-zinc-800">
                @foreach ($projects as $project)
                    @php
                        $tools = collect([
                            $project->tool1,
                            $project->tool2,
                            $project->tool3,
                            $project->tool4,
                            $project->tool5,
                        ])->filter();
                    @endphp

                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50 cursor-pointer" wire:click="showItem({{ $project->id }})">
                        <td>
                            <div class="min-w-0">
                                <p class="truncate font-black text-zinc-900 dark:text-zinc-100">{{ $project->name }}</p>
                                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">#{{ $project->id }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p class="font-black text-zinc-900 dark:text-zinc-100">{{ $project->customer?->username ?: 'بدون عميل' }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $project->customer?->email ?: '---' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal leading-7 text-zinc-600 dark:text-zinc-400">
                            {{ $project->details ?: 'لا توجد تفاصيل' }}
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($tools as $tool)
                                    <span class="dashboard-badge">{{ $tool }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لا توجد أدوات</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
                                {{ $project->applications_count }} تقديم
                            </div>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900 dark:text-zinc-100">{{ optional($project->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ optional($project->created_at)->locale('ar')->translatedFormat('h:i A') }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6 dark:border-zinc-800">
            {{ $projects->onEachSide(1)->links() }}
        </div>
        <x-dashboard.detail-modal>
            @if ($this->selectedItem)
                <x-slot:header>
                    <div class="mb-3 grid h-24 w-24 place-items-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        <x-dashboard.icon name="projects" class="size-10" />
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $this->selectedItem->name }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zinc-500 dark:text-zinc-400">مشروع عمل حر • #{{ $this->selectedItem->id }}</p>
                </x-slot:header>

                <div class="border-t border-brand-100/80 pt-8 dark:border-zinc-800">
                    <div class="mb-8 text-center">
                        <h4 class="mb-4 text-lg font-black text-zinc-900 dark:text-zinc-100">تفاصيل المشروع</h4>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            {{ $this->selectedItem->details ?: 'لا توجد تفاصيل.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-y-8 text-center">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">العميل الناشر</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->customer?->username ?: 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">تاريخ النشر</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ optional($this->selectedItem->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">عدد التقديمات</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->applications_count }} تقديم</p>
                        </div>
                        <div class="col-span-full mt-4">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">الأدوات المطلوبة</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                @php
                                    $tools = collect([
                                        $this->selectedItem->tool1,
                                        $this->selectedItem->tool2,
                                        $this->selectedItem->tool3,
                                        $this->selectedItem->tool4,
                                        $this->selectedItem->tool5,
                                    ])->filter();
                                @endphp
                                @forelse ($tools as $tool)
                                    <span class="dashboard-badge">{{ $tool }}</span>
                                @empty
                                    <span class="text-sm font-semibold text-zinc-400 dark:text-zinc-500">لا توجد أدوات مسجلة</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </x-dashboard.detail-modal>
    @else
        <x-dashboard.empty-state title="لا توجد مشاريع " description="" icon="projects" />
    @endif
</x-dashboard.page-shell>