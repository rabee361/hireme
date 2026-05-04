<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full scroll-smooth">
    <head>
        @include('partials.head', ['title' => $title ?? 'دخول لوحة التحكم'])
    </head>
    <body class="dashboard-shell min-h-screen font-sans text-zinc-900 antialiased">
        <div class="fixed top-4 left-4 z-50">
            <x-theme-toggle />
        </div>
        <div class="dashboard-grid pointer-events-none absolute inset-0 opacity-60"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-[radial-gradient(circle_at_top,rgba(79,208,157,0.24),transparent_68%)]"></div>

        <main class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>