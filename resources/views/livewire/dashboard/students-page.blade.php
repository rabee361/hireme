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
            <thead class="border-b border-brand-100/80 bg-white/60 dark:border-zinc-800 dark:bg-zinc-900/40">
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
            <tbody class="divide-y divide-brand-50/80 dark:divide-zinc-800">
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

                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50 cursor-pointer" wire:click="showItem({{ $student->id }})">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white dark:bg-brand-500">
                                    {{ $student->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-black text-zinc-900 dark:text-zinc-100">{{ $student->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">#{{ $student->id }} • {{ optional($student->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-left font-semibold text-zinc-700 dark:text-zinc-300">{{ $student->email }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $student->phone_number ?: 'بدون رقم مسجل' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal">
                            <p class="font-black text-zinc-900 dark:text-zinc-100">{{ $profile?->title ?: 'بدون مسمى محدد' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $profile?->college ?: 'بدون كلية مسجلة' }}</p>
                        </td>
                        <td>
                            <div class="flex max-w-[16rem] flex-wrap gap-2">
                                @forelse ($techs as $tech)
                                    <span class="dashboard-badge">{{ $tech }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لا توجد مهارات مسجلة</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900 dark:text-zinc-100">{{ $profile?->experience_years ? $profile->experience_years.' سنة' : 'غير محددة' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $profile?->hour_cost ? $profile->hour_cost.' $ / ساعة' : 'لا توجد تكلفة مسجلة' }}</p>
                        </td>
                        <td>
                            <div class="space-y-1 text-sm font-black text-brand-700 dark:text-brand-400">
                                <p>{{ $totalApplications }} إجمالي</p>
                                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $profile?->ad_applications_count ?? 0 }} إعلانات • {{ $profile?->project_applications_count ?? 0 }} مشاريع</p>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $student->is_verified ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ $student->is_verified ? 'مفعّل' : 'بانتظار التفعيل' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6 dark:border-zinc-800">
            {{ $students->onEachSide(1)->links() }}
        </div>
        <x-dashboard.detail-modal>
            @if ($this->selectedItem)
                <x-slot:header>
                    <div class="mb-3 grid h-24 w-24 place-items-center rounded-full bg-brand-100 text-3xl font-black text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        {{ $this->selectedItem->initials() }}
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $this->selectedItem->username }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zinc-500 dark:text-zinc-400">طالب مسجل • #{{ $this->selectedItem->id }}</p>
                    
                    <div class="mt-6 flex justify-center gap-4">
                        <a href="tel:{{ $this->selectedItem->phone_number }}" class="grid h-12 w-12 place-items-center rounded-full bg-zinc-50 text-emerald-600 transition hover:bg-emerald-50 dark:bg-zinc-800/50 dark:text-emerald-400 dark:hover:bg-emerald-900/20" title="اتصال">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </a>
                        <a href="mailto:{{ $this->selectedItem->email }}" class="grid h-12 w-12 place-items-center rounded-full bg-zinc-50 text-brand-600 transition hover:bg-brand-50 dark:bg-zinc-800/50 dark:text-brand-400 dark:hover:bg-brand-900/20" title="مراسلة">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </a>
                    </div>
                </x-slot:header>

                <div class="border-t border-brand-100/80 pt-8 dark:border-zinc-800">
                    <div class="mb-8 text-center">
                        <h4 class="mb-4 text-lg font-black text-zinc-900 dark:text-zinc-100">حول</h4>
                        <p class="text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                            {{ $this->selectedItem->description ?: 'لا يوجد وصف حتى الآن.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-y-8 text-center">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">المسمى الوظيفي والجهة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $this->selectedItem->profile?->title ?: 'غير متوفر' }}<br>
                                <span class="text-xs">{{ $this->selectedItem->profile?->college ?: 'بدون جهة تعليمية' }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">تكلفة الساعة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->profile?->hour_cost ? $this->selectedItem->profile->hour_cost . ' $' : 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">إجمالي التقديمات</p>
                            @php
                                $adCount = $this->selectedItem->profile?->ad_applications_count ?? 0;
                                $projectCount = $this->selectedItem->profile?->project_applications_count ?? 0;
                                $total = $adCount + $projectCount;
                            @endphp
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $total }} تقديم</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">سنوات الخبرة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->profile?->experience_years ? $this->selectedItem->profile->experience_years . ' سنة' : 'غير متوفر' }}</p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">حالة الحساب</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->is_verified ? 'مفعّل' : 'بانتظار التفعيل' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </x-dashboard.detail-modal>
    @else
        <x-dashboard.empty-state title="لا توجد حسابات طلاب " description="جرّب توسيع البحث أو أنشئ حسابات طلاب جديدة ليبدأ هذا القسم بعرض الملفات والتقديمات." icon="students" />
    @endif
</x-dashboard.page-shell>