@props(['value' => 0, 'tone' => 'brand'])

@php
    $percent = max(0, min(100, (float) $value));

    $tones = [
        'brand' => 'from-cyan-400 via-cyan-500 to-teal-500 shadow-[0_0_12px_rgba(6,182,212,0.5)]',
        'success' => 'from-emerald-400 via-emerald-500 to-teal-500 shadow-[0_0_12px_rgba(16,185,129,0.5)]',
        'warning' => 'from-amber-400 via-amber-500 to-orange-500 shadow-[0_0_12px_rgba(245,158,11,0.5)]',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'h-2.5 w-full overflow-hidden rounded-full border border-cyan-100/60 bg-cyan-950/5 p-0.5 shadow-inner backdrop-blur-sm']) }}>
    <div class="h-full rounded-full bg-gradient-to-r transition-all duration-700 ease-out {{ $tones[$tone] ?? $tones['brand'] }}"
        style="width: {{ $percent }}%"></div>
</div>
