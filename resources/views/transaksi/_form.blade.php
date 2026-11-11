@php
    $nilaiJenis = old('jenis', $jenisTerpilih);
    $nilaiSumber = old('sumber_pemasukan', $transaksi?->sumber_pemasukan ?? 'uang_saku');
    $nilaiTanggal = old('tanggal', $transaksi?->tanggal?->toDateString() ?? $tanggalHariIni);

    $akunCair = $akun->firstWhere('jenis', 'cair');
    $akunTabungan = $akun->firstWhere('jenis', 'tabungan');

    $kategoriMasuk = $kategori->get('pemasukan', collect());
    $kategoriKeluar = $kategori->get('pengeluaran', collect());

    $pilihanTantangan = collect();

    if ($tantanganAktif) {
        $pilihanTantangan->push($tantanganAktif);
    }

    if ($transaksi?->tantangan && (int) $transaksi->tantangan->id !== (int) $tantanganAktif?->id) {
        $pilihanTantangan->push($transaksi->tantangan);
    }

    $nilaiTantangan = old('tantangan_id', $transaksi?->tantangan_id);
@endphp

<div class="space-y-5">
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="jenis" class="label">Jenis transaksi</label>
            <select id="jenis" name="jenis" required class="input @error('jenis') input-error @enderror">
                @foreach (['pemasukan' => 'Pemasukan — uang masuk', 'pengeluaran' => 'Pengeluaran — uang keluar', 'transfer' => 'Transfer — antar akun', 'saldo_awal' => 'Saldo awal — saldo pembuka'] as $value => $label)
                    <option value="{{ $value }}" @selected($nilaiJenis === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('jenis')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tanggal" class="label">Tanggal</label>
            <input id="tanggal" name="tanggal" type="date" value="{{ $nilaiTanggal }}" required
                class="input @error('tanggal') input-error @enderror">
            @error('tanggal')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="nominal" class="label">Nominal</label>
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-sm font-bold text-slate-400">
                Rp
            </span>
            <input id="nominal" name="nominal" type="number" step="0.01" min="0.01"
                value="{{ old('nominal', $transaksi?->nominal) }}" required placeholder="0"
                class="input pl-11 text-base font-bold tabular-nums @error('nominal') input-error @enderror">
        </div>
        @error('nominal')
            <p class="help">{{ $message }}</p>
        @enderror
    </div>

    <div data-jenis-section="pemasukan">
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-xs font-medium text-emerald-800">
            Semua pemasukan otomatis masuk ke <strong>{{ $akunCair->nama ?? 'Saldo Cair' }}</strong>.
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="sumber_pemasukan" class="label">Sumber pemasukan</label>
                <select id="sumber_pemasukan" name="sumber_pemasukan"
                    class="input @error('sumber_pemasukan') input-error @enderror">
                    <option value="uang_saku" @selected($nilaiSumber === 'uang_saku')>Uang saku</option>
                    <option value="penghasilan_sendiri" @selected($nilaiSumber === 'penghasilan_sendiri')>Penghasilan sendiri</option>
                </select>
                @error('sumber_pemasukan')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_pemasukan" class="label">Kategori</label>
                <select id="kategori_pemasukan" name="kategori_id"
                    class="input @error('kategori_id') input-error @enderror">
                    <option value="">Tanpa kategori</option>
                    @foreach ($kategoriMasuk as $item)
                        <option value="{{ $item->id }}" @selected((int) old('kategori_id', $transaksi?->kategori_id) === (int) $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div id="tantangan-wrap" class="mt-4">
            <label for="tantangan_id" class="label">Tantangan (opsional)</label>
            <select id="tantangan_id" name="tantangan_id"
                class="input @error('tantangan_id') input-error @enderror">
                <option value="">Tidak terkait tantangan</option>
                @foreach ($pilihanTantangan as $item)
                    <option value="{{ $item->id }}" @selected((int) $nilaiTantangan === (int) $item->id)>
                        {{ $item->nama }}
                        @if ($item->status !== 'aktif')
                            ({{ str_replace('_', ' ', $item->status) }})
                        @endif
                    </option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-slate-400">
                Hanya penghasilan sendiri yang dihitung sebagai progress tantangan.
            </p>
            @error('tantangan_id')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div data-jenis-section="pengeluaran">
        <div class="rounded-xl border border-rose-100 bg-rose-50/60 px-4 py-3 text-xs font-medium text-rose-700">
            Pengeluaran hanya boleh menggunakan <strong>{{ $akunCair->nama ?? 'Saldo Cair' }}</strong>.
            Saldo cair saat ini
            <strong class="tabular-nums">Rp {{ number_format($ringkasan['cair'], 0, ',', '.') }}</strong>.
        </div>

        <div class="mt-4">
            <label for="kategori_pengeluaran" class="label">Kategori</label>
            <select id="kategori_pengeluaran" name="kategori_id"
                class="input @error('kategori_id') input-error @enderror">
                <option value="">Tanpa kategori</option>
                @foreach ($kategoriKeluar as $item)
                    <option value="{{ $item->id }}" @selected((int) old('kategori_id', $transaksi?->kategori_id) === (int) $item->id)>
                        {{ $item->nama }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div data-jenis-section="transfer">
        <div class="rounded-xl border border-brand-100 bg-brand-50/60 px-4 py-3 text-xs font-medium text-brand-800">
            Transfer tidak mengubah total saldo. Pastikan saldo akun asal mencukupi.
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="akun_asal_id" class="label">Dari akun</label>
                <select id="akun_asal_id" name="akun_asal_id"
                    class="input @error('akun_asal_id') input-error @enderror">
                    @foreach ($akun as $item)
                        <option value="{{ $item->id }}" @selected((int) old('akun_asal_id', $transaksi?->akun_asal_id ?? $akunCair?->id) === (int) $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('akun_asal_id')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="akun_tujuan_id" class="label">Ke akun</label>
                <select id="akun_tujuan_id" name="akun_tujuan_id"
                    class="input @error('akun_tujuan_id') input-error @enderror">
                    @foreach ($akun as $item)
                        <option value="{{ $item->id }}" @selected((int) old('akun_tujuan_id', $transaksi?->akun_tujuan_id ?? $akunTabungan?->id) === (int) $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('akun_tujuan_id')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div data-jenis-section="saldo_awal">
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-medium text-slate-600">
            Catat saldo pembuka sesuai kondisi nyata. Bisa untuk Saldo Cair maupun Saldo Tabungan.
        </div>

        <div class="mt-4">
            <label for="akun_saldo_awal" class="label">Masuk ke akun</label>
            <select id="akun_saldo_awal" name="akun_tujuan_id"
                class="input @error('akun_tujuan_id') input-error @enderror">
                @foreach ($akun as $item)
                    <option value="{{ $item->id }}" @selected((int) old('akun_tujuan_id', $transaksi?->akun_tujuan_id ?? $akunCair?->id) === (int) $item->id)>
                        {{ $item->nama }}
                    </option>
                @endforeach
            </select>
            @error('akun_tujuan_id')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="keterangan" class="label">Keterangan <span class="font-normal text-slate-400">(opsional)</span></label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000"
            placeholder="Contoh: Makan siang, gaji freelance, atau pindah dana ke tabungan"
            class="input resize-none @error('keterangan') input-error @enderror">{{ old('keterangan', $transaksi?->keterangan) }}</textarea>
        @error('keterangan')
            <p class="help">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $transaksi ? route('app.transaksi.show', $transaksi) : route('app.transaksi.index') }}"
            class="btn btn-secondary">
            Batal
        </a>

        <button type="submit" class="btn btn-primary">
            <x-icon name="check" class="h-4 w-4" />
            {{ $transaksi ? 'Simpan perubahan' : 'Simpan transaksi' }}
        </button>
    </div>
</div>
