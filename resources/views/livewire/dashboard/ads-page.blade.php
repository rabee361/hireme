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
            <thead class="border-b border-brand-100/80 bg-white/60">
                <tr>
                    <th>الإعلان</th>
                    <th>الشركة</th>
                    <th>المتطلبات</th>
                    <th>المهام</th>
                    <th>التقديمات</th>
                    <th>الحقول المطلوبة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80">
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

                    <tr class="transition hover:bg-brand-50/45">
                        <td>
                            <div class="min-w-0">
                                <p class="truncate font-black text-zinc-900">{{ $ad->job_name }}</p>
                                <p class="text-xs font-semibold text-zinc-500">#{{ $ad->id }} • {{ optional($ad->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p class="font-black text-zinc-900">{{ $ad->company?->username ?: 'بدون شركة' }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500">{{ $ad->company?->email ?: '---' }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($requirements as $requirement)
                                    <span class="dashboard-badge">{{ $requirement }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400">لا توجد متطلبات</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal text-sm leading-7 text-zinc-600">
                            @if ($tasks->isNotEmpty())
                                {{ $tasks->implode('، ') }}
                            @else
                                <span class="text-xs font-semibold text-zinc-400">لا توجد مهام</span>
                            @endif
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700">
                                {{ $ad->applications_count }} تقديم
                            </div>
                        </td>
                        <td>
                            <div class="flex max-w-56 flex-wrap gap-2">
                                @forelse ($requiredFields as $field)
                                    <span class="dashboard-badge">{{ $field }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400">لا توجد حقول إضافية</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6">
            {{ $ads->onEachSide(1)->links() }}
        </div>
    @else
        <x-dashboard.empty-state title="لا توجد إعلانات " description="" icon="ads" />
    @endif
</x-dashboard.page-shell>