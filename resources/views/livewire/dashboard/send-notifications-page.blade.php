<x-dashboard.page-shell title="إرسال الإشعارات" description="واجهة أولية لإرسال إشعارات فردية أو جماعية عبر Firebase مع حفظها كسجل داخل النظام.">
    <x-slot:toolbar>
        <div>
            <h2 class="text-lg font-black text-zinc-900 dark:text-zinc-100">إعداد الإشعار</h2>
        </div>
    </x-slot:toolbar>

    @if (session('notification-status'))
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300">
            {{ session('notification-status') }}
        </div>
    @endif

    <form wire:submit="send" class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,1fr)]">
        <section class="space-y-5 rounded-[2rem] border border-brand-100/80 bg-white/90 p-6 shadow-[0_30px_60px_-45px_rgba(15,23,42,0.45)] dark:border-zinc-800 dark:bg-zinc-900/70">
            <div class="space-y-2">
                <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">العنوان</label>
                <input wire:model="title" type="text" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100" placeholder="مثال: إعلان جديد للطلاب" />
                @error('title') <p class="text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">المحتوى</label>
                <textarea wire:model="body" rows="6" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100" placeholder="اكتب نص الإشعار هنا"></textarea>
                @error('body') <p class="text-xs font-bold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3">
                <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">نوع الجمهور</label>
                <select wire:model.live="audienceType" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100">
                    <option value="users">مستخدمون محددون</option>
                    <option value="user_type">حسب نوع المستخدم</option>
                    <option value="topic">حسب الموضوع الثابت</option>
                </select>
            </div>

            @if ($audienceType === 'user_type')
                <div class="space-y-2">
                    <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">نوع المستخدم</label>
                    <select wire:model="userType" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100">
                        @foreach ($userTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->value }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($audienceType === 'topic')
                <div class="space-y-2">
                    <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">الموضوع</label>
                    <select wire:model="topic" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100">
                        @foreach ($topics as $item)
                            <option value="{{ $item->value }}">{{ $item->value }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-brand-600 px-5 py-3 text-sm font-black text-white transition hover:bg-brand-500">
                جدولة الإشعار
            </button>
        </section>

        <aside class="space-y-5 rounded-[2rem] border border-brand-100/80 bg-white/90 p-6 shadow-[0_30px_60px_-45px_rgba(15,23,42,0.45)] dark:border-zinc-800 dark:bg-zinc-900/70">
            <div class="space-y-2">
                <label class="text-sm font-black text-zinc-800 dark:text-zinc-200">بحث المستخدمين</label>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full rounded-2xl border border-brand-100 bg-white px-4 py-3 text-sm font-semibold text-zinc-900 outline-none transition focus:border-brand-400 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-100" placeholder="ابحث بالاسم" />
            </div>

            @if ($audienceType === 'users')
                <div class="space-y-3">
                    @foreach ($users as $user)
                        <label class="flex items-center justify-between rounded-2xl border border-brand-100/80 px-4 py-3 text-sm font-semibold text-zinc-700 dark:border-zinc-800 dark:text-zinc-200">
                            <span>{{ $user->username }}</span>
                            <input wire:model="selectedUserIds" type="checkbox" value="{{ $user->id }}" class="h-4 w-4 rounded border-zinc-300 text-brand-600 focus:ring-brand-500" />
                        </label>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-brand-200 px-4 py-5 text-sm font-semibold text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    التحديد اليدوي للمستخدمين يظهر فقط عند اختيار جمهور من المستخدمين المحددين.
                </div>
            @endif
        </aside>
    </form>
</x-dashboard.page-shell>