@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', now()->locale('id')->translatedFormat('l, j F Y'))

@section('actions')
    <a href="{{ route('app.transaksi.create') }}" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Catat Transaksi
    </a>
@endsection

@section('content')
    <!-- Top Stat Cards (Clean Glasses) -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total Saldo" :value="'Rp '.number_format($ringkasan['total'], 0, ',', '.')" tone="brand"
            icon="wallet" hint="Saldo cair + tabungan tersimpan" />

        <x-stat-card label="Saldo Cair" :value="'Rp '.number_format($ringkasan['cair'], 0, ',', '.')" tone="white"
            icon="banknotes" hint="Siap untuk transaksi & belanja" />

        <x-stat-card label="Saldo Tabungan" :value="'Rp '.number_format($ringkasan['tabungan'], 0, ',', '.')" tone="white"
            icon="wallet" hint="Khusus alokasi & simpanan" />
    </div>

    <!-- Main Dashboard Grid -->
    <div class="mt-6 grid gap-6 lg:grid-cols-5">
        <!-- Left Section (3 Cols) -->
        <div class="space-y-6 lg:col-span-3">
            <!-- Recent Transactions Table Card -->
            <section class="glass-card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl border border-cyan-200/60 bg-cyan-50 text-cyan-600">
                            <x-icon name="clock" class="h-4 w-4" />
                        </span>
                        <div>
                            <h2 class="text-sm font-black tracking-tight text-slate-900">Transaksi Terbaru</h2>
                            <p class="text-[11px] font-medium text-slate-400">6 aktivitas finansial paling akhir</p>
                        </div>
                    </div>

                    <a href="{{ route('app.transaksi.index') }}"
                        class="inline-flex items-center gap-1 text-xs font-bold text-cyan-600 transition hover:text-cyan-700 hover:underline">
                        Lihat semua
                        <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                @if ($transaksiTerbaru->isEmpty())
                    <div class="p-6">
                        <x-empty-state title="Belum Ada Transaksi"
                            message="Mulai dengan mencatat saldo awal pembuka, lalu tambahkan pemasukan atau pengeluaran harian.">
                            <x-slot:action>
                                <a href="{{ route('app.transaksi.create', ['jenis' => 'saldo_awal']) }}"
                                    class="btn btn-primary btn-sm">
                                    <x-icon name="plus" class="h-3.5 w-3.5" />
                                    Catat Saldo Awal
                                </a>
                            </x-slot:action>
                        </x-empty-state>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[560px]">
                            <thead class="bg-cyan-50/40 border-b border-cyan-100/40">
                                <tr>
                                    <th class="table-th text-cyan-900/60">Aktivitas</th>
                                    <th class="table-th text-cyan-900/60">Tanggal</th>
                                    <th class="table-th text-right text-cyan-900/60">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-cyan-100/30">
                                @foreach ($transaksiTerbaru as $item)
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
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        <span class="text-[11px] font-semibold text-slate-400">
                                                            {{ ucfirst(str_replace('_', ' ', $item->jenis)) }}
                                                        </span>
                                                        @if ($item->kategori)
                                                            <span class="text-slate-300">·</span>
                                                            <span class="rounded-md bg-slate-100/80 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                                {{ $item->kategori->nama }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-xs font-medium text-slate-500">
                                            {{ $item->tanggal->locale('id')->translatedFormat('d M Y') }}
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right font-black tabular-nums text-sm {{ $warna }}">
                                            {{ $tanda }}Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <!-- Quick Action Glass Cards -->
            <section class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="sparkles" class="h-4 w-4" />
                    </span>
                    <h2 class="text-sm font-black tracking-tight text-slate-900">Aksi Cepat Finansial</h2>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @php
                        $quickActions = [
                            [
                                'jenis' => 'pemasukan',
                                'label' => 'Catat Pemasukan',
                                'desc' => 'Uang saku atau penghasilan',
                                'icon' => 'arrow-down-left',
                                'iconBg' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20',
                                'hoverBorder' => 'hover:border-emerald-300 hover:bg-emerald-50/30',
                            ],
                            [
                                'jenis' => 'pengeluaran',
                                'label' => 'Catat Pengeluaran',
                                'desc' => 'Belanja & kebutuhan harian',
                                'icon' => 'arrow-up-right',
                                'iconBg' => 'bg-rose-500/10 text-rose-600 border border-rose-500/20',
                                'hoverBorder' => 'hover:border-rose-300 hover:bg-rose-50/30',
                            ],
                            [
                                'jenis' => 'transfer',
                                'label' => 'Transfer Saldo',
                                'desc' => 'Pindah dana cair ↔ tabungan',
                                'icon' => 'transfer',
                                'iconBg' => 'bg-cyan-500/10 text-cyan-600 border border-cyan-500/20',
                                'hoverBorder' => 'hover:border-cyan-300 hover:bg-cyan-50/30',
                            ],
                            [
                                'jenis' => 'saldo_awal',
                                'label' => 'Saldo Awal',
                                'desc' => 'Inisialisasi dana akun',
                                'icon' => 'banknotes',
                                'iconBg' => 'bg-slate-500/10 text-slate-600 border border-slate-300/60',
                                'hoverBorder' => 'hover:border-slate-300 hover:bg-slate-50/50',
                            ],
                        ];
                    @endphp

                    @foreach ($quickActions as $aksi)
                        <a href="{{ route('app.transaksi.create', ['jenis' => $aksi['jenis']]) }}"
                            class="group flex items-center gap-3.5 rounded-2xl border border-white/80 bg-white/60 p-3.5 backdrop-blur-md shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-cyan-950/5 {{ $aksi['hoverBorder'] }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $aksi['iconBg'] }} backdrop-blur-sm transition-transform duration-200 group-hover:scale-105">
                                <x-icon :name="$aksi['icon']" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-extrabold text-slate-800 group-hover:text-cyan-900">{{ $aksi['label'] }}</p>
                                <p class="text-xs font-medium text-slate-400">{{ $aksi['desc'] }}</p>
                            </div>
                            <x-icon name="chevron-right" class="h-4 w-4 text-slate-300 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:text-cyan-600" />
                        </a>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Right Section (2 Cols) -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Tantangan (Challenge) Glass Card -->
            <section class="glass-card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                            <x-icon name="target" class="h-4 w-4" />
                        </span>
                        <h2 class="text-sm font-black tracking-tight text-slate-900">Target Tantangan</h2>
                    </div>

                    <a href="{{ route('app.tantangan.index') }}"
                        class="text-xs font-bold text-cyan-600 transition hover:text-cyan-700 hover:underline">Kelola</a>
                </div>

                <div class="p-5">
                    @if ($aktif && $progress)
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-base font-black text-slate-900">{{ $aktif->nama }}</p>
                                <p class="mt-0.5 text-xs font-medium text-slate-400">
                                    Dimulai {{ $aktif->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <x-badge tone="aktif">Aktif</x-badge>
                        </div>

                        <div class="mt-5 flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pencapaian Mandiri</p>
                                <p class="mt-1 text-2xl font-black tabular-nums text-slate-900">
                                    Rp {{ number_format($progress['progress'], 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="text-2xl font-black tabular-nums text-cyan-600">
                                {{ number_format($progress['persentase'], 0, ',', '.') }}%
                            </p>
                        </div>

                        <x-progress-bar :value="$progress['persentase']" class="mt-3.5" />

                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                            <div class="rounded-xl border border-cyan-100/60 bg-cyan-50/50 p-3 backdrop-blur-sm">
                                <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Total Target</p>
                                <p class="mt-1 font-extrabold tabular-nums text-slate-800">
                                    Rp {{ number_format((float) $aktif->target, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-slate-200/60 bg-white/60 p-3 backdrop-blur-sm">
                                <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Sisa Target</p>
                                <p class="mt-1 font-extrabold tabular-nums text-cyan-700">
                                    Rp {{ number_format($progress['sisa_target'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('app.tantangan.index') }}" class="btn btn-secondary mt-5 w-full">
                            <span>Lihat Detail Tantangan</span>
                            <x-icon name="chevron-right" class="h-4 w-4 text-cyan-600" />
                        </a>
                    @elseif ($perluPenyesuaian)
                        <div class="rounded-2xl border border-amber-300/40 bg-amber-500/10 p-4 backdrop-blur-md">
                            <div class="flex items-start gap-3">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-amber-400/40 bg-amber-500/20 text-amber-700">
                                    <x-icon name="alert" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-extrabold text-amber-900">Tantangan Perlu Penyesuaian</p>
                                    <p class="mt-1 text-xs text-amber-800 leading-relaxed">
                                        “{{ $perluPenyesuaian->nama }}” turun di bawah target akibat transaksi penyesuaian. Riwayatnya tetap aman tersimpan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('app.tantangan.index') }}" class="btn btn-secondary mt-4 w-full">
                            Buka Riwayat Tantangan
                        </a>
                    @else
                        <x-empty-state title="Belum Ada Tantangan" icon="target"
                            message="Tentukan target penghasilan pribadimu dan pantau progresnya secara otomatis.">
                            <x-slot:action>
                                <a href="{{ route('app.tantangan.index') }}" class="btn btn-primary btn-sm">
                                    <x-icon name="plus" class="h-3.5 w-3.5" />
                                    Buat Tantangan
                                </a>
                            </x-slot:action>
                        </x-empty-state>
                    @endif
                </div>
            </section>

            <!-- Ringkasan Akun Hero Glass Card -->
            <section class="glass-hero relative overflow-hidden p-6 text-white shadow-xl shadow-cyan-950/20">
                <!-- Specular highlights & light orbs -->
                <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-cyan-400/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-10 -left-10 h-36 w-36 rounded-full bg-teal-400/20 blur-2xl"></div>
                <div class="pointer-events-none absolute inset-x-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-cyan-300/60 to-transparent"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-cyan-200">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-cyan-300/30 bg-white/10 backdrop-blur-md">
                                <x-icon name="banknotes" class="h-4 w-4 text-cyan-200" />
                            </span>
                            <p class="text-xs font-black uppercase tracking-widest">Ringkasan Akun</p>
                        </div>
                        <span class="rounded-full border border-cyan-300/30 bg-white/10 px-2.5 py-0.5 text-[10px] font-bold text-cyan-200 backdrop-blur-sm">2 Akun</span>
                    </div>

                    <div class="mt-6 space-y-3.5">
                        <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 backdrop-blur-sm">
                            <span class="text-xs font-medium text-cyan-100/90">Saldo Cair</span>
                            <span class="font-extrabold tabular-nums text-white text-sm">
                                <x-money :value="$ringkasan['cair']" />
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-3.5 py-2.5 backdrop-blur-sm">
                            <span class="text-xs font-medium text-cyan-100/90">Saldo Tabungan</span>
                            <span class="font-extrabold tabular-nums text-white text-sm">
                                <x-money :value="$ringkasan['tabungan']" />
                            </span>
                        </div>

                        <div class="flex items-center justify-between border-t border-cyan-400/30 pt-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-cyan-200">Total Akumulasi</span>
                            <span class="text-lg font-black tabular-nums text-cyan-300 drop-shadow">
                                <x-money :value="$ringkasan['total']" />
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('app.akun.index') }}" class="btn mt-6 w-full border border-white/20 bg-white/15 text-white backdrop-blur-md hover:bg-white/25 hover:border-white/40">
                        <span>Kelola Akun Keuangan</span>
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
