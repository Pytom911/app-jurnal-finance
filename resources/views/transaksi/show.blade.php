@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('subtitle', $transaksi->tanggal->locale('id')->translatedFormat('l, j F Y'))

@section('actions')
    <a href="{{ route('app.transaksi.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" class="h-4 w-4" />
        Kembali ke Daftar
    </a>
@endsection

@section('content')
    @php
        $masuk = in_array($transaksi->jenis, ['pemasukan', 'saldo_awal'], true);
        $keluar = $transaksi->jenis === 'pengeluaran';

        $warna = $masuk ? 'text-emerald-600' : ($keluar ? 'text-rose-600' : 'text-cyan-700');
        $tanda = $masuk ? '+' : ($keluar ? '−' : '');
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Detail Receipt (Clean Glass) -->
        <div class="space-y-6 lg:col-span-2">
            <section class="glass-card overflow-hidden">
                <!-- Top Hero Header -->
                <div class="relative overflow-hidden border-b border-cyan-100/50 bg-gradient-to-br from-white/90 via-cyan-50/30 to-white/70 px-6 py-6 sm:px-8">
                    <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-cyan-400/10 blur-2xl"></div>

                    <div class="relative flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <x-badge :tone="$transaksi->jenis">
                                {{ ucfirst(str_replace('_', ' ', $transaksi->jenis)) }}
                            </x-badge>

                            <p class="mt-3 text-3xl sm:text-4xl font-black tracking-tight tabular-nums {{ $warna }} drop-shadow-sm">
                                {{ $tanda }}Rp {{ number_format((float) $transaksi->nominal, 0, ',', '.') }}
                            </p>

                            <p class="mt-1.5 text-sm font-semibold text-slate-600">
                                {{ $transaksi->keterangan ?: 'Tanpa keterangan khusus' }}
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex shrink-0 items-center gap-1.5">
                            <a href="{{ route('app.transaksi.edit', $transaksi) }}"
                                class="rounded-xl border border-slate-200/80 bg-white/80 p-2.5 text-slate-600 shadow-sm backdrop-blur-sm transition-all hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700"
                                title="Edit transaksi">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>

                            <form method="POST" action="{{ route('app.transaksi.destroy', $transaksi) }}"
                                data-confirm="Hapus transaksi ini? Saldo dan progress tantangan akan dihitung ulang secara otomatis.">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="rounded-xl border border-rose-200/80 bg-rose-50/80 p-2.5 text-rose-500 shadow-sm backdrop-blur-sm transition-all hover:border-rose-300 hover:bg-rose-100 hover:text-rose-700"
                                    title="Hapus transaksi">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Receipt Fields Breakdown -->
                <dl class="divide-y divide-cyan-100/30">
                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Pencatatan</dt>
                        <dd class="text-sm font-extrabold text-slate-800">
                            {{ $transaksi->tanggal->locale('id')->translatedFormat('l, j F Y') }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Kategori</dt>
                        <dd class="text-sm font-extrabold text-slate-800">
                            @if ($transaksi->kategori)
                                <span class="rounded-lg border border-cyan-200/60 bg-cyan-50/70 px-2.5 py-1 text-xs text-cyan-800">
                                    {{ $transaksi->kategori->nama }}
                                </span>
                            @else
                                <span class="text-slate-400 italic">Tanpa kategori</span>
                            @endif
                        </dd>
                    </div>

                    @if ($transaksi->jenis === 'transfer')
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Dari Akun Rekening</dt>
                            <dd class="text-sm font-extrabold text-slate-800">
                                {{ $transaksi->akunAsal->nama ?? '—' }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Ke Akun Rekening</dt>
                            <dd class="text-sm font-extrabold text-cyan-700">
                                {{ $transaksi->akunTujuan->nama ?? '—' }}
                            </dd>
                        </div>
                    @else
                        <div class="flex items-center justify-between gap-4 px-6 py-4">
                            <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Akun Rekening Terkait</dt>
                            <dd class="text-sm font-extrabold text-slate-800">
                                {{ $transaksi->akunTujuan->nama ?? $transaksi->akunAsal->nama ?? '—' }}
                            </dd>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4 px-6 py-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Sumber Pemasukan</dt>
                        <dd class="text-sm font-extrabold text-slate-800">
                            {{ $transaksi->sumber_pemasukan ? ucfirst(str_replace('_', ' ', $transaksi->sumber_pemasukan)) : '—' }}
                        </dd>
                    </div>

                    <div class="px-6 py-4">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Keterangan Tambahan</dt>
                        <dd class="mt-1.5 whitespace-pre-line rounded-xl border border-slate-200/50 bg-white/50 p-3.5 text-xs font-medium leading-relaxed text-slate-700">
                            {{ $transaksi->keterangan ?: 'Tidak ada keterangan tambahan yang dicatat.' }}
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- Linked Challenge Card -->
            @if ($transaksi->tantangan)
                <section class="glass-card p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-cyan-200/80 bg-gradient-to-br from-cyan-50 to-teal-50 text-cyan-600 shadow-sm">
                                <x-icon name="target" class="h-5 w-5" />
                            </span>
                            <div>
                                <p class="text-sm font-black text-slate-900">{{ $transaksi->tantangan->nama }}</p>
                                <p class="text-xs font-medium text-slate-400">Tantangan yang terhubung dengan transaksi ini</p>
                            </div>
                        </div>

                        <x-badge :tone="$transaksi->tantangan->status">
                            {{ ucfirst(str_replace('_', ' ', $transaksi->tantangan->status)) }}
                        </x-badge>
                    </div>

                    <div class="mt-4 flex items-center justify-between rounded-xl border border-cyan-100/60 bg-cyan-50/50 px-4 py-3 text-xs">
                        <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Target Nominal Tantangan</span>
                        <span class="font-black tabular-nums text-slate-900 text-sm">
                            <x-money :value="$transaksi->tantangan->target" />
                        </span>
                    </div>
                </section>
            @endif
        </div>

        <!-- Sidebar Information -->
        <aside class="space-y-5">
            <div class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="clock" class="h-4 w-4" />
                    </span>
                    <h2 class="section-title">Audit Metadata</h2>
                </div>

                <div class="mt-4 space-y-3 text-xs font-medium">
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/50 bg-white/50 p-3">
                        <span class="text-slate-400">Waktu Dibuat:</span>
                        <span class="font-bold text-slate-800">
                            {{ $transaksi->created_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200/50 bg-white/50 p-3">
                        <span class="text-slate-400">Terakhir Update:</span>
                        <span class="font-bold text-slate-800">
                            {{ $transaksi->updated_at->locale('id')->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>
            </div>

            <!-- Notice card -->
            <div class="rounded-2xl border border-cyan-200/60 bg-gradient-to-br from-cyan-50/70 to-teal-50/40 p-5 backdrop-blur-md">
                <div class="flex items-start gap-3">
                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-cyan-500/20 text-cyan-700">
                        <x-icon name="alert" class="h-3.5 w-3.5" />
                    </span>
                    <p class="text-xs leading-relaxed text-cyan-950 font-medium">
                        Menghapus transaksi ini akan mengembalikan saldo seperti semula dan otomatis menghitung ulang capaian target tantangan.
                    </p>
                </div>
            </div>

            <a href="{{ route('app.transaksi.create', ['jenis' => $transaksi->jenis]) }}" class="btn btn-primary w-full">
                <x-icon name="plus" class="h-4 w-4" />
                Catat Transaksi Serupa
            </a>
        </aside>
    </div>
@endsection
