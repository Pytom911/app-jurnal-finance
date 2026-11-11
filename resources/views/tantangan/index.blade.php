@extends('layouts.app')

@section('title', 'Tantangan')
@section('subtitle', 'Pantau target penghasilanmu secara otomatis')

@section('actions')
    @if ($aktif)
        <button type="button" disabled
            class="btn btn-secondary cursor-not-allowed opacity-60"
            title="Masih ada tantangan aktif. Selesaikan atau hapus terlebih dahulu.">
            <x-icon name="plus" class="h-4 w-4" />
            Buat tantangan
        </button>
    @else
        <button type="button" data-modal-open="tantangan-create" class="btn btn-primary">
            <x-icon name="plus" class="h-4 w-4" />
            Buat tantangan
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

    <div class="grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            @if ($aktif && $progress)
                <section class="card overflow-hidden">
                    <div class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500 px-5 py-6 text-white sm:px-6">
                        <div class="pointer-events-none absolute -right-12 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
                        <div class="pointer-events-none absolute -bottom-20 right-20 h-40 w-40 rounded-full bg-white/10"></div>

                        <div class="relative flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 text-brand-100">
                                    <x-icon name="target" class="h-4 w-4" />
                                    <p class="text-xs font-bold uppercase tracking-wider">Tantangan aktif</p>
                                </div>

                                <h2 class="mt-3 truncate text-2xl font-extrabold tracking-tight">{{ $aktif->nama }}</h2>
                                <p class="mt-1 text-xs text-brand-100/90">
                                    Dimulai {{ $aktif->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                </p>
                            </div>

                            <div class="flex shrink-0 items-center gap-1.5">
                                <button type="button" data-edit-tantangan data-id="{{ $aktif->id }}"
                                    data-action="{{ route('app.tantangan.update', $aktif) }}"
                                    data-nama="{{ $aktif->nama }}" data-target="{{ $aktif->target }}"
                                    class="rounded-xl bg-white/15 p-2.5 text-white transition hover:bg-white/25"
                                    title="Edit tantangan">
                                    <x-icon name="pencil" class="h-4 w-4" />
                                </button>

                                @if ($aktifMemilikiTransaksi)
                                    <span class="cursor-not-allowed rounded-xl bg-white/10 p-2.5 text-white/50"
                                        title="Tantangan yang memiliki riwayat transaksi tidak dapat dihapus">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('app.tantangan.destroy', $aktif) }}"
                                        data-confirm="Hapus tantangan “{{ $aktif->nama }}”?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="rounded-xl bg-white/15 p-2.5 text-white transition hover:bg-rose-500"
                                            title="Hapus tantangan">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="relative mt-7 flex items-end justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold text-brand-100/80">Progress penghasilan sendiri</p>
                                <p class="mt-1 text-3xl font-extrabold tabular-nums">
                                    <x-money :value="$progress['progress']" />
                                </p>
                            </div>

                            <p class="text-3xl font-extrabold tabular-nums text-white">
                                {{ number_format($persentase, 0, ',', '.') }}%
                            </p>
                        </div>

                        <div class="relative mt-4 h-3 w-full overflow-hidden rounded-full bg-white/20">
                            <div class="h-full rounded-full bg-gradient-to-r from-white to-brand-100 transition-all duration-500"
                                style="width: {{ max(0, min(100, $persentase)) }}%"></div>
                        </div>

                        <div class="relative mt-2 flex items-center justify-between text-xs text-brand-100/90">
                            <span>Target <x-money :value="$aktif->target" /></span>
                            <span>Sisa <x-money :value="$progress['sisa_target']" /></span>
                        </div>
                    </div>

                    <div class="grid gap-3 p-5 sm:grid-cols-3">
                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-400">Target</p>
                            <p class="mt-1 text-sm font-extrabold tabular-nums text-slate-800">
                                <x-money :value="$aktif->target" />
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-400">Sisa target</p>
                            <p class="mt-1 text-sm font-extrabold tabular-nums text-slate-800">
                                <x-money :value="$progress['sisa_target']" />
                            </p>
                        </div>

                        <div class="rounded-xl bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold text-slate-400">Status</p>
                            <p class="mt-1">
                                <x-badge tone="aktif">Aktif</x-badge>
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 px-5 py-4 text-xs leading-relaxed text-slate-500">
                        Progress dihitung otomatis dari transaksi pemasukan dengan sumber
                        <strong class="font-semibold text-slate-700">penghasilan sendiri</strong> yang terkait
                        tantangan ini. Uang saku tidak dihitung.
                    </div>
                </section>
            @else
                <section class="card p-5 sm:p-6">
                    <x-empty-state title="Belum ada tantangan aktif" icon="target"
                        message="Tentukan target penghasilan sendiri, lalu hubungkan pemasukanmu ke tantangan ini agar progress terpantau otomatis.">
                        <x-slot:action>
                            <button type="button" data-modal-open="tantangan-create" class="btn btn-primary">
                                <x-icon name="plus" class="h-4 w-4" />
                                Buat tantangan pertama
                            </button>
                        </x-slot:action>
                    </x-empty-state>
                </section>
            @endif

            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900">Riwayat tantangan</h2>
                        <p class="text-xs text-slate-400">Tantangan selesai dan yang perlu penyesuaian</p>
                    </div>

                    <x-badge tone="slate">{{ $riwayat->total() }}</x-badge>
                </div>

                @if ($riwayat->isEmpty())
                    <div class="p-5">
                        <x-empty-state title="Riwayat masih kosong" icon="trophy"
                            message="Tantangan yang sudah mencapai target atau turun di bawah target akan muncul di sini." />
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px]">
                            <thead class="bg-slate-50/70">
                                <tr>
                                    <th class="table-th">Tantangan</th>
                                    <th class="table-th text-right">Target</th>
                                    <th class="table-th text-right">Progress</th>
                                    <th class="table-th">Status</th>
                                    <th class="table-th">Selesai</th>
                                    <th class="table-th text-right">Durasi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @foreach ($riwayat as $row)
                                    @php
                                        $item = $row['tantangan'];
                                        $ringkasan = $row['ringkasan'];
                                        $tanggalAkhir = $item->tanggal_selesai ?? $item->tanggal_tercapai;
                                    @endphp

                                    <tr class="transition hover:bg-slate-50/60">
                                        <td class="table-td">
                                            <div class="flex items-center gap-3">
                                                <span
                                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                                        {{ $item->status === 'selesai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                    <x-icon name="{{ $item->status === 'selesai' ? 'trophy' : 'alert' }}"
                                                        class="h-4 w-4" />
                                                </span>

                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-bold text-slate-800">{{ $item->nama }}</p>
                                                    <p class="text-xs text-slate-400">
                                                        Mulai {{ $item->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right text-sm font-semibold tabular-nums text-slate-700">
                                            <x-money :value="$item->target" />
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right">
                                            <p class="text-sm font-bold tabular-nums text-slate-800">
                                                <x-money :value="$ringkasan['progress']" />
                                            </p>
                                            <p class="text-xs text-slate-400">
                                                {{ number_format($ringkasan['persentase'], 0, ',', '.') }}%
                                            </p>
                                        </td>

                                        <td class="table-td">
                                            <x-badge :tone="$item->status">
                                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                            </x-badge>
                                        </td>

                                        <td class="table-td whitespace-nowrap text-xs text-slate-500">
                                            {{ $tanggalAkhir ? $tanggalAkhir->locale('id')->translatedFormat('d M Y') : '—' }}
                                        </td>

                                        <td class="table-td whitespace-nowrap text-right text-xs font-semibold text-slate-500">
                                            {{ $ringkasan['durasi_hari'] !== null ? $ringkasan['durasi_hari'].' hari' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <div class="card p-5">
                <div class="flex items-center gap-2">
                    <x-icon name="sparkles" class="h-5 w-5 text-brand-500" />
                    <h2 class="section-title">Cara kerja tantangan</h2>
                </div>

                <ul class="mt-4 space-y-3 text-xs leading-relaxed text-slate-500">
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Hanya boleh ada satu tantangan aktif dalam satu waktu.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Progress dihitung dari pemasukan bersumber penghasilan sendiri.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Tantangan otomatis selesai saat progress mencapai target.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Jika progress turun di bawah target, status berubah menjadi perlu penyesuaian.
                    </li>
                    <li class="flex gap-2.5">
                        <x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-brand-500" />
                        Tantangan dengan riwayat transaksi tidak dapat dihapus.
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-brand-100 bg-brand-50/60 p-5">
                <div class="flex items-start gap-2.5">
                    <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
                    <div class="text-xs leading-relaxed text-brand-800">
                        <p class="font-bold">Hubungkan transaksi</p>
                        <p class="mt-1">
                            Saat mencatat pemasukan dengan sumber penghasilan sendiri, pilih tantangan
                            aktif agar nominalnya masuk ke progress.
                        </p>
                        <a href="{{ route('app.transaksi.create', ['jenis' => 'pemasukan']) }}"
                            class="mt-3 inline-flex items-center gap-1 font-bold text-brand-700 hover:text-brand-800">
                            Catat pemasukan
                            <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <x-modal id="tantangan-create" title="Buat tantangan"
        description="Tantangan baru langsung aktif dan dihitung sejak hari ini.">
        <form method="POST" action="{{ route('app.tantangan.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="tantangan-create">

            <div>
                <label for="tantangan_nama" class="label">Nama tantangan</label>
                <input id="tantangan_nama" name="nama" type="text" maxlength="150" required
                    value="{{ $createNama }}" placeholder="Contoh: Target freelance bulan ini"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tantangan_target" class="label">Target penghasilan</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-bold text-slate-400">
                        Rp
                    </span>
                    <input id="tantangan_target" name="target" type="number" step="0.01" min="0.01" required
                        value="{{ $createTarget }}" placeholder="0"
                        class="input pl-11 font-bold tabular-nums @error('target') input-error @enderror">
                </div>
                @error('target')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <p class="rounded-xl bg-slate-50 px-4 py-3 text-xs leading-relaxed text-slate-500">
                Pastikan tidak ada tantangan aktif lain. Selesaikan atau hapus tantangan aktif terlebih
                dahulu sebelum membuat yang baru.
            </p>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Buat tantangan
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal id="tantangan-edit" title="Edit tantangan"
        description="Perbarui nama atau target. Status akan disesuaikan otomatis dengan progress saat ini.">
        <form id="tantangan-edit-form" method="POST" action="{{ $editAction }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="tantangan-edit">
            <input type="hidden" name="_tantangan_id" value="{{ $editTantanganId }}">

            <div>
                <label for="tantangan_edit_nama" class="label">Nama tantangan</label>
                <input id="tantangan_edit_nama" name="nama" type="text" maxlength="150" required
                    value="{{ $editNama }}" placeholder="Nama tantangan"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tantangan_edit_target" class="label">Target penghasilan</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-bold text-slate-400">
                        Rp
                    </span>
                    <input id="tantangan_edit_target" name="target" type="number" step="0.01" min="0.01" required
                        value="{{ $editTarget }}" placeholder="0"
                        class="input pl-11 font-bold tabular-nums @error('target') input-error @enderror">
                </div>
                @error('target')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50 px-4 py-3 text-xs leading-relaxed text-amber-800">
                Menurunkan target di bawah progress saat ini akan menandai tantangan sebagai selesai.
                Tantangan yang sudah selesai tidak dapat diubah lagi.
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan perubahan
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
