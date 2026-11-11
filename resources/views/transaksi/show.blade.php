@extends('layouts.app')

@section('title', 'Detail transaksi')
@section('subtitle', $transaksi->tanggal->locale('id')->translatedFormat('l, j F Y'))

@section('actions')
    <a href="{{ route('app.transaksi.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali
    </a>
@endsection

@section('content')
    @php
        $masuk = in_array($transaksi->jenis, ['pemasukan', 'saldo_awal'], true);
        $keluar = $transaksi->jenis === 'pengeluaran';

        $warna = $masuk ? 'text-emerald-600' : ($keluar ? 'text-rose-500' : 'text-brand-600');
        $tanda = $masuk ? '+' : ($keluar ? '−' : '');
    @endphp

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
                    <div class="min-w-0">
                        <x-badge :tone="$transaksi->jenis">
                            {{ ucfirst(str_replace('_', ' ', $transaksi->jenis)) }}
                        </x-badge>

                        <p class="mt-3 text-3xl font-extrabold tracking-tight tabular-nums {{ $warna }}">
                            {{ $tanda }}Rp {{ number_format((float) $transaksi->nominal, 0, ',', '.') }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $transaksi->keterangan ?: 'Tanpa keterangan' }}
                        </p>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <a href="{{ route('app.transaksi.edit', $transaksi) }}"
                            class="rounded-xl border border-slate-200 bg-white p-2.5 text-slate-500 transition hover:border-brand-200 hover:text-brand-600"
                            title="Edit transaksi">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </a>

                        <form method="POST" action="{{ route('app.transaksi.destroy', $transaksi) }}"
                            data-confirm="Hapus transaksi ini? Saldo dan progress tantangan akan dihitung ulang.">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-xl border border-rose-100 bg-rose-50 p-2.5 text-rose-500 transition hover:bg-rose-100"
                                title="Hapus transaksi">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                    </div>
                </div>

                <dl class="divide-y divide-slate-100">
                    <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                        <dt class="text-sm font-medium text-slate-400">Tanggal</dt>
                        <dd class="text-sm font-semibold text-slate-800">
                            {{ $transaksi->tanggal->locale('id')->translatedFormat('l, j F Y') }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                        <dt class="text-sm font-medium text-slate-400">Kategori</dt>
                        <dd class="text-sm font-semibold text-slate-800">
                            {{ $transaksi->kategori->nama ?? '—' }}
                        </dd>
                    </div>

                    @if ($transaksi->jenis === 'transfer')
                        <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">Dari akun</dt>
                            <dd class="text-sm font-semibold text-slate-800">
                                {{ $transaksi->akunAsal->nama ?? '—' }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">Ke akun</dt>
                            <dd class="text-sm font-semibold text-slate-800">
                                {{ $transaksi->akunTujuan->nama ?? '—' }}
                            </dd>
                        </div>
                    @else
                        <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                            <dt class="text-sm font-medium text-slate-400">Akun</dt>
                            <dd class="text-sm font-semibold text-slate-800">
                                {{ $transaksi->akunTujuan->nama ?? $transaksi->akunAsal->nama ?? '—' }}
                            </dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-6">
                        <dt class="text-sm font-medium text-slate-400">Sumber pemasukan</dt>
                        <dd class="text-sm font-semibold text-slate-800">
                            {{ $transaksi->sumber_pemasukan ? ucfirst(str_replace('_', ' ', $transaksi->sumber_pemasukan)) : '—' }}
                        </dd>
                    </div>

                    <div class="px-5 py-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-400">Keterangan</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-700">
                            {{ $transaksi->keterangan ?: 'Tidak ada keterangan.' }}
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($transaksi->tantangan)
                <section class="card p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                <x-icon name="target" class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $transaksi->tantangan->nama }}</p>
                                <p class="text-xs text-slate-400">Tantangan terkait transaksi ini</p>
                            </div>
                        </div>

                        <x-badge :tone="$transaksi->tantangan->status">
                            {{ ucfirst(str_replace('_', ' ', $transaksi->tantangan->status)) }}
                        </x-badge>
                    </div>

                    <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 text-sm">
                        <span class="font-medium text-slate-500">Target tantangan</span>
                        <span class="font-bold tabular-nums text-slate-800">
                            <x-money :value="$transaksi->tantangan->target" />
                        </span>
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <h2 class="section-title">Informasi pencatatan</h2>

                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="flex items-center gap-2 text-slate-400">
                            <x-icon name="clock" class="h-4 w-4" />
                            Dibuat
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $transaksi->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="flex items-center gap-2 text-slate-400">
                            <x-icon name="pencil" class="h-4 w-4" />
                            Diperbarui
                        </span>
                        <span class="font-semibold text-slate-700">
                            {{ $transaksi->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-brand-100 bg-brand-50/60 p-5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <p class="text-xs leading-relaxed text-brand-800">
                        Menghapus transaksi akan mengembalikan saldo seperti sebelum transaksi dicatat dan
                        menghitung ulang progress tantangan terkait.
                    </p>
                </div>
            </div>

            <a href="{{ route('app.transaksi.create', ['jenis' => $transaksi->jenis]) }}" class="btn btn-primary w-full">
                <x-icon name="plus" class="h-4 w-4" />
                Catat transaksi serupa
            </a>
        </aside>
    </div>
@endsection
