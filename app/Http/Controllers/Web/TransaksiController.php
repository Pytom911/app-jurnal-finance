<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTransaksiRequest;
use App\Http\Requests\StoreTransaksiRequest;
use App\Http\Requests\UpdateTransaksiRequest;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\BalanceService;
use App\Services\ChallengeService;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly BalanceService $balanceService,
        private readonly ChallengeService $challengeService,
    ) {}

    public function index(IndexTransaksiRequest $request): View
    {
        $user = $request->user();
        $filter = $request->validated();

        $transaksi = $this->queryTransaksi($user, $filter)
            ->paginate($filter['per_page'] ?? 10)
            ->withQueryString();

        return view('transaksi.index', [
            'transaksi' => $transaksi,
            'filter' => $filter,
            'kategori' => $user->kategoriTransaksi()->orderBy('nama')->get(),
            'ringkasan' => $this->balanceService->ringkasan($user),
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        $jenis = $request->query('jenis', Transaksi::JENIS_PEMASUKAN);

        if (! in_array($jenis, $this->jenisValid(), true)) {
            $jenis = Transaksi::JENIS_PEMASUKAN;
        }

        return view('transaksi.create', array_merge($this->formData($user), [
            'transaksi' => null,
            'jenisTerpilih' => $jenis,
            'tanggalHariIni' => now()->toDateString(),
        ]));
    }

    public function store(StoreTransaksiRequest $request): RedirectResponse
    {
        $transaksi = $this->transactionService->buat(
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('app.transaksi.show', $transaksi)
            ->with('status', 'Transaksi berhasil disimpan.');
    }

    public function show(Transaksi $transaksi): View
    {
        $this->authorize('view', $transaksi);

        $transaksi->load([
            'kategori:id,nama,jenis',
            'tantangan:id,nama,status,target',
            'akunAsal:id,nama,jenis',
            'akunTujuan:id,nama,jenis',
        ]);

        return view('transaksi.show', [
            'transaksi' => $transaksi,
        ]);
    }

    public function edit(Transaksi $transaksi): View
    {
        $this->authorize('update', $transaksi);

        $transaksi->load(['kategori', 'tantangan', 'akunAsal', 'akunTujuan']);

        return view('transaksi.edit', array_merge($this->formData($transaksi->user), [
            'transaksi' => $transaksi,
            'jenisTerpilih' => $transaksi->jenis,
            'tanggalHariIni' => $transaksi->tanggal->toDateString(),
        ]));
    }

    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi): RedirectResponse
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

        $this->transactionService->perbarui($request->user(), $transaksi, $data);

        return redirect()
            ->route('app.transaksi.show', $transaksi)
            ->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Request $request, Transaksi $transaksi): RedirectResponse
    {
        $this->authorize('delete', $transaksi);

        $this->transactionService->hapus($request->user(), $transaksi);

        return redirect()
            ->route('app.transaksi.index')
            ->with('status', 'Transaksi berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $filter
     */
    private function queryTransaksi(User $user, array $filter)
    {
        return $user->transaksi()
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
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(User $user): array
    {
        return [
            'akun' => $user->akunKeuangan()->orderBy('id')->get(),
            'kategori' => $user->kategoriTransaksi()->orderBy('nama')->get()->groupBy('jenis'),
            'tantanganAktif' => $this->challengeService->aktif($user),
            'ringkasan' => $this->balanceService->ringkasan($user),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function jenisValid(): array
    {
        return [
            Transaksi::JENIS_PEMASUKAN,
            Transaksi::JENIS_PENGELUARAN,
            Transaksi::JENIS_TRANSFER,
            Transaksi::JENIS_SALDO_AWAL,
        ];
    }
}
