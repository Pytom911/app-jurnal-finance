<?php

namespace App\Services;

use App\Models\AkunKeuangan;
use App\Models\KategoriTransaksi;
use App\Models\Tantangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionService
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly ChallengeService $challengeService,
    ) {}

    public function buat(User $user, array $data): Transaksi
    {
        return DB::transaction(function () use ($user, $data) {
            $this->kunciUser($user);

            $transaksi = $user->transaksi()->create(
                $this->siapkanAtribut($user, $data)
            );

            $this->sinkronkanTantangan($user, $transaksi);

            return $transaksi->refresh();
        });
    }

    public function perbarui(User $user, Transaksi $transaksi, array $data): Transaksi
    {
        return DB::transaction(function () use ($user, $transaksi, $data) {
            $this->kunciUser($user);
            $this->pastikanMilik($user, $transaksi);

            $tantanganLama = $transaksi->tantangan_id;

            $transaksi->update(
                $this->siapkanAtribut($user, $data, $transaksi)
            );

            $transaksi->refresh();

            $this->sinkronkanTantangan($user, $transaksi, $tantanganLama);

            return $transaksi;
        });
    }

    public function hapus(User $user, Transaksi $transaksi): void
    {
        DB::transaction(function () use ($user, $transaksi) {
            $this->kunciUser($user);
            $this->pastikanMilik($user, $transaksi);

            $tantanganId = $transaksi->tantangan_id;

            $transaksi->delete();

            if ($tantanganId) {
                $tantangan = $user->tantangan()->whereKey($tantanganId)->first();

                if ($tantangan) {
                    $this->challengeService->sinkronkan($user, $tantangan);
                }
            }
        });
    }

    /**
     * Susun atribut transaksi yang sudah dinormalisasi dan divalidasi.
     *
     * @return array<string, mixed>
     */
    private function siapkanAtribut(User $user, array $data, ?Transaksi $kecuali = null): array
    {
        $jenis = $data['jenis'] ?? null;

        if (! in_array($jenis, [
            Transaksi::JENIS_PEMASUKAN,
            Transaksi::JENIS_PENGELUARAN,
            Transaksi::JENIS_TRANSFER,
            Transaksi::JENIS_SALDO_AWAL,
        ], true)) {
            throw ValidationException::withMessages(['jenis' => 'Jenis transaksi tidak valid.']);
        }

        $nominal = round((float) ($data['nominal'] ?? 0), 2);

        if ($nominal <= 0) {
            throw ValidationException::withMessages(['nominal' => 'Nominal harus lebih besar dari nol.']);
        }

        $dasar = [
            'jenis' => $jenis,
            'nominal' => $nominal,
            'tanggal' => $this->parseTanggal($data['tanggal'] ?? null),
            'keterangan' => $this->parseKeterangan($data['keterangan'] ?? null),
            'kategori_id' => null,
            'tantangan_id' => null,
            'sumber_pemasukan' => null,
            'akun_asal_id' => null,
            'akun_tujuan_id' => null,
        ];

        return match ($jenis) {
            Transaksi::JENIS_PEMASUKAN => array_merge(
                $dasar,
                $this->atributPemasukan($user, $data, $kecuali)
            ),
            Transaksi::JENIS_PENGELUARAN => array_merge(
                $dasar,
                $this->atributPengeluaran($user, $data, $nominal, $kecuali)
            ),
            Transaksi::JENIS_TRANSFER => array_merge(
                $dasar,
                $this->atributTransfer($user, $data, $nominal, $kecuali)
            ),
            Transaksi::JENIS_SALDO_AWAL => array_merge(
                $dasar,
                $this->atributSaldoAwal($user, $data)
            ),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function atributPemasukan(User $user, array $data, ?Transaksi $kecuali = null): array
    {
        $sumber = $data['sumber_pemasukan'] ?? null;

        if (! in_array($sumber, [
            Transaksi::SUMBER_UANG_SAKU,
            Transaksi::SUMBER_PENGHASILAN_SENDIRI,
        ], true)) {
            throw ValidationException::withMessages([
                'sumber_pemasukan' => 'Sumber pemasukan tidak valid.',
            ]);
        }

        $akunCair = $this->balanceService->akunCair($user);

        if (! empty($data['akun_tujuan_id'])
            && (int) $data['akun_tujuan_id'] !== (int) $akunCair->getKey()) {
            throw ValidationException::withMessages([
                'akun_tujuan_id' => 'Semua pemasukan hanya masuk ke Saldo Cair.',
            ]);
        }

        $tantanganId = null;

        if ($sumber === Transaksi::SUMBER_PENGHASILAN_SENDIRI && ! empty($data['tantangan_id'])) {
            $tantanganId = $this->validasiTantanganAktif(
                $user,
                $data['tantangan_id'],
                $kecuali?->tantangan_id
            );
        }

        return [
            'sumber_pemasukan' => $sumber,
            'kategori_id' => $this->validasiKategori(
                $user,
                $data['kategori_id'] ?? null,
                KategoriTransaksi::JENIS_PEMASUKAN
            ),
            'tantangan_id' => $tantanganId,
            'akun_tujuan_id' => $akunCair->getKey(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function atributPengeluaran(User $user, array $data, float $nominal, ?Transaksi $kecuali): array
    {
        $akunCair = $this->balanceService->akunCair($user);

        if (! empty($data['akun_asal_id'])
            && (int) $data['akun_asal_id'] !== (int) $akunCair->getKey()) {
            throw ValidationException::withMessages([
                'akun_asal_id' => 'Pengeluaran hanya boleh menggunakan Saldo Cair.',
            ]);
        }

        $saldo = $this->balanceService->saldoAkun($user, $akunCair, $kecuali);

        if ($this->toCents($saldo) < $this->toCents($nominal)) {
            throw ValidationException::withMessages([
                'nominal' => 'Saldo cair tidak mencukupi untuk pengeluaran ini.',
            ]);
        }

        return [
            'kategori_id' => $this->validasiKategori(
                $user,
                $data['kategori_id'] ?? null,
                KategoriTransaksi::JENIS_PENGELUARAN
            ),
            'akun_asal_id' => $akunCair->getKey(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function atributTransfer(User $user, array $data, float $nominal, ?Transaksi $kecuali): array
    {
        $akunAsal = $this->akunMilik($user, $data['akun_asal_id'] ?? null, 'akun_asal_id');
        $akunTujuan = $this->akunMilik($user, $data['akun_tujuan_id'] ?? null, 'akun_tujuan_id');

        if ((int) $akunAsal->getKey() === (int) $akunTujuan->getKey()) {
            throw ValidationException::withMessages([
                'akun_tujuan_id' => 'Akun asal dan akun tujuan tidak boleh sama.',
            ]);
        }

        $saldo = $this->balanceService->saldoAkun($user, $akunAsal, $kecuali);

        if ($this->toCents($saldo) < $this->toCents($nominal)) {
            throw ValidationException::withMessages([
                'nominal' => "Saldo akun {$akunAsal->nama} tidak mencukupi untuk transfer ini.",
            ]);
        }

        return [
            'akun_asal_id' => $akunAsal->getKey(),
            'akun_tujuan_id' => $akunTujuan->getKey(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function atributSaldoAwal(User $user, array $data): array
    {
        $akunTujuan = ! empty($data['akun_tujuan_id'])
            ? $this->akunMilik($user, $data['akun_tujuan_id'], 'akun_tujuan_id')
            : $this->balanceService->akunCair($user);

        return [
            'akun_tujuan_id' => $akunTujuan->getKey(),
        ];
    }

    private function validasiKategori(User $user, mixed $kategoriId, string $jenis): ?int
    {
        if ($kategoriId === null || $kategoriId === '') {
            return null;
        }

        $kategori = $user->kategoriTransaksi()->whereKey($kategoriId)->first();

        if (! $kategori) {
            throw ValidationException::withMessages([
                'kategori_id' => 'Kategori tidak ditemukan atau bukan milik user.',
            ]);
        }

        if ($kategori->jenis !== $jenis) {
            throw ValidationException::withMessages([
                'kategori_id' => 'Jenis kategori tidak sesuai dengan jenis transaksi.',
            ]);
        }

        return (int) $kategori->getKey();
    }

    private function validasiTantanganAktif(User $user, mixed $tantanganId, ?int $tautanLama = null): int
    {
        $tantangan = $user->tantangan()->whereKey($tantanganId)->first();

        if (! $tantangan) {
            throw ValidationException::withMessages([
                'tantangan_id' => 'Tantangan tidak ditemukan atau bukan milik user.',
            ]);
        }

        $sudahTertaut = $tautanLama !== null && (int) $tantangan->getKey() === (int) $tautanLama;

        if ($tantangan->status !== Tantangan::STATUS_AKTIF && ! $sudahTertaut) {
            throw ValidationException::withMessages([
                'tantangan_id' => 'Penghasilan hanya dapat dikaitkan dengan tantangan yang masih aktif.',
            ]);
        }

        return (int) $tantangan->getKey();
    }

    private function akunMilik(User $user, mixed $akunId, string $field): AkunKeuangan
    {
        if ($akunId === null || $akunId === '') {
            throw ValidationException::withMessages([$field => 'Akun wajib dipilih.']);
        }

        $akun = $user->akunKeuangan()->whereKey($akunId)->first();

        if (! $akun) {
            throw ValidationException::withMessages([
                $field => 'Akun tidak ditemukan atau bukan milik user.',
            ]);
        }

        return $akun;
    }

    private function sinkronkanTantangan(User $user, Transaksi $transaksi, ?int $tantanganLama = null): void
    {
        $ids = array_values(array_unique(array_filter([
            $tantanganLama,
            $transaksi->tantangan_id ? (int) $transaksi->tantangan_id : null,
        ])));

        foreach ($ids as $id) {
            $tantangan = $user->tantangan()->whereKey($id)->first();

            if ($tantangan) {
                $this->challengeService->sinkronkan($user, $tantangan);
            }
        }
    }

    private function parseTanggal(mixed $tanggal): string
    {
        if ($tanggal === null || $tanggal === '') {
            return Carbon::today()->toDateString();
        }

        try {
            return Carbon::parse($tanggal)->toDateString();
        } catch (Throwable) {
            throw ValidationException::withMessages(['tanggal' => 'Tanggal tidak valid.']);
        }
    }

    private function parseKeterangan(mixed $keterangan): ?string
    {
        if ($keterangan === null || $keterangan === '') {
            return null;
        }

        return trim((string) $keterangan) ?: null;
    }

    private function pastikanMilik(User $user, Transaksi $transaksi): void
    {
        if ((int) $transaksi->user_id !== (int) $user->getKey()) {
            throw new AuthorizationException('Transaksi bukan milik user.');
        }
    }

    private function kunciUser(User $user): void
    {
        User::query()->whereKey($user->getKey())->lockForUpdate()->first();
    }

    private function toCents(float $nominal): int
    {
        return (int) round($nominal * 100);
    }
}
