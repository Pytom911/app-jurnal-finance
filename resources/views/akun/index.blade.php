@extends('layouts.app')

@section('title', 'Akun keuangan')
@section('subtitle', 'Saldo cair dan tabunganmu dalam satu tampilan')

@section('actions')
    <a href="{{ route('app.transaksi.create', ['jenis' => 'transfer']) }}" class="btn btn-primary">
        <x-icon name="transfer" class="h-4 w-4" />
        Transfer saldo
    </a>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total saldo" :value="'Rp '.number_format($ringkasan['total'], 0, ',', '.')" tone="brand"
            icon="wallet" hint="Saldo cair + tabungan" />

        <x-stat-card label="Saldo cair" :value="'Rp '.number_format($ringkasan['cair'], 0, ',', '.')" tone="white"
            icon="banknotes" hint="Untuk pemasukan & pengeluaran" />

        <x-stat-card label="Saldo tabungan" :value="'Rp '.number_format($ringkasan['tabungan'], 0, ',', '.')"
            tone="white" icon="wallet" hint="Hanya lewat transfer" />
    </div>

    <div class="mt-5 flex items-start gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 px-4 py-3.5 text-sm text-brand-800">
        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
        <p class="leading-relaxed">
            Akun bersifat <strong>read-only</strong>. Saldo cair dan tabungan dibuat otomatis untuk setiap
            pengguna, dan saldonya hanya berubah melalui transaksi pemasukan, pengeluaran, transfer, atau
            saldo awal.
        </p>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($akun as $row)
                    @php
                        $item = $row['akun'];
                        $saldo = $row['saldo'];
                        $cair = $item->jenis === 'cair';
                    @endphp

                    <section class="card overflow-hidden">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl {{ $cair ? 'bg-brand-50 text-brand-600' : 'bg-emerald-50 text-emerald-600' }}">
                                <x-icon :name="$cair ? 'banknotes' : 'wallet'" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-base font-extrabold tracking-tight text-slate-900">
                                    {{ $item->nama }}
                                </h2>
                                <p class="text-xs text-slate-400">
                                    {{ $cair ? 'Akun transaksi harian' : 'Akun penyimpanan' }}
                                </p>
                            </div>

                            <x-badge :tone="$cair ? 'transfer' : 'selesai'">
                                {{ $cair ? 'Cair' : 'Tabungan' }}
                            </x-badge>
                        </div>

                        <div class="px-5 py-5">
                            <p class="text-xs font-semibold text-slate-400">Saldo saat ini</p>
                            <p class="mt-1 text-2xl font-extrabold tracking-tight tabular-nums text-slate-900">
                                <x-money :value="$saldo" />
                            </p>

                            <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500">
                                @if ($cair)
                                    Semua pemasukan masuk ke akun ini, dan semua pengeluaran diambil dari akun
                                    ini. Saldo tidak boleh negatif.
                                @else
                                    Saldo tabungan hanya bertambah atau berkurang melalui transaksi transfer.
                                    Tidak bisa dipakai untuk pengeluaran langsung.
                                @endif
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('app.transaksi.create', ['jenis' => $cair ? 'saldo_awal' : 'transfer']) }}"
                                    class="btn btn-secondary btn-sm flex-1">
                                    <x-icon :name="$cair ? 'banknotes' : 'transfer'" class="h-4 w-4" />
                                    {{ $cair ? 'Catat saldo awal' : 'Transfer ke sini' }}
                                </a>

                                <a href="{{ route('app.transaksi.index') }}" class="btn btn-ghost btn-sm">
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>

            <section class="card p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="sparkles" class="h-5 w-5 text-brand-500" />
                    <h2 class="text-base font-extrabold tracking-tight text-slate-900">Alur saldo</h2>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <x-icon name="arrow-down-left" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-bold text-slate-800">Pemasukan</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                            Uang saku dan penghasilan sendiri menambah Saldo Cair.
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                            <x-icon name="arrow-up-right" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-bold text-slate-800">Pengeluaran</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                            Hanya mengurangi Saldo Cair dan tidak boleh melebihi saldo.
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                            <x-icon name="transfer" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-bold text-slate-800">Transfer</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                            Memindahkan saldo antar akun tanpa mengubah total kekayaan.
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="section-title">Aturan akun</h2>

                <ul class="mt-4 space-y-3 text-xs leading-relaxed text-slate-500">
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Setiap pengguna hanya memiliki satu Saldo Cair dan satu Saldo Tabungan.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Nama dan jenis akun tidak dapat diubah dari aplikasi.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Saldo cair tidak boleh negatif.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Total saldo adalah penjumlahan Saldo Cair dan Saldo Tabungan.
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-brand-100 bg-brand-50/60 p-5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <div class="text-xs leading-relaxed text-brand-800">
                        <p class="font-bold">Ingin memindahkan dana?</p>
                        <p class="mt-1">
                            Gunakan transaksi transfer agar saldo cair dan tabungan tetap seimbang.
                        </p>
                        <a href="{{ route('app.transaksi.create', ['jenis' => 'transfer']) }}"
                            class="mt-3 inline-flex items-center gap-1 font-bold text-brand-700 hover:text-brand-800">
                            Buat transfer
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
