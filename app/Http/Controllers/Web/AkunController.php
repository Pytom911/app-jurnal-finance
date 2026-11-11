<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $akun = $user->akunKeuangan()
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => [
                'akun' => $item,
                'saldo' => $this->balanceService->saldoAkun($user, $item),
            ]);

        return view('akun.index', [
            'akun' => $akun,
            'ringkasan' => $this->balanceService->ringkasan($user),
        ]);
    }
}
