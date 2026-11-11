@extends('layouts.app')

@section('title', 'Edit transaksi')
@section('subtitle', 'Perbarui data transaksi, saldo akan dihitung ulang')

@section('actions')
    <a href="{{ route('app.transaksi.show', $transaksi) }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali
    </a>
@endsection

@section('content')
    <div class="grid gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('app.transaksi.update', $transaksi) }}" class="card p-5 sm:p-6">
                @csrf
                @method('PUT')

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

            <div class="rounded-2xl border border-amber-100 bg-amber-50/70 p-5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                    <div class="text-xs leading-relaxed text-amber-800">
                        <p class="font-bold">Perubahan memengaruhi saldo</p>
                        <p class="mt-1">
                            Menambah nominal atau mengganti jenis dapat membuat saldo tidak cukup.
                            Sistem akan menolak perubahan yang melanggar aturan saldo.
                        </p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
