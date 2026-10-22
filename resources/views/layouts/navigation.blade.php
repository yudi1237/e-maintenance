<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-700/80 dark:bg-slate-900/95">
    <div class="em-shell">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ Auth::user()->canAccessAdmin() ? route('admin.dashboard') : route('home') }}" class="font-semibold text-slate-900 dark:text-white">
                        e-Maintenance
                        <span class="block text-xs font-normal text-slate-500 dark:text-slate-300">PT Dharma Poliplast</span>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center">
                    @if (Auth::user()->canAccessAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard*') || request()->routeIs('admin.machines.*')">
                            Dashboard Mesin
                        </x-nav-link>
                        @if (Auth::user()->seesFullDashboard())
                            <x-nav-link :href="route('admin.preventive.index')" :active="request()->routeIs('admin.preventive.*')">
                                Preventive Mesin
                            </x-nav-link>
                            <x-nav-link :href="route('admin.finding-recaps.index')" :active="request()->routeIs('admin.finding-recaps.*')">
                                Log Temuan
                            </x-nav-link>
                            <x-nav-link :href="route('admin.sparepart-history.index')" :active="request()->routeIs('admin.sparepart-history.*')">
                                History Spare Part
                            </x-nav-link>
                            <x-nav-link :href="route('admin.machine-names.index')" :active="request()->routeIs('admin.machine-names.*')">
                                Master Data Mesin
                            </x-nav-link>
                            <x-nav-link :href="route('admin.preventive-checklist.index')" :active="request()->routeIs('admin.preventive-checklist.*')">
                                Master Data Preventive
                            </x-nav-link>
                            <x-nav-link :href="route('admin.robots.index')" :active="request()->routeIs('admin.robots.*')">
                                Master Data Equipment
                            </x-nav-link>
                            <x-nav-link :href="route('admin.damage-categories.index')" :active="request()->routeIs('admin.damage-categories.*')">
                                Master Kategori
                            </x-nav-link>
                            <x-nav-link :href="route('admin.technicians.index')" :active="request()->routeIs('admin.technicians.*')">
                                Tambah Teknisi
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                Pengguna
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            Beranda
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

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            @if (Auth::user()->canAccessAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')">Dashboard Mesin</x-responsive-nav-link>
                @if (Auth::user()->seesFullDashboard())
                    <x-responsive-nav-link :href="route('admin.preventive.index')">Preventive Mesin</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.finding-recaps.index')">Log Temuan</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.sparepart-history.index')">History Spare Part</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.machine-names.index')">Master Data Mesin</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.preventive-checklist.index')">Master Data Preventive</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.robots.index')">Master Data Equipment</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.damage-categories.index')">Master Kategori</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.technicians.index')">Tambah Teknisi</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')">Pengguna</x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')">Beranda</x-responsive-nav-link>
                <x-responsive-nav-link
                    :href="route('preventive.menu')"
                    data-pic-pin-open
                    data-mode="preventive"
                    data-action="{{ route('preventive.pic') }}"
                    data-destination="menu"
                >{{ \App\Support\PreventiveCategory::hubTitle() }}</x-responsive-nav-link>
            @endif
        </div>
    </div>
</nav>
