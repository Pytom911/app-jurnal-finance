@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('subtitle', 'Pemasukan, pengeluaran, transfer, dan saldo awal tersimpan rapi')

@section('actions')
    <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Catat Transaksi
    </a>
@endsection

@section('content')
    <!-- Filter Card (Clean Glass) -->
    <section class="glass-card mb-6 p-5 sm:p-6">
        <form method="GET" action="{{ route('app.transaksi.index') }}">
            <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <label for="search" class="label">Cari Catatan</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-cyan-600/70">
                            <x-icon name="search" class="h-4 w-4" />
                        </span>
                        <input id="search" name="search" type="text" value="{{ $filter['search'] ?? '' }}"
                            placeholder="Keterangan / kategori..." class="input pl-10">
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label for="tanggal_dari" class="label">Dari Tanggal</label>
                    <input id="tanggal_dari" name="tanggal_dari" type="date"
                        value="{{ $filter['tanggal_dari'] ?? '' }}" class="input">
                </div>

                <div class="lg:col-span-2">
                    <label for="tanggal_sampai" class="label">Sampai Tanggal</label>
                    <input id="tanggal_sampai" name="tanggal_sampai" type="date"
                        value="{{ $filter['tanggal_sampai'] ?? '' }}" class="input">
                </div>

                <div class="lg:col-span-2">
                    <label for="jenis" class="label">Jenis Transaksi</label>
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

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-cyan-100/50 pt-4">
                <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                    <span class="flex h-2 w-2 rounded-full bg-cyan-500"></span>
                    <span>Saldo cair saat ini:</span>
                    <span class="font-extrabold tabular-nums text-slate-900">
                        <x-money :value="$ringkasan['cair']" />
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('app.transaksi.index') }}" class="btn btn-ghost btn-sm">
                        Reset Filter
                    </a>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <x-icon name="filter" class="h-3.5 w-3.5 text-cyan-600" />
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Transactions Table Card (Clean Glass) -->
    <section class="glass-card overflow-hidden">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
            <div>
                <h2 class="text-sm font-black tracking-tight text-slate-900">Daftar Transaksi</h2>
                <p class="text-[11px] font-medium text-slate-400">
                    Menampilkan <span class="font-bold text-slate-700">{{ $transaksi->firstItem() ?? 0 }}–{{ $transaksi->lastItem() ?? 0 }}</span> dari
                    <span class="font-bold text-slate-700">{{ $transaksi->total() }}</span> transaksi tersimpan
                </p>
            </div>
        </div>

        @if ($transaksi->isEmpty())
            <div class="p-6">
                <x-empty-state title="Transaksi Tidak Ditemukan"
                    message="Tidak ada catatan yang cocok dengan filter yang Anda gunakan. Coba sesuaikan kata kunci atau filter tanggal.">
                    <x-slot:action>
                        <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary btn-sm">
                            <x-icon name="plus" class="h-3.5 w-3.5" />
                            Catat Transaksi Baru
                        </a>
                    </x-slot:action>
                </x-empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px]">
                    <thead class="bg-cyan-50/40 border-b border-cyan-100/40">
                        <tr>
                            <th class="table-th text-cyan-900/60">Waktu</th>
                            <th class="table-th text-cyan-900/60">Aktivitas & Kategori</th>
                            <th class="table-th text-cyan-900/60">Akun Rekening</th>
                            <th class="table-th text-right text-cyan-900/60">Nominal</th>
                            <th class="table-th text-right text-cyan-900/60">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-cyan-100/30">
                        @foreach ($transaksi as $item)
                            @php
                                $masuk = in_array($item->jenis, ['pemasukan', 'saldo_awal'], true);
                                $keluar = $item->jenis === 'pengeluaran';

                                $warna = $masuk ? 'text-emerald-600' : ($keluar ? 'text-rose-600' : 'text-cyan-700');
                                $tanda = $masuk ? '+' : ($keluar ? '−' : '');

                                $iconBg = $masuk ? 'bg-emerald-50 text-emerald-600 border-emerald-200/50' : 
                                    ($keluar ? 'bg-rose-50 text-rose-600 border-rose-200/50' : 
                                    'bg-cyan-50 text-cyan-600 border-cyan-200/50');
                            @endphp

                            <tr class="transition-colors hover:bg-cyan-50/40 group">
                                <td class="table-td whitespace-nowrap text-xs">
                                    <div class="font-bold text-slate-800">
                                        {{ $item->tanggal->locale('id')->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] font-medium text-slate-400">
                                        {{ $item->created_at->locale('id')->translatedFormat('H:i') }} WIB
                                    </div>
                                </td>

                                <td class="table-td">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border {{ $iconBg }} shadow-sm backdrop-blur-sm">
                                            <x-icon
                                                :name="$item->jenis === 'transfer' ? 'transfer' : ($item->jenis === 'pemasukan' ? 'arrow-down-left' : ($item->jenis === 'pengeluaran' ? 'arrow-up-right' : 'banknotes'))"
                                                class="h-4 w-4" />
                                        </span>

                                        <div class="min-w-0">
                                            <a href="{{ route('app.transaksi.show', $item) }}" class="truncate text-sm font-extrabold text-slate-800 transition hover:text-cyan-600 block">
                                                {{ $item->keterangan ?: ($item->kategori->nama ?? ucfirst(str_replace('_', ' ', $item->jenis))) }}
                                            </a>
                                            <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                                <x-badge :tone="$item->jenis">
                                                    {{ ucfirst(str_replace('_', ' ', $item->jenis)) }}
                                                </x-badge>

                                                @if ($item->kategori)
                                                    <span class="rounded-md border border-slate-200/60 bg-white/60 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                        {{ $item->kategori->nama }}
                                                    </span>
                                                @endif

                                                @if ($item->tantangan)
                                                    <span class="flex items-center gap-1 rounded-md border border-cyan-300/60 bg-cyan-50/80 px-1.5 py-0.5 text-[10px] font-bold text-cyan-700">
                                                        <x-icon name="target" class="h-3 w-3 text-cyan-600" />
                                                        {{ $item->tantangan->nama }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="table-td">
                                    @if ($item->jenis === 'transfer')
                                        <div class="inline-flex items-center gap-1.5 rounded-lg border border-cyan-100/60 bg-white/50 px-2 py-1 text-xs font-semibold text-slate-700">
                                            <span>{{ $item->akunAsal->nama ?? '—' }}</span>
                                            <x-icon name="chevron-right" class="h-3 w-3 text-cyan-500" />
                                            <span class="text-cyan-700 font-bold">{{ $item->akunTujuan->nama ?? '—' }}</span>
                                        </div>
                                    @else
                                        <span class="inline-block rounded-lg border border-slate-200/50 bg-white/50 px-2 py-1 text-xs font-semibold text-slate-700">
                                            {{ $item->akunTujuan->nama ?? $item->akunAsal->nama ?? '—' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="table-td whitespace-nowrap text-right text-sm font-black tabular-nums {{ $warna }}">
                                    {{ $tanda }}Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                                </td>

                                <td class="table-td">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('app.transaksi.show', $item) }}"
                                            class="rounded-xl border border-slate-200/60 bg-white/60 p-2 text-slate-500 transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700"
                                            title="Lihat detail">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>

                                        <a href="{{ route('app.transaksi.edit', $item) }}"
                                            class="rounded-xl border border-slate-200/60 bg-white/60 p-2 text-slate-500 transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700"
                                            title="Edit transaksi">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>

                                        <form method="POST" action="{{ route('app.transaksi.destroy', $item) }}"
                                            data-confirm="Hapus transaksi ini? Saldo dan progress tantangan akan dihitung ulang secara otomatis.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-xl border border-rose-200/60 bg-rose-50/60 p-2 text-rose-500 transition hover:border-rose-300 hover:bg-rose-100 hover:text-rose-700"
                                                title="Hapus transaksi">
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

            <div class="border-t border-cyan-100/50 bg-white/40 px-5 py-4">
                {{ $transaksi->links() }}
            </div>
        @endif
    </section>
@endsection
