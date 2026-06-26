<x-dashboard.page-shell title="سجل المشرفين" description="متابعة صلاحيات الإدارة، حالة التفعيل، ومستوى الأمان الخاص بالحسابات التي تدير المنصة.">

    <x-slot:toolbar>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-black text-zinc-900 dark:text-zinc-100">قائمة المشرفين</h2>
            </div>

            <x-dashboard.search-input placeholder="ابحث عن مشرف" />
        </div>
    </x-slot:toolbar>

    @if ($admins->count())
        <table class="dashboard-table min-w-full">
            <thead class="border-b border-brand-100/80 bg-white/60 dark:border-zinc-800 dark:bg-zinc-900/40">
                <tr>
                    <th>المشرف</th>
                    <th>ايميل</th>
                    <th>التحقق</th>
                    <th>الانضمام</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50/80 dark:divide-zinc-800">
                @foreach ($admins as $admin)
                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50 cursor-pointer" wire:click="showItem({{ $admin->id }})">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-[1.2rem] bg-brand-600 text-sm font-black text-white dark:bg-brand-500">
                                    {{ $admin->initials() }}
                                </div>
                                <div>
                                    <p class="font-black text-zinc-900 dark:text-zinc-100">{{ $admin->username }}</p>
                                    <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">#{{ $admin->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                <p dir="ltr" class="text-center font-semibold text-zinc-700 dark:text-zinc-300">{{ $admin->email }}</p>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-3 py-2 text-xs font-black {{ $admin->is_verified ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ $admin->is_verified ? 'موثّق' : 'غير موثّق' }}
                            </span>
                        </td>
                        <td>
                            <p class="font-black text-zinc-900 dark:text-zinc-100">{{ optional($admin->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                            <p class="mt-1 text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ optional($admin->created_at)->locale('ar')->translatedFormat('h:i A') }}</p>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-brand-100/80 px-5 py-4 sm:px-6 dark:border-zinc-800">
            {{ $admins->onEachSide(1)->links() }}
        </div>
        <x-dashboard.detail-modal>
            @if ($this->selectedItem)
                <x-slot:header>
                    <div class="mb-3 grid h-24 w-24 place-items-center rounded-full bg-brand-100 text-3xl font-black text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        {{ $this->selectedItem->initials() }}
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $this->selectedItem->username }}</h3>
                    <p class="mt-1 text-sm font-semibold text-zinc-500 dark:text-zinc-400">مشرف نظام • #{{ $this->selectedItem->id }}</p>
                    
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
                    <div class="grid grid-cols-2 gap-y-8 text-center">
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">تاريخ الانضمام</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ optional($this->selectedItem->created_at)->locale('ar')->translatedFormat('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">التحقق الثنائي (2FA)</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                                @if ($this->selectedItem->two_factor_confirmed_at)
                                    <span class="text-emerald-600 dark:text-emerald-400">مفعل</span>
                                @else
                                    <span class="text-zinc-500 dark:text-zinc-400">غير مفعل</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-span-full">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">حالة الحساب</p>
                            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $this->selectedItem->is_verified ? 'مفعّل' : 'غير مفعّل' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </x-dashboard.detail-modal>
    @else
        <x-dashboard.empty-state title="لا توجد حسابات إدارة " description="أنشئ مشرفين جدد أو عدّل كلمة البحث لإظهار النتائج المطلوبة داخل صفحة المشرفين." icon="admins" />
    @endif
</x-dashboard.page-shell>