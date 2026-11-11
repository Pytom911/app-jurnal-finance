@props(['label', 'value', 'tone' => 'white', 'icon' => 'wallet', 'hint' => null])

@php
    $tones = [
        'brand' => 'border-transparent bg-gradient-to-br from-brand-600 via-brand-600 to-brand-500 text-white shadow-lg shadow-brand-600/25',
        'white' => 'bg-white text-slate-900',
    ];

    $iconTones = [
        'brand' => 'bg-white/15 text-white',
        'white' => 'bg-brand-50 text-brand-600',
    ];

    $labelTones = [
        'brand' => 'text-brand-50/90',
        'white' => 'text-slate-500',
    ];

    $hintTones = [
        'brand' => 'text-brand-50/80',
        'white' => 'text-slate-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card relative overflow-hidden p-5 '.$tones[$tone]]) }}>
    @if ($tone === 'brand')
        <div class="pointer-events-none absolute -right-8 -top-10 h-32 w-32 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 right-10 h-24 w-24 rounded-full bg-white/10"></div>
    @endif

    <div class="relative flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider {{ $labelTones[$tone] }}">{{ $label }}</p>
            <p class="stat-value mt-2 truncate">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1.5 text-xs font-medium {{ $hintTones[$tone] }}">{{ $hint }}</p>
            @endif
        </div>

        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconTones[$tone] }}">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
    </div>
</div>
