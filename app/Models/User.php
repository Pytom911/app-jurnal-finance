<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Akun keuangan milik user.
     *
     * @return HasMany<AkunKeuangan, $this>
     */
    public function akunKeuangan(): HasMany
    {
        return $this->hasMany(AkunKeuangan::class);
    }

    /**
     * Kategori transaksi milik user.
     *
     * @return HasMany<KategoriTransaksi, $this>
     */
    public function kategoriTransaksi(): HasMany
    {
        return $this->hasMany(KategoriTransaksi::class);
    }

    /**
     * Transaksi milik user.
     *
     * @return HasMany<Transaksi, $this>
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    /**
     * Tantangan milik user.
     *
     * @return HasMany<Tantangan, $this>
     */
    public function tantangan(): HasMany
    {
        return $this->hasMany(Tantangan::class);
    }
}
