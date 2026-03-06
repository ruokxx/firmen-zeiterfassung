<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Navigation Links (Buttons) -->
                <div class="hidden sm:-my-px sm:flex sm:items-center sm:gap-4 overflow-x-auto no-scrollbar">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                        class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('dashboard') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('help')" :active="request()->routeIs('help')" 
                        class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('help') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                        {{ __('Hilfe & FAQ') }}
                    </x-nav-link>

                    <x-nav-link :href="route('material-orders.index')" :active="request()->routeIs('material-orders.*')" 
                        class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('material-orders.*') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                        {{ __('Material Bestellungen') }}
                    </x-nav-link>

                    <x-nav-link :href="route('materials.index')" :active="request()->routeIs('materials.index')" 
                        class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('materials.index') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                        {{ __('Lager') }}
                    </x-nav-link>

                    @if(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)
                        <x-nav-link :href="route('materials.manage')" :active="request()->routeIs('materials.manage') || request()->routeIs('materials.stats')" 
                            class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('materials.manage') || request()->routeIs('materials.stats') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                            {{ __('Materialverwaltung') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->is_admin)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" 
                            class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                            {{ __('Chef Bereich') }}
                        </x-nav-link>

                        @if(auth()->user()->is_super_admin)
                            <x-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" 
                                class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.settings') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}"
                                onclick="return confirm('ACHTUNG: Sie sind dabei, gravierende Server-Einstellungen zu verändern. Möchten Sie wirklich fortfahren?');">
                                {{ __('Einstellungen') }}
                            </x-nav-link>
                        @endif

                        <x-nav-link :href="route('admin.documents.index')" :active="request()->routeIs('admin.documents.*')" 
                            class="px-4 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition {{ request()->routeIs('admin.documents.*') ? 'bg-orange-600 text-white shadow-md' : 'bg-gray-800 text-gray-300 hover:bg-gray-700 hover:text-orange-400' }}">
                            {{ __('Dokument an alle senden') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 bg-gray-800 hover:text-orange-400 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-orange-400 hover:bg-gray-800 focus:outline-none focus:bg-gray-800 focus:text-orange-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-900 border-t border-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('help')" :active="request()->routeIs('help')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                {{ __('Hilfe & FAQ') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('material-orders.index')" :active="request()->routeIs('material-orders.*')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                {{ __('Material Bestellungen') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('materials.index')" :active="request()->routeIs('materials.index')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                {{ __('Lager') }}
            </x-responsive-nav-link>

            @if(auth()->user()->is_admin || auth()->user()->is_chef || auth()->user()->is_materialwart)
                <x-responsive-nav-link :href="route('materials.manage')" :active="request()->routeIs('materials.manage') || request()->routeIs('materials.stats')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                    {{ __('Materialverwaltung') }}
                </x-responsive-nav-link>
            @endif

            @if(auth()->user()->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                    {{ __('Chef Bereich') }}
                </x-responsive-nav-link>

                @if(auth()->user()->is_super_admin)
                    <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')" class="text-white hover:text-orange-400 hover:bg-gray-800"
                        onclick="return confirm('ACHTUNG: Sie sind dabei, gravierende Server-Einstellungen zu verändern. Möchten Sie wirklich fortfahren?');">
                        {{ __('Einstellungen') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('admin.documents.index')" :active="request()->routeIs('admin.documents.*')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                    {{ __('Dokument an alle senden') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-800">
            <div class="px-4">
                <div class="font-medium text-base text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:text-orange-400 hover:bg-gray-800">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" class="text-white hover:text-orange-400 hover:bg-gray-800"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
