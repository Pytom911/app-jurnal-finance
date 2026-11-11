<?php

namespace App\Policies;

use App\Models\AkunKeuangan;
use App\Models\User;

class AkunKeuanganPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AkunKeuangan $akun): bool
    {
        return (int) $akun->user_id === (int) $user->getKey();
    }
}
