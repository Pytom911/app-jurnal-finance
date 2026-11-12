@extends('layouts.app')

@section('title', 'Akun Rekening & Simpanan')
@section('subtitle', 'Pemisahan saldo cair transaksi dan saldo tabungan dalam sistem terintegrasi')

@section('actions')
    <a href="{{ route('app.transaksi.create', ['jenis' => 'transfer']) }}" class="btn btn-primary">
        <x-icon name="transfer" class="h-4 w-4" />
        Transfer Saldo
    </a>
@endsection

@section('content')
    <!-- Top Stat Row (Clean Glass) -->
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total Kekayaan" :value="'Rp '.number_format($ringkasan['total'], 0, ',', '.')" tone="brand"
            icon="wallet" hint="Akumulasi seluruh dana cair & tabungan" />

        <x-stat-card label="Saldo Cair Siap Pakai" :value="'Rp '.number_format($ringkasan['cair'], 0, ',', '.')" tone="white"
            icon="banknotes" hint="Khusus perputaran belanja harian" />

        <x-stat-card label="Saldo Tabungan Aman" :value="'Rp '.number_format($ringkasan['tabungan'], 0, ',', '.')"
            tone="white" icon="wallet" hint="Khusus pos simpanan berjangka" />
    </div>

    <!-- Notice Banner (Clean Glass) -->
    <div class="mt-6 flex items-start gap-3.5 rounded-2xl border border-cyan-200/60 bg-gradient-to-r from-cyan-50/80 via-white/70 to-teal-50/40 p-4 text-xs font-medium text-cyan-950 shadow-sm backdrop-blur-md">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-cyan-500/20 text-cyan-700">
            <x-icon name="alert" class="h-4 w-4" />
        </span>
        <p class="leading-relaxed pt-0.5">
            Akun keuangan didesain <strong>terproteksi (read-only)</strong>. Saldo Cair dan Tabungan dikonfigurasi permanen untuk menjaga konsistensi akuntansi. Mutasi saldo terjadi murni lewat transaksi pemasukan, pengeluaran, transfer, atau saldo awal.
        </p>
    </div>

    <!-- Accounts Display Grid -->
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <!-- Account Cards (Modern Glass Banking Cards) -->
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($akun as $row)
                    @php
                        $item = $row['akun'];
                        $saldo = $row['saldo'];
                        $cair = $item->jenis === 'cair';
                    @endphp

                    <section class="glass-card glass-card-hover group relative overflow-hidden transition-all duration-300">
                        <!-- Top specular highlight line -->
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-cyan-400/60 to-transparent"></div>

                        <div class="flex items-center gap-3.5 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl border {{ $cair ? 'border-cyan-300/60 bg-gradient-to-br from-cyan-50 to-teal-50 text-cyan-600' : 'border-emerald-300/60 bg-gradient-to-br from-emerald-50 to-teal-50 text-emerald-600' }} shadow-sm backdrop-blur-sm">
                                <x-icon :name="$cair ? 'banknotes' : 'wallet'" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <h2 class="truncate text-sm font-black tracking-tight text-slate-900">
                                    {{ $item->nama }}
                                </h2>
                                <p class="text-[11px] font-medium text-slate-400">
                                    {{ $cair ? 'Akun Operasional Harian' : 'Akun Pos Simpanan' }}
                                </p>
                            </div>

                            <x-badge :tone="$cair ? 'transfer' : 'selesai'">
                                {{ $cair ? 'Cair' : 'Tabungan' }}
                            </x-badge>
                        </div>

                        <div class="p-5">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Saldo Tersedia</p>
                            <p class="mt-1 text-2xl sm:text-3xl font-black tracking-tight tabular-nums text-slate-900">
                                <x-money :value="$saldo" />
                            </p>

                            <div class="mt-4 rounded-xl border border-slate-200/50 bg-white/50 p-3.5 text-xs font-medium leading-relaxed text-slate-600">
                                @if ($cair)
                                    Semua pemasukan otomatis masuk ke sini, dan pengeluaran belanja dipotong langsung dari akun ini.
                                @else
                                    Saldo tabungan tersimpan aman dan hanya bertambah/berkurang lewat transaksi transfer dana.
                                @endif
                            </div>

                            <div class="mt-5 flex items-center gap-2">
                                <a href="{{ route('app.transaksi.create', ['jenis' => $cair ? 'saldo_awal' : 'transfer']) }}"
                                    class="btn btn-secondary btn-sm flex-1">
                                    <x-icon :name="$cair ? 'banknotes' : 'transfer'" class="h-3.5 w-3.5 text-cyan-600" />
                                    <span>{{ $cair ? 'Catat Saldo Awal' : 'Transfer ke Sini' }}</span>
                                </a>

                                <a href="{{ route('app.transaksi.index') }}" class="btn btn-ghost btn-sm">
                                    Lihat Riwayat
                                </a>
                            </div>
                        </div>
                    </section>
                @endforeach
            </div>

            <!-- Alur Saldo / Money Flow (Clean Glass Steps) -->
            <section class="glass-card p-5 sm:p-6">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="sparkles" class="h-4 w-4" />
                    </span>
                    <h2 class="text-sm font-black tracking-tight text-slate-900">Alur Sirkulasi Keuangan</h2>
                </div>

                <div class="mt-4 grid gap-3.5 sm:grid-cols-3">
                    <div class="rounded-2xl border border-emerald-200/60 bg-gradient-to-b from-white/80 to-emerald-50/30 p-4 shadow-sm backdrop-blur-md">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-300/60 bg-emerald-100/80 text-emerald-700 shadow-sm">
                            <x-icon name="arrow-down-left" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-extrabold text-slate-900">1. Pemasukan</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500 font-medium">
                            Uang saku dan hasil pekerjaan mandiri menambah Saldo Cair Anda.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-rose-200/60 bg-gradient-to-b from-white/80 to-rose-50/30 p-4 shadow-sm backdrop-blur-md">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-rose-300/60 bg-rose-100/80 text-rose-700 shadow-sm">
                            <x-icon name="arrow-up-right" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-extrabold text-slate-900">2. Pengeluaran</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500 font-medium">
                            Hanya memotong Saldo Cair dan otomatis dicegah jika saldo tidak cukup.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-cyan-200/60 bg-gradient-to-b from-white/80 to-cyan-50/30 p-4 shadow-sm backdrop-blur-md">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-cyan-300/60 bg-cyan-100/80 text-cyan-700 shadow-sm">
                            <x-icon name="transfer" class="h-4 w-4" />
                        </span>
                        <p class="mt-3 text-sm font-extrabold text-slate-900">3. Transfer</p>
                        <p class="mt-1 text-xs leading-relaxed text-slate-500 font-medium">
                            Pindahkan dana ke Saldo Tabungan agar aman tersimpan dan terproteksi.
                        </p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar Guidelines -->
        <aside class="space-y-5">
            <div class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="wallet" class="h-4 w-4" />
                    </span>
                    <h2 class="section-title">Ketentuan Akun</h2>
                </div>

                <ul class="mt-4 space-y-3 text-xs font-medium leading-relaxed text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Tiap profil pengguna memiliki sepasang akun: <strong>Saldo Cair</strong> & <strong>Saldo Tabungan</strong>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Struktur akun terkunci agar seluruh riwayat audit transaksi tetap valid dan presisi.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Saldo Cair tidak diizinkan bernilai minus.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Total kekayaan adalah penjumlahan mutlak antara Saldo Cair dan Tabungan.</span>
                    </li>
                </ul>
            </div>

            <!-- Transfer Callout -->
            <div class="rounded-2xl border border-cyan-200/60 bg-gradient-to-br from-cyan-50/80 via-white/70 to-teal-50/50 p-5 backdrop-blur-md shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-cyan-500/20 text-cyan-700">
                        <x-icon name="transfer" class="h-4 w-4" />
                    </span>
                    <div class="text-xs leading-relaxed text-slate-700">
                        <p class="font-extrabold text-cyan-950">Ingin Mengalokasikan Tabungan?</p>
                        <p class="mt-1 font-medium text-slate-600">
                            Pindahkan surplus dari saldo cair ke saldo tabungan agar tidak terpakai untuk belanja konsumtif harian.
                        </p>
                        <a href="{{ route('app.transaksi.create', ['jenis' => 'transfer']) }}"
                            class="mt-3.5 inline-flex items-center gap-1.5 font-bold text-cyan-700 hover:text-cyan-800 transition">
                            <span>Buka Formulir Transfer</span>
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
