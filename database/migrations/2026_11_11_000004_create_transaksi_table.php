<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_transaksi')->nullOnDelete();
            $table->foreignId('tantangan_id')->nullable()->constrained('tantangan')->nullOnDelete();
            $table->enum('jenis', ['pemasukan', 'pengeluaran', 'transfer', 'saldo_awal']);
            $table->enum('sumber_pemasukan', ['uang_saku', 'penghasilan_sendiri'])->nullable();
            $table->foreignId('akun_asal_id')->nullable()->constrained('akun_keuangan')->nullOnDelete();
            $table->foreignId('akun_tujuan_id')->nullable()->constrained('akun_keuangan')->nullOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian, filter, dan perhitungan saldo/progress.
            $table->index(['user_id', 'tanggal']);
            $table->index(['user_id', 'jenis']);
            $table->index(['user_id', 'kategori_id']);
            $table->index(['user_id', 'tantangan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
