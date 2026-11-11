@props(['value' => 0, 'prefix' => true])

@php
    $nominal = (float) $value;
    $formatted = number_format(abs($nominal), 0, ',', '.');
@endphp

<span {{ $attributes }}>{{ $nominal < 0 ? '−' : '' }}{{ $prefix ? 'Rp ' : '' }}{{ $formatted }}</span>
