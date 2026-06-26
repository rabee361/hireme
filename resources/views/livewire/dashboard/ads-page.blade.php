<x-dashboard.page-shell title="سجل الإعلانات" description="">
    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900">قائمة الإعلانات</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن إعلان" />
        </div>
    </x-slot:toolbar>

    @if ($ads->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60 dark:border-zinc-800 dark:bg-zinc-900/40">
                <tr>
                    <th>الإعلان</th>
                    <th>الشركة</th>
                    <th>المتطلبات</th>
                    <th>المهام</th>
                    <th>التقديمات</th>
                    <th>الحقول المطلوبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80 dark:divide-zinc-800">
                @foreach ($ads as $ad)
                    @php
                        $requirements = collect([
                            $ad->req1,
                            $ad->req2,
                            $ad->req3,
                            $ad->req4,
                            $ad->req5,
                        ])->filter();

                        $tasks = collect([
                            $ad->task1,
                            $ad->task2,
                            $ad->task3,
                            $ad->task4,
                            $ad->task5,
                        ])->filter();

                        $requiredFields = collect([
                            $ad->github_required ? 'GitHub' : null,
                            $ad->resume_required ? 'CV' : null,
                            $ad->prev_work_required ? 'أعمال سابقة' : null,
                            $ad->expected_salary_required ? 'راتب متوقع' : null,
                        ])->filter();
                    @endphp

                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50 cursor-pointer" wire:click="showItem({{ $ad->id }})">
                        <td>
                            <div class="min-w-0">
                                <p class="truncate font-black text-zinc-900 dark:text-zinc-100">{{ $ad->job_name }}</p>
                                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">#{{ $ad->id }} • {{ optional($ad->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p class="font-black text-zinc-900 dark:text-zinc-100">{{ $ad->company?->username ?: 'بدون شركة' }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $ad->company?->email ?: '---' }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($requirements as $requirement)
                                    <span class="dashboard-badge">{{ $requirement }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لا توجد متطلبات</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                            @if ($tasks->isNotEmpty())
                                {{ $tasks->implode('، ') }}
                            @else
                                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لا توجد مهام</span>
                            @endif
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
                                {{ $ad->applications_count }} تقديم
                            </div>
                        </td>
                        <td>
                            <div class="flex max-w-56 flex-wrap gap-2">
                                @forelse ($requiredFields as $field)
                                    <span class="dashboard-badge">{{ $field }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لا توجد حقول إضافية</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6 dark:border-zinc-800">
            {{ $ads->onEachSide(1)->links() }}
        </div>
        <x-dashboard.detail-modal>
            @if ($this->selectedItem)
                <x-slot:header>
                    <div class="mb-3 grid h-24 w-24 place-items-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        <x-dashboard.icon name="ads" class="size-10" />
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $this->selectedItem->job_name }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zinc-500 dark:text-zinc-400">إعلان توظيف • #{{ $this->selectedItem->id }}</p>
                </x-slot:header>

                <div class="border-t border-brand-100/80 pt-8 dark:border-zinc-800">
                    <div class="mb-8 text-center">
                        <h4 class="mb-4 text-lg font-black text-zinc-900 dark:text-zinc-100">تفاصيل إضافية</h4>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            {{ $this->selectedItem->additional_details ?: 'لا توجد تفاصيل إضافية.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-y-8 text-center">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">الشركة الناشرة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->company?->username ?: 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">عدد التقديمات</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->applications_count }} تقديم</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">تاريخ النشر</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ optional($this->selectedItem->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">الحقول الإضافية المطلوبة</p>
                            <div class="mt-2 flex flex-wrap justify-center gap-2">
                                @php
                                    $requiredFields = collect([
                                        $this->selectedItem->github_required ? 'حساب GitHub' : null,
                                        $this->selectedItem->resume_required ? 'السيرة الذاتية (CV)' : null,
                                        $this->selectedItem->prev_work_required ? 'نماذج أعمال سابقة' : null,
                                        $this->selectedItem->expected_salary_required ? 'الراتب المتوقع' : null,
                                    ])->filter();
                                @endphp
                                @forelse ($requiredFields as $field)
                                    <span class="dashboard-badge">{{ $field }}</span>
                                @empty
                                    <span class="text-sm font-semibold text-zinc-400 dark:text-zinc-500">لا توجد حقول إضافية مطلوبة</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-8 border-t border-brand-100/80 pt-8 md:grid-cols-3 dark:border-zinc-800">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 text-center">متطلبات الوظيفة</p>
                            <ul class="mt-4 list-inside list-disc space-y-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                @foreach (collect([$this->selectedItem->req1, $this->selectedItem->req2, $this->selectedItem->req3, $this->selectedItem->req4, $this->selectedItem->req5])->filter() as $req)
                                    <li>{{ $req }}</li>
                                @endforeach
                            </ul>
                            @if (collect([$this->selectedItem->req1, $this->selectedItem->req2, $this->selectedItem->req3, $this->selectedItem->req4, $this->selectedItem->req5])->filter()->isEmpty())
                                <p class="mt-4 text-center text-sm text-zinc-500 dark:text-zinc-400">لا توجد متطلبات مسجلة.</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 text-center">المهام الوظيفية</p>
                            <ul class="mt-4 list-inside list-disc space-y-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                @foreach (collect([$this->selectedItem->task1, $this->selectedItem->task2, $this->selectedItem->task3, $this->selectedItem->task4, $this->selectedItem->task5])->filter() as $task)
                                    <li>{{ $task }}</li>
                                @endforeach
                            </ul>
                            @if (collect([$this->selectedItem->task1, $this->selectedItem->task2, $this->selectedItem->task3, $this->selectedItem->task4, $this->selectedItem->task5])->filter()->isEmpty())
                                <p class="mt-4 text-center text-sm text-zinc-500 dark:text-zinc-400">لا توجد مهام مسجلة.</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 text-center">المميزات</p>
                            <ul class="mt-4 list-inside list-disc space-y-2 text-sm font-medium text-zinc-600 dark:text-zinc-400">
                                @foreach (collect([$this->selectedItem->feature1, $this->selectedItem->feature2, $this->selectedItem->feature3, $this->selectedItem->feature4, $this->selectedItem->feature5])->filter() as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            @if (collect([$this->selectedItem->feature1, $this->selectedItem->feature2, $this->selectedItem->feature3, $this->selectedItem->feature4, $this->selectedItem->feature5])->filter()->isEmpty())
                                <p class="mt-4 text-center text-sm text-zinc-500 dark:text-zinc-400">لا توجد مميزات مسجلة.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </x-dashboard.detail-modal>
    @else
        <x-dashboard.empty-state title="لا توجد إعلانات " description="" icon="ads" />
    @endif
</x-dashboard.page-shell>