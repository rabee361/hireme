<x-dashboard.page-shell title="سجل الشركات" description="واجهة متابعة مركزة تعرض الشركات، بياناتها الأساسية، وحجم النشاط الإعلاني بطريقة سهلة للمراجعة السريعة.">
    <x-slot:actions>
        <div class="dashboard-panel flex items-center gap-3 px-4 py-3">
            <div class="grid h-12 w-12 place-items-center rounded-[1.25rem] bg-brand-600 text-white">
                <x-dashboard.icon name="companies" class="size-6" />
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-500">إجمالي النتائج</p>
                <p class="text-lg font-black text-zinc-900">{{ $companies->total() }}</p>
            </div>
        </div>
    </x-slot:actions>

    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900">قائمة الشركات</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن شركة" />
        </div>
    </x-slot:toolbar>

    @if ($companies->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60 dark:border-zinc-800 dark:bg-zinc-900/40">
                <tr>
                    <th>الشركة</th>
                    <th>التواصل</th>
                    <th>الوصف</th>
                    <th>التقنيات</th>
                    <th>الإعلانات</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80 dark:divide-zinc-800">
                @foreach ($companies as $company)
                    @php
                        $techs = collect([
                            $company->profile?->tech1,
                            $company->profile?->tech2,
                            $company->profile?->tech3,
                        ])->filter();
                    @endphp

                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50 cursor-pointer" wire:click="showItem({{ $company->id }})">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white dark:bg-brand-500">
                                    {{ $company->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-black text-zinc-900 dark:text-zinc-100">{{ $company->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">#{{ $company->id }} • {{ optional($company->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-left font-semibold text-zinc-700 dark:text-zinc-300">{{ $company->email }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ $company->phone_number ?: 'لا يوجد رقم مسجل' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal leading-7 text-zinc-600 dark:text-zinc-400">
                            {{ $company->description ?: 'لا يوجد وصف حتى الآن.' }}
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($techs as $tech)
                                    <span class="dashboard-badge">{{ $tech }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500">لم تُحدد تقنيات بعد</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">
                                {{ $company->ads_count }} إعلان
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $company->is_verified ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ $company->is_verified ? 'مفعلة' : 'بانتظار التفعيل' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6 dark:border-zinc-800">
            {{ $companies->onEachSide(1)->links() }}
        </div>
        <x-dashboard.detail-modal>
            @if ($this->selectedItem)
                <x-slot:header>
                    <div class="mb-3 grid h-24 w-24 place-items-center rounded-full bg-brand-100 text-3xl font-black text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        {{ $this->selectedItem->initials() }}
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $this->selectedItem->username }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zinc-500 dark:text-zinc-400">شركة مسجلة • #{{ $this->selectedItem->id }}</p>
                    
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
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">الصناعة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->profile?->industry ?: 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">حجم الشركة</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400" dir="ltr">{{ $this->selectedItem->profile?->employees_count ? $this->selectedItem->profile->employees_count . ' موظف' : 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">سنة التأسيس</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->profile?->started_at ? $this->selectedItem->profile->started_at->locale('ar')->translatedFormat('Y') : 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">إجمالي الإعلانات</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->ads_count }} إعلان</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">الموقع الإلكتروني</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                @if ($this->selectedItem->profile?->website)
                                    <a href="{{ $this->selectedItem->profile->website }}" target="_blank" class="text-brand-600 hover:underline dark:text-brand-400" dir="ltr">زيارة الموقع</a>
                                @else
                                    غير متوفر
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">حالة الحساب</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->is_verified ? 'مفعّل' : 'بانتظار التفعيل' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </x-dashboard.detail-modal>
    @else
        <x-dashboard.empty-state title="لا توجد شركات " description="جرّب تعديل كلمات البحث أو أضف شركات جديدة إلى النظام ليظهر هذا القسم بالبيانات." icon="companies" />
    @endif
</x-dashboard.page-shell>