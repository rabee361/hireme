<div class="flex justify-center w-full gap-6 items-center">

    <section class="dashboard-panel p-8 sm:p-10">
        <div class="space-y-6">
            <div class="space-y-3 text-center lg:text-right">
                <h2 class="text-3xl text-center font-black text-zinc-900 dark:text-zinc-100">تسجيل الدخول </h2>
            </div>

            @if (session('status'))
                <div class="rounded-[1.5rem] border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-700 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-sm font-black text-zinc-800 dark:text-zinc-300">البريد الإلكتروني</label>
                    <input id="email" type="email" wire:model.blur="email" class="dashboard-input" placeholder="admin@hireme.test" dir="ltr" autocomplete="email">
                    @error('email')
                        <p class="text-sm font-semibold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-black text-zinc-800 dark:text-zinc-300">كلمة المرور</label>
                    <input id="password" type="password" wire:model.blur="password" class="dashboard-input" placeholder="********" autocomplete="current-password">
                    @error('password')
                        <p class="text-sm font-semibold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 rounded-[1.25rem] border border-brand-100 bg-brand-50/70 px-4 py-3 text-sm font-semibold text-zinc-600 dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400">
                    <input type="checkbox" wire:model="remember" class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-brand-500">
                    <span>تذكرني على هذا الجهاز</span>
                </label>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-[1.5rem] bg-brand-600 px-4 py-3 text-sm font-black text-white shadow-[0_22px_45px_rgba(21,152,104,0.26)] transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-70 dark:bg-brand-500 dark:shadow-none dark:hover:bg-brand-600" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="login">دخول لوحة التحكم</span>
                    <span wire:loading wire:target="login">جارٍ التحقق...</span>
                </button>
            </form>

        </div>
    </section>
</div>