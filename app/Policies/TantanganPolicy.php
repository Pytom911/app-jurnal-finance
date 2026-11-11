<?php

namespace App\Policies;

use App\Models\Tantangan;
use App\Models\User;

class TantanganPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Tantangan $tantangan): bool
    {
        return (int) $tantangan->user_id === (int) $user->getKey();
    }

    public function update(User $user, Tantangan $tantangan): bool
    {
        return (int) $tantangan->user_id === (int) $user->getKey();
    }

    public function delete(User $user, Tantangan $tantangan): bool
    {
        return (int) $tantangan->user_id === (int) $user->getKey();
    }
}
