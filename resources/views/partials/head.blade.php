<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<script>
    window.HiremeeTheme = (() => {
        const primaryKey = 'hiremee-theme';
        const legacyKey = 'theme';
        let currentTheme = 'light';
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

        const systemTheme = () => mediaQuery.matches ? 'dark' : 'light';

        const getStoredTheme = () => {
            const primaryTheme = localStorage.getItem(primaryKey);

            if (primaryTheme === 'dark' || primaryTheme === 'light') {
                return primaryTheme;
            }

            const legacyTheme = localStorage.getItem(legacyKey);

            if (legacyTheme === 'dark' || legacyTheme === 'light') {
                return legacyTheme;
            }

            return null;
        };

        const resolvedTheme = () => getStoredTheme() ?? systemTheme();

        const applyTheme = (theme, persist = true) => {
            const nextTheme = theme === 'dark' ? 'dark' : 'light';

            document.documentElement.classList.toggle('dark', nextTheme === 'dark');
            document.documentElement.style.colorScheme = nextTheme;

            if (persist) {
                localStorage.setItem(primaryKey, nextTheme);
                localStorage.setItem(legacyKey, nextTheme);
            }

            currentTheme = nextTheme;

            return nextTheme;
        };

        const syncTheme = () => applyTheme(resolvedTheme(), false);

        const clearStoredTheme = () => {
            localStorage.removeItem(primaryKey);
            localStorage.removeItem(legacyKey);
        };

        syncTheme();
        document.addEventListener('livewire:navigated', syncTheme);
        window.addEventListener('pageshow', syncTheme);
        mediaQuery.addEventListener('change', () => {
            if (!getStoredTheme()) {
                syncTheme();
            }
        });

        return {
            get: resolvedTheme,
            set: (theme) => applyTheme(theme, true),
            clear: () => {
                clearStoredTheme();

                return syncTheme();
            },
            toggle: () => applyTheme(resolvedTheme() === 'dark' ? 'light' : 'dark', true),
            sync: syncTheme,
            current: () => currentTheme,
        };
    })();
</script>

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
