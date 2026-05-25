@php
    $activePage = $activePage ?? 'home';

    $navItems = [
        [
            'key' => 'home',
            'label' => 'Beranda',
            'route' => route('home'),
        ],
        [
            'key' => 'monitoring',
            'label' => 'Monitoring NO2',
            'route' => route('monitoring'),
        ],
        [
            'key' => 'tentang',
            'label' => 'Tentang Sistem',
            'route' => route('tentang'),
        ],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-[#D7E2D8] bg-white/92 backdrop-blur-xl">
    <nav class="flex h-[76px] w-full items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center">
            <img class="h-12 w-auto sm:h-14" src="{{ asset('images/logoCila.png') }}" alt="Logo CilaAir" />
        </a>

        <div class="ml-auto hidden items-center gap-2 md:flex">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="{{ $activePage === $item['key'] ? 'bg-[#EEF3EE] text-[#163126] shadow-[inset_0_0_0_1px_rgba(22,49,38,0.08)]' : 'text-[#587060] hover:bg-[#F5F8F5] hover:text-[#163126]' }} rounded-full px-5 py-2.5 text-sm font-semibold transition"
                >
                    {!! $item['label'] === 'Monitoring NO2' ? 'Monitoring NO<sub>2</sub>' : e($item['label']) !!}
                </a>
            @endforeach
        </div>

        <button
            type="button"
            id="menu-btn"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-[#D7E2D8] bg-white text-[#163126] transition hover:bg-[#F5F8F5] md:hidden"
            aria-label="Buka atau tutup menu navigasi"
            aria-controls="mobile-menu"
            aria-expanded="false"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </nav>

    <div id="mobile-menu" class="hidden border-t border-[#E5ECE6] bg-white/96 px-4 pb-4 pt-3 md:hidden">
        <div class="flex flex-col gap-2">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['route'] }}"
                    class="{{ $activePage === $item['key'] ? 'bg-[#EEF3EE] text-[#163126]' : 'text-[#587060] hover:bg-[#F5F8F5] hover:text-[#163126]' }} rounded-2xl px-4 py-3 text-sm font-semibold transition"
                >
                    {!! $item['label'] === 'Monitoring NO2' ? 'Monitoring NO<sub>2</sub>' : e($item['label']) !!}
                </a>
            @endforeach
        </div>
    </div>
</header>

