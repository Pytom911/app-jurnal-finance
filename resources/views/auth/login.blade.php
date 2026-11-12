@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="relative min-h-screen overflow-hidden bg-slate-950 lg:grid lg:grid-cols-12">
        <!-- Ambient Mesh Glowing Orbs -->
        <div class="pointer-events-none absolute -left-40 -top-40 h-[600px] w-[600px] rounded-full bg-cyan-500/20 blur-[130px]"></div>
        <div class="pointer-events-none absolute -bottom-40 right-10 h-[600px] w-[600px] rounded-full bg-teal-500/15 blur-[140px]"></div>
        <div class="pointer-events-none absolute top-1/2 left-1/3 h-[500px] w-[500px] -translate-y-1/2 rounded-full bg-sky-600/15 blur-[120px]"></div>

        <!-- Left Column: Glass Branding & Showcase -->
        <div class="relative hidden flex-col justify-between overflow-hidden border-r border-white/10 bg-gradient-to-br from-slate-900/80 via-slate-900/60 to-cyan-950/70 p-12 backdrop-blur-3xl lg:col-span-7 lg:flex">
            <!-- Subtle Grid pattern overlay -->
            <div class="pointer-events-none absolute inset-0 opacity-15"
                style="background-image: radial-gradient(circle at 1px 1px, rgba(34,211,238,0.5) 1px, transparent 0); background-size: 32px 32px;"></div>

            <!-- Brand Logo -->
            <div class="relative z-10 flex items-center gap-3.5">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/30 bg-gradient-to-tr from-cyan-500 via-cyan-400 to-teal-300 text-slate-950 shadow-xl shadow-cyan-500/30">
                    <x-icon name="trend-up" class="h-6 w-6 stroke-[2.2]" />
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-xl font-black tracking-tight text-white">Jurnal</p>
                        <span class="rounded-full border border-cyan-400/30 bg-cyan-500/10 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider text-cyan-300">Glass Edition</span>
                    </div>
                    <p class="text-xs font-semibold text-cyan-200/70">Sistem Keuangan Pribadi</p>
                </div>
            </div>

            <!-- Hero Showcase & Glass Mockups -->
            <div class="relative z-10 my-auto py-10">
                <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/30 bg-cyan-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-cyan-300 backdrop-blur-md">
                    <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 shadow-[0_0_8px_rgba(34,211,238,1)]"></span>
                    Modern Clean Glassmorphism
                </div>

                <h1 class="mt-5 max-w-xl text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl">
                    Kendalikan Finansial,<br>
                    <span class="bg-gradient-to-r from-cyan-300 via-teal-300 to-sky-300 bg-clip-text text-transparent">
                        Wujudkan Setiap Target.
                    </span>
                </h1>

                <p class="mt-4 max-w-lg text-sm leading-relaxed text-slate-300">
                    Pemisahan saldo cair & tabungan yang aman, pemantauan progress tantangan otomatis,
                    serta pencatatan transaksi yang jernih dan bebas rasa cemas.
                </p>

                <!-- Floating Interactive Glass Cards Preview -->
                <div class="mt-8 grid max-w-lg gap-3.5 sm:grid-cols-2">
                    <!-- Glass Mini Stat 1 -->
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 shadow-xl backdrop-blur-xl transition hover:border-cyan-400/40 hover:bg-white/15">
                        <div class="flex items-center justify-between text-xs text-cyan-200">
                            <span class="font-bold uppercase tracking-wider">Saldo Cair</span>
                            <x-icon name="banknotes" class="h-4 w-4 text-cyan-400" />
                        </div>
                        <p class="mt-2 text-xl font-black tabular-nums text-white">Rp 12.850.000</p>
                        <p class="mt-1 text-[11px] text-slate-300">Siap untuk kebutuhan harian</p>
                    </div>

                    <!-- Glass Mini Stat 2 -->
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 shadow-xl backdrop-blur-xl transition hover:border-teal-400/40 hover:bg-white/15">
                        <div class="flex items-center justify-between text-xs text-teal-200">
                            <span class="font-bold uppercase tracking-wider">Tantangan Aktif</span>
                            <span class="text-xs font-black text-teal-300">68%</span>
                        </div>
                        <div class="mt-2.5 h-2 w-full overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-[68%] rounded-full bg-gradient-to-r from-cyan-400 to-teal-400 shadow-[0_0_8px_rgba(45,212,191,0.8)]"></div>
                        </div>
                        <p class="mt-2 text-[11px] text-slate-300">Menuju Target Rp 100 Juta</p>
                    </div>
                </div>

                <!-- Feature bullet pills -->
                <div class="mt-8 flex flex-wrap gap-2.5">
                    @foreach (['Saldo Terpisah', 'Otomatisasi Tantangan', 'Audit Perubahan Saldo', 'Desain Jernih'] as $feat)
                        <div class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300 backdrop-blur-sm">
                            <x-icon name="check" class="h-3.5 w-3.5 text-cyan-400" />
                            {{ $feat }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer note -->
            <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-5 text-xs text-slate-400">
                <p>© {{ date('Y') }} Jurnal Keuangan · Clean Glasses UI</p>
                <div class="flex items-center gap-2 text-cyan-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Sistem Aman & Privat</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Card -->
        <div class="relative flex min-h-screen items-center justify-center p-6 sm:p-10 lg:col-span-5">
            <div class="w-full max-w-md">
                <!-- Mobile Brand Header -->
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/30 bg-gradient-to-tr from-cyan-500 to-teal-400 text-slate-950 shadow-lg shadow-cyan-500/25">
                        <x-icon name="trend-up" class="h-6 w-6 stroke-[2.2]" />
                    </span>
                    <div>
                        <p class="text-lg font-black tracking-tight text-white">Jurnal</p>
                        <p class="text-xs font-semibold text-cyan-300">Keuangan pribadi</p>
                    </div>
                </div>

                <!-- Frosted Glass Login Box -->
                <div class="relative overflow-hidden rounded-3xl border border-white/20 bg-white/10 p-7 shadow-2xl shadow-cyan-950/40 backdrop-blur-2xl sm:p-9">
                    <!-- Top specular highlight line -->
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>

                    <div class="text-left">
                        <h2 class="text-2xl font-black tracking-tight text-white">Selamat Datang</h2>
                        <p class="mt-1 text-xs font-medium text-slate-300">Masuk untuk melanjutkan kelola catatan keuangan.</p>
                    </div>

                    <div class="mt-6">
                        @include('partials.flash')
                    </div>

                    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="label text-slate-200">Email Pengguna</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-cyan-300/70">
                                    <x-icon name="user" class="h-5 w-5" />
                                </span>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                    autocomplete="username" placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-white/20 bg-white/10 pl-11 pr-3.5 py-2.5 text-sm font-medium text-white shadow-inner placeholder:text-slate-400 backdrop-blur-md transition-all focus:border-cyan-400 focus:bg-white/15 focus:outline-none focus:ring-4 focus:ring-cyan-500/20 @error('email') border-rose-400 focus:ring-rose-500/20 @enderror">
                            </div>
                            @error('email')
                                <p class="help text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="label text-slate-200">Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-cyan-300/70">
                                    <x-icon name="lock" class="h-5 w-5" />
                                </span>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                    placeholder="Masukkan password"
                                    class="w-full rounded-xl border border-white/20 bg-white/10 pl-11 pr-11 py-2.5 text-sm font-medium text-white shadow-inner placeholder:text-slate-400 backdrop-blur-md transition-all focus:border-cyan-400 focus:bg-white/15 focus:outline-none focus:ring-4 focus:ring-cyan-500/20 @error('password') border-rose-400 focus:ring-rose-500/20 @enderror">

                                <button type="button" data-password-toggle="password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-300 transition hover:text-cyan-300"
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
                                <p class="help text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-300 cursor-pointer">
                                <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                                    class="h-4 w-4 rounded border-white/30 bg-white/10 text-cyan-500 focus:ring-cyan-500/30">
                                Ingat saya
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-full py-3 text-sm font-black shadow-lg shadow-cyan-500/35">
                            <span>Masuk ke Jurnal</span>
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </button>
                    </form>

                    <p class="mt-6 text-center text-xs text-slate-400">
                        Akun dibuat via seeder lokal. Hubungi pemilik aplikasi bila belum memiliki akun.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
