<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexTransaksiRequest;
use App\Http\Requests\StoreTransaksiRequest;
use App\Http\Requests\UpdateTransaksiRequest;
use App\Models\Transaksi;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {}

    public function index(IndexTransaksiRequest $request): JsonResponse
    {
        $user = $request->user();
        $filter = $request->validated();

        $transaksi = $user->transaksi()
            ->with([
                'kategori:id,nama,jenis',
                'tantangan:id,nama,status',
                'akunAsal:id,nama,jenis',
                'akunTujuan:id,nama,jenis',
            ])
            ->when($filter['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('kategori', fn ($query) => $query->where('nama', 'like', "%{$search}%"));
                });
            })
            ->when($filter['tanggal_dari'] ?? null, fn ($query, string $tanggal) => $query->whereDate('tanggal', '>=', $tanggal))
            ->when($filter['tanggal_sampai'] ?? null, fn ($query, string $tanggal) => $query->whereDate('tanggal', '<=', $tanggal))
            ->when($filter['jenis'] ?? null, fn ($query, string $jenis) => $query->where('jenis', $jenis))
            ->when($filter['kategori_id'] ?? null, fn ($query, int $kategori) => $query->where('kategori_id', $kategori))
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        return response()->json(
            $transaksi->paginate($filter['per_page'] ?? 15)->withQueryString()
        );
    }

    public function store(StoreTransaksiRequest $request): JsonResponse
    {
        $transaksi = $this->transactionService->buat(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'data' => $this->muatRelasi($transaksi),
        ], 201);
    }

    public function show(Transaksi $transaksi): JsonResponse
    {
        $this->authorize('view', $transaksi);

        return response()->json([
            'data' => $this->muatRelasi($transaksi),
        ]);
    }

    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi): JsonResponse
    {
        $this->authorize('update', $transaksi);

        $data = array_merge(
            $transaksi->only([
                'jenis',
                'nominal',
                'tanggal',
                'keterangan',
                'kategori_id',
                'tantangan_id',
                'sumber_pemasukan',
                'akun_asal_id',
                'akun_tujuan_id',
            ]),
            $request->validated()
        );

        $transaksi = $this->transactionService->perbarui(
            $request->user(),
            $transaksi,
            $data
        );

        return response()->json([
            'data' => $this->muatRelasi($transaksi),
        ]);
    }

    public function destroy(Request $request, Transaksi $transaksi): JsonResponse
    {
        $this->authorize('delete', $transaksi);

        $this->transactionService->hapus($request->user(), $transaksi);

        return response()->json(null, 204);
    }

    private function muatRelasi(Transaksi $transaksi): Transaksi
    {
        return $transaksi->load([
            'kategori:id,nama,jenis',
            'tantangan:id,nama,status',
            'akunAsal:id,nama,jenis',
            'akunTujuan:id,nama,jenis',
        ]);
    }
}
