<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriTransaksiRequest;
use App\Http\Requests\UpdateKategoriTransaksiRequest;
use App\Models\KategoriTransaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = $request->user()->kategoriTransaksi()
            ->withCount('transaksi')
            ->orderBy('nama')
            ->get()
            ->groupBy('jenis');

        return view('kategori.index', [
            'kategori' => $kategori,
        ]);
    }

    public function store(StoreKategoriTransaksiRequest $request): RedirectResponse
    {
        $request->user()->kategoriTransaksi()->create($request->validated());

        return redirect()
            ->route('app.kategori.index')
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateKategoriTransaksiRequest $request, KategoriTransaksi $kategori): RedirectResponse
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

        return redirect()
            ->route('app.kategori.index')
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriTransaksi $kategori): RedirectResponse
    {
        $this->authorize('delete', $kategori);

        if ($kategori->transaksi()->exists()) {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori masih digunakan oleh transaksi dan tidak dapat dihapus.',
            ]);
        }

        $kategori->delete();

        return redirect()
            ->route('app.kategori.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
