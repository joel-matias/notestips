<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title', 'NotesTips')</title>
</head>
<body class="antialiased" style="background: var(--color-bg);" data-page="@yield('page')">

    <div
        x-data="{
            sidebarOpen: JSON.parse(localStorage.getItem('nt_sidebar') ?? JSON.stringify(window.innerWidth >= 1024)),
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('nt_sidebar', JSON.stringify(this.sidebarOpen));
            },
            closeSidebarOnMobile() {
                if (window.innerWidth < 1024) {
                    this.sidebarOpen = false;
                    localStorage.setItem('nt_sidebar', 'false');
                }
            }
        }"
        @close-sidebar.window="closeSidebarOnMobile()"
        class="h-screen flex overflow-hidden">

        {{-- ── Sidebar ── --}}
        <div
            class="fixed inset-y-0 left-0 z-40 shrink-0 flex flex-col overflow-hidden transition-all duration-300 ease-in-out lg:relative"
            :class="sidebarOpen ? 'w-80' : 'w-0'"
            style="background: white; border-right: 1px solid var(--color-border);">
            @include('partials.notes.sidebar')
        </div>

        {{-- Overlay móvil --}}
        <div x-show="sidebarOpen" x-cloak
            class="fixed inset-0 z-30 lg:hidden"
            style="background: rgba(17,24,39,0.4); backdrop-filter: blur(2px);"
            @click="toggleSidebar()">
        </div>

        {{-- ── Área principal ── --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Header mínimo --}}
            <header class="h-12 shrink-0 flex items-center px-3 gap-2"
                style="background: white; border-bottom: 1px solid var(--color-border);">

                <button type="button"
                    @click="toggleSidebar()"
                    class="p-2 rounded-lg transition-fast focus-visible:outline-none shrink-0"
                    style="color: var(--color-text-muted);"
                    onmouseover="this.style.background='var(--color-border)';"
                    onmouseout="this.style.background='transparent';"
                    aria-label="Toggle menú">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                {{-- Breadcrumb / título de página --}}
                <div class="flex-1 min-w-0">
                    @yield('header-title')
                </div>

                {{-- Acciones de header (si la vista las define) --}}
                @yield('header-actions')
            </header>

            <main class="flex-1 overflow-hidden">
                @yield('main-content')
            </main>
        </div>
    </div>

    {{-- FAB nueva nota (móvil) --}}
    @if (!request()->routeIs('notes.create') && !request()->routeIs('notes.edit'))
    <a href="{{ route('notes.create') }}"
        class="fixed bottom-5 right-5 z-50 lg:hidden flex items-center justify-center w-14 h-14 rounded-2xl text-white shadow-lg transition-fast"
        style="background: var(--color-primary);"
        onmouseover="this.style.transform='scale(1.08)';"
        onmouseout="this.style.transform='scale(1)';"
        aria-label="Nueva nota">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
    </a>
    @endif

    {{-- Toast container --}}
    <div id="toast-container"></div>

    @if (session('status'))
        <script>window.__flashStatus = @json(session('status'));</script>
    @endif

    @stack('scripts')

    <script>
    (() => {
        const messages = {
            created: { type: 'success', text: 'Nota creada correctamente' },
            updated: { type: 'success', text: 'Cambios guardados' },
            deleted: { type: 'info',    text: 'Nota eliminada' },
        };

        function showToast(type, text) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            const icons = {
                success: `<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>`,
                error:   `<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>`,
                info:    `<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>`,
            };
            toast.innerHTML = `${icons[type] || ''}<span>${text}</span>`;
            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('toast-hiding'); setTimeout(() => toast.remove(), 220); }, 3200);
        }

        const status = window.__flashStatus;
        if (status && messages[status]) setTimeout(() => showToast(messages[status].type, messages[status].text), 80);
        window.showToast = showToast;
    })();
    </script>
</body>
</html>
