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
                    <tr class="transition hover:bg-brand-50/45 dark:hover:bg-zinc-800/50">
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
    @else
        <x-dashboard.empty-state title="لا توجد حسابات إدارة " description="أنشئ مشرفين جدد أو عدّل كلمة البحث لإظهار النتائج المطلوبة داخل صفحة المشرفين." icon="admins" />
    @endif
</x-dashboard.page-shell>