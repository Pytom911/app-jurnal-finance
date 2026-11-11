<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'jenis'])]
class KategoriTransaksi extends Model
{
    use HasFactory;

    public const JENIS_PEMASUKAN = 'pemasukan';

    public const JENIS_PENGELUARAN = 'pengeluaran';

    /**
     * Nama tabel tidak mengikuti bentuk jamak bahasa Inggris.
     *
     * @var string
     */
    protected $table = 'kategori_transaksi';

    /**
     * User pemilik kategori.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaksi yang menggunakan kategori ini.
     *
     * @return HasMany<Transaksi, $this>
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kategori_id');
    }
}
