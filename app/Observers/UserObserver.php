<?php

namespace App\Observers;

use App\Models\AkunKeuangan;
use App\Models\User;

class UserObserver
{
    /**
     * Setiap user baru otomatis memiliki Saldo Cair dan Saldo Tabungan.
     */
    public function created(User $user): void
    {
        $user->akunKeuangan()->firstOrCreate(
            ['jenis' => AkunKeuangan::JENIS_CAIR],
            ['nama' => 'Saldo Cair'],
        );

        $user->akunKeuangan()->firstOrCreate(
            ['jenis' => AkunKeuangan::JENIS_TABUNGAN],
            ['nama' => 'Saldo Tabungan'],
        );
    }
}
