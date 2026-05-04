<x-dashboard.page-shell title="سجل العملاء" description="عرض منظم لحسابات العملاء أصحاب المشاريع، مع لمحة سريعة عن الخبرة المهنية وعدد المشاريع المنشورة.">

    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900">قائمة العملاء</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن عميل" />
        </div>
    </x-slot:toolbar>

    @if ($customers->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60">
                <tr>
                    <th>العميل</th>
                    <th>التواصل</th>
                    <th>المسمى والخلفية</th>
                    <th>الخبرة والتكلفة</th>
                    <th>المشاريع</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80">
                @foreach ($customers as $customer)
                    @php
                        $profile = $customer->profile;
                    @endphp

                    <tr class="transition hover:bg-brand-50/45">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white">
                                    {{ $customer->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-black text-zinc-900">{{ $customer->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500">#{{ $customer->id }} • {{ optional($customer->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-left font-semibold text-zinc-700">{{ $customer->email }}</p>
                                <p dir="ltr" class="text-left text-xs font-semibold text-zinc-500">{{ $customer->phone_number ?: 'لا يوجد رقم مسجل' }}</p>
                            </div>
                        </td>
                        <td class="max-w-xs whitespace-normal">
                            <p class="font-black text-zinc-900">{{ $profile?->title ?: 'بدون مسمى وظيفي' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500">{{ $profile?->college ?: 'بدون جهة تعليمية' }}</p>
                            <p class="mt-3 text-sm leading-7 text-zinc-600">{{ $customer->description ?: 'لا يوجد وصف مختصر.' }}</p>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900">{{ $profile?->experience_years ? $profile->experience_years.' سنة' : 'غير محددة' }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500">{{ $profile?->hour_cost ? $profile->hour_cost.' $ / ساعة' : 'لا توجد تكلفة مسجلة' }}</p>
                        </td>
                        <td>
                            <div class="inline-flex items-center rounded-full bg-brand-50 px-3 py-2 text-sm font-black text-brand-700">
                                {{ $customer->projects_count }} مشروع
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $customer->is_verified ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $customer->is_verified ? 'مفعّل' : 'بانتظار التفعيل' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6">
            {{ $customers->onEachSide(1)->links() }}
        </div>
    @else
        <x-dashboard.empty-state title="لا توجد حسابات عملاء " description="يمكنك استخدام حقل البحث بشكل أوسع أو إضافة عملاء جدد لتعبئة هذه الصفحة." icon="customers" />
    @endif
</x-dashboard.page-shell>