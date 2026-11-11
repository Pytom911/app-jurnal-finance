<?php

namespace App\Services;

use App\Models\AkunKeuangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BalanceService
{
    /**
     * Ambil akun milik user berdasarkan jenisnya.
     */
    public function akun(User $user, string $jenis): AkunKeuangan
    {
        $akun = $user->akunKeuangan()->where('jenis', $jenis)->first();

        if (! $akun) {
            throw (new ModelNotFoundException)->setModel(AkunKeuangan::class, [$jenis]);
        }

        return $akun;
    }

    public function akunCair(User $user): AkunKeuangan
    {
        return $this->akun($user, AkunKeuangan::JENIS_CAIR);
    }

    public function akunTabungan(User $user): AkunKeuangan
    {
        return $this->akun($user, AkunKeuangan::JENIS_TABUNGAN);
    }

    /**
     * Saldo satu akun berdasarkan riwayat transaksi aktif.
     *
     * Transaksi yang sedang diedit dapat dikecualikan agar validasi saldo
     * tidak menghitung efek transaksi lama dua kali.
     */
    public function saldoAkun(User $user, AkunKeuangan $akun, ?Transaksi $kecuali = null): float
    {
        if ((int) $akun->user_id !== (int) $user->getKey()) {
            throw new AuthorizationException('Akun keuangan bukan milik user.');
        }

        if ($kecuali && (int) $kecuali->user_id !== (int) $user->getKey()) {
            throw new AuthorizationException('Transaksi bukan milik user.');
        }

        $query = Transaksi::query()->where('user_id', $user->getKey());

        if ($kecuali) {
            $query->where('id', '!=', $kecuali->getKey());
        }

        $masuk = (clone $query)
            ->where('akun_tujuan_id', $akun->getKey())
            ->whereIn('jenis', [
                Transaksi::JENIS_PEMASUKAN,
                Transaksi::JENIS_TRANSFER,
                Transaksi::JENIS_SALDO_AWAL,
            ])
            ->sum('nominal');

        $keluar = (clone $query)
            ->where('akun_asal_id', $akun->getKey())
            ->whereIn('jenis', [
                Transaksi::JENIS_PENGELUARAN,
                Transaksi::JENIS_TRANSFER,
            ])
            ->sum('nominal');

        return round((float) $masuk - (float) $keluar, 2);
    }

    public function saldoCair(User $user, ?Transaksi $kecuali = null): float
    {
        return $this->saldoAkun($user, $this->akunCair($user), $kecuali);
    }

    public function saldoTabungan(User $user, ?Transaksi $kecuali = null): float
    {
        return $this->saldoAkun($user, $this->akunTabungan($user), $kecuali);
    }

    public function totalSaldo(User $user, ?Transaksi $kecuali = null): float
    {
        return round(
            $this->saldoCair($user, $kecuali) + $this->saldoTabungan($user, $kecuali),
            2
        );
    }

    /**
     * @return array{cair: float, tabungan: float, total: float}
     */
    public function ringkasan(User $user): array
    {
        $cair = $this->saldoCair($user);
        $tabungan = $this->saldoTabungan($user);

        return [
            'cair' => $cair,
            'tabungan' => $tabungan,
            'total' => round($cair + $tabungan, 2),
        ];
    }
}
