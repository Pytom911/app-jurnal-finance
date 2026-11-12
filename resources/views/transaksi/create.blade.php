@extends('layouts.app')

@section('title', 'Catat Transaksi')
@section('subtitle', 'Tambahkan pemasukan, pengeluaran, transfer saldo, atau saldo awal')

@section('actions')
    <a href="{{ route('app.transaksi.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali
    </a>
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.transaksi.store') }}" class="glass-card p-6 sm:p-7">
                @csrf

                @include('transaksi._form')
            </form>
        </div>

        <aside class="space-y-5">
            <!-- Saldo Saat Ini Glass Card -->
            <div class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="wallet" class="h-4 w-4" />
                    </span>
                    <h2 class="section-title">Saldo Saat Ini</h2>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-xl border border-cyan-200/60 bg-gradient-to-r from-cyan-50/80 to-teal-50/60 px-4 py-3 shadow-sm">
                        <span class="flex items-center gap-2 text-xs font-bold text-cyan-800 uppercase tracking-wider">
                            Total Saldo
                        </span>
                        <span class="text-sm font-black tabular-nums text-cyan-900">
                            <x-money :value="$ringkasan['total']" />
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-slate-200/60 bg-white/60 px-4 py-3">
                        <span class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            Saldo Cair
                        </span>
                        <span class="text-sm font-extrabold tabular-nums text-slate-800">
                            <x-money :value="$ringkasan['cair']" />
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-slate-200/60 bg-white/60 px-4 py-3">
                        <span class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                            <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                            Saldo Tabungan
                        </span>
                        <span class="text-sm font-extrabold tabular-nums text-slate-800">
                            <x-money :value="$ringkasan['tabungan']" />
                        </span>
                    </div>
                </div>
            </div>

            <!-- Aturan Singkat Glass Card -->
            <div class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="sparkles" class="h-4 w-4" />
                    </span>
                    <h2 class="section-title">Panduan Singkat</h2>
                </div>

                <ul class="mt-4 space-y-3 text-xs font-medium leading-relaxed text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Semua pemasukan otomatis dimasukkan ke <strong>Saldo Cair</strong>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Pengeluaran harian hanya dapat dipotong dari <strong>Saldo Cair</strong>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Transfer antar rekening tidak merubah total keseluruhan saldo.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Pemasukan dengan sumber <em>Penghasilan Sendiri</em> dapat dikaitkan dengan tantangan.</span>
                    </li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
