@php
    $navigation = [
        [
            'label' => 'الشركات',
            'route' => 'dashboard.companies',
            'icon' => 'companies',
        ],
        [
            'label' => 'العملاء',
            'route' => 'dashboard.customers',
            'icon' => 'customers',
        ],
        [
            'label' => 'المشرفون',
            'route' => 'dashboard.admins',
            'icon' => 'admins',
        ],
        [
            'label' => 'الطلاب',
            'route' => 'dashboard.students',
            'icon' => 'students',
        ],
    ];

    $user = auth()->user();
@endphp

<div>
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-30 bg-zinc-950/30 backdrop-blur-[2px] lg:hidden" @click="sidebarOpen = false"></div>

    <aside
        class="fixed inset-y-0 right-0 z-40 w-[19rem] translate-x-full transform px-4 py-4 transition duration-300 ease-out lg:sticky lg:top-0 lg:block lg:h-screen lg:w-[21rem] lg:translate-x-0 lg:px-5 lg:py-6"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
        @keydown.escape.window="sidebarOpen = false"
    >
        <div class="dashboard-panel flex h-full flex-col overflow-hidden px-4 py-4 sm:px-5">
            <div class="flex items-center justify-center gap-3 border-b border-brand-100/80 pb-4 dark:border-zinc-800">
                <div>
                    <p class="text-3xl font-black text-center tracking-tight text-zinc-900 dark:text-zinc-100">
                        hire<span class="text-brand-600 dark:text-brand-400">Me</span>
                    </p>
                </div>

                <button type="button" class="grid h-10 w-10 place-items-center rounded-[1rem] border border-brand-100 bg-white text-brand-700 lg:hidden dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300" @click="sidebarOpen = false" aria-label="إغلاق القائمة">
                    <x-dashboard.icon name="menu" class="size-5" />
                </button>
            </div>

            <nav class="mt-6 flex-1 space-y-2">
                @foreach ($navigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        wire:current="bg-brand-600 text-white shadow-[0_20px_45px_rgba(21,152,104,0.3)] dark:bg-brand-500 dark:shadow-none"
                        class="group flex items-center gap-3 rounded-[1.5rem] px-4 py-3 text-zinc-700 transition   dark:text-zinc-400 "
                    >
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[1.15rem] border border-brand-100 bg-white text-brand-600 transition  group-[.bg-brand-600]:border-white/20 group-[.bg-brand-600]:bg-white/10 group-[.bg-brand-600]:text-white dark:border-zinc-800 dark:bg-zinc-900 dark:text-brand-400 group-active:scale-95">
                            <x-dashboard.icon :name="$item['icon']" class="size-5" />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-brand-600 group-[.bg-brand-600]:text-white text-sm font-black">{{ $item['label'] }}</span>
                        </span>
                    </a>
                @endforeach
            </nav>

            <form method="POST" action="{{ route('dashboard.logout') }}" class="mt-3">
                @csrf

                <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-[1.5rem] border border-brand-100 bg-white px-4 py-3 text-sm font-black text-zinc-700 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400 dark:hover:border-zinc-700 dark:hover:bg-zinc-900 dark:hover:text-zinc-200">
                    <x-dashboard.icon name="logout" class="size-5" />
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </aside>
</div>