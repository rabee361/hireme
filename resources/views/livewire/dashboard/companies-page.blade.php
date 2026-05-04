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
            <thead class="border-b border-brand-100/80 bg-white/60">
                <tr>
                    <th>الشركة</th>
                    <th>التواصل</th>
                    <th>الوصف</th>
                    <th>التقنيات</th>
                    <th>الإعلانات</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80">
                @foreach ($companies as $company)
                    @php
                        $techs = collect([
                            $company->profile?->tech1,
                            $company->profile?->tech2,
                            $company->profile?->tech3,
                        ])->filter();
                    @endphp

                    <tr class="transition hover:bg-brand-50/45">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white">
                                    {{ $company->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-black text-zinc-900">{{ $company->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500">#{{ $company->id }} • {{ optional($company->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-left font-semibold text-zinc-700">{{ $company->email }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500">{{ $company->phone_number ?: 'لا يوجد رقم مسجل' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal leading-7 text-zinc-600">
                            {{ $company->description ?: 'لا يوجد وصف حتى الآن.' }}
                        </td>
                        <td>
                            <div class="flex max-w-[18rem] flex-wrap gap-2">
                                @forelse ($techs as $tech)
                                    <span class="dashboard-badge">{{ $tech }}</span>
                                @empty
                                    <span class="text-xs font-semibold text-zinc-400">لم تُحدد تقنيات بعد</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700">
                                {{ $company->ads_count }} إعلان
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $company->is_verified ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $company->is_verified ? 'مفعلة' : 'بانتظار التفعيل' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6">
            {{ $companies->onEachSide(1)->links() }}
        </div>
    @else
        <x-dashboard.empty-state title="لا توجد شركات " description="جرّب تعديل كلمات البحث أو أضف شركات جديدة إلى النظام ليظهر هذا القسم بالبيانات." icon="companies" />
    @endif
</x-dashboard.page-shell>