<header class="sticky top-0 z-40 flex h-16 w-full justify-start px-4 lg:px-10">
    <div class="flex items-center gap-4">
        <!-- Desktop: Hidden (Sidebar is already visible) -->
        <!-- Mobile: Hamburger Menu -->
        <button
            @click="sidebarOpen = true"
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-zinc-500 hover:bg-zinc-100 focus:outline-none lg:hidden dark:text-zinc-400 dark:hover:bg-zinc-800"
        >
            <span class="sr-only">Open sidebar</span>
            <x-dashboard.icon name="menu" class="h-6 w-6" />
        </button>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        <x-theme-toggle />

        <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-700"></div>

        <div class="flex items-center gap-3">
            <div class="flex flex-col items-end text-sm max-sm:hidden">
                <span class="font-semibold text-zinc-900 dark:text-zinc-100 leading-tight">
                    {{ auth()->user()->name }}
                </span>
                <span class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-tight">
                    test
                </span>
            </div>

            <button type="button" class="group flex items-center gap-2 rounded-full ring-offset-2 transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                @else
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                        {{ auth()->user()->initials() }}
                    </div>
                @endif
            </button>
        </div>
    </div>
</header>
