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

<div class="space-y-6">
    <!-- Row: Jenis & Tanggal -->
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="jenis" class="label">Jenis Transaksi</label>
            <div class="relative">
                <select id="jenis" name="jenis" required class="input font-semibold @error('jenis') input-error @enderror">
                    @foreach (['pemasukan' => 'Pemasukan — Uang Masuk', 'pengeluaran' => 'Pengeluaran — Uang Keluar', 'transfer' => 'Transfer — Pindah Antar Akun', 'saldo_awal' => 'Saldo Awal — Pembuka Akun'] as $value => $label)
                        <option value="{{ $value }}" @selected($nilaiJenis === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @error('jenis')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tanggal" class="label">Tanggal Transaksi</label>
            <input id="tanggal" name="tanggal" type="date" value="{{ $nilaiTanggal }}" required
                class="input @error('tanggal') input-error @enderror">
            @error('tanggal')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Nominal Input (Prominent Glass Display) -->
    <div class="rounded-2xl border border-cyan-200/60 bg-gradient-to-br from-white/90 via-cyan-50/30 to-white/70 p-4 shadow-sm backdrop-blur-md">
        <label for="nominal" class="label text-cyan-900">Jumlah Nominal Transaksi</label>
        <div class="relative mt-1">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-base font-black text-cyan-600">
                Rp
            </span>
            <input id="nominal" name="nominal" type="number" step="0.01" min="0.01"
                value="{{ old('nominal', $transaksi?->nominal) }}" required placeholder="0"
                class="w-full rounded-xl border border-cyan-200/80 bg-white/80 pl-13 pr-4 py-3 text-xl font-black tabular-nums text-slate-900 shadow-inner backdrop-blur-md transition-all focus:border-cyan-400 focus:bg-white focus:outline-none focus:ring-4 focus:ring-cyan-500/20 @error('nominal') input-error @enderror">
        </div>
        @error('nominal')
            <p class="help">{{ $message }}</p>
        @enderror
    </div>

    <!-- Section: Pemasukan -->
    <div data-jenis-section="pemasukan" class="space-y-4">
        <div class="flex items-center gap-2.5 rounded-2xl border border-emerald-300/40 bg-emerald-500/10 p-3.5 text-xs font-semibold text-emerald-900 backdrop-blur-md">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-700">
                <x-icon name="check" class="h-4 w-4" />
            </span>
            <p>Pemasukan akan dialirkan otomatis ke saldo <strong>{{ $akunCair->nama ?? 'Saldo Cair' }}</strong>.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="sumber_pemasukan" class="label">Sumber Dana Pemasukan</label>
                <select id="sumber_pemasukan" name="sumber_pemasukan"
                    class="input @error('sumber_pemasukan') input-error @enderror">
                    <option value="uang_saku" @selected($nilaiSumber === 'uang_saku')>Uang Saku / Pemberian</option>
                    <option value="penghasilan_sendiri" @selected($nilaiSumber === 'penghasilan_sendiri')>Penghasilan Sendiri (Mandiri)</option>
                </select>
                @error('sumber_pemasukan')
                    <p class="help">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kategori_pemasukan" class="label">Kategori Pemasukan</label>
                <select id="kategori_pemasukan" name="kategori_id"
                    class="input @error('kategori_id') input-error @enderror">
                    <option value="">Tanpa Kategori</option>
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

        <div id="tantangan-wrap" class="rounded-2xl border border-cyan-200/60 bg-cyan-50/40 p-4 backdrop-blur-md">
            <label for="tantangan_id" class="label text-cyan-900">Hubungkan ke Target Tantangan (Opsional)</label>
            <select id="tantangan_id" name="tantangan_id"
                class="input @error('tantangan_id') input-error @enderror">
                <option value="">Tidak Terkait Tantangan</option>
                @foreach ($pilihanTantangan as $item)
                    <option value="{{ $item->id }}" @selected((int) $nilaiTantangan === (int) $item->id)>
                        {{ $item->nama }}
                        @if ($item->status !== 'aktif')
                            ({{ str_replace('_', ' ', $item->status) }})
                        @endif
                    </option>
                @endforeach
            </select>
            <p class="mt-2 text-xs font-medium text-slate-500">
                Hanya penghasilan sendiri yang dikumulatifkan ke progress tantangan.
            </p>
            @error('tantangan_id')
                <p class="help">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Section: Pengeluaran -->
    <div data-jenis-section="pengeluaran" class="space-y-4">
        <div class="flex items-center gap-2.5 rounded-2xl border border-rose-300/40 bg-rose-500/10 p-3.5 text-xs font-semibold text-rose-900 backdrop-blur-md">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-rose-500/20 text-rose-700">
                <x-icon name="alert" class="h-4 w-4" />
            </span>
            <p>
                Pengeluaran dipotong langsung dari <strong>{{ $akunCair->nama ?? 'Saldo Cair' }}</strong>. 
                Sisa saldo cair saat ini: <span class="font-extrabold text-slate-900 tabular-nums">Rp {{ number_format($ringkasan['cair'], 0, ',', '.') }}</span>.
            </p>
        </div>

        <div>
            <label for="kategori_pengeluaran" class="label">Kategori Pengeluaran</label>
            <select id="kategori_pengeluaran" name="kategori_id"
                class="input @error('kategori_id') input-error @enderror">
                <option value="">Tanpa Kategori</option>
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

    <!-- Section: Transfer -->
    <div data-jenis-section="transfer" class="space-y-4">
        <div class="flex items-center gap-2.5 rounded-2xl border border-cyan-300/40 bg-cyan-500/10 p-3.5 text-xs font-semibold text-cyan-900 backdrop-blur-md">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-cyan-500/20 text-cyan-700">
                <x-icon name="transfer" class="h-4 w-4" />
            </span>
            <p>Transfer antar akun menjaga total saldo tetap utuh. Pastikan saldo pada akun asal mencukupi.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="akun_asal_id" class="label">Dari Akun (Pengirim)</label>
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
                <label for="akun_tujuan_id" class="label">Ke Akun (Penerima)</label>
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

    <!-- Section: Saldo Awal -->
    <div data-jenis-section="saldo_awal" class="space-y-4">
        <div class="flex items-center gap-2.5 rounded-2xl border border-slate-300/50 bg-slate-500/10 p-3.5 text-xs font-semibold text-slate-800 backdrop-blur-md">
            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-slate-500/20 text-slate-700">
                <x-icon name="banknotes" class="h-4 w-4" />
            </span>
            <p>Digunakan untuk inisialisasi saldo riil saat pertama kali membuat akun atau penyesuaian modal awal.</p>
        </div>

        <div>
            <label for="akun_saldo_awal" class="label">Setor Saldo Awal ke Akun</label>
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

    <!-- Keterangan -->
    <div>
        <label for="keterangan" class="label">Keterangan Catatan <span class="font-normal text-slate-400 lowercase">(opsional)</span></label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000"
            placeholder="Tuliskan catatan tambahan seperti tujuan belanja, rincian pekerjaan freelance, dll."
            class="input resize-none @error('keterangan') input-error @enderror">{{ old('keterangan', $transaksi?->keterangan) }}</textarea>
        @error('keterangan')
            <p class="help">{{ $message }}</p>
        @enderror
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col-reverse gap-3 border-t border-cyan-100/50 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $transaksi ? route('app.transaksi.show', $transaksi) : route('app.transaksi.index') }}"
            class="btn btn-secondary">
            Batal
        </a>

        <button type="submit" class="btn btn-primary px-6">
            <x-icon name="check" class="h-4 w-4" />
            {{ $transaksi ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
        </button>
    </div>
</div>
