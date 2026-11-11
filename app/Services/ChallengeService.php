<?php

namespace App\Services;

use App\Models\Tantangan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChallengeService
{
    public function aktif(User $user): ?Tantangan
    {
        return $user->tantangan()
            ->where('status', Tantangan::STATUS_AKTIF)
            ->first();
    }

    /**
     * Progress hanya dihitung dari pemasukan penghasilan sendiri.
     */
    public function progress(Tantangan $tantangan): float
    {
        $progress = $tantangan->transaksi()
            ->where('user_id', $tantangan->user_id)
            ->where('jenis', Transaksi::JENIS_PEMASUKAN)
            ->where('sumber_pemasukan', Transaksi::SUMBER_PENGHASILAN_SENDIRI)
            ->sum('nominal');

        return round((float) $progress, 2);
    }

    public function persentase(Tantangan $tantangan, ?float $progress = null): float
    {
        $target = (float) $tantangan->target;

        if ($target <= 0) {
            return 0.0;
        }

        $progress ??= $this->progress($tantangan);

        return round(($progress / $target) * 100, 2);
    }

    public function sisaTarget(Tantangan $tantangan, ?float $progress = null): float
    {
        $progress ??= $this->progress($tantangan);

        return round(max(0, (float) $tantangan->target - $progress), 2);
    }

    public function durasiHari(Tantangan $tantangan): ?int
    {
        $selesai = $tantangan->tanggal_selesai ?? $tantangan->tanggal_tercapai;

        if (! $selesai) {
            return null;
        }

        return (int) $tantangan->tanggal_mulai->diffInDays($selesai);
    }

    /**
     * @return array{progress: float, persentase: float, sisa_target: float, durasi_hari: int|null}
     */
    public function ringkasan(Tantangan $tantangan): array
    {
        $progress = $this->progress($tantangan);

        return [
            'progress' => $progress,
            'persentase' => $this->persentase($tantangan, $progress),
            'sisa_target' => $this->sisaTarget($tantangan, $progress),
            'durasi_hari' => $this->durasiHari($tantangan),
        ];
    }

    /**
     * Sinkronkan status tantangan dengan progress saat ini.
     *
     * - Mencapai target: selesai, isi tanggal tercapai/selesai.
     * - Turun di bawah target saat sudah selesai: perlu_penyesuaian.
     * - Tidak pernah mengaktifkan ulang tantangan secara otomatis.
     */
    public function sinkronkan(User $user, Tantangan $tantangan): Tantangan
    {
        $this->pastikanMilik($user, $tantangan);

        $progress = $this->toCents($this->progress($tantangan));
        $target = $this->toCents((float) $tantangan->target);

        if ($progress >= $target) {
            $perubahan = [];

            if ($tantangan->status !== Tantangan::STATUS_SELESAI) {
                $perubahan['status'] = Tantangan::STATUS_SELESAI;
            }

            if (! $tantangan->tanggal_tercapai) {
                $perubahan['tanggal_tercapai'] = Carbon::today();
            }

            if (! $tantangan->tanggal_selesai) {
                $perubahan['tanggal_selesai'] = Carbon::today();
            }

            if ($perubahan !== []) {
                $tantangan->update($perubahan);
            }
        } elseif ($tantangan->status === Tantangan::STATUS_SELESAI) {
            $tantangan->update(['status' => Tantangan::STATUS_PERLU_PENYESUAIAN]);
        }

        return $tantangan->refresh();
    }

    public function buat(User $user, array $data): Tantangan
    {
        return DB::transaction(function () use ($user, $data) {
            $this->kunciUser($user);

            if ($this->aktif($user)) {
                throw ValidationException::withMessages([
                    'tantangan' => 'Masih ada tantangan aktif. Selesaikan atau hapus terlebih dahulu.',
                ]);
            }

            $nama = trim((string) ($data['nama'] ?? ''));
            $target = round((float) ($data['target'] ?? 0), 2);

            if ($nama === '') {
                throw ValidationException::withMessages(['nama' => 'Nama tantangan wajib diisi.']);
            }

            if ($target <= 0) {
                throw ValidationException::withMessages(['target' => 'Target tantangan harus lebih besar dari nol.']);
            }

            return $user->tantangan()->create([
                'nama' => $nama,
                'target' => $target,
                'tanggal_mulai' => Carbon::today(),
                'status' => Tantangan::STATUS_AKTIF,
            ]);
        });
    }

    public function perbarui(User $user, Tantangan $tantangan, array $data): Tantangan
    {
        return DB::transaction(function () use ($user, $tantangan, $data) {
            $this->kunciUser($user);
            $this->pastikanMilik($user, $tantangan);

            if ($tantangan->status === Tantangan::STATUS_SELESAI) {
                throw ValidationException::withMessages([
                    'tantangan' => 'Tantangan yang sudah selesai tidak dapat diubah.',
                ]);
            }

            $perubahan = [];

            if (array_key_exists('nama', $data)) {
                $nama = trim((string) $data['nama']);

                if ($nama === '') {
                    throw ValidationException::withMessages(['nama' => 'Nama tantangan wajib diisi.']);
                }

                $perubahan['nama'] = $nama;
            }

            if (array_key_exists('target', $data)) {
                $target = round((float) $data['target'], 2);

                if ($target <= 0) {
                    throw ValidationException::withMessages(['target' => 'Target tantangan harus lebih besar dari nol.']);
                }

                $perubahan['target'] = $target;
            }

            if ($perubahan !== []) {
                $tantangan->update($perubahan);
            }

            return $this->sinkronkan($user, $tantangan);
        });
    }

    public function hapus(User $user, Tantangan $tantangan): void
    {
        DB::transaction(function () use ($user, $tantangan) {
            $this->kunciUser($user);
            $this->pastikanMilik($user, $tantangan);

            if ($tantangan->transaksi()->exists()) {
                throw ValidationException::withMessages([
                    'tantangan' => 'Tantangan yang memiliki riwayat transaksi tidak dapat dihapus.',
                ]);
            }

            $tantangan->delete();
        });
    }

    /**
     * Query riwayat tantangan yang sudah selesai atau perlu penyesuaian.
     */
    public function riwayat(User $user): HasMany
    {
        return $user->tantangan()
            ->whereIn('status', [
                Tantangan::STATUS_SELESAI,
                Tantangan::STATUS_PERLU_PENYESUAIAN,
            ])
            ->orderByDesc('tanggal_selesai')
            ->orderByDesc('id');
    }

    private function pastikanMilik(User $user, Tantangan $tantangan): void
    {
        if ((int) $tantangan->user_id !== (int) $user->getKey()) {
            throw new AuthorizationException('Tantangan bukan milik user.');
        }
    }

    private function kunciUser(User $user): void
    {
        User::query()->whereKey($user->getKey())->lockForUpdate()->first();
    }

    private function toCents(float $nominal): int
    {
        return (int) round($nominal * 100);
    }
}
