<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTantanganRequest;
use App\Http\Requests\UpdateTantanganRequest;
use App\Models\Tantangan;
use App\Services\ChallengeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TantanganController extends Controller
{
    public function __construct(
        private readonly ChallengeService $challengeService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $aktif = $this->challengeService->aktif($user);

        $riwayat = $this->challengeService->riwayat($user)->paginate(10)->withQueryString();

        $riwayat->setCollection(
            $riwayat->getCollection()->map(fn (Tantangan $item) => [
                'tantangan' => $item,
                'ringkasan' => $this->challengeService->ringkasan($item),
            ])
        );

        return view('tantangan.index', [
            'aktif' => $aktif,
            'progress' => $aktif ? $this->challengeService->ringkasan($aktif) : null,
            'aktifMemilikiTransaksi' => $aktif !== null && $aktif->transaksi()->exists(),
            'riwayat' => $riwayat,
        ]);
    }

    public function store(StoreTantanganRequest $request): RedirectResponse
    {
        $this->challengeService->buat($request->user(), $request->validated());

        return redirect()
            ->route('app.tantangan.index')
            ->with('status', 'Tantangan berhasil dibuat.');
    }

    public function update(UpdateTantanganRequest $request, Tantangan $tantangan): RedirectResponse
    {
        $this->authorize('update', $tantangan);

        $this->challengeService->perbarui(
            $request->user(),
            $tantangan,
            $request->validated()
        );

        return redirect()
            ->route('app.tantangan.index')
            ->with('status', 'Tantangan berhasil diperbarui.');
    }

    public function destroy(Request $request, Tantangan $tantangan): RedirectResponse
    {
        $this->authorize('delete', $tantangan);

        $this->challengeService->hapus($request->user(), $tantangan);

        return redirect()
            ->route('app.tantangan.index')
            ->with('status', 'Tantangan berhasil dihapus.');
    }
}
