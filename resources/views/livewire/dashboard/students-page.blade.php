<x-dashboard.page-shell title="سجل الطلاب" description="لوحة متابعة للطلاب تضم الخلفية التعليمية، المهارات، وسجل التقديمات على الإعلانات والمشاريع.">
    <x-slot:actions>
        <div class="dashboard-panel flex items-center gap-3 px-4 py-3">
            <div class="grid h-12 w-12 place-items-center rounded-[1.25rem] bg-brand-600 text-white">
                <x-dashboard.icon name="students" class="size-6" />
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-500">إجمالي النتائج</p>
                <p class="text-lg font-black text-zinc-900">{{ $students->total() }}</p>
            </div>
        </div>
    </x-slot:actions>

    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900">قائمة الطلاب</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن طالب" />
        </div>
    </x-slot:toolbar>

    @if ($students->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60">
                <tr>
                    <th>الطالب</th>
                    <th>التواصل</th>
                    <th>المسمى والكلية</th>
                    <th>المهارات</th>
                    <th>الخبرة</th>
                    <th>التقديمات</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80">
                @foreach ($students as $student)
                    @php
                        $profile = $student->profile;
                        $techs = collect([
                            $profile?->tech1,
                            $profile?->tech2,
                            $profile?->tech3,
                        ])->filter();
                        $totalApplications = (int) ($profile?->ad_applications_count ?? 0) + (int) ($profile?->project_applications_count ?? 0);
                    @endphp

                    <tr class="transition hover:bg-brand-50/45">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white">
                                    {{ $student->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-black text-zinc-900">{{ $student->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500">#{{ $student->id }} • {{ optional($student->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-left font-semibold text-zinc-700">{{ $student->email }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500">{{ $student->phone_number ?: 'بدون رقم مسجل' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal">
                            <p class="font-black text-zinc-900">{{ $profile?->title ?: 'بدون مسمى محدد' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500">{{ $profile?->college ?: 'بدون كلية مسجلة' }}</p>
                        </td>
                        <td>
                            <div class="flex max-w-[16rem] flex-wrap gap-2">
                                @forelse ($techs as $tech)
                                    <span class="dashboard-badge">{{ $tech }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400">لا توجد مهارات مسجلة</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900">{{ $profile?->experience_years ? $profile->experience_years.' سنة' : 'غير محددة' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500">{{ $profile?->hour_cost ? $profile->hour_cost.' $ / ساعة' : 'لا توجد تكلفة مسجلة' }}</p>
                        </td>
                        <td>
                            <div class="space-y-1 text-sm font-black text-brand-700">
                                <p>{{ $totalApplications }} إجمالي</p>
                                <p class="text-xs font-semibold text-zinc-500">{{ $profile?->ad_applications_count ?? 0 }} إعلانات • {{ $profile?->project_applications_count ?? 0 }} مشاريع</p>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $student->is_verified ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $student->is_verified ? 'مفعّل' : 'بانتظار التفعيل' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6">
            {{ $students->onEachSide(1)->links() }}
        </div>
    @else
        <x-dashboard.empty-state title="لا توجد حسابات طلاب " description="جرّب توسيع البحث أو أنشئ حسابات طلاب جديدة ليبدأ هذا القسم بعرض الملفات والتقديمات." icon="students" />
    @endif
</x-dashboard.page-shell>