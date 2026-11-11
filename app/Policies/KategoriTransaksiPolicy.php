<?php

namespace App\Policies;

use App\Models\KategoriTransaksi;
use App\Models\User;

class KategoriTransaksiPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, KategoriTransaksi $kategori): bool
    {
        return (int) $kategori->user_id === (int) $user->getKey();
    }

    public function update(User $user, KategoriTransaksi $kategori): bool
    {
        return (int) $kategori->user_id === (int) $user->getKey();
    }

    public function delete(User $user, KategoriTransaksi $kategori): bool
    {
        return (int) $kategori->user_id === (int) $user->getKey();
    }
}
