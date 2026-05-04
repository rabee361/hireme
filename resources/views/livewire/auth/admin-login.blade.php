<div class="flex justify-center w-full gap-6 items-center">

    <section class="dashboard-panel p-8 sm:p-10">
        <div class="space-y-6">
            <div class="space-y-3 text-center lg:text-right">
                <h2 class="text-3xl text-center font-black text-zinc-900">تسجيل الدخول </h2>
            </div>

            @if (session('status'))
                <div class="rounded-[1.5rem] border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-700">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login" class="space-y-4">
                <div class="space-y-2">
                    <label for="email" class="text-sm font-black text-zinc-800">البريد الإلكتروني</label>
                    <input id="email" type="email" wire:model.blur="email" class="dashboard-input" placeholder="admin@hireme.test" dir="ltr" autocomplete="email">
                    @error('email')
                        <p class="text-sm font-semibold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-black text-zinc-800">كلمة المرور</label>
                    <input id="password" type="password" wire:model.blur="password" class="dashboard-input" placeholder="********" autocomplete="current-password">
                    @error('password')
                        <p class="text-sm font-semibold text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 rounded-[1.25rem] border border-brand-100 bg-brand-50/70 px-4 py-3 text-sm font-semibold text-zinc-600">
                    <input type="checkbox" wire:model="remember" class="h-4 w-4 rounded border-brand-300 text-brand-600 focus:ring-brand-200">
                    <span>تذكرني على هذا الجهاز</span>
                </label>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-[1.5rem] bg-brand-600 px-4 py-3 text-sm font-black text-white shadow-[0_22px_45px_rgba(21,152,104,0.26)] transition hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-70" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="login">دخول لوحة التحكم</span>
                    <span wire:loading wire:target="login">جارٍ التحقق...</span>
                </button>
            </form>

        </div>
    </section>
</div>