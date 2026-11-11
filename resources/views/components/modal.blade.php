@props(['id', 'title', 'description' => null, 'maxWidth' => 'max-w-lg'])

<div id="{{ $id }}" data-modal class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="animate-fade-up w-full {{ $maxWidth }} rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-900">{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
                    @endif
                </div>

                <button type="button" data-modal-close
                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Tutup">
                    <x-icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <div class="px-6 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
