@props(['name', 'class' => 'h-5 w-5'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
            @break

        @case('wallet')
            <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5H18a2 2 0 0 1 2 2v2H6.5A2.5 2.5 0 0 0 4 11.5v0A2.5 2.5 0 0 0 6.5 14H20v4a2 2 0 0 1-2 2H6.5A2.5 2.5 0 0 1 4 17.5v-10Z" />
            <path d="M16.5 14h.01" />
            @break

        @case('transfer')
            <path d="M4 7h13" />
            <path d="m14 4 3 3-3 3" />
            <path d="M20 17H7" />
            <path d="m10 14-3 3 3 3" />
            @break

        @case('tag')
            <path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0l-7.2-7.2A2 2 0 0 1 3 12V5a2 2 0 0 1 2-2h7a2 2 0 0 1 1.4.6l7.2 7.2a2 2 0 0 1 0 2.6Z" />
            <path d="M7.5 7.5h.01" />
            @break

        @case('target')
            <circle cx="12" cy="12" r="9" />
            <circle cx="12" cy="12" r="5" />
            <circle cx="12" cy="12" r="1.5" />
            @break

        @case('trophy')
            <path d="M8 4h8v5a4 4 0 0 1-8 0V4Z" />
            <path d="M8 6H5a3 3 0 0 0 3 3" />
            <path d="M16 6h3a3 3 0 0 1-3 3" />
            <path d="M12 13v4" />
            <path d="M9 21h6" />
            <path d="M10 17h4l.5 4h-5l.5-4Z" />
            @break

        @case('logout')
            <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3" />
            <path d="M10 17l5-5-5-5" />
            <path d="M15 12H3" />
            @break

        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break

        @case('search')
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.2-3.2" />
            @break

        @case('pencil')
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" />
            @break

        @case('trash')
            <path d="M3 6h18" />
            <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
            <path d="M10 11v6M14 11v6" />
            @break

        @case('eye')
            <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z" />
            <circle cx="12" cy="12" r="3" />
            @break

        @case('eye-off')
            <path d="M3 3l18 18" />
            <path d="M10.6 6.1A9.9 9.9 0 0 1 12 6c6 0 9.5 6 9.5 6a17 17 0 0 1-2.1 2.8" />
            <path d="M6.6 6.6A17 17 0 0 0 2.5 12S6 18 12 18a9.6 9.6 0 0 0 3.4-.6" />
            <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" />
            @break

        @case('arrow-up-right')
            <path d="M7 17 17 7" />
            <path d="M8 7h9v9" />
            @break

        @case('arrow-down-left')
            <path d="M17 7 7 17" />
            <path d="M16 17H7V8" />
            @break

        @case('arrow-left')
            <path d="M19 12H5" />
            <path d="m11 18-6-6 6-6" />
            @break

        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break

        @case('chevron-right')
            <path d="m9 6 6 6-6 6" />
            @break

        @case('x')
            <path d="M18 6 6 18M6 6l12 12" />
            @break

        @case('check')
            <path d="m4 12.5 5 5L20 6.5" />
            @break

        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break

        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M8 3v4M16 3v4M3 10h18" />
            @break

        @case('banknotes')
            <rect x="3" y="6" width="18" height="12" rx="2" />
            <circle cx="12" cy="12" r="2.5" />
            <path d="M6 9v6M18 9v6" />
            @break

        @case('clock')
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
            @break

        @case('alert')
            <path d="M10.3 3.9 2.3 18a2 2 0 0 0 1.7 3h16a2 2 0 0 0 1.7-3l-8-14.1a2 2 0 0 0-3.4 0Z" />
            <path d="M12 9v4M12 17h.01" />
            @break

        @case('sparkles')
            <path d="M12 3l1.4 4.1L17.5 8.5l-4.1 1.4L12 14l-1.4-4.1L6.5 8.5l4.1-1.4L12 3Z" />
            <path d="M18.5 14.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2Z" />
            <path d="M5 14l.6 1.7L7.3 16l-1.7.6L5 18.3l-.6-1.7L2.7 16l1.7-.6L5 14Z" />
            @break

        @case('trend-up')
            <path d="M3 17l6-6 4 4 8-8" />
            <path d="M15 7h6v6" />
            @break

        @case('user')
            <circle cx="12" cy="8" r="4" />
            <path d="M4 21a8 8 0 0 1 16 0" />
            @break

        @case('lock')
            <rect x="4" y="10" width="16" height="10" rx="2" />
            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
            @break

        @case('filter')
            <path d="M3 5h18l-7 8v6l-4 2v-8L3 5Z" />
            @break

        @case('inbox')
            <path d="M4 13h4l1.5 2h5L16 13h4" />
            <path d="M5 5h14l1 8v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4l1-8Z" />
            @break

        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
