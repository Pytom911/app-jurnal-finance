@props(['id', 'title', 'description' => null, 'maxWidth' => 'max-w-lg'])

<div id="{{ $id }}" data-modal class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-md transition-opacity" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="animate-fade-up relative w-full {{ $maxWidth }} overflow-hidden rounded-3xl border border-white/90 bg-white/85 shadow-2xl shadow-cyan-950/20 backdrop-blur-2xl">
            <!-- Top specular accent -->
            <div class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
            
            <div class="flex items-start justify-between gap-4 border-b border-cyan-100/60 bg-gradient-to-b from-white/70 to-transparent px-6 py-5">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900">{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-1 text-xs text-slate-500">{{ $description }}</p>
                    @endif
                </div>

                <button type="button" data-modal-close
                    class="rounded-xl border border-slate-200/60 bg-white/60 p-2 text-slate-400 backdrop-blur-sm transition-all hover:border-cyan-300 hover:bg-cyan-50 hover:text-cyan-700"
                    aria-label="Tutup">
                    <x-icon name="x" class="h-4 w-4" />
                </button>
            </div>

            <div class="px-6 py-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
