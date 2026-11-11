@extends('layouts.app')

@section('title', 'Transaksi')
@section('subtitle', 'Semua pemasukan, pengeluaran, transfer, dan saldo awal')

@section('actions')
    <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Catat transaksi
    </a>
@endsection

@section('content')
    <section class="card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('app.transaksi.index') }}">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <label for="search" class="label">Cari</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input id="search" name="search" type="text" value="{{ $filter['search'] ?? '' }}"
                            placeholder="Keterangan / kategori" class="input pl-10">
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label for="tanggal_dari" class="label">Dari tanggal</label>
                    <input id="tanggal_dari" name="tanggal_dari" type="date"
                        value="{{ $filter['tanggal_dari'] ?? '' }}" class="input">
                </div>

                <div class="lg:col-span-2">
                    <label for="tanggal_sampai" class="label">Sampai tanggal</label>
                    <input id="tanggal_sampai" name="tanggal_sampai" type="date"
                        value="{{ $filter['tanggal_sampai'] ?? '' }}" class="input">
                </div>

                <div class="lg:col-span-2">
                    <label for="jenis" class="label">Jenis</label>
                    <select id="jenis" name="jenis" class="input">
                        <option value="">Semua jenis</option>
                        @foreach (['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran', 'transfer' => 'Transfer', 'saldo_awal' => 'Saldo awal'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filter['jenis'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label for="kategori_id" class="label">Kategori</label>
                    <select id="kategori_id" name="kategori_id" class="input">
                        <option value="">Semua kategori</option>
                        @foreach ($kategori as $item)
                            <option value="{{ $item->id }}" @selected((int) ($filter['kategori_id'] ?? 0) === (int) $item->id)>
                                {{ $item->nama }} ({{ $item->jenis }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span>Saldo cair saat ini:</span>
                    <span class="font-bold tabular-nums text-slate-800">
                        <x-money :value="$ringkasan['cair']" />
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('app.transaksi.index') }}" class="btn btn-ghost btn-sm">Reset</a>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-icon name="filter" class="h-4 w-4" />
                        Terapkan filter
                    </button>
                </div>
            </div>
        </form>
    </section>

    <section class="card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-base font-extrabold tracking-tight text-slate-900">Riwayat transaksi</h2>
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }} dari
                    {{ $transaksi->total() }} transaksi
                </p>
            </div>
        </div>

        @if ($transaksi->isEmpty())
            <div class="p-5">
                <x-empty-state title="Transaksi tidak ditemukan"
                    message="Coba ubah filter, atau catat transaksi baru untuk memulai.">
                    <x-slot:action>
                        <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="h-4 w-4" />
                            Catat transaksi
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px]">
                    <thead class="bg-slate-50/70">
                        <tr>
                            <th class="table-th">Tanggal</th>
                            <th class="table-th">Transaksi</th>
                            <th class="table-th">Akun</th>
                            <th class="table-th text-right">Nominal</th>
                            <th class="table-th text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($transaksi as $item)
                            @php
                                $masuk = in_array($item->jenis, ['pemasukan', 'saldo_awal'], true);
                                $keluar = $item->jenis === 'pengeluaran';

                                $warna = $masuk ? 'text-emerald-600' : ($keluar ? 'text-rose-500' : 'text-brand-600');
                                $tanda = $masuk ? '+' : ($keluar ? '−' : '');
                            @endphp

                            <tr class="transition hover:bg-slate-50/60">
                                <td class="table-td whitespace-nowrap text-xs text-slate-500">
                                    <div class="font-semibold text-slate-700">
                                        {{ $item->tanggal->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $item->created_at->locale('id')->translatedFormat('H:i') }}
                                    </div>
                                </td>

                                <td class="table-td">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                            <x-icon
                                                :name="$item->jenis === 'transfer' ? 'transfer' : ($item->jenis === 'pemasukan' ? 'arrow-down-left' : ($item->jenis === 'pengeluaran' ? 'arrow-up-right' : 'banknotes'))"
                                                class="h-4 w-4" />
                                        </span>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-slate-800">
                                                {{ $item->keterangan ?: ($item->kategori->nama ?? ucfirst(str_replace('_', ' ', $item->jenis))) }}
                                            </p>
                                            <div class="mt-0.5 flex flex-wrap items-center gap-1.5">
                                                <x-badge :tone="$item->jenis">
                                                    {{ ucfirst(str_replace('_', ' ', $item->jenis)) }}
                                                </x-badge>

                                                @if ($item->kategori)
                                                    <span class="text-xs text-slate-400">{{ $item->kategori->nama }}</span>
                                                @endif

                                                @if ($item->tantangan)
                                                    <span class="text-xs text-brand-500">· {{ $item->tantangan->nama }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="table-td">
                                    @if ($item->jenis === 'transfer')
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <span class="font-semibold text-slate-700">{{ $item->akunAsal->nama ?? '—' }}</span>
                                            <x-icon name="chevron-right" class="h-3.5 w-3.5 text-slate-400" />
                                            <span class="font-semibold text-slate-700">{{ $item->akunTujuan->nama ?? '—' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-slate-700">
                                            {{ $item->akunTujuan->nama ?? $item->akunAsal->nama ?? '—' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td whitespace-nowrap text-right text-sm font-bold tabular-nums {{ $warna }}">
                                    {{ $tanda }}Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                                </td>

                                <td class="table-td">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('app.transaksi.show', $item) }}"
                                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                                            title="Lihat detail">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('app.transaksi.edit', $item) }}"
                                            class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-600"
                                            title="Edit">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>

                                        <form method="POST" action="{{ route('app.transaksi.destroy', $item) }}"
                                            data-confirm="Hapus transaksi ini? Saldo dan progress tantangan akan dihitung ulang.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-500"
                                                title="Hapus">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $transaksi->links() }}
            </div>
        @endif
    </section>
@endsection
