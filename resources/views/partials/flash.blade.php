@if (session('status'))
    <div data-alert data-auto-dismiss
        class="mb-4 flex items-start gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/80 px-4 py-3.5 text-sm font-medium text-emerald-800 transition duration-300">
        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
        <p class="flex-1">{{ session('status') }}</p>
        <button type="button" data-dismiss-alert class="text-emerald-500 transition hover:text-emerald-700"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if (session('error'))
    <div data-alert data-auto-dismiss
        class="mb-4 flex items-start gap-3 rounded-2xl border border-rose-100 bg-rose-50/80 px-4 py-3.5 text-sm font-medium text-rose-700 transition duration-300">
        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" />
        <p class="flex-1">{{ session('error') }}</p>
        <button type="button" data-dismiss-alert class="text-rose-400 transition hover:text-rose-600"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif

@if ($errors->any())
    <div data-alert
        class="mb-4 flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50/80 px-4 py-3.5 text-sm text-amber-800">
        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" />
        <div class="flex-1">
            <p class="font-bold">Periksa kembali input Anda.</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-amber-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" data-dismiss-alert class="text-amber-500 transition hover:text-amber-700"
            aria-label="Tutup">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    </div>
@endif
