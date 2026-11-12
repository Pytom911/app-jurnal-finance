@props(['tone' => 'slate'])

@php
    $tones = [
        'pemasukan' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 shadow-sm shadow-emerald-500/5',
        'pengeluaran' => 'border-rose-500/25 bg-rose-500/10 text-rose-600 shadow-sm shadow-rose-500/5',
        'transfer' => 'border-cyan-500/30 bg-cyan-500/12 text-cyan-800 shadow-sm shadow-cyan-500/5',
        'saldo_awal' => 'border-slate-300/60 bg-slate-500/10 text-slate-700 shadow-sm',
        'aktif' => 'border-cyan-400/40 bg-gradient-to-r from-cyan-500/15 to-teal-500/15 text-cyan-800 shadow-sm shadow-cyan-500/10',
        'selesai' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 shadow-sm shadow-emerald-500/5',
        'perlu_penyesuaian' => 'border-amber-500/30 bg-amber-500/10 text-amber-700 shadow-sm shadow-amber-500/5',
        'slate' => 'border-slate-200/80 bg-slate-100/70 text-slate-600',
        'success' => 'border-emerald-500/25 bg-emerald-500/10 text-emerald-700 shadow-sm shadow-emerald-500/5',
        'danger' => 'border-rose-500/25 bg-rose-500/10 text-rose-600 shadow-sm shadow-rose-500/5',
    ];

    $dotColors = [
        'pemasukan' => 'bg-emerald-500',
        'pengeluaran' => 'bg-rose-500',
        'transfer' => 'bg-cyan-500',
        'saldo_awal' => 'bg-slate-400',
        'aktif' => 'bg-cyan-500 animate-pulse',
        'selesai' => 'bg-emerald-500',
        'perlu_penyesuaian' => 'bg-amber-500',
        'slate' => 'bg-slate-400',
        'success' => 'bg-emerald-500',
        'danger' => 'bg-rose-500',
    ];

    $activeTone = $tones[$tone] ?? $tones['slate'];
    $activeDot = $dotColors[$tone] ?? $dotColors['slate'];
@endphp

<span {{ $attributes->merge(['class' => 'badge border backdrop-blur-md '.$activeTone]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $activeDot }}"></span>
    <span>{{ $slot }}</span>
</span>
