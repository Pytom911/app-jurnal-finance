@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-brand-700 via-brand-600 to-brand-500 lg:flex lg:flex-col lg:justify-between"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.18) 1px, transparent 0); background-size: 26px 26px;">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-16 h-96 w-96 rounded-full bg-brand-300/20 blur-3xl"></div>

            <div class="relative px-12 pt-12">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-white backdrop-blur">
                        <x-icon name="trend-up" class="h-6 w-6" />
                    </span>
                    <div>
                        <p class="text-xl font-extrabold tracking-tight text-white">Jurnal</p>
                        <p class="text-xs font-medium text-brand-100">Keuangan pribadi</p>
                    </div>
                </div>
            </div>

            <div class="relative px-12">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-brand-200">Tenang, rapi, terkendali</p>
                <h1 class="mt-4 max-w-md text-4xl font-extrabold leading-tight tracking-tight text-white">
                    Catat setiap rupiah,<br>wujudkan targetmu.
                </h1>
                <p class="mt-4 max-w-md text-sm leading-relaxed text-brand-50/90">
                    Kelola saldo cair dan tabungan, pantau pengeluaran, dan lihat progress tantangan
                    penghasilanmu dalam satu tempat.
                </p>

                <div class="mt-9 space-y-3">
                    @foreach (['Saldo cair & tabungan terpisah', 'Progress tantangan otomatis', 'Riwayat transaksi yang rapi'] as $feature)
                        <div class="flex items-center gap-3 text-sm font-medium text-white/90">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/15">
                                <x-icon name="check" class="h-3.5 w-3.5" />
                            </span>
                            {{ $feature }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative px-12 pb-12">
                <p class="text-xs font-medium text-brand-200/80">© {{ date('Y') }} Jurnal. Dibuat untuk keuanganmu.</p>
            </div>
        </div>

        <div class="flex min-h-screen flex-col justify-center px-5 py-10 sm:px-10">
            <div class="mx-auto w-full max-w-md">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-brand-400 text-white shadow-lg shadow-brand-500/25">
                        <x-icon name="trend-up" class="h-6 w-6" />
                    </span>
                    <div>
                        <p class="text-lg font-extrabold tracking-tight text-slate-900">Jurnal</p>
                        <p class="text-xs font-medium text-slate-400">Keuangan pribadi</p>
                    </div>
                </div>

                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">Selamat datang kembali</h2>
                <p class="mt-1.5 text-sm text-slate-500">Masuk untuk melanjutkan mencatat keuanganmu.</p>

                <div class="mt-6">
                    @include('partials.flash')
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="label">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                autocomplete="username" placeholder="nama@email.com"
                                class="input pl-11 @error('email') input-error @enderror">
                        </div>
                        @error('email')
                            <p class="help">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <x-icon name="lock" class="h-5 w-5" />
                            </span>
                            <input id="password" name="password" type="password" required autocomplete="current-password"
                                placeholder="Masukkan password" class="input px-11 @error('password') input-error @enderror">

                            <button type="button" data-password-toggle="password"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 transition hover:text-slate-600"
                                aria-label="Lihat password">
                                <span data-password-label>
                                    <x-icon name="eye" class="h-5 w-5" />
                                </span>
                                <span data-password-label-off class="hidden">
                                    <x-icon name="eye-off" class="h-5 w-5" />
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <p class="help">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2.5 text-sm font-medium text-slate-600">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                            class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                        Ingat saya di perangkat ini
                    </label>

                    <button type="submit" class="btn btn-primary w-full py-3">
                        Masuk
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                </form>

                <p class="mt-6 text-center text-xs text-slate-400">
                    Akun dibuat melalui seeder. Hubungi pemilik aplikasi jika belum punya akses.
                </p>
            </div>
        </div>
    </div>
@endsection
