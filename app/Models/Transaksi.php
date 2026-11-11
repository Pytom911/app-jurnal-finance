<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'kategori_id',
    'tantangan_id',
    'jenis',
    'sumber_pemasukan',
    'akun_asal_id',
    'akun_tujuan_id',
    'nominal',
    'tanggal',
    'keterangan',
])]
class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    public const JENIS_PEMASUKAN = 'pemasukan';

    public const JENIS_PENGELUARAN = 'pengeluaran';

    public const JENIS_TRANSFER = 'transfer';

    public const JENIS_SALDO_AWAL = 'saldo_awal';

    public const SUMBER_UANG_SAKU = 'uang_saku';

    public const SUMBER_PENGHASILAN_SENDIRI = 'penghasilan_sendiri';

    /**
     * Nama tabel tidak mengikuti bentuk jamak bahasa Inggris.
     *
     * @var string
     */
    protected $table = 'transaksi';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nominal' => 'decimal:2',
            'tanggal' => 'date',
        ];
    }

    /**
     * User pemilik transaksi.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kategori transaksi.
     *
     * @return BelongsTo<KategoriTransaksi, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTransaksi::class, 'kategori_id');
    }

    /**
     * Tantangan yang terkait dengan transaksi.
     *
     * @return BelongsTo<Tantangan, $this>
     */
    public function tantangan(): BelongsTo
    {
        return $this->belongsTo(Tantangan::class, 'tantangan_id');
    }

    /**
     * Akun asal transaksi (untuk pengeluaran dan transfer).
     *
     * @return BelongsTo<AkunKeuangan, $this>
     */
    public function akunAsal(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_asal_id');
    }

    /**
     * Akun tujuan transaksi (untuk pemasukan dan transfer).
     *
     * @return BelongsTo<AkunKeuangan, $this>
     */
    public function akunTujuan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'akun_tujuan_id');
    }
}
