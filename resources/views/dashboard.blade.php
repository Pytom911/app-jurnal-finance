@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', now()->locale('id')->translatedFormat('l, j F Y'))

@section('actions')
    <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Catat transaksi
    </a>
@endsection

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total saldo" :value="'Rp '.number_format($ringkasan['total'], 0, ',', '.')" tone="brand"
            icon="wallet" hint="Saldo cair + tabungan" />

        <x-stat-card label="Saldo cair" :value="'Rp '.number_format($ringkasan['cair'], 0, ',', '.')" tone="white"
            icon="banknotes" hint="Untuk pemasukan & pengeluaran" />

        <x-stat-card label="Saldo tabungan" :value="'Rp '.number_format($ringkasan['tabungan'], 0, ',', '.')" tone="white"
            icon="wallet" hint="Hanya lewat transfer" />
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-3">
            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900">Transaksi terbaru</h2>
                        <p class="text-xs text-slate-400">6 aktivitas terakhir</p>
                    </div>

                    <a href="{{ route('app.transaksi.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-700">
                        Lihat semua
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>

                @if ($transaksiTerbaru->isEmpty())
                    <div class="p-5">
                        <x-empty-state title="Belum ada transaksi"
                            message="Mulai dengan mencatat saldo awal, lalu tambahkan pemasukan atau pengeluaran.">
                            <x-slot:action>
                                <a href="{{ route('app.transaksi.create', ['jenis' => 'saldo_awal']) }}"
                                    class="btn btn-primary btn-sm">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Catat saldo awal
                                </a>
                            </x-slot:action>
                        </x-empty-state>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px]">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th class="table-th">Transaksi</th>
                                    <th class="table-th">Tanggal</th>
                                    <th class="table-th text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($transaksiTerbaru as $item)
                                    @php
                                        $masuk = in_array($item->jenis, ['pemasukan', 'saldo_awal'], true);
                                        $keluar = $item->jenis === 'pengeluaran';

                                        $warna = $masuk ? 'text-emerald-600' : ($keluar ? 'text-rose-500' : 'text-brand-600');
                                        $tanda = $masuk ? '+' : ($keluar ? '−' : '');
                                    @endphp

                                    <tr class="transition hover:bg-slate-50/60">
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
                                                    <p class="text-xs text-slate-400">
                                                        {{ ucfirst(str_replace('_', ' ', $item->jenis)) }}
                                                        @if ($item->kategori)
                                                            · {{ $item->kategori->nama }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-xs text-slate-500">
                                            {{ $item->tanggal->locale('id')->translatedFormat('d M Y') }}
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right font-bold tabular-nums {{ $warna }}">
                                            {{ $tanda }}Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="card p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="sparkles" class="h-5 w-5 text-brand-500" />
                    <h2 class="text-base font-extrabold tracking-tight text-slate-900">Aksi cepat</h2>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ([['jenis' => 'pemasukan', 'label' => 'Pemasukan', 'desc' => 'Uang saku / penghasilan', 'icon' => 'arrow-down-left', 'class' => 'bg-emerald-50 text-emerald-600'], ['jenis' => 'pengeluaran', 'label' => 'Pengeluaran', 'desc' => 'Belanja & kebutuhan', 'icon' => 'arrow-up-right', 'class' => 'bg-rose-50 text-rose-600'], ['jenis' => 'transfer', 'label' => 'Transfer', 'desc' => 'Cair ↔ tabungan', 'icon' => 'transfer', 'class' => 'bg-brand-50 text-brand-600'], ['jenis' => 'saldo_awal', 'label' => 'Saldo awal', 'desc' => 'Saldo pembuka akun', 'icon' => 'banknotes', 'class' => 'bg-slate-100 text-slate-600']] as $aksi)
                        <a href="{{ route('app.transaksi.create', ['jenis' => $aksi['jenis']]) }}"
                            class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3.5 transition hover:border-brand-200 hover:bg-brand-50/40">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $aksi['class'] }}">
                                <x-icon :name="$aksi['icon']" class="h-4 w-4" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-bold text-slate-800">{{ $aksi['label'] }}</span>
                                <span class="block text-xs text-slate-400">{{ $aksi['desc'] }}</span>
                            </span>
                            <x-icon name="chevron-right"
                                class="ml-auto h-4 w-4 text-slate-300 transition group-hover:text-brand-500" />
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div class="flex items-center gap-2">
                        <x-icon name="target" class="h-5 w-5 text-brand-500" />
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900">Tantangan</h2>
                    </div>

                    <a href="{{ route('app.tantangan.index') }}"
                        class="text-xs font-bold text-brand-600 hover:text-brand-700">Kelola</a>
                </div>

                <div class="p-5">
                    @if ($aktif && $progress)
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-base font-extrabold text-slate-900">{{ $aktif->nama }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    Mulai {{ $aktif->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <x-badge tone="aktif">Aktif</x-badge>
                        </div>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold text-slate-400">Progress</p>
                                <p class="mt-1 text-xl font-extrabold tabular-nums text-slate-900">
                                    Rp {{ number_format($progress['progress'], 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="text-2xl font-extrabold tabular-nums text-brand-600">
                                {{ number_format($progress['persentase'], 0, ',', '.') }}%
                            </p>
                        </div>

                        <x-progress-bar :value="$progress['persentase']" class="mt-3" />

                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                            <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                                <p class="font-semibold text-slate-400">Target</p>
                                <p class="mt-0.5 font-bold tabular-nums text-slate-700">
                                    Rp {{ number_format((float) $aktif->target, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 px-3 py-2.5">
                                <p class="font-semibold text-slate-400">Sisa target</p>
                                <p class="mt-0.5 font-bold tabular-nums text-slate-700">
                                    Rp {{ number_format($progress['sisa_target'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('app.tantangan.index') }}" class="btn btn-secondary mt-4 w-full">
                            Lihat detail tantangan
                        </a>
                    @elseif ($perluPenyesuaian)
                        <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3.5">
                            <div class="flex items-start gap-2.5">
                                <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
                                <div>
                                    <p class="text-sm font-bold text-amber-800">Tantangan perlu penyesuaian</p>
                                    <p class="mt-1 text-xs text-amber-700">
                                        “{{ $perluPenyesuaian->nama }}” turun di bawah target. Riwayatnya tetap
                                        tersimpan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('app.tantangan.index') }}" class="btn btn-secondary mt-4 w-full">
                            Buka riwayat tantangan
                        </a>
                    @else
                        <x-empty-state title="Belum ada tantangan" icon="target"
                            message="Buat target penghasilan sendiri dan pantau progressnya otomatis.">
                            <x-slot:action>
                                <a href="{{ route('app.tantangan.index') }}" class="btn btn-primary btn-sm">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Buat tantangan
                                </a>
                            </x-slot:action>
                        </x-empty-state>
                    @endif
                </div>
            </section>

            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 to-brand-900 p-5 text-white shadow-lg">
                <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-brand-400/20 blur-2xl"></div>

                <div class="relative">
                    <div class="flex items-center gap-2 text-brand-200">
                        <x-icon name="banknotes" class="h-5 w-5" />
                        <p class="text-xs font-bold uppercase tracking-wider">Ringkasan akun</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white/60">Saldo cair</span>
                            <span class="font-bold tabular-nums">
                                <x-money :value="$ringkasan['cair']" />
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-white/60">Saldo tabungan</span>
                            <span class="font-bold tabular-nums">
                                <x-money :value="$ringkasan['tabungan']" />
                            </span>
                        </div>
                        <div class="flex items-center justify-between border-t border-white/10 pt-3 text-sm">
                            <span class="text-white/60">Total</span>
                            <span class="text-base font-extrabold tabular-nums text-brand-200">
                                <x-money :value="$ringkasan['total']" />
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('app.akun.index') }}" class="btn mt-5 w-full bg-white/10 text-white hover:bg-white/15">
                        Lihat akun
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
