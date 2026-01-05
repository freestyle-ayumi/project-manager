<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-12">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-8 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
                        {{ __('イベント管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                        {{ __('タスク管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('quotes.index')" :active="request()->routeIs('quotes.index')">
                        {{ __('見積書管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('deliveries.index')" :active="request()->routeIs('deliveries.index')">
                        {{ __('納品書管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.index')">
                        {{ __('請求書管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.index')">
                        {{ __('経費管理') }}
                    </x-nav-link>

                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.index')">
                        {{ __('顧客管理') }}
                    </x-nav-link>

                    @auth
                        @if(in_array(Auth::user()->role->name, ['master', 'developer']))
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                                {{ __('ユーザー管理') }}
                            </x-nav-link>
                        @endif
                    @endauth

            </div>

 <!-- Settings Dropdown -->
<div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-3 text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                @auth
                    <div>{{ Auth::user()->name }}</div>
                @else
                    <div>ゲスト</div>
                @endauth

                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            @auth
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            @else
                <x-dropdown-link :href="route('login')">
                    {{ __('Log in') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('register')">
                    {{ __('Register') }}
                </x-dropdown-link>
            @endauth
        </x-slot>
    </x-dropdown>
</div>


            <!-- Hamburger -->
            <div class="-me-2 flex items-center !justify-end sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
                {{ __('イベント管理') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                {{ __('タスク管理') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('quotes.index')" :active="request()->routeIs('quotes.index')">
                {{ __('見積書一覧') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('deliveries.index')" :active="request()->routeIs('deliveries.index')">
                {{ __('納品書一覧') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.index')">
                {{ __('請求書一覧') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.index')">
                {{ __('経費一覧') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.index')">
                {{ __('顧客管理') }}
            </x-responsive-nav-link>

            @auth
                @if(in_array(Auth::user()->role->name, ['master', 'developer']))
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('ユーザー管理') }}
                    </x-responsive-nav-link>
                @endif
            @endauth

        </div>
    </div>
</nav>
