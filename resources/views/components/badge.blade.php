@props(['tone' => 'slate'])

@php
    $tones = [
        'pemasukan' => 'bg-emerald-50 text-emerald-700',
        'pengeluaran' => 'bg-rose-50 text-rose-600',
        'transfer' => 'bg-brand-50 text-brand-700',
        'saldo_awal' => 'bg-slate-100 text-slate-600',
        'aktif' => 'bg-brand-50 text-brand-700',
        'selesai' => 'bg-emerald-50 text-emerald-700',
        'perlu_penyesuaian' => 'bg-amber-50 text-amber-700',
        'slate' => 'bg-slate-100 text-slate-600',
        'success' => 'bg-emerald-50 text-emerald-700',
        'danger' => 'bg-rose-50 text-rose-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge '.($tones[$tone] ?? $tones['slate'])]) }}>
    {{ $slot }}
</span>
