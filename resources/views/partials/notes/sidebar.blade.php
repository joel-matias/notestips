<aside class="h-full w-80 flex flex-col overflow-hidden" style="background: white;">

    {{-- ── Top: logo + nueva nota ── --}}
    <div class="shrink-0 px-4 pt-4 pb-3 flex items-center gap-2"
        style="border-bottom: 1px solid var(--color-border);">
        <a href="{{ route('home') }}" class="flex items-center gap-2 min-w-0 flex-1">
            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center"
                style="background: var(--color-primary);">
                <svg class="w-4.5 h-4.5 text-white" viewBox="0 0 24 24" fill="none">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M8 7h8M8 11h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="font-bold text-sm truncate" style="color: var(--color-text);">NotesTips</span>
        </a>
        <a href="{{ route('notes.create') }}"
            id="new-note-btn"
            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white transition-fast"
            style="background: var(--color-primary);"
            onmouseover="this.style.background='var(--color-primary-hover)';"
            onmouseout="this.style.background='var(--color-primary)';"
            aria-label="Nueva nota">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nueva
        </a>
    </div>

    {{-- ── Búsqueda ── --}}
    <div class="shrink-0 px-3 py-2.5" style="border-bottom: 1px solid var(--color-border);">
        <form action="{{ route('notes.index') }}" method="GET" id="search-form">
            <div class="relative">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                    style="color: var(--color-text-subtle);"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input id="q" name="q" type="search" value="{{ request('q') }}"
                    autocomplete="off"
                    placeholder="Buscar notas..."
                    class="w-full h-9 pl-8 pr-3 rounded-xl text-sm transition-fast focus:outline-none"
                    style="background: var(--color-bg); border: 1px solid var(--color-border); color: var(--color-text);"
                    onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 2px var(--color-primary-ring)'; this.style.background='white';"
                    onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'; this.style.background='var(--color-bg)';">
            </div>
        </form>
    </div>

    {{-- ── Filtros colapsables ── --}}
    <div x-data="{ filtersOpen: {{ request()->hasAny(['importance','due_date_mode','due_date','order_by']) ? 'true' : 'false' }} }"
        class="shrink-0" style="border-bottom: 1px solid var(--color-border);">

        <button type="button"
            @click="filtersOpen = !filtersOpen"
            class="w-full flex items-center justify-between px-4 py-2 text-xs font-semibold transition-fast focus:outline-none"
            style="color: var(--color-text-subtle);"
            onmouseover="this.style.background='var(--color-bg)';"
            onmouseout="this.style.background='transparent';">
            <span class="flex items-center gap-1.5 uppercase tracking-widest">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"/>
                </svg>
                Filtros
                @if(request()->hasAny(['importance','due_date_mode','due_date','order_by']))
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-white text-xs font-bold"
                        style="background: var(--color-primary); font-size: 9px;">
                        {{ count(array_filter(request()->only(['importance','due_date_mode','order_by']))) }}
                    </span>
                @endif
            </span>
            <svg class="w-3.5 h-3.5 transition-transform" :class="filtersOpen ? 'rotate-180' : ''"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>

        <div x-show="filtersOpen" x-cloak class="px-3 pb-3 space-y-2">

            <div class="flex flex-col gap-1">
                <label for="importance" class="text-xs font-medium" style="color: var(--color-text-subtle);">Importancia</label>
                <select id="importance" name="importance"
                    class="h-8 px-2 rounded-lg text-xs cursor-pointer transition-fast focus:outline-none"
                    style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                    onfocus="this.style.borderColor='var(--color-primary)';"
                    onblur="this.style.borderColor='var(--color-border)';">
                    <option value="" @selected(request('importance') === '')>Todas</option>
                    <option value="none"  @selected(request('importance') === 'none')>Sin importancia</option>
                    <option value="alta"  @selected(request('importance') === 'alta')>🔴 Alta</option>
                    <option value="media" @selected(request('importance') === 'media')>🟡 Media</option>
                    <option value="baja"  @selected(request('importance') === 'baja')>🔵 Baja</option>
                </select>
            </div>

            <div class="flex gap-2">
                <div class="flex-1 flex flex-col gap-1">
                    <label for="due_date_mode" class="text-xs font-medium" style="color: var(--color-text-subtle);">Entrega</label>
                    <select id="due_date_mode" name="due_date_mode"
                        class="h-8 px-2 rounded-lg text-xs cursor-pointer transition-fast focus:outline-none"
                        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                        onfocus="this.style.borderColor='var(--color-primary)';"
                        onblur="this.style.borderColor='var(--color-border)';">
                        <option value="" @selected(request('due_date_mode') === '')>Todas</option>
                        <option value="with"  @selected(request('due_date_mode') === 'with')>Con fecha</option>
                        <option value="none"  @selected(request('due_date_mode') === 'none')>Sin fecha</option>
                        <option value="exact" @selected(request('due_date_mode') === 'exact')>Exacta</option>
                    </select>
                </div>
                <div class="flex-1 flex flex-col gap-1">
                    <label for="due_date" class="text-xs font-medium" style="color: var(--color-text-subtle);">Fecha</label>
                    <input id="due_date" type="date" name="due_date" value="{{ request('due_date') }}"
                        class="h-8 px-2 rounded-lg text-xs transition-fast focus:outline-none"
                        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                        onfocus="this.style.borderColor='var(--color-primary)';"
                        onblur="this.style.borderColor='var(--color-border)';">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label for="order_by" class="text-xs font-medium" style="color: var(--color-text-subtle);">Ordenar</label>
                <select name="order_by" id="order_by"
                    class="h-8 px-2 rounded-lg text-xs cursor-pointer transition-fast focus:outline-none"
                    style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                    onfocus="this.style.borderColor='var(--color-primary)';"
                    onblur="this.style.borderColor='var(--color-border)';">
                    <option value="" @selected(request('order_by') === '')>Más recientes</option>
                    <option value="created_at" @selected(request('order_by') === 'created_at')>Creación</option>
                    <option value="due_date"   @selected(request('order_by') === 'due_date')>Entrega</option>
                </select>
            </div>

            {{-- Limpiar filtros --}}
            <button type="button" id="clear-filters-btn"
                class="w-full text-xs py-1.5 rounded-lg transition-fast"
                style="color: var(--color-primary); border: 1px solid var(--color-primary-soft); background: var(--color-primary-soft);"
                onmouseover="this.style.background='var(--color-primary)'; this.style.color='white';"
                onmouseout="this.style.background='var(--color-primary-soft)'; this.style.color='var(--color-primary)';">
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- ── Lista de notas ── --}}
    @include('partials.notes.list')

    {{-- ── Usuario ── --}}
    <div class="shrink-0 px-3 py-3" style="border-top: 1px solid var(--color-border);">
        <div class="flex items-center gap-2.5 mb-2.5 min-w-0">
            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-white text-sm font-bold"
                style="background: var(--color-primary);">
                {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium truncate" style="color: var(--color-text);">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate" style="color: var(--color-text-subtle);">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="cursor-pointer w-full flex items-center justify-center gap-2 rounded-xl py-1.5 text-xs transition-fast"
                style="border: 1px solid var(--color-border); color: var(--color-text-muted); background: white;"
                onmouseover="this.style.background='var(--color-bg)';"
                onmouseout="this.style.background='white';">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                </svg>
                Cerrar sesión
            </button>
        </form>
    </div>

</aside>
