<div class="space-y-6">
    <section class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-zinc-900 dark:text-zinc-100">إحصائيات المستخدمين والفرص</h1>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">عرض مركّز لعدد الشركات والعملاء والطلاب مع نمو التسجيلات وتوزيع أنواع المستخدمين والفرص.</p>
        </div>

        <label class="inline-flex items-center gap-3 rounded-full border border-brand-100 bg-white/90 px-4 py-2 text-sm font-semibold text-zinc-600 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/80 dark:text-zinc-300">
            <span>الفترة</span>
            <select wire:model.live="range" class="rounded-full bg-transparent pe-7 text-sm font-black text-zinc-900 outline-none dark:text-zinc-100">
                @foreach ($rangeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(20rem,0.82fr)]">
        <div class="grid gap-6">
            <div class="grid gap-4 md:grid-cols-3">
        @foreach ($stats as $stat)
                <article class="dashboard-panel mt-0 px-5 py-5 sm:px-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-bold text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-black tracking-tight text-zinc-900 dark:text-zinc-50">{{ $stat['value'] }}</p>
                            <p class="mt-3 text-xs font-semibold leading-6 text-zinc-500 dark:text-zinc-400">{{ $stat['hint'] }}</p>
                        </div>

                        <div class="grid h-12 w-12 place-items-center rounded-[1.3rem] bg-brand-50 {{ $stat['accent'] }} dark:bg-zinc-900/80">
                            <x-dashboard.icon :name="$stat['icon']" class="size-6" />
                        </div>
                    </div>
                </article>
        @endforeach
            </div>

            <article class="dashboard-panel mt-0 overflow-hidden px-5 py-5 sm:px-6">
                <div class="flex items-center justify-between gap-4 border-b border-brand-100/80 pb-4 dark:border-zinc-800">
                    <div>
                        <p class="text-lg font-black text-zinc-900 dark:text-zinc-100">المستخدمون الجدد</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">ثلاثة خطوط تمثل عدد الشركات والعملاء والطلاب الجدد خلال {{ $selectedRangeLabel }}.</p>
                    </div>
                </div>

                <div class="mt-5 h-96" wire:ignore>
                    <canvas data-dashboard-chart='@json($charts['users_growth'])'></canvas>
                </div>
            </article>
        </div>

        <div class="grid gap-6">
            <article class="dashboard-panel mt-0 overflow-hidden px-5 py-5 sm:px-6">
                <div class="border-b border-brand-100/80 pb-4 dark:border-zinc-800">
                    <p class="text-lg font-black text-zinc-900 dark:text-zinc-100">نسب أنواع المستخدمين</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">توزيع الشركات والعملاء والطلاب داخل النظام.</p>
                </div>

                <div class="mt-5 h-72" wire:ignore>
                    <canvas data-dashboard-chart='@json($charts['user_types'])'></canvas>
                </div>
            </article>

            <article class="dashboard-panel mt-0 overflow-hidden px-5 py-5 sm:px-6">
                <div class="border-b border-brand-100/80 pb-4 dark:border-zinc-800">
                    <p class="text-lg font-black text-zinc-900 dark:text-zinc-100">نسب أنواع الفرص</p>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">توزيع الإعلانات مقابل المشاريع داخل المنصة.</p>
                </div>

                <div class="mt-5 h-72" wire:ignore>
                    <canvas data-dashboard-chart='@json($charts['opportunities'])'></canvas>
                </div>
            </article>
        </div>
    </section>
</div>