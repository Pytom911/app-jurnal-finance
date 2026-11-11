<?php

namespace App\Policies;

use App\Models\Transaksi;
use App\Models\User;

class TransaksiPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaksi $transaksi): bool
    {
        return (int) $transaksi->user_id === (int) $user->getKey();
    }

    public function update(User $user, Transaksi $transaksi): bool
    {
        return (int) $transaksi->user_id === (int) $user->getKey();
    }

    public function delete(User $user, Transaksi $transaksi): bool
    {
        return (int) $transaksi->user_id === (int) $user->getKey();
    }
}
