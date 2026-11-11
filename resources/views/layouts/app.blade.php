<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') · Jurnal</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-full bg-slate-50 text-slate-800">
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

    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/40 backdrop-blur-sm lg:hidden"
        data-sidebar-close></div>

    <aside id="app-sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-slate-200/80 bg-white transition-transform duration-300 lg:translate-x-0">
        <div class="flex items-center gap-3 px-6 pb-5 pt-6">
            <span
                class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-600 to-brand-400 text-white shadow-lg shadow-brand-500/25">
                <x-icon name="trend-up" class="h-6 w-6" />
            </span>

            <div>
                <p class="text-lg font-extrabold tracking-tight text-slate-900">Jurnal</p>
                <p class="text-xs font-medium text-slate-400">Keuangan pribadi</p>
            </div>

            <button type="button" data-sidebar-close
                class="ml-auto rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 lg:hidden"
                aria-label="Tutup menu">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 pb-4">
            <p class="px-3.5 pb-2 pt-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Menu</p>

            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                    class="nav-link {{ request()->routeIs($item['pattern']) ? 'nav-link-active' : '' }}">
                    <x-icon :name="$item['icon']" class="h-5 w-5" />
                    <span>{{ $item['label'] }}</span>

                    @if (request()->routeIs($item['pattern']))
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    @endif
                </a>
            @endforeach

            <div class="pt-4">
                <p class="px-3.5 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Aksi cepat</p>

                <a href="{{ route('app.transaksi.create', ['jenis' => 'pemasukan']) }}"
                    class="nav-link text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700">
                    <x-icon name="arrow-down-left" class="h-5 w-5" />
                    <span>Catat pemasukan</span>
                </a>

                <a href="{{ route('app.transaksi.create', ['jenis' => 'pengeluaran']) }}"
                    class="nav-link text-rose-500 hover:bg-rose-50 hover:text-rose-600">
                    <x-icon name="arrow-up-right" class="h-5 w-5" />
                    <span>Catat pengeluaran</span>
                </a>
            </div>
        </nav>

        <div class="border-t border-slate-100 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-slate-50 px-3 py-3">
                <span
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-extrabold text-brand-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold text-slate-800">{{ $user->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="rounded-lg p-1.5 text-slate-400 transition hover:bg-white hover:text-rose-500"
                        title="Keluar">
                        <x-icon name="logout" class="h-5 w-5" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header
            class="sticky top-0 z-20 border-b border-slate-200/70 bg-slate-50/80 backdrop-blur-md">
            <div class="mx-auto flex w-full max-w-7xl items-center gap-3 px-4 py-3.5 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle
                    class="rounded-xl border border-slate-200 bg-white p-2 text-slate-500 shadow-sm lg:hidden"
                    aria-label="Buka menu">
                    <x-icon name="menu" class="h-5 w-5" />
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate text-lg font-extrabold tracking-tight text-slate-900">
                        @yield('title', 'Dashboard')
                    </h1>
                    @hasSection('subtitle')
                        <p class="truncate text-xs text-slate-500">@yield('subtitle')</p>
                    @endif
                </div>

                @hasSection('actions')
                    <div class="hidden items-center gap-2 sm:flex">
                        @yield('actions')
                    </div>
                @endif

                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle
                        class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 shadow-sm transition hover:border-slate-300">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-extrabold text-brand-700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-sm font-semibold text-slate-700 sm:block">{{ $user->name }}</span>
                        <x-icon name="chevron-down" class="hidden h-4 w-4 text-slate-400 sm:block" />
                    </button>

                    <div data-dropdown-menu
                        class="absolute right-0 mt-2 hidden w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="text-sm font-bold text-slate-800">{{ $user->name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ $user->email }}</p>
                        </div>

                        <a href="{{ route('app.akun.index') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            <x-icon name="wallet" class="h-4 w-4" />
                            Akun keuangan
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                                <x-icon name="logout" class="h-4 w-4" />
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 pb-20 pt-5 sm:px-6 lg:px-8">
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
