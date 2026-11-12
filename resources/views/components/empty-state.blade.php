@props(['title', 'message' => null, 'icon' => 'inbox'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-cyan-200/70 bg-gradient-to-b from-white/60 via-cyan-50/20 to-white/40 px-6 py-12 text-center backdrop-blur-md shadow-sm shadow-cyan-900/5']) }}>
    <div class="relative">
        <div class="pointer-events-none absolute -inset-2 rounded-3xl bg-cyan-400/20 blur-lg"></div>
        <span class="relative flex h-13 w-13 items-center justify-center rounded-2xl border border-white/90 bg-white/85 text-cyan-600 shadow-md shadow-cyan-500/15 backdrop-blur-md">
            <x-icon :name="$icon" class="h-6 w-6" />
        </span>
    </div>

    <p class="mt-4 text-sm font-extrabold text-slate-800">{{ $title }}</p>

    @if ($message)
        <p class="mt-1.5 max-w-sm text-xs leading-relaxed text-slate-500">{{ $message }}</p>
    @endif

    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</div>
