<?php

namespace Database\Seeders;

use App\Models\AkunKeuangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun utama aplikasi.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'tomif30@gmail.com'],
            [
                'name' => 'Tomi',
                'password' => 'Tomi200811!!',
                'email_verified_at' => now(),
            ],
        );

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
