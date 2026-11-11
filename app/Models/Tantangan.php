<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nama',
    'target',
    'tanggal_mulai',
    'tanggal_tercapai',
    'tanggal_selesai',
    'status',
])]
class Tantangan extends Model
{
    use HasFactory;

    /**
     * Nama tabel tidak mengikuti bentuk jamak bahasa Inggris.
     *
     * @var string
     */
    protected $table = 'tantangan';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'tanggal_mulai' => 'date',
            'tanggal_tercapai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    /**
     * User pemilik tantangan.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Transaksi penghasilan yang terhubung ke tantangan ini.
     *
     * @return HasMany<Transaksi, $this>
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'tantangan_id');
    }
}
