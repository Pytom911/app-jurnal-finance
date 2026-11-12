@extends('layouts.app')

@section('title', 'Kategori Transaksi')
@section('subtitle', 'Kelompokkan pos pemasukan dan pengeluaran agar analisis lebih akurat')

@section('actions')
    <button type="button" data-modal-open="kategori-create" class="btn btn-primary">
        <x-icon name="plus" class="h-4 w-4" />
        Tambah Kategori
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
                'label' => 'Kategori Pemasukan',
                'desc' => 'Untuk pos uang saku, gaji, dividen, dan penghasilan mandiri.',
                'icon' => 'arrow-down-left',
                'class' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20',
                'tone' => 'pemasukan',
            ],
            'pengeluaran' => [
                'label' => 'Kategori Pengeluaran',
                'desc' => 'Untuk belanja, tagihan, makanan, dan kebutuhan sehari-hari.',
                'icon' => 'arrow-up-right',
                'class' => 'bg-rose-500/10 text-rose-600 border border-rose-500/20',
                'tone' => 'pengeluaran',
            ],
        ];
    @endphp

    <!-- Info Banner (Clean Glass) -->
    <div class="mb-6 flex items-start gap-3 rounded-2xl border border-cyan-200/60 bg-gradient-to-r from-cyan-50/80 via-white/60 to-teal-50/40 p-4 text-xs font-medium text-cyan-950 shadow-sm backdrop-blur-md">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-cyan-500/20 text-cyan-700">
            <x-icon name="tag" class="h-4 w-4" />
        </span>
        <p class="leading-relaxed pt-0.5">
            Kategori membantu merapikan riwayat mutasi dana Anda. Kategori yang sudah pernah digunakan pada transaksi aktif terkunci dari penghapusan demi menjaga integritas data riwayat.
        </p>
    </div>

    <!-- Category Groups Grid -->
    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($kelompok as $jenis => $meta)
            @php
                $daftar = $kategori->get($jenis, collect());
            @endphp

            <section class="glass-card overflow-hidden">
                <div class="flex items-center gap-3.5 border-b border-cyan-100/50 bg-gradient-to-r from-white/70 via-cyan-50/20 to-transparent px-5 py-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $meta['class'] }} shadow-sm backdrop-blur-sm">
                        <x-icon :name="$meta['icon']" class="h-5 w-5" />
                    </span>

                    <div class="min-w-0 flex-1">
                        <h2 class="text-sm font-black tracking-tight text-slate-900">{{ $meta['label'] }}</h2>
                        <p class="text-[11px] font-medium text-slate-400">{{ $meta['desc'] }}</p>
                    </div>

                    <x-badge :tone="$meta['tone']">{{ $daftar->count() }} Kategori</x-badge>
                </div>

                @if ($daftar->isEmpty())
                    <div class="p-6">
                        <x-empty-state :title="'Belum Ada Kategori '.$jenis" icon="tag"
                            message="Tambahkan pos kategori agar setiap transaksi yang dicatat lebih mudah dilacak.">
                            <x-slot:action>
                                <button type="button" data-modal-open="kategori-create" class="btn btn-secondary btn-sm">
                                    <x-icon name="plus" class="h-3.5 w-3.5" />
                                    Tambah Kategori
                                </button>
                            </x-slot:action>
                        </x-empty-state>
                    </div>
                @else
                    <ul class="divide-y divide-cyan-100/30">
                        @foreach ($daftar as $item)
                            <li class="flex items-center gap-3.5 px-5 py-3.5 transition-colors hover:bg-cyan-50/40 group">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200/60 bg-white/70 text-slate-500 shadow-sm backdrop-blur-sm transition-colors group-hover:border-cyan-300 group-hover:text-cyan-600">
                                    <x-icon :name="$jenis === 'pemasukan' ? 'arrow-down-left' : 'arrow-up-right'" class="h-4 w-4" />
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-extrabold text-slate-800 group-hover:text-cyan-900">{{ $item->nama }}</p>
                                    <p class="text-[11px] font-medium text-slate-400">
                                        Digunakan pada {{ $item->transaksi_count }} transaksi
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1.5">
                                    <button type="button" data-edit-kategori data-id="{{ $item->id }}"
                                        data-action="{{ route('app.kategori.update', $item) }}"
                                        data-nama="{{ $item->nama }}" data-jenis="{{ $item->jenis }}"
                                        class="rounded-xl border border-slate-200/60 bg-white/60 p-2 text-slate-500 backdrop-blur-sm transition hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700"
                                        title="Edit kategori">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </button>

                                    @if ($item->transaksi_count > 0)
                                        <span
                                            class="cursor-not-allowed rounded-xl border border-slate-200/40 bg-slate-100/40 p-2 text-slate-300"
                                            title="Kategori masih aktif digunakan pada transaksi dan tidak dapat dihapus">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('app.kategori.destroy', $item) }}"
                                            data-confirm="Hapus kategori “{{ $item->nama }}”?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-xl border border-rose-200/60 bg-rose-50/60 p-2 text-rose-500 backdrop-blur-sm transition hover:border-rose-300 hover:bg-rose-100 hover:text-rose-700"
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

    <!-- Modal Create (Clean Glass) -->
    <x-modal id="kategori-create" title="Tambah Kategori Baru"
        description="Kelompokkan pos agar muncul sesuai saat pencatatan transaksi.">
        <form method="POST" action="{{ route('app.kategori.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="kategori-create">

            <div>
                <label for="kategori_nama" class="label">Nama Kategori</label>
                <input id="kategori_nama" name="nama" type="text" maxlength="100" required
                    value="{{ $createNama }}" placeholder="Contoh: Gaji Pokok, Belanja Bulanan, Freelance"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_jenis" class="label">Jenis Kategori</label>
                <select id="kategori_jenis" name="jenis" class="input @error('jenis') input-error @enderror">
                    <option value="pemasukan" @selected($createJenis === 'pemasukan')>Pemasukan (Uang Masuk)</option>
                    <option value="pengeluaran" @selected($createJenis === 'pengeluaran')>Pengeluaran (Uang Keluar)</option>
                </select>
                @error('jenis')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-cyan-100/50 pt-4 sm:flex-row sm:justify-end">
                <button type="button" data-modal-close class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <x-icon name="check" class="h-4 w-4" />
                    Simpan Kategori
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit (Clean Glass) -->
    <x-modal id="kategori-edit" title="Perbarui Kategori"
        description="Nama pos dapat diubah sewaktu-waktu. Jenis terkunci jika sudah memiliki transaksi terkait.">
        <form id="kategori-edit-form" method="POST" action="{{ $editAction }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="kategori-edit">
            <input type="hidden" name="_kategori_id" value="{{ $editKategoriId }}">

            <div>
                <label for="kategori_edit_nama" class="label">Nama Kategori</label>
                <input id="kategori_edit_nama" name="nama" type="text" maxlength="100" required
                    value="{{ $editNama }}" placeholder="Nama kategori"
                    class="input @error('nama') input-error @enderror">
                @error('nama')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_edit_jenis" class="label">Jenis Kategori</label>
                <select id="kategori_edit_jenis" name="jenis" class="input @error('jenis') input-error @enderror">
                    <option value="pemasukan" @selected($editJenis === 'pemasukan')>Pemasukan</option>
                    <option value="pengeluaran" @selected($editJenis === 'pengeluaran')>Pengeluaran</option>
                </select>
                @error('jenis')
                    <p class="help">{{ $message }}</p>
                @enderror
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
