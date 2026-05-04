@props([
    'name',
])

@switch($name)
    @case('companies')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M5.25 21V7.5A2.25 2.25 0 0 1 7.5 5.25h9A2.25 2.25 0 0 1 18.75 7.5V21M9 9.75h.008v.008H9V9.75Zm0 3h.008v.008H9v-.008Zm0 3h.008v.008H9v-.008Zm6-6h.008v.008H15V9.75Zm0 3h.008v.008H15v-.008Zm0 3h.008v.008H15v-.008Z" />
        </svg>
        @break

    @case('customers')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25V6.75A2.25 2.25 0 0 1 11.25 4.5h7.5A2.25 2.25 0 0 1 21 6.75v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5A2.25 2.25 0 0 1 9 14.25ZM3 9.75A2.25 2.25 0 0 1 5.25 7.5H9v9H5.25A2.25 2.25 0 0 1 3 14.25v-4.5Z" />
        </svg>
        @break

    @case('admins')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M4.5 12c0-5.186 3.814-7.542 6.967-8.713a1.71 1.71 0 0 1 1.066 0C15.686 4.458 19.5 6.814 19.5 12c0 5.187-3.814 7.543-6.967 8.714a1.71 1.71 0 0 1-1.066 0C8.314 19.543 4.5 17.187 4.5 12Z" />
        </svg>
        @break

    @case('students')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.26 10.147 7.38-4.199a.75.75 0 0 1 .72 0l7.38 4.2a.75.75 0 0 1 0 1.304l-7.38 4.2a.75.75 0 0 1-.72 0l-7.38-4.2a.75.75 0 0 1 0-1.305ZM6.75 12.94v4.87a.75.75 0 0 0 .39.658l4.5 2.559a.75.75 0 0 0 .72 0l4.5-2.56a.75.75 0 0 0 .39-.657v-4.87" />
        </svg>
        @break

    @case('search')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z" />
        </svg>
        @break

    @case('menu')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        @break

    @case('logout')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m4.5-3H9.75m0 0 3-3m-3 3 3 3" />
        </svg>
        @break

    @case('shield')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M4.5 12c0-5.186 3.814-7.542 6.967-8.713a1.71 1.71 0 0 1 1.066 0C15.686 4.458 19.5 6.814 19.5 12c0 5.187-3.814 7.543-6.967 8.714a1.71 1.71 0 0 1-1.066 0C8.314 19.543 4.5 17.187 4.5 12Z" />
        </svg>
        @break

    @case('chart')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v18m0 0H21m-13.5-4.5 3.75-3.75 3 3 5.25-6.75" />
        </svg>
        @break

    @case('spark')
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.813 15.904.126.63a3.375 3.375 0 0 0 2.483 2.482l.63.126-.63.126a3.375 3.375 0 0 0-2.482 2.483l-.126.63-.126-.63a3.375 3.375 0 0 0-2.483-2.482l-.63-.126.63-.126a3.375 3.375 0 0 0 2.482-2.483l.126-.63ZM18.25 10.5l.084.418a2.25 2.25 0 0 0 1.648 1.648l.418.084-.418.084a2.25 2.25 0 0 0-1.648 1.648l-.084.418-.084-.418a2.25 2.25 0 0 0-1.648-1.648l-.418-.084.418-.084a2.25 2.25 0 0 0 1.648-1.648l.084-.418ZM16.5 3.75l.063.315a2.25 2.25 0 0 0 1.622 1.622l.315.063-.315.063a2.25 2.25 0 0 0-1.622 1.622l-.063.315-.063-.315a2.25 2.25 0 0 0-1.622-1.622l-.315-.063.315-.063a2.25 2.25 0 0 0 1.622-1.622l.063-.315Z" />
        </svg>
        @break

    @default
        <svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.8', 'class' => 'size-5']) }}>
            <circle cx="12" cy="12" r="8" />
        </svg>
@endswitch