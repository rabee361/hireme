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
            <thead class="border-b border-brand-100/80 bg-white/60">
                <tr>
                    <th>المشروع</th>
                    <th>العميل</th>
                    <th>التفاصيل</th>
                    <th>الأدوات</th>
                    <th>التقديمات</th>
                    <th>الإنشاء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80">
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

                    <tr class="transition hover:bg-brand-50/45">
                        <td>
                            <div class="min-w-0">
                                <p class="truncate font-black text-zinc-900">{{ $project->name }}</p>
                                <p class="text-xs font-semibold text-zinc-500">#{{ $project->id }}</p>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p class="font-black text-zinc-900">{{ $project->customer?->username ?: 'بدون عميل' }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500">{{ $project->customer?->email ?: '---' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal leading-7 text-zinc-600">
                            {{ $project->details ?: 'لا توجد تفاصيل' }}
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($tools as $tool)
                                    <span class="dashboard-badge">{{ $tool }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400">لا توجد أدوات</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700">
                                {{ $project->applications_count }} تقديم
                            </div>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900">{{ optional($project->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500">{{ optional($project->created_at)->locale('ar')->translatedFormat('h:i A') }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6">
            {{ $projects->onEachSide(1)->links() }}
        </div>
    @else
        <x-dashboard.empty-state title="لا توجد مشاريع " description="" icon="projects" />
    @endif
</x-dashboard.page-shell>