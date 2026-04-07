@extends('layout.app')

@section('title', 'Iniciar sesión | NotesTips')
@section('page', 'login')

@section('main-content')
<main class="min-h-screen flex" style="background: var(--color-bg);">

    {{-- Panel izquierdo decorativo (solo desktop) --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col items-center justify-center p-12"
        style="background: var(--color-primary);">
        <div class="max-w-sm text-center">
            <div class="w-20 h-20 rounded-3xl bg-white/20 flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                    <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" />
                    <path d="M8 7h8M8 11h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-4">NotesTips</h1>
            <p class="text-white/75 text-lg leading-relaxed">
                Tu espacio personal para capturar ideas, tomar apuntes en clase y organizar tus tareas.
            </p>
            <div class="mt-10 grid grid-cols-2 gap-4 text-left">
                @foreach (['Markdown soportado', 'Checklists interactivas', 'Filtros y búsqueda', 'Funciona en móvil'] as $feat)
                    <div class="flex items-center gap-2.5 text-white/85 text-sm">
                        <svg class="w-4 h-4 shrink-0 text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        {{ $feat }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-sm">

            {{-- Logo móvil --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center" style="background: var(--color-primary);">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                        <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round" />
                        <path d="M8 7h8M8 11h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <span class="text-xl font-bold" style="color: var(--color-text);">NotesTips</span>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold mb-1" style="color: var(--color-text);">Bienvenido de vuelta</h2>
                <p class="text-sm" style="color: var(--color-text-muted);">Inicia sesión para acceder a tus notas</p>
            </div>

            <form id="loginForm" method="POST" action="{{ route('login.store') }}" class="space-y-5" novalidate>
                @csrf

                {{-- Errores globales --}}
                @error('username')
                    @if (session('lock_seconds'))
                        <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
                            style="background: var(--color-error-soft); color: var(--color-error); border: 1px solid #FECACA;">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                            </svg>
                            <p id="lockMsg" data-seconds="{{ session('lock_seconds') }}"></p>
                        </div>
                    @elseif ($errors->first('username') !== 'LOCKED')
                        <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
                            style="background: var(--color-error-soft); color: var(--color-error); border: 1px solid #FECACA;">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                            </svg>
                            {{ $errors->first('username') }}
                        </div>
                    @endif
                @enderror

                @error('password')
                    <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
                        style="background: var(--color-error-soft); color: var(--color-error); border: 1px solid #FECACA;">
                        {{ $errors->first('password') }}
                    </div>
                @enderror

                {{-- Usuario --}}
                <div class="space-y-1.5">
                    <label for="username" class="block text-sm font-medium" style="color: var(--color-text);">
                        Nombre de usuario
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 pointer-events-none"
                            style="color: var(--color-text-subtle);"
                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <input id="username" type="text" name="username" value="{{ old('username') }}"
                            required maxlength="32" autocomplete="username" placeholder="tu_usuario"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm transition-fast focus:outline-none"
                            style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                            onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px var(--color-primary-ring)';"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none';">
                    </div>
                </div>

                {{-- Contraseña --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-medium" style="color: var(--color-text);">
                        Contraseña
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 pointer-events-none"
                            style="color: var(--color-text-subtle);"
                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                        <input id="password" type="password" name="password" required
                            autocomplete="current-password" placeholder="••••••••"
                            class="w-full pl-10 pr-20 py-2.5 rounded-xl text-sm transition-fast focus:outline-none"
                            style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                            onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px var(--color-primary-ring)';"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none';">
                        <button type="button" id="toggle1" aria-controls="password" aria-pressed="false"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold transition-fast"
                            style="color: var(--color-primary);">
                            Mostrar
                        </button>
                    </div>
                </div>

                {{-- Recordar sesión --}}
                <label class="flex items-center gap-2.5 text-sm cursor-pointer" style="color: var(--color-text-muted);">
                    <input type="checkbox" name="remember" value="1"
                        class="w-4 h-4 rounded cursor-pointer"
                        style="accent-color: var(--color-primary);"
                        {{ old('remember') ? 'checked' : '' }}>
                    Mantener sesión iniciada
                </label>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-semibold text-white transition-fast focus-visible:outline-none shadow-sm"
                    style="background: var(--color-primary);"
                    onmouseover="this.style.background='var(--color-primary-hover)';"
                    onmouseout="this.style.background='var(--color-primary)';">
                    Iniciar sesión
                </button>

                <p class="text-center text-sm" style="color: var(--color-text-muted);">
                    ¿Aún no tienes cuenta?
                    <a href="{{ url('/auth/register') }}"
                        class="font-medium transition-fast hover:underline"
                        style="color: var(--color-primary);">
                        Regístrate
                    </a>
                </p>
            </form>
        </div>
    </div>
</main>
@endsection
