<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') · Jurnal Keuangan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-slate-50/70 text-slate-800 antialiased selection:bg-cyan-200 selection:text-cyan-900">
    <!-- Ambient Cyan Background Mesh -->
    <div class="bg-ambient-cyan" aria-hidden="true"></div>

    @php
        $user = auth()->user();

        $navItems = [
            ['route' => 'app.dashboard', 'pattern' => 'app.dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
            ['route' => 'app.transaksi.index', 'pattern' => 'app.transaksi.*', 'label' => 'Transaksi', 'icon' => 'transfer'],
            ['route' => 'app.kategori.index', 'pattern' => 'app.kategori.*', 'label' => 'Kategori', 'icon' => 'tag'],
            ['route' => 'app.tantangan.index', 'pattern' => 'app.tantangan.*', 'label' => 'Tantangan', 'icon' => 'target'],
            ['route' => 'app.akun.index', 'pattern' => 'app.akun.*', 'label' => 'Akun', 'icon' => 'wallet'],
        ];
    @endphp

    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-950/40 backdrop-blur-md transition-opacity lg:hidden"
        data-sidebar-close></div>

    <aside id="app-sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-white/80 bg-white/80 shadow-[4px_0_30px_rgba(6,182,212,0.05)] backdrop-blur-2xl transition-transform duration-300 lg:translate-x-0">
        
        <!-- Brand Header -->
        <div class="flex items-center gap-3 px-6 pb-5 pt-6 border-b border-cyan-100/40">
            <div class="relative">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/40 bg-gradient-to-tr from-cyan-600 via-cyan-500 to-teal-400 text-white shadow-lg shadow-cyan-500/30">
                    <x-icon name="trend-up" class="h-6 w-6 drop-shadow" />
                </span>
                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-emerald-400"></span>
            </div>

            <div>
                <div class="flex items-center gap-1.5">
                    <p class="text-lg font-black tracking-tight text-slate-900">Jurnal</p>
                    <span class="rounded-md border border-cyan-300/60 bg-cyan-50 px-1.5 py-0.5 text-[10px] font-extrabold uppercase text-cyan-700">Pro</span>
                </div>
                <p class="text-xs font-semibold text-slate-400">Keuangan pribadi</p>
            </div>

            <button type="button" data-sidebar-close
                class="ml-auto rounded-xl border border-slate-200/60 p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 lg:hidden"
                aria-label="Tutup menu">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5">
            <p class="px-3.5 pb-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Navigasi Utama</p>

            @foreach ($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['pattern']);
                @endphp
                <a href="{{ route($item['route']) }}"
                    class="nav-link {{ $isActive ? 'nav-link-active' : '' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg transition-colors {{ $isActive ? 'bg-cyan-500 text-white shadow-sm shadow-cyan-500/40' : 'text-slate-400 group-hover:text-cyan-600' }}">
                        <x-icon :name="$item['icon']" class="h-4 w-4" />
                    </span>
                    <span>{{ $item['label'] }}</span>

                    @if ($isActive)
                        <span class="ml-auto h-2 w-2 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.8)]"></span>
                    @endif
                </a>
            @endforeach

            <!-- Quick Action Links -->
            <div class="pt-6">
                <p class="px-3.5 pb-2 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Aksi Cepat</p>

                <div class="space-y-1.5">
                    <a href="{{ route('app.transaksi.create', ['jenis' => 'pemasukan']) }}"
                        class="nav-link text-emerald-700 hover:bg-emerald-50/80 hover:text-emerald-800 border border-transparent hover:border-emerald-200/60">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-300/50 bg-emerald-100/70 text-emerald-700">
                            <x-icon name="arrow-down-left" class="h-4 w-4" />
                        </span>
                        <span>Catat pemasukan</span>
                    </a>

                    <a href="{{ route('app.transaksi.create', ['jenis' => 'pengeluaran']) }}"
                        class="nav-link text-rose-600 hover:bg-rose-50/80 hover:text-rose-700 border border-transparent hover:border-rose-200/60">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-300/50 bg-rose-100/70 text-rose-600">
                            <x-icon name="arrow-up-right" class="h-4 w-4" />
                        </span>
                        <span>Catat pengeluaran</span>
                    </a>

                    <a href="{{ route('app.transaksi.create', ['jenis' => 'transfer']) }}"
                        class="nav-link text-cyan-700 hover:bg-cyan-50/80 hover:text-cyan-800 border border-transparent hover:border-cyan-200/60">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-cyan-300/50 bg-cyan-100/70 text-cyan-700">
                            <x-icon name="transfer" class="h-4 w-4" />
                        </span>
                        <span>Transfer saldo</span>
                    </a>
                </div>
            </div>
        </nav>

        <!-- User Profile Card -->
        <div class="border-t border-cyan-100/50 p-4">
            <div class="flex items-center gap-3 rounded-2xl border border-white/80 bg-gradient-to-br from-white/90 via-cyan-50/40 to-white/70 p-3 shadow-sm shadow-cyan-950/5 backdrop-blur-md">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-cyan-300/60 bg-gradient-to-br from-cyan-500 to-teal-500 text-sm font-black text-white shadow-sm shadow-cyan-500/25">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-extrabold text-slate-800">{{ $user->name }}</p>
                    <p class="truncate text-[11px] font-medium text-slate-400">{{ $user->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="rounded-xl border border-slate-200/50 p-2 text-slate-400 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                        title="Keluar">
                        <x-icon name="logout" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="relative z-10 lg:pl-72">
        <!-- Floating Glass Header -->
        <header class="sticky top-0 z-20 border-b border-white/80 bg-white/75 backdrop-blur-2xl shadow-sm shadow-cyan-950/5">
            <div class="mx-auto flex w-full max-w-7xl items-center gap-3 px-4 py-3.5 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle
                    class="rounded-xl border border-slate-200/80 bg-white/80 p-2 text-slate-600 shadow-sm backdrop-blur-sm lg:hidden hover:border-cyan-300 hover:text-cyan-700"
                    aria-label="Buka menu">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-black tracking-tight text-slate-900">
                        @yield('title', 'Dashboard')
                    </h1>
                    @hasSection('subtitle')
                        <p class="truncate text-xs font-medium text-slate-500">@yield('subtitle')</p>
                    @endif
                </div>

                @hasSection('actions')
                    <div class="hidden items-center gap-2.5 sm:flex">
                        @yield('actions')
                    </div>
                @endif

                <!-- User Dropdown Menu -->
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle
                        class="flex items-center gap-2.5 rounded-2xl border border-white/80 bg-white/70 px-3 py-1.5 shadow-sm shadow-cyan-950/5 backdrop-blur-md transition-all hover:border-cyan-300 hover:bg-white/95">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-tr from-cyan-500 to-teal-400 text-xs font-black text-white shadow-sm shadow-cyan-500/20">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-xs font-bold text-slate-700 sm:block">{{ $user->name }}</span>
                        <x-icon name="chevron-down" class="hidden h-3.5 w-3.5 text-slate-400 sm:block" />
                    </button>

                    <div data-dropdown-menu
                        class="absolute right-0 mt-2 hidden w-60 overflow-hidden rounded-2xl border border-white/90 bg-white/85 shadow-2xl shadow-cyan-950/15 backdrop-blur-2xl">
                        <div class="border-b border-cyan-100/50 bg-gradient-to-r from-cyan-50/50 to-transparent px-4 py-3">
                            <p class="text-xs font-bold text-slate-800">{{ $user->name }}</p>
                            <p class="truncate text-[11px] text-slate-400">{{ $user->email }}</p>
                        </div>

                        <div class="p-1.5 space-y-0.5">
                            <a href="{{ route('app.akun.index') }}"
                                class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700">
                                <x-icon name="wallet" class="h-4 w-4 text-cyan-600" />
                                Akun keuangan
                            </a>

                            <a href="{{ route('app.tantangan.index') }}"
                                class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-cyan-50 hover:text-cyan-700">
                                <x-icon name="target" class="h-4 w-4 text-cyan-600" />
                                Target tantangan
                            </a>

                            <div class="my-1 border-t border-slate-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-left text-xs font-semibold text-rose-600 transition hover:bg-rose-50 hover:text-rose-700">
                                    <x-icon name="logout" class="h-4 w-4 text-rose-500" />
                                    Keluar dari akun
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="mx-auto w-full max-w-7xl px-4 pb-20 pt-6 sm:px-6 lg:px-8">
            @if (request()->routeIs('app.*') && ! request()->routeIs('app.dashboard'))
                <div class="mb-4 flex items-center gap-2 sm:hidden">
                    @yield('actions')
                </div>
            @endif

            @include('partials.flash')

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
