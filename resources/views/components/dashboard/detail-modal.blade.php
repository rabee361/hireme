<div
    x-data="{ show: @entangle('selectedItemId').live }"
    x-show="show"
    style="display: none;"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-md"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 backdrop-blur-md"
        x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
    >
        <div class="absolute inset-0 bg-zinc-900/60 backdrop-blur-md dark:bg-zinc-950/80"></div>
    </div>

    <!-- Panel -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="dashboard-panel relative mt-0 w-full max-w-2xl transform overflow-hidden p-0 text-right transition-all sm:my-8"
        x-on:click.stop
    >
        <div class="p-6">
            <div class="relative mb-6 pb-6">
                <!-- Close Button positioned at Top Left (since RTL, left is visually the end/opposite of start) -->
                <button type="button" x-on:click="show = false" class="absolute left-0 top-0 rounded-full p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:hover:bg-zinc-800 dark:hover:text-zinc-300">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex flex-col items-center justify-center text-center">
                    {{ $header ?? '' }}
                </div>
            </div>
            
            <div class="space-y-6">
                {{ $slot }}
            </div>
            
            <!-- Hidden footer since we will mostly rely on the X button, or we can keep it inside slot if needed. Removed from here for cleaner profile look -->
        </div>
    </div>
</div>
