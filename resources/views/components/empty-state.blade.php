@props(['title', 'message' => null, 'icon' => 'inbox'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 px-6 py-12 text-center']) }}>
    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-brand-600 shadow-sm">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>

    <p class="mt-4 text-sm font-bold text-slate-800">{{ $title }}</p>

    @if ($message)
        <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $message }}</p>
    @endif

    @isset($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endisset
</div>
