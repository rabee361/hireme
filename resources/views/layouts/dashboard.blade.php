<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
    <head>
        @include('partials.head', ['title' => $title ?? 'لوحة التحكم'])
    </head>
    <body class="dashboard-shell min-h-screen font-sans text-zinc-900 antialiased">
        <div x-data="{ sidebarOpen: false }" class="relative min-h-screen overflow-hidden">
            <div class="dashboard-grid pointer-events-none absolute inset-0 opacity-60"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-[radial-gradient(circle_at_top,_rgba(79,208,157,0.24),_transparent_70%)]"></div>

            <div class="relative flex min-h-screen flex-col lg:flex-row-reverse">
                <x-dashboard.sidebar />

                <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:ps-2">
                    <x-dashboard.topbar />

                    <main class="flex-1 px-4 pb-6 pt-2 sm:px-6 lg:px-10 lg:pb-10 lg:pt-0">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>