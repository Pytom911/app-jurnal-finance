<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AkunKeuanganController extends Controller
{
    public function __construct(
        private readonly BalanceService $balanceService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $akun = $user->akunKeuangan()
            ->orderBy('id')
            ->get()
            ->map(fn (AkunKeuangan $akun) => $this->dataAkun($user, $akun))
            ->values();

        return response()->json([
            'data' => $akun,
            'ringkasan' => $this->balanceService->ringkasan($user),
        ]);
    }

    public function show(Request $request, AkunKeuangan $akun): JsonResponse
    {
        $this->authorize('view', $akun);

        return response()->json([
            'data' => $this->dataAkun($request->user(), $akun),
        ]);
    }

    public function saldo(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->balanceService->ringkasan($request->user()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataAkun(User $user, AkunKeuangan $akun): array
    {
        return [
            'id' => $akun->getKey(),
            'nama' => $akun->nama,
            'jenis' => $akun->jenis,
            'saldo' => $this->balanceService->saldoAkun($user, $akun),
            'created_at' => $akun->created_at,
            'updated_at' => $akun->updated_at,
        ];
    }
}
