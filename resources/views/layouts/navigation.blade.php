<nav x-data="{ open: false }" class="bg-gradient-to-r from-sky-500 via-teal-500 to-emerald-500 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <x-application-logo class="block h-16 w-auto text-white" />
                    <span class="text-white font-bold text-lg">Klinik Sehat</span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                <x-nav-link :href="route('dashboard')" 
                    :active="request()->routeIs('dashboard')" 
                    class="text-white hover:text-yellow-200 transition">
                    {{ __('Dashboard') }}

                </x-nav-link>

                @if(Auth::user()->role === \App\Enums\UserRole::ADMIN)
                    <x-nav-link :href="route('admin.polis.index')" :active="request()->routeIs('admin.polis.*')" class="text-white hover:text-yellow-200 transition">
                        {{ __('Manajemen Poli') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.doctors.index')" :active="request()->routeIs('admin.doctors.*')" class="text-white hover:text-yellow-200 transition">
                        {{ __('Manajemen Dokter') }}
                    </x-nav-link>

                    <x-nav-link :href="route('admin.medicines.index')" :active="request()->routeIs('admin.medicines.*')" class="text-white hover:text-yellow-200 transition">
                        {{ __('Inventaris Obat') }}
                    </x-nav-link>

                    <!-- Dropdown -->
                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 rounded-md text-white hover:text-yellow-200 transition">
                                    <span>Laporan</span>
                                    <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('admin.reports.revenue')">
                                    {{ __('Laporan Pendapatan') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.reports.patients')">
                                    {{ __('Laporan Pasien') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                @if(Auth::user()->role === \App\Enums\UserRole::PASIEN)
                    <x-nav-link :href="route('pasien.booking.step-one')" :active="request()->routeIs('pasien.booking.*')" class="text-white hover:text-yellow-200 transition">
                        {{ __('Booking Online') }}
                    </x-nav-link>
                @endif
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center">
                <!-- Theme Toggle Button -->
                <div x-data="{ darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }">
                    <button @click="darkMode = !darkMode; if(darkMode){ document.documentElement.classList.add('dark'); localStorage.theme = 'dark'; } else { document.documentElement.classList.remove('dark'); localStorage.theme = 'light'; }" class="text-white hover:text-yellow-200 p-2 mr-2 focus:outline-none transition" title="Toggle Dark Mode">
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center px-3 py-2 text-white hover:text-yellow-200 transition">
                            <span class="font-medium">{{ Auth::user()->name }}</span>
                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.584l3.71-4.354a.75.75 0 011.14.976l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Toggle -->
            <div class="sm:hidden flex items-center space-x-2">
                <!-- Mobile Theme Toggle -->
                <div x-data="{ darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }">
                    <button @click="darkMode = !darkMode; if(darkMode){ document.documentElement.classList.add('dark'); localStorage.theme = 'dark'; } else { document.documentElement.classList.remove('dark'); localStorage.theme = 'light'; }" class="text-white hover:text-yellow-200 p-2 focus:outline-none transition">
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </button>
                </div>

                <button @click="open = ! open" class="text-white hover:text-yellow-200 p-2 rounded-md focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="sm:hidden bg-white shadow-md">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(Auth::user()->role === \App\Enums\UserRole::ADMIN)
                <x-responsive-nav-link :href="route('admin.polis.index')">{{ __('Manajemen Poli') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.doctors.index')">{{ __('Manajemen Dokter') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.medicines.index')">{{ __('Inventaris Obat') }}</x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === \App\Enums\UserRole::PASIEN)
                <x-responsive-nav-link :href="route('pasien.booking.step-one')">{{ __('Booking Online') }}</x-responsive-nav-link>
            @endif
        </div>

        <!-- Mobile User Info -->
        <div class="border-t border-gray-200 px-4 py-3 bg-gray-50">
            <div class="font-medium text-base">{{ Auth::user()->name }}</div>
            <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
