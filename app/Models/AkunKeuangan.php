<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'jenis'])]
class AkunKeuangan extends Model
{
    use HasFactory;

    /**
     * Nama tabel tidak mengikuti bentuk jamak bahasa Inggris.
     *
     * @var string
     */
    protected $table = 'akun_keuangan';

    /**
     * User pemilik akun.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaksi yang menggunakan akun ini sebagai akun asal.
     *
     * @return HasMany<Transaksi, $this>
     */
    public function transaksiSebagaiAsal(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'akun_asal_id');
    }

    /**
     * Transaksi yang menggunakan akun ini sebagai akun tujuan.
     *
     * @return HasMany<Transaksi, $this>
     */
    public function transaksiSebagaiTujuan(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'akun_tujuan_id');
    }
}
