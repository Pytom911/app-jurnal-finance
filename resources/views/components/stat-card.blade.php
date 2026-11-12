@props(['label', 'value', 'tone' => 'white', 'icon' => 'wallet', 'hint' => null])

@php
    $isBrand = $tone === 'brand';
@endphp

@if ($isBrand)
    <div {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-2xl border border-cyan-300/40 bg-gradient-to-br from-cyan-600 via-cyan-500 to-teal-600 p-5 text-white shadow-lg shadow-cyan-600/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-cyan-500/35']) }}>
        <!-- Ambient specular highlights -->
        <div class="pointer-events-none absolute -right-10 -top-12 h-36 w-36 rounded-full bg-white/20 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-10 right-14 h-28 w-28 rounded-full bg-cyan-300/25 blur-xl"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent"></div>

        <div class="relative flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-cyan-100/90">{{ $label }}</p>
                <p class="mt-2 text-2xl font-black tracking-tight tabular-nums text-white truncate drop-shadow-sm">{{ $value }}</p>
                @if ($hint)
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-cyan-100/85">
                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-200"></span>
                        {{ $hint }}
                    </p>
                @endif
            </div>

            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-white/30 bg-white/15 text-white shadow-inner backdrop-blur-md transition-transform duration-300 group-hover:scale-105">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'glass-card glass-card-hover group relative overflow-hidden p-5 transition-all duration-300']) }}>
        <!-- Subtle glass sheen -->
        <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-cyan-400/10 blur-xl transition-all duration-300 group-hover:bg-cyan-400/20"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-white to-transparent"></div>

        <div class="relative flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-black tracking-tight tabular-nums text-slate-900 truncate">{{ $value }}</p>
                @if ($hint)
                    <p class="mt-1.5 flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-cyan-400"></span>
                        {{ $hint }}
                    </p>
                @endif
            </div>

            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-cyan-100/80 bg-gradient-to-br from-cyan-50/90 to-sky-50/60 text-cyan-600 shadow-sm shadow-cyan-900/5 backdrop-blur-md transition-all duration-300 group-hover:border-cyan-200 group-hover:bg-cyan-100/70 group-hover:text-cyan-700 group-hover:scale-105">
                <x-icon :name="$icon" class="h-5 w-5" />
            </span>
        </div>
    </div>
@endif
