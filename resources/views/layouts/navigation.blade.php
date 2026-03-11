<nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-4">
        <div class="flex justify-between sm:justify-center h-12">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-8 w-auto fill-current text-gray-600" />
                    </a>
                </div>

                <!-- Navigation Links (PCのみ表示) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-8 sm:flex sm:justify-center">
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
                        @if(Auth::user()->developer == 1)
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                                {{ __('ユーザー管理') }}
                            </x-nav-link>
                        @endif
                    @endauth

                </div>
            </div>

            <!-- 右側：ユーザーアイコン（PC・モバイル共通） -->
            <div class="flex items-center pl-0 sm:pl-5">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-3 text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            @auth
                                <div class="flex gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24">
                                        <g fill="none" fill-rule="evenodd"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10s10-4.477 10-10S17.523 2 12 2M8.5 9.5a3.5 3.5 0 1 1 7 0a3.5 3.5 0 0 1-7 0m9.758 7.484A7.99 7.99 0 0 1 12 20a7.99 7.99 0 0 1-6.258-3.016C7.363 15.821 9.575 15 12 15s4.637.821 6.258 1.984"/></g>
                                    </svg><span class="pt-0.5 text-xs">{{ Auth::user()->name }}</span>
                                </div>
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
                            {{-- 管理者・開発者・経理向けのメニュー --}}
                            @if(Auth::user()->developer == 1 || Auth::user()->role_id == 11)
                                {{-- 勤務地登録はデベロッパーのみにするならここをさらに分ける --}}
                                @if(Auth::user()->developer == 1)
                                    <x-dropdown-link :href="route('admin.locations.index')" :active="request()->routeIs('admin.locations.index')">
                                        勤務地登録管理
                                    </x-dropdown-link>
                                @endif

                                <x-dropdown-link :href="route('admin.summary.index')" :active="request()->routeIs('admin.summary.*')">
                                    {{ __('勤務集計') }}
                                </x-dropdown-link>
                            @endif

                            {{-- 全ログインユーザー共通のメニュー --}}
                            <x-dropdown-link :href="route('attendance.history')" :active="request()->routeIs('attendance.history')">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                        <g fill="none"><path d="m12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036q-.016-.004-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.016-.018m.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M17 12a5 5 0 1 1 0 10a5 5 0 0 1 0-10m-1-9a1 1 0 0 1 1 1v1h2a2 2 0 0 1 2 2v3h-2V7H5v12h5v2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h2V4a1 1 0 0 1 2 0v1h6V4a1 1 0 0 1 1-1m1 11a3 3 0 1 0 0 6a3 3 0 0 0 0-6m0 .5a1 1 0 0 1 1 1v.5a1 1 0 1 1 0 2h-1a1 1 0 0 1-1-1v-1.5a1 1 0 0 1 1-1M8.5 14a1 1 0 0 1 .117 1.993L8.5 16H8a1 1 0 0 1-.117-1.993L8 14zm2.5-4a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2z"/></g>
                                    </svg>
                                    タイムカード
                                </div>
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                        <g fill="none" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M10.92 2.868a1.25 1.25 0 0 1 2.16 0l2.795 4.798l5.428 1.176a1.25 1.25 0 0 1 .667 2.054l-3.7 4.141l.56 5.525a1.25 1.25 0 0 1-1.748 1.27L12 19.592l-5.082 2.24a1.25 1.25 0 0 1-1.748-1.27l.56-5.525l-3.7-4.14a1.25 1.25 0 0 1 .667-2.055l5.428-1.176zM12 4.987L9.687 8.959a1.25 1.25 0 0 1-.816.592l-4.492.973l3.062 3.427c.234.262.347.61.312.959l-.463 4.573l4.206-1.854a1.25 1.25 0 0 1 1.008 0l4.206 1.854l-.463-4.573a1.25 1.25 0 0 1 .311-.959l3.063-3.427l-4.492-.973a1.25 1.25 0 0 1-.816-.592z"/></g>
                                    </svg>
                                    {{ __('プロフィール') }}
                                </div>
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24">
                                            <g fill="none"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M12 3a1 1 0 0 1 .117 1.993L12 5H7a1 1 0 0 0-.993.883L6 6v12a1 1 0 0 0 .883.993L7 19h4.5a1 1 0 0 1 .117 1.993L11.5 21H7a3 3 0 0 1-2.995-2.824L4 18V6a3 3 0 0 1 2.824-2.995L7 3zm5.707 5.464l2.828 2.829a1 1 0 0 1 0 1.414l-2.828 2.829a1 1 0 1 1-1.414-1.415L17.414 13H12a1 1 0 1 1 0-2h5.414l-1.121-1.121a1 1 0 0 1 1.414-1.415"/></g>
                                        </svg>
                                        {{ __('ログアウト') }}
                                    </div>
                                </x-dropdown-link>
                            </form>

                        @else
                            {{-- ゲスト（未ログイン）向けのメニュー --}}
                            <x-dropdown-link :href="route('login')">
                                {{ __('Log in') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('register')">
                                {{ __('Register') }}
                            </x-dropdown-link>
                        @endauth
                    </x-slot>
                </x-dropdown>

                <!-- Hamburger（モバイル専用） - 左側メニュー -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (モバイル専用・左側メニュー) -->
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
                {{-- デベロッパー または 経理(11) --}}
                @if(Auth::user()->developer == 1 || Auth::user()->role_id == 11)
                    {{-- ユーザー管理はデベロッパーだけにするなら条件を分ける --}}
                    @if(Auth::user()->developer == 1)
                        <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                            {{ __('ユーザー管理') }}
                        </x-responsive-nav-link>
                    @endif
                    
                    <x-responsive-nav-link :href="route('admin.summary.index')" :active="request()->routeIs('admin.summary.*')">
                        {{ __('勤務集計') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
            
        </div>
    </div>
</nav>