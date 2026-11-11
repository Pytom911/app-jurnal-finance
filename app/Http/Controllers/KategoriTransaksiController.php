<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriTransaksiRequest;
use App\Http\Requests\UpdateKategoriTransaksiRequest;
use App\Models\KategoriTransaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategoriTransaksiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $kategori = $request->user()->kategoriTransaksi()
            ->orderBy('jenis')
            ->orderBy('nama')
            ->get();

        return response()->json(['data' => $kategori]);
    }

    public function store(StoreKategoriTransaksiRequest $request): JsonResponse
    {
        $kategori = $request->user()->kategoriTransaksi()->create(
            $request->validated()
        );

        return response()->json(['data' => $kategori], 201);
    }

    public function update(UpdateKategoriTransaksiRequest $request, KategoriTransaksi $kategori): JsonResponse
    {
        $this->authorize('update', $kategori);

        $data = $request->validated();

        if (isset($data['jenis'])
            && $data['jenis'] !== $kategori->jenis
            && $kategori->transaksi()->exists()) {
            throw ValidationException::withMessages([
                'jenis' => 'Jenis kategori tidak dapat diubah karena sudah digunakan transaksi.',
            ]);
        }

        $kategori->update($data);

        return response()->json(['data' => $kategori->refresh()]);
    }

    public function destroy(KategoriTransaksi $kategori): JsonResponse
    {
        $this->authorize('delete', $kategori);

        if ($kategori->transaksi()->exists()) {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori masih digunakan oleh transaksi dan tidak dapat dihapus.',
            ]);
        }

        $kategori->delete();

        return response()->json(null, 204);
    }
}
