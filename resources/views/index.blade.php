@extends('layout.app')

@section('title', 'NotesTips — Tus notas siempre contigo')

@section('main-content')
<main class="min-h-screen" style="background: var(--color-bg);">

    {{-- Header --}}
    <header style="background: white; border-bottom: 1px solid var(--color-border);">
        <div class="mx-auto max-w-6xl px-4 py-4 flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background: var(--color-primary);">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" />
                        <path d="M8 7h8M8 11h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <span class="text-lg font-bold" style="color: var(--color-text);">NotesTips</span>
            </a>

            <nav class="flex items-center gap-2">
                @auth
                    <a href="{{ route('notes.index') }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-white transition-fast"
                        style="background: var(--color-primary);"
                        onmouseover="this.style.background='var(--color-primary-hover)';"
                        onmouseout="this.style.background='var(--color-primary)';">
                        Ir a mis notas
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium transition-fast"
                        style="border: 1px solid var(--color-border); background: white; color: var(--color-text-muted);"
                        onmouseover="this.style.background='var(--color-bg)';"
                        onmouseout="this.style.background='white';">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-white transition-fast"
                        style="background: var(--color-primary);"
                        onmouseover="this.style.background='var(--color-primary-hover)';"
                        onmouseout="this.style.background='var(--color-primary)';">
                        Empezar gratis
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold mb-6"
                style="background: var(--color-primary-soft); color: var(--color-primary);">
                Toma apuntes. Organiza ideas. Sin fricción.
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6"
                style="color: var(--color-text);">
                Tus notas y apuntes,<br>
                <span style="color: var(--color-primary);">siempre a mano</span>
            </h1>
            <p class="text-lg sm:text-xl leading-relaxed mb-10 max-w-2xl mx-auto"
                style="color: var(--color-text-muted);">
                NotesTips es una app pensada para estudiantes: rápida, limpia y completamente responsiva.
                Escribe en Markdown, gestiona tareas y accede desde cualquier dispositivo.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-3">
                @auth
                    <a href="{{ route('notes.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-base font-semibold text-white shadow-sm transition-fast"
                        style="background: var(--color-primary);"
                        onmouseover="this.style.background='var(--color-primary-hover)';"
                        onmouseout="this.style.background='var(--color-primary)';">
                        Abrir mis notas
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-base font-semibold text-white shadow-sm transition-fast"
                        style="background: var(--color-primary);"
                        onmouseover="this.style.background='var(--color-primary-hover)';"
                        onmouseout="this.style.background='var(--color-primary)';">
                        Crear cuenta gratis
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-6 py-3 rounded-xl text-base font-medium transition-fast"
                        style="border: 1px solid var(--color-border); background: white; color: var(--color-text-muted);"
                        onmouseover="this.style.background='var(--color-bg)';"
                        onmouseout="this.style.background='white';">
                        Ya tengo cuenta
                    </a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @php
            $features = [
                ['icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125', 'title' => 'Editor Markdown', 'desc' => 'Escribe con formato: negrita, listas, código, tablas y más. Toolbar con atajos de teclado.'],
                ['icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'title' => 'Checklists', 'desc' => 'Marca tareas completadas directamente en la vista de la nota, sin recargar la página.'],
                ['icon' => 'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3h3m-3 3h3M6.75 21h10.5', 'title' => '100% Responsiva', 'desc' => 'Diseñada para móvil y escritorio. Toma apuntes en clase desde tu teléfono sin problemas.'],
                ['icon' => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z', 'title' => 'Búsqueda y filtros', 'desc' => 'Encuentra tus notas al instante. Filtra por importancia, fecha de entrega y más.'],
            ];
            @endphp

            @foreach ($features as $f)
                <article class="rounded-2xl p-5 transition-fast"
                    style="background: white; border: 1px solid var(--color-border);"
                    onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.07)';"
                    onmouseout="this.style.boxShadow='none';">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4"
                        style="background: var(--color-primary-soft);">
                        <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="font-semibold mb-1.5" style="color: var(--color-text);">{{ $f['title'] }}</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-text-muted);">{{ $f['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

</main>
@endsection
