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
        Schema::create('tantangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama', 150);
            $table->decimal('target', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_tercapai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'perlu_penyesuaian'])->default('aktif');
            $table->timestamps();

            // Mempercepat pencarian tantangan aktif dan riwayat per user.
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'tanggal_mulai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tantangan');
    }
};
