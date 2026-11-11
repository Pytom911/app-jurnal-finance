@extends('layouts.app')

@section('title', 'Kategori')
@section('subtitle', 'Kelompokkan transaksi agar laporan lebih rapi')

@section('actions')
    <button type="button" data-modal-open="kategori-create" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Tambah kategori
    </button>
@endsection

@section('content')
    @php
        $modalTerbuka = old('_modal');
        $editKategoriId = old('_kategori_id');
        $editAction = $editKategoriId ? route('app.kategori.update', $editKategoriId) : '';

        $createNama = $modalTerbuka === 'kategori-create' ? old('nama') : '';
        $createJenis = $modalTerbuka === 'kategori-create' ? old('jenis', 'pemasukan') : 'pemasukan';

        $editNama = $modalTerbuka === 'kategori-edit' ? old('nama') : '';
        $editJenis = $modalTerbuka === 'kategori-edit' ? old('jenis', 'pemasukan') : 'pemasukan';

        $kelompok = [
            'pemasukan' => [
                'label' => 'Kategori pemasukan',
                'desc' => 'Untuk uang saku dan penghasilan sendiri.',
                'icon' => 'arrow-down-left',
                'class' => 'bg-emerald-50 text-emerald-600',
                'tone' => 'pemasukan',
            ],
            'pengeluaran' => [
                'label' => 'Kategori pengeluaran',
                'desc' => 'Untuk belanja dan kebutuhan sehari-hari.',
                'icon' => 'arrow-up-right',
                'class' => 'bg-rose-50 text-rose-500',
                'tone' => 'pengeluaran',
            ],
        ];
    @endphp

    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-brand-100 bg-brand-50/60 px-4 py-3.5 text-sm text-brand-800">
        <x-icon name="tag" class="mt-0.5 h-5 w-5 shrink-0 text-brand-500" />
        <p class="leading-relaxed">
            Kategori membantu merapikan riwayat transaksi. Kategori yang sudah dipakai transaksi
            tidak dapat dihapus, dan jenisnya tidak dapat diubah.
        </p>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        @foreach ($kelompok as $jenis => $meta)
            @php
                $daftar = $kategori->get($jenis, collect());
            @endphp

            <section class="card overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $meta['class'] }}">
                        <x-icon :name="$meta['icon']" class="h-5 w-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900">{{ $meta['label'] }}</h2>
                        <p class="text-xs text-slate-400">{{ $meta['desc'] }}</p>
                    </div>

                    <x-badge :tone="$meta['tone']">{{ $daftar->count() }}</x-badge>
                </div>

                @if ($daftar->isEmpty())
                    <div class="p-5">
                        <x-empty-state :title="'Belum ada kategori '.$jenis" icon="tag"
                            message="Tambahkan kategori agar transaksi lebih mudah dicari dan direkap.">
                            <x-slot:action>
                                <button type="button" data-modal-open="kategori-create" class="btn btn-secondary btn-sm">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Tambah kategori
                                </button>
                            </x-slot:action>
                        </x-empty-state>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($daftar as $item)
                            <li class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50/60">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                    <x-icon :name="$jenis === 'pemasukan' ? 'arrow-down-left' : 'arrow-up-right'" class="h-4 w-4" />
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-bold text-slate-800">{{ $item->nama }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $item->transaksi_count }} transaksi
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1">
                                    <button type="button" data-edit-kategori data-id="{{ $item->id }}"
                                        data-action="{{ route('app.kategori.update', $item) }}"
                                        data-nama="{{ $item->nama }}" data-jenis="{{ $item->jenis }}"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-600"
                                        title="Edit kategori">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>

                                    @if ($item->transaksi_count > 0)
                                        <span
                                            class="cursor-not-allowed rounded-lg p-2 text-slate-300"
                                            title="Kategori masih digunakan oleh transaksi dan tidak dapat dihapus">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('app.kategori.destroy', $item) }}"
                                            data-confirm="Hapus kategori “{{ $item->nama }}”?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-500"
                                                title="Hapus kategori">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>

    <x-modal id="kategori-create" title="Tambah kategori"
        description="Pilih jenis yang sesuai agar kategori hanya muncul pada form transaksi terkait.">
        <form method="POST" action="{{ route('app.kategori.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="kategori-create">

            <div>
                <label for="kategori_nama" class="label">Nama kategori</label>
                <input id="kategori_nama" name="nama" type="text" maxlength="100" required
                    value="{{ $createNama }}" placeholder="Contoh: Gaji, Makan, Transportasi"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_jenis" class="label">Jenis</label>
                <select id="kategori_jenis" name="jenis" class="input @error('jenis') input-error @enderror">
                    <option value="pemasukan" @selected($createJenis === 'pemasukan')>Pemasukan</option>
                    <option value="pengeluaran" @selected($createJenis === 'pengeluaran')>Pengeluaran</option>
                </select>
                @error('jenis')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan kategori
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal id="kategori-edit" title="Edit kategori"
        description="Nama bisa diubah kapan saja. Jenis hanya bisa diubah jika kategori belum dipakai transaksi.">
        <form id="kategori-edit-form" method="POST" action="{{ $editAction }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="kategori-edit">
            <input type="hidden" name="_kategori_id" value="{{ $editKategoriId }}">

            <div>
                <label for="kategori_edit_nama" class="label">Nama kategori</label>
                <input id="kategori_edit_nama" name="nama" type="text" maxlength="100" required
                    value="{{ $editNama }}" placeholder="Nama kategori"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_edit_jenis" class="label">Jenis</label>
                <select id="kategori_edit_jenis" name="jenis" class="input @error('jenis') input-error @enderror">
                    <option value="pemasukan" @selected($editJenis === 'pemasukan')>Pemasukan</option>
                    <option value="pengeluaran" @selected($editJenis === 'pengeluaran')>Pengeluaran</option>
                </select>
                @error('jenis')
                    <p class="help">{{ $message }}</p>
                @enderror
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
