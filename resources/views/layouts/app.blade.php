<!DOCTYPE html>
<html lang="id" x-data="appLayout()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ERP Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Early Theme Script: Prevent flash of light mode -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="antialiased font-sans">
    <!-- Notification Toast -->
    <div x-data="notificationComponent()" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed top-6 right-6 z-[200] w-full max-w-xs"
         style="display: none;">
        <div :class="{
                'border-emerald-500': type === 'success',
                'border-rose-500': type === 'error',
                'border-amber-500': type === 'warning'
             }" class="bg-[var(--bg-card)] px-5 py-4 rounded-xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.4)] flex items-start space-x-4 border border-l-4 border-[var(--border)]">
            
            <div class="flex-1">
                <p x-text="message" class="text-sm font-bold leading-tight text-[var(--text-main)]"></p>
                <p class="text-[10px] text-[var(--text-muted)] mt-1 font-bold tracking-wider uppercase" x-text="type === 'success' ? 'Berhasil' : 'Pemberitahuan'"></p>
            </div>

            <button @click="show = false" class="flex-shrink-0 text-[var(--text-muted)] hover:text-[var(--text-main)] transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- Confirmation Modal Component -->
    <div x-data="confirmComponent()"
         x-show="show"
         class="fixed inset-0 z-[300] flex items-center justify-center p-4 overflow-hidden"
         style="display: none;">
        
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
        
        <div x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-[var(--bg-card)] w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden relative z-[310] border border-[var(--border)]">
            
            <div class="p-8 text-center">
                <h3 class="text-xl font-bold text-[var(--text-main)] mb-2" x-text="title"></h3>
                <p class="text-sm text-[var(--text-muted)] leading-relaxed" x-text="message"></p>
            </div>

            <div class="flex border-t border-[var(--border)]">
                <button @click="show = false" 
                        class="flex-1 px-6 py-4 text-sm font-bold text-[var(--text-muted)] hover:bg-slate-500/5 transition-colors border-r border-[var(--border)]" 
                        x-text="cancelLabel"></button>
                <button @click="proceed" 
                        :disabled="loading"
                        class="flex-1 px-6 py-4 text-sm font-bold text-rose-500 hover:bg-rose-500/5 transition-all">
                    <span x-show="!loading" x-text="confirmLabel"></span>
                    <span x-show="loading" class="animate-pulse">Proses...</span>
                </button>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden text-sm">
        <!-- Sidebar -->
        <aside class="sidebar transition-all duration-300 ease-in-out" 
               :class="{ '-ml-56': !sidebarOpen, 'ml-0': sidebarOpen }">
            <div class="px-5 py-5">
                <h1 class="text-base font-bold tracking-tight">ERP<span class="text-[var(--primary)]">System</span></h1>
            </div>
            
            <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto">
                @php
                    $menus = [
                        ['name' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'dashboard'],
                        ['name' => 'Customers', 'icon' => 'users', 'route' => 'customers'],
                        ['name' => 'Services', 'icon' => 'package', 'route' => 'services'],
                        ['name' => 'Subscriptions', 'icon' => 'refresh-cw', 'route' => 'subscriptions'],
                        ['name' => 'Invoices', 'icon' => 'file-text', 'route' => 'invoices'],
                    ];
                @endphp

                @foreach($menus as $menu)
                    <a href="{{ route($menu['route']) }}" 
                       class="{{ request()->routeIs($menu['route']) ? 'nav-link-active' : 'nav-link' }} py-2 px-3">
                        <i data-lucide="{{ $menu['icon'] }}" class="mr-2.5 w-4 h-4"></i>
                        <span class="font-semibold text-xs">{{ $menu['name'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="p-3 border-t border-[var(--border)]">
                <button @click="darkMode = !darkMode; localStorage.setItem('theme', darkMode ? 'dark' : 'light'); $nextTick(() => lucide.createIcons())" 
                        class="dark-mode-btn scale-90 origin-left">
                    <template x-if="!darkMode">
                        <div class="flex items-center">
                            <i data-lucide="moon" class="mr-3 w-5 h-5"></i> Dark Mode
                        </div>
                    </template>
                    <template x-if="darkMode">
                        <div class="flex items-center">
                            <i data-lucide="sun" class="mr-3 w-5 h-5 text-yellow-400"></i> Light Mode
                        </div>
                    </template>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar (Mobile) -->
            <header class="topbar">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="text-[var(--text-main)] p-2 hover:bg-slate-500/5 rounded-lg transition-colors focus:outline-none">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <!-- Optional: Page indicator or breadcrumb could go here -->
                </div>
                
                <div class="md:flex-1 hidden md:block">
                    <!-- Search or info can go here -->
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex flex-col items-end">
                        <span class="text-xs font-semibold text-[var(--text-main)]">{{ Auth::user()->name ?? 'Administrator' }}</span>
                        <span class="text-[9px] uppercase tracking-widest text-[var(--text-muted)] font-bold">Administrator</span>
                    </div>
                    
                    <!-- Logout Trigger -->
                    <div class="border-l border-[var(--border)] h-6 ml-2"></div>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <button type="button" 
                                class="p-1.5 text-[var(--text-muted)] hover:text-rose-500 transition-colors"
                                title="Log Out"
                                @click="window.confirmAction({
                                    title: 'Keluar Sesi',
                                    message: 'Apakah Anda yakin ingin keluar dari aplikasi?',
                                    confirmLabel: 'Ya, Keluar',
                                    onConfirm: () => document.getElementById('logout-form').submit()
                                })">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6 scroll-smooth">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>