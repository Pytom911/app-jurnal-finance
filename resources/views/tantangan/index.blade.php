@extends('layouts.app')

@section('title', 'Target & Tantangan')
@section('subtitle', 'Pantau akumulasi penghasilan mandiri dan raih target finansialmu')

@section('actions')
    @if ($aktif)
        <button type="button" disabled
            class="btn btn-secondary cursor-not-allowed opacity-60"
            title="Masih ada tantangan aktif. Selesaikan atau hapus terlebih dahulu.">
            <x-icon name="plus" class="h-4 w-4" />
            Buat Tantangan
        </button>
    @else
        <button type="button" data-modal-open="tantangan-create" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Buat Tantangan
        </button>
    @endif
@endsection

@section('content')
    @php
        $modalTerbuka = old('_modal');
        $editTantanganId = old('_tantangan_id');
        $editAction = $editTantanganId ? route('app.tantangan.update', $editTantanganId) : '';

        $createNama = $modalTerbuka === 'tantangan-create' ? old('nama') : '';
        $createTarget = $modalTerbuka === 'tantangan-create' ? old('target') : '';

        $editNama = $modalTerbuka === 'tantangan-edit' ? old('nama') : '';
        $editTarget = $modalTerbuka === 'tantangan-edit' ? old('target') : '';

        $persentase = $progress['persentase'] ?? 0;
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Challenge Display & History -->
        <div class="space-y-6 lg:col-span-2">
            @if ($aktif && $progress)
                <!-- Active Challenge Hero Card (Cyan Glass) -->
                <section class="glass-hero relative overflow-hidden text-white shadow-xl shadow-cyan-950/20">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-cyan-300/25 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-20 right-24 h-48 w-48 rounded-full bg-teal-400/20 blur-2xl"></div>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-cyan-300 to-transparent"></div>

                    <div class="relative p-6 sm:p-7">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-300/30 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-cyan-200 backdrop-blur-md">
                                    <span class="h-2 w-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                    Tantangan Aktif
                                </div>

                                <h2 class="mt-3 truncate text-2xl sm:text-3xl font-black tracking-tight drop-shadow-sm">{{ $aktif->nama }}</h2>
                                <p class="mt-1 text-xs font-medium text-cyan-100/90">
                                    Mulai dicatat {{ $aktif->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-1.5">
                                <button type="button" data-edit-tantangan data-id="{{ $aktif->id }}"
                                    data-action="{{ route('app.tantangan.update', $aktif) }}"
                                    data-nama="{{ $aktif->nama }}" data-target="{{ $aktif->target }}"
                                    class="rounded-xl border border-white/20 bg-white/15 p-2.5 text-white backdrop-blur-md transition hover:bg-white/25 hover:border-white/40"
                                    title="Edit target tantangan">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>

                                @if ($aktifMemilikiTransaksi)
                                    <span class="cursor-not-allowed rounded-xl border border-white/10 bg-white/10 p-2.5 text-white/40"
                                        title="Tantangan yang telah memiliki riwayat transaksi terkunci dari penghapusan">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('app.tantangan.destroy', $aktif) }}"
                                        data-confirm="Hapus tantangan “{{ $aktif->nama }}”?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl border border-white/20 bg-white/15 p-2.5 text-white backdrop-blur-md transition hover:bg-rose-500 hover:border-rose-400"
                                            title="Hapus tantangan">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Progress Display -->
                        <div class="mt-8 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-cyan-100/80">Penghasilan Mandiri Terkumpul</p>
                                <p class="mt-1 text-3xl sm:text-4xl font-black tabular-nums tracking-tight drop-shadow-sm">
                                    <x-money :value="$progress['progress']" />
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-3xl sm:text-4xl font-black tabular-nums text-cyan-300 drop-shadow-sm">
                                    {{ number_format($persentase, 0, ',', '.') }}%
                                </p>
                            </div>
                        </div>

                        <!-- Luminous Bar -->
                        <div class="relative mt-4 h-3.5 w-full overflow-hidden rounded-full border border-white/30 bg-black/20 p-0.5 backdrop-blur-sm">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-300 via-teal-300 to-white shadow-[0_0_15px_rgba(255,255,255,0.8)] transition-all duration-700 ease-out"
                                style="width: {{ max(0, min(100, $persentase)) }}%"></div>
                        </div>

                        <div class="mt-2.5 flex items-center justify-between text-xs font-medium text-cyan-100/90">
                            <span>Target: <x-money :value="$aktif->target" /></span>
                            <span>Kurang: <x-money :value="$progress['sisa_target']" /></span>
                        </div>
                    </div>

                    <!-- Bottom Stat Pods -->
                    <div class="grid gap-3 border-t border-white/10 bg-black/15 p-5 backdrop-blur-md sm:grid-cols-3">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200">Target Nominal</p>
                            <p class="mt-1 text-sm font-black tabular-nums text-white">
                                <x-money :value="$aktif->target" />
                            </p>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200">Sisa Dibutuhkan</p>
                            <p class="mt-1 text-sm font-black tabular-nums text-white">
                                <x-money :value="$progress['sisa_target']" />
                            </p>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-cyan-200">Status Sistem</p>
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-300/40 bg-cyan-400/20 px-2 py-0.5 text-[10px] font-extrabold uppercase text-cyan-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-300 animate-pulse"></span>
                                    Berjalan
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-white/10 bg-black/20 px-6 py-3.5 text-xs font-medium text-cyan-100/80 leading-relaxed">
                        Progress dihitung eksklusif dari transaksi pemasukan bersumber <strong>Penghasilan Sendiri</strong>. Uang saku atau transfer tidak dihitung ke tantangan ini.
                    </div>
                </section>
            @else
                <!-- Empty State Active Challenge -->
                <section class="glass-card p-6 sm:p-8">
                    <x-empty-state title="Belum Ada Tantangan Aktif" icon="target"
                        message="Tentukan target nominal impianmu, lalu hubungkan transaksi pemasukan mandiri agar progres tercatat otomatis hingga selesai.">
                        <x-slot:action>
                            <button type="button" data-modal-open="tantangan-create" class="btn btn-primary">
                                <x-icon name="plus" class="h-4 w-4" />
                                Buat Tantangan Baru
                            </button>
                        </x-slot:action>
                    </x-empty-state>
                </section>
            @endif

            <!-- Challenge History (Clean Glass Table) -->
            <section class="glass-card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
                    <div>
                        <h2 class="text-sm font-black tracking-tight text-slate-900">Riwayat Tantangan Selesai</h2>
                        <p class="text-[11px] font-medium text-slate-400">Arsip pencapaian masa lalu & penyesuaian target</p>
                    </div>

                    <span class="rounded-full border border-cyan-200/80 bg-cyan-50 px-2.5 py-0.5 text-xs font-extrabold text-cyan-800">
                        {{ $riwayat->total() }} Arsip
                    </span>
                </div>

                @if ($riwayat->isEmpty())
                    <div class="p-6">
                        <x-empty-state title="Riwayat Masih Kosong" icon="trophy"
                            message="Tantangan yang berhasil mencapai target 100% atau telah ditutup akan terarsip rapi di sini." />
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px]">
                            <thead class="bg-cyan-50/40 border-b border-cyan-100/40">
                                <tr>
                                    <th class="table-th text-cyan-900/60">Tantangan</th>
                                    <th class="table-th text-right text-cyan-900/60">Target</th>
                                    <th class="table-th text-right text-cyan-900/60">Pencapaian</th>
                                    <th class="table-th text-cyan-900/60">Status</th>
                                    <th class="table-th text-cyan-900/60">Tanggal Selesai</th>
                                    <th class="table-th text-right text-cyan-900/60">Durasi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-cyan-100/30">
                                @foreach ($riwayat as $row)
                                    @php
                                        $item = $row['tantangan'];
                                        $ringkasan = $row['ringkasan'];
                                        $tanggalAkhir = $item->tanggal_selesai ?? $item->tanggal_tercapai;
                                        $isSelesai = $item->status === 'selesai';
                                    @endphp

                                    <tr class="transition-colors hover:bg-cyan-50/40 group">
                                        <td class="table-td">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border {{ $isSelesai ? 'border-emerald-300/60 bg-emerald-50 text-emerald-600' : 'border-amber-300/60 bg-amber-50 text-amber-600' }} shadow-sm backdrop-blur-sm">
                                                    <x-icon name="{{ $isSelesai ? 'trophy' : 'alert' }}" class="h-4 w-4" />
                                                </span>

                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-extrabold text-slate-800">{{ $item->nama }}</p>
                                                    <p class="text-[11px] font-medium text-slate-400">
                                                        Mulai {{ $item->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right text-sm font-extrabold tabular-nums text-slate-700">
                                            <x-money :value="$item->target" />
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right">
                                            <p class="text-sm font-black tabular-nums text-slate-900">
                                                <x-money :value="$ringkasan['progress']" />
                                            </p>
                                            <p class="text-[11px] font-bold text-cyan-600">
                                                {{ number_format($ringkasan['persentase'], 0, ',', '.') }}%
                                            </p>
                                        </td>

                                        <td class="table-td">
                                            <x-badge :tone="$item->status">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </x-badge>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-xs font-semibold text-slate-600">
                                            {{ $tanggalAkhir ? $tanggalAkhir->locale('id')->translatedFormat('d M Y') : '—' }}
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right text-xs font-extrabold tabular-nums text-slate-700">
                                            {{ $ringkasan['durasi_hari'] !== null ? $ringkasan['durasi_hari'].' hari' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-cyan-100/50 bg-white/40 px-5 py-4">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </section>
        </div>

        <!-- Sidebar Info -->
        <aside class="space-y-5">
            <div class="glass-card p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-cyan-100/70 text-cyan-600">
                        <x-icon name="sparkles" class="h-4 w-4" />
                    </span>
                    <h2 class="section-title">Cara Kerja Tantangan</h2>
                </div>

                <ul class="mt-4 space-y-3 text-xs font-medium leading-relaxed text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Sistem membatasi maksimal <strong>satu tantangan aktif</strong> dalam satu kurun waktu.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Akumulasi nominal dihitung khusus dari pemasukan dengan kategori <em>Penghasilan Sendiri</em>.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Tantangan otomatis berstatus <strong>Selesai</strong> begitu akumulasi menyentuh 100%.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-cyan-100 text-cyan-700 mt-0.5">
                            <x-icon name="check" class="h-3 w-3" />
                        </span>
                        <span>Tantangan yang telah memiliki riwayat transaksi terlindungi dari penghapusan permanen.</span>
                    </li>
                </ul>
            </div>

            <!-- Callout -->
            <div class="rounded-2xl border border-cyan-200/60 bg-gradient-to-br from-cyan-50/80 via-white/60 to-teal-50/50 p-5 backdrop-blur-md">
                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-cyan-500/20 text-cyan-700">
                        <x-icon name="target" class="h-4 w-4" />
                    </span>
                    <div class="text-xs leading-relaxed text-slate-700">
                        <p class="font-extrabold text-cyan-950">Hubungkan Catatan Pemasukan</p>
                        <p class="mt-1 font-medium text-slate-600">
                            Setiap ada hasil proyek atau gaji mandiri, pastikan memilih opsi tantangan aktif pada formulir pencatatan.
                        </p>
                        <a href="{{ route('app.transaksi.create', ['jenis' => 'pemasukan']) }}"
                            class="mt-3.5 inline-flex items-center gap-1.5 font-bold text-cyan-700 hover:text-cyan-800 transition">
                            <span>Catat Pemasukan Sekarang</span>
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <!-- Modal Create (Clean Glass) -->
    <x-modal id="tantangan-create" title="Buat Target Tantangan Baru"
        description="Tantangan baru akan langsung aktif dan mulai mengakumulasikan transaksi penghasilan sendiri.">
        <form method="POST" action="{{ route('app.tantangan.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="tantangan-create">

            <div>
                <label for="tantangan_nama" class="label">Nama Tantangan</label>
                <input id="tantangan_nama" name="nama" type="text" maxlength="150" required
                    value="{{ $createNama }}" placeholder="Contoh: Tabungan 100 Juta Sebelum Usia 20 Tahun"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tantangan_target" class="label">Target Nominal Capaian</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-base font-black text-cyan-600">
                        Rp
                    </span>
                    <input id="tantangan_target" name="target" type="number" step="0.01" min="0.01" required
                        value="{{ $createTarget }}" placeholder="0"
                        class="input pl-12 text-base font-black tabular-nums @error('target') input-error @enderror">
                </div>
                @error('target')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <p class="rounded-xl border border-cyan-100 bg-cyan-50/50 p-3 text-xs leading-relaxed text-slate-600">
                Pastikan tidak ada tantangan lain yang sedang aktif di akun Anda.
            </p>

            <div class="flex flex-col-reverse gap-2 border-t border-cyan-100/50 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Buat Tantangan
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit (Clean Glass) -->
    <x-modal id="tantangan-edit" title="Perbarui Tantangan"
        description="Ubah nama atau nominal target. Status tantangan akan disinkronkan otomatis.">
        <form id="tantangan-edit-form" method="POST" action="{{ $editAction }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="tantangan-edit">
            <input type="hidden" name="_tantangan_id" value="{{ $editTantanganId }}">

            <div>
                <label for="tantangan_edit_nama" class="label">Nama Tantangan</label>
                <input id="tantangan_edit_nama" name="nama" type="text" maxlength="150" required
                    value="{{ $editNama }}" placeholder="Nama tantangan"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tantangan_edit_target" class="label">Target Nominal Capaian</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-base font-black text-cyan-600">
                        Rp
                    </span>
                    <input id="tantangan_edit_target" name="target" type="number" step="0.01" min="0.01" required
                        value="{{ $editTarget }}" placeholder="0"
                        class="input pl-12 text-base font-black tabular-nums @error('target') input-error @enderror">
                </div>
                @error('target')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-amber-200/80 bg-amber-500/10 p-3 text-xs leading-relaxed text-amber-900 font-medium">
                Menyesuaikan target di bawah progress yang sudah dicapai akan langsung menandai tantangan ini sebagai selesai.
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-cyan-100/50 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($modalTerbuka)
                if (typeof window.openModal === 'function') {
                    window.openModal(@json($modalTerbuka));
                }
            @endif
        });
    </script>
@endpush
