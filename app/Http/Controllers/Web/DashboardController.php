<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tantangan;
use App\Services\BalanceService;
use App\Services\ChallengeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
        private readonly ChallengeService $challengeService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $ringkasan = $this->balanceService->ringkasan($user);
        $aktif = $this->challengeService->aktif($user);
        $progress = $aktif ? $this->challengeService->ringkasan($aktif) : null;

        $perluPenyesuaian = $user->tantangan()
            ->where('status', Tantangan::STATUS_PERLU_PENYESUAIAN)
            ->latest('updated_at')
            ->first();

        $transaksiTerbaru = $user->transaksi()
            ->with(['kategori:id,nama,jenis', 'akunAsal:id,nama', 'akunTujuan:id,nama'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'ringkasan' => $ringkasan,
            'aktif' => $aktif,
            'progress' => $progress,
            'perluPenyesuaian' => $perluPenyesuaian,
            'transaksiTerbaru' => $transaksiTerbaru,
        ]);
    }
}
