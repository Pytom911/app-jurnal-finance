@extends('layouts.app')

@section('title', 'Catat transaksi')
@section('subtitle', 'Tambahkan pemasukan, pengeluaran, transfer, atau saldo awal')

@section('actions')
    <a href="{{ route('app.transaksi.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali
    </a>
@endsection

@section('content')
    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.transaksi.store') }}" class="card p-5 sm:p-6">
                @csrf

                @include('transaksi._form')
            </form>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="section-title">Saldo saat ini</h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between rounded-xl bg-brand-50 px-4 py-3">
                        <span class="flex items-center gap-2 text-sm font-semibold text-brand-700">
                            <x-icon name="wallet" class="h-4 w-4" />
                            Total
                        </span>
                        <span class="text-sm font-extrabold tabular-nums text-brand-800">
                            <x-money :value="$ringkasan['total']" />
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                            <x-icon name="banknotes" class="h-4 w-4" />
                            Saldo cair
                        </span>
                        <span class="text-sm font-bold tabular-nums text-slate-800">
                            <x-money :value="$ringkasan['cair']" />
                        </span>
                    </div>

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                            <x-icon name="wallet" class="h-4 w-4" />
                            Tabungan
                        </span>
                        <span class="text-sm font-bold tabular-nums text-slate-800">
                            <x-money :value="$ringkasan['tabungan']" />
                        </span>
                    </div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="sparkles" class="h-5 w-5 text-brand-500" />
                    <h2 class="section-title">Aturan singkat</h2>
                </div>

                <ul class="mt-3 space-y-2.5 text-xs leading-relaxed text-slate-500">
                    <li class="flex gap-2">
                        <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-500" />
                        Semua pemasukan masuk ke Saldo Cair.
                    </li>
                    <li class="flex gap-2">
                        <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-500" />
                        Pengeluaran hanya dari Saldo Cair.
                    </li>
                    <li class="flex gap-2">
                        <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-500" />
                        Transfer antar akun tidak mengubah total saldo.
                    </li>
                    <li class="flex gap-2">
                        <x-icon name="check" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-brand-500" />
                        Penghasilan sendiri bisa dihubungkan ke tantangan aktif.
                    </li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
