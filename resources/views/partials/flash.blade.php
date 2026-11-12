@if (session('status'))
    <div data-alert data-auto-dismiss
        class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3.5 text-sm font-semibold text-emerald-900 shadow-sm shadow-emerald-500/5 backdrop-blur-xl transition duration-300">
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-emerald-400/40 bg-emerald-500/20 text-emerald-700">
            <x-icon name="check" class="h-4 w-4" />
        </span>
        <p class="flex-1 pt-0.5 leading-relaxed">{{ session('status') }}</p>
        <button type="button" data-dismiss-alert class="rounded-lg p-1 text-emerald-600 transition hover:bg-emerald-500/20 hover:text-emerald-800"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div data-alert data-auto-dismiss
        class="mb-5 flex items-start gap-3 rounded-2xl border border-rose-400/30 bg-rose-500/10 px-4 py-3.5 text-sm font-semibold text-rose-900 shadow-sm shadow-rose-500/5 backdrop-blur-xl transition duration-300">
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-rose-400/40 bg-rose-500/20 text-rose-700">
            <x-icon name="alert" class="h-4 w-4" />
        </span>
        <p class="flex-1 pt-0.5 leading-relaxed">{{ session('error') }}</p>
        <button type="button" data-dismiss-alert class="rounded-lg p-1 text-rose-600 transition hover:bg-rose-500/20 hover:text-rose-800"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div data-alert
        class="mb-5 flex items-start gap-3 rounded-2xl border border-amber-400/35 bg-amber-500/10 px-4 py-3.5 text-sm text-amber-950 shadow-sm shadow-amber-500/5 backdrop-blur-xl">
        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-amber-400/40 bg-amber-500/20 text-amber-800">
            <x-icon name="alert" class="h-4 w-4" />
        </span>
        <div class="flex-1 pt-0.5">
            <p class="font-extrabold text-amber-900">Periksa kembali input Anda:</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs font-medium text-amber-800">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" data-dismiss-alert class="rounded-lg p-1 text-amber-600 transition hover:bg-amber-500/20 hover:text-amber-800"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif
