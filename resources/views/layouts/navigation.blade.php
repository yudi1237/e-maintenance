@php
    $assetMenuActive = request()->routeIs('admin.machine-names.*', 'admin.robots.*', 'admin.molds.*');
    $preventiveMenuActive = request()->routeIs('admin.preventive.*', 'admin.preventive-checklist.*');
    $usersMenuActive = request()->routeIs('admin.technicians.*', 'admin.users.*');
    $reportMenuActive = request()->routeIs('admin.finding-recaps.*', 'admin.report.*');
    $reportCounts = $reportMenuCounts ?? ['findings' => 0, 'machines' => 0, 'areas' => 0];
    $findingMenuHref = auth()->user()?->seesFullDashboard()
        ? route('admin.finding-recaps.index')
        : route('admin.report.findings');
@endphp
<style>
    .em-nav-current {
        background-color: #0891b2 !important;
        color: #ffffff !important;
    }
    .em-nav-current:hover,
    .em-nav-current:focus {
        color: #ffffff !important;
    }
    .em-nav-sub-current {
        background-color: #ecfeff !important;
        color: #0e7490 !important;
    }
    html.dark .em-nav-sub-current {
        background-color: rgba(6, 182, 212, 0.22) !important;
        color: #cffafe !important;
    }
    .em-nav-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.15rem;
        height: 1.15rem;
        margin-left: 0.35rem;
        padding: 0 0.3rem;
        border-radius: 999px;
        background: #e11d48;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        vertical-align: middle;
    }
</style>
<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/95">
    <div class="em-shell">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route(Auth::user()->homeRouteName()) }}" class="font-semibold text-slate-900 dark:text-white">
                        e-Maintenance
                        <span class="block text-xs font-normal text-slate-500 dark:text-slate-300">PT Dharma Poliplast</span>
                    </a>
                </div>

                <div class="hidden sm:ms-6 sm:flex sm:items-center sm:gap-1">
                    @if (Auth::user()->isOperator())
                        <x-nav-link :href="route('operator.home')" :active="request()->routeIs('operator.home')">
                            Mesin
                        </x-nav-link>
                    @elseif (Auth::user()->canAccessAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard*') || request()->routeIs('admin.machines.*')">
                            Dashboard Mesin
                        </x-nav-link>
                        @if (Auth::user()->seesFullDashboard())
                            <x-nav-dropdown :active="$preventiveMenuActive">
                                <x-slot name="trigger">Preventive Mesin</x-slot>
                                <x-slot name="content">
                                    <x-nav-dropdown-link :href="route('admin.preventive-checklist.index')" :active="request()->routeIs('admin.preventive-checklist.*')">
                                        Check Sheet
                                    </x-nav-dropdown-link>
                                    <x-nav-dropdown-link :href="route('admin.preventive.index')" :active="request()->routeIs('admin.preventive.*')">
                                        Preventive Mesin
                                    </x-nav-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>
                        @elseif (Auth::user()->isQuality())
                            <x-nav-link :href="route('admin.preventive.index')" :active="request()->routeIs('admin.preventive.*')">
                                Preventive Mesin
                            </x-nav-link>
                        @endif
                        <x-nav-dropdown :active="$reportMenuActive">
                            <x-slot name="trigger">
                                Report
                                <span class="em-nav-count">{{ array_sum($reportCounts) }}</span>
                            </x-slot>
                            <x-slot name="content">
                                <x-nav-dropdown-link :href="$findingMenuHref" :active="request()->routeIs('admin.finding-recaps.*', 'admin.report.findings')">
                                    Log Temuan <span class="em-nav-count">{{ $reportCounts['findings'] }}</span>
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('admin.report.op-mesin')" :active="request()->routeIs('admin.report.op-mesin')">
                                    OP Mesin <span class="em-nav-count">{{ $reportCounts['machines'] }}</span>
                                </x-nav-dropdown-link>
                                <x-nav-dropdown-link :href="route('admin.report.op-area')" :active="request()->routeIs('admin.report.op-area')">
                                    OP Area <span class="em-nav-count">{{ $reportCounts['areas'] }}</span>
                                </x-nav-dropdown-link>
                            </x-slot>
                        </x-nav-dropdown>
                        @if (Auth::user()->seesFullDashboard())
                            <x-nav-link :href="route('admin.sparepart-history.index')" :active="request()->routeIs('admin.sparepart-history.*')">
                                History Spare Part
                            </x-nav-link>
                            <x-nav-dropdown :active="$assetMenuActive">
                                <x-slot name="trigger">Asset</x-slot>
                                <x-slot name="content">
                                    <x-nav-dropdown-link :href="route('admin.machine-names.index')" :active="request()->routeIs('admin.machine-names.*')">
                                        Data Mesin
                                    </x-nav-dropdown-link>
                                    <x-nav-dropdown-link :href="route('admin.robots.index')" :active="request()->routeIs('admin.robots.*')">
                                        Data Equipment
                                    </x-nav-dropdown-link>
                                    <x-nav-dropdown-link :href="route('admin.molds.index')" :active="request()->routeIs('admin.molds.*')">
                                        Data Mold
                                    </x-nav-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>
                            <x-nav-link :href="route('admin.damage-categories.index')" :active="request()->routeIs('admin.damage-categories.*')">
                                Category
                            </x-nav-link>
                            <x-nav-dropdown :active="$usersMenuActive">
                                <x-slot name="trigger">Users</x-slot>
                                <x-slot name="content">
                                    <x-nav-dropdown-link :href="route('admin.technicians.index')" :active="request()->routeIs('admin.technicians.*')">
                                        Teknisi
                                    </x-nav-dropdown-link>
                                    <x-nav-dropdown-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                        User Login
                                    </x-nav-dropdown-link>
                                </x-slot>
                            </x-nav-dropdown>
                        @endif
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            Beranda
                        </x-nav-link>
                        <x-nav-link :href="route('home.op-tickets')" :active="request()->routeIs('home.op-tickets')">
                            Tiket OP
                        </x-nav-link>
                        <x-nav-link
                            :href="route('preventive.menu')"
                            :active="request()->routeIs('preventive.*', 'preventive-conveyor.*')"
                            class="max-w-[13rem] whitespace-normal text-center leading-tight"
                            data-pic-pin-open
                            data-mode="preventive"
                            data-action="{{ route('preventive.pic') }}"
                            data-destination="menu"
                        >
                            {{ \App\Support\PreventiveCategory::hubTitle() }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <x-theme-toggle />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm text-slate-700 hover:text-slate-900 dark:text-slate-200 dark:hover:text-white">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if (Auth::user()->canAccessAdmin())
                            <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center gap-1 sm:hidden">
                <x-theme-toggle />
                <button @click="open = ! open" class="p-2 rounded-md text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800" x-data="{ assetOpen: {{ $assetMenuActive ? 'true' : 'false' }}, preventiveOpen: {{ $preventiveMenuActive ? 'true' : 'false' }}, usersOpen: {{ $usersMenuActive ? 'true' : 'false' }}, reportOpen: {{ $reportMenuActive ? 'true' : 'false' }} }">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @if (Auth::user()->isOperator())
                <x-responsive-nav-link :href="route('operator.home')" :active="request()->routeIs('operator.home')">Mesin</x-responsive-nav-link>
            @elseif (Auth::user()->canAccessAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard*') || request()->routeIs('admin.machines.*')">Dashboard Mesin</x-responsive-nav-link>
                @if (Auth::user()->seesFullDashboard())
                    <button type="button" @click="preventiveOpen = ! preventiveOpen" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-start text-base font-medium {{ $preventiveMenuActive ? 'em-nav-current' : 'text-slate-700 dark:text-slate-200' }}">
                        Preventive Mesin
                        <svg class="h-4 w-4 transition" :class="preventiveOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="preventiveOpen" x-cloak class="space-y-1 ps-3">
                        <x-responsive-nav-link :href="route('admin.preventive-checklist.index')" :active="request()->routeIs('admin.preventive-checklist.*')">Check Sheet</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.preventive.index')" :active="request()->routeIs('admin.preventive.*')">Preventive Mesin</x-responsive-nav-link>
                    </div>
                    @elseif (Auth::user()->isQuality())
                        <x-responsive-nav-link :href="route('admin.preventive.index')" :active="request()->routeIs('admin.preventive.*')">Preventive Mesin</x-responsive-nav-link>
                    @endif
                    <button type="button" @click="reportOpen = ! reportOpen" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-start text-base font-medium {{ $reportMenuActive ? 'em-nav-current' : 'text-slate-700 dark:text-slate-200' }}">
                        <span>Report <span class="em-nav-count">{{ array_sum($reportCounts) }}</span></span>
                        <svg class="h-4 w-4 transition" :class="reportOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="reportOpen" x-cloak class="space-y-1 ps-3">
                        <x-responsive-nav-link :href="$findingMenuHref" :active="request()->routeIs('admin.finding-recaps.*', 'admin.report.findings')">Log Temuan <span class="em-nav-count">{{ $reportCounts['findings'] }}</span></x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.report.op-mesin')" :active="request()->routeIs('admin.report.op-mesin')">OP Mesin <span class="em-nav-count">{{ $reportCounts['machines'] }}</span></x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.report.op-area')" :active="request()->routeIs('admin.report.op-area')">OP Area <span class="em-nav-count">{{ $reportCounts['areas'] }}</span></x-responsive-nav-link>
                    </div>
                    @if (Auth::user()->seesFullDashboard())
                    <x-responsive-nav-link :href="route('admin.sparepart-history.index')" :active="request()->routeIs('admin.sparepart-history.*')">History Spare Part</x-responsive-nav-link>
                    <button type="button" @click="assetOpen = ! assetOpen" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-start text-base font-medium {{ $assetMenuActive ? 'em-nav-current' : 'text-slate-700 dark:text-slate-200' }}">
                        Asset
                        <svg class="h-4 w-4 transition" :class="assetOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="assetOpen" x-cloak class="space-y-1 ps-3">
                        <x-responsive-nav-link :href="route('admin.machine-names.index')" :active="request()->routeIs('admin.machine-names.*')">Data Mesin</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.robots.index')" :active="request()->routeIs('admin.robots.*')">Data Equipment</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.molds.index')" :active="request()->routeIs('admin.molds.*')">Data Mold</x-responsive-nav-link>
                    </div>
                    <x-responsive-nav-link :href="route('admin.damage-categories.index')" :active="request()->routeIs('admin.damage-categories.*')">Category</x-responsive-nav-link>
                    <button type="button" @click="usersOpen = ! usersOpen" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-start text-base font-medium {{ $usersMenuActive ? 'em-nav-current' : 'text-slate-700 dark:text-slate-200' }}">
                        Users
                        <svg class="h-4 w-4 transition" :class="usersOpen ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="usersOpen" x-cloak class="space-y-1 ps-3">
                        <x-responsive-nav-link :href="route('admin.technicians.index')" :active="request()->routeIs('admin.technicians.*')">Teknisi</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">User Login</x-responsive-nav-link>
                    </div>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Beranda</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('home.op-tickets')" :active="request()->routeIs('home.op-tickets')">Tiket OP</x-responsive-nav-link>
                <x-responsive-nav-link
                    :href="route('preventive.menu')"
                    data-pic-pin-open
                    data-mode="preventive"
                    data-action="{{ route('preventive.pic') }}"
                    data-destination="menu"
                >{{ \App\Support\PreventiveCategory::hubTitle() }}</x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-700">
            <div class="px-3 pb-2 text-sm font-medium text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-start text-base font-medium text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-500/10">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
