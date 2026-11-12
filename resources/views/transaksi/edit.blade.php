@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('subtitle', 'Perbarui data transaksi Anda, saldo & rekapan akan disinkronkan ulang')

@section('actions')
    <a href="{{ route('app.transaksi.show', $transaksi) }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali
    </a>
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.transaksi.update', $transaksi) }}" class="glass-card p-6 sm:p-7">
                @csrf
                @method('PUT')

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

            <!-- Peringatan Audit Perubahan Saldo -->
            <div class="rounded-2xl border border-amber-300/40 bg-amber-500/10 p-5 backdrop-blur-md">
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-500/20 text-amber-700">
                        <x-icon name="alert" class="h-4 w-4" />
                    </span>
                    <div class="text-xs leading-relaxed text-amber-900">
                        <p class="font-extrabold">Perubahan Memengaruhi Saldo</p>
                        <p class="mt-1 font-medium text-amber-800">
                            Mengubah nominal atau mengganti jenis transaksi akan mengalkulasi ulang mutasi akun. Sistem otomatis menolak perubahan yang menyebabkan saldo akun menjadi minus.
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
