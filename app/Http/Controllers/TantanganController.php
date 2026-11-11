<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTantanganRequest;
use App\Http\Requests\UpdateTantanganRequest;
use App\Models\Tantangan;
use App\Services\ChallengeService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TantanganController extends Controller
{
    public function __construct(
        private readonly ChallengeService $challengeService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $this->perPage($request);

        $paginator = $user->tantangan()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        $aktif = $this->challengeService->aktif($user);

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn (Tantangan $tantangan) => $this->dataTantangan($tantangan))
                ->values(),
            'meta' => $this->meta($paginator),
            'aktif' => $aktif ? $this->dataTantangan($aktif) : null,
        ]);
    }

    public function store(StoreTantanganRequest $request): JsonResponse
    {
        $tantangan = $this->challengeService->buat(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'data' => $this->dataTantangan($tantangan),
        ], 201);
    }

    public function show(Tantangan $tantangan): JsonResponse
    {
        $this->authorize('view', $tantangan);

        return response()->json([
            'data' => $this->dataTantangan($tantangan),
        ]);
    }

    public function update(UpdateTantanganRequest $request, Tantangan $tantangan): JsonResponse
    {
        $this->authorize('update', $tantangan);

        $tantangan = $this->challengeService->perbarui(
            $request->user(),
            $tantangan,
            $request->validated()
        );

        return response()->json([
            'data' => $this->dataTantangan($tantangan),
        ]);
    }

    public function destroy(Request $request, Tantangan $tantangan): JsonResponse
    {
        $this->authorize('delete', $tantangan);

        $this->challengeService->hapus($request->user(), $tantangan);

        return response()->json(null, 204);
    }

    public function riwayat(Request $request): JsonResponse
    {
        $paginator = $this->challengeService
            ->riwayat($request->user())
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($paginator->items())
                ->map(fn (Tantangan $tantangan) => $this->dataTantangan($tantangan))
                ->values(),
            'meta' => $this->meta($paginator),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataTantangan(Tantangan $tantangan): array
    {
        return array_merge(
            $tantangan->toArray(),
            $this->challengeService->ringkasan($tantangan)
        );
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->input('per_page', 15), 1), 100);
    }

    /**
     * @return array{current_page: int, last_page: int, per_page: int, total: int}
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
