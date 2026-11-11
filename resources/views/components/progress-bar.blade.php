@props(['value' => 0, 'tone' => 'brand'])

@php
    $percent = max(0, min(100, (float) $value));

    $tones = [
        'brand' => 'bg-gradient-to-r from-brand-500 to-brand-400',
        'success' => 'bg-gradient-to-r from-emerald-500 to-emerald-400',
        'warning' => 'bg-gradient-to-r from-amber-500 to-amber-400',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'h-2.5 w-full overflow-hidden rounded-full bg-slate-100']) }}>
    <div class="h-full rounded-full transition-all duration-500 {{ $tones[$tone] ?? $tones['brand'] }}"
        style="width: {{ $percent }}%"></div>
</div>
