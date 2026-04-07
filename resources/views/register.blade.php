@extends('layout.app')

@section('title', 'Crear cuenta | NotesTips')
@section('page', 'register')

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
                Crea tu cuenta gratuita y empieza a organizar tus notas y apuntes en segundos.
            </p>
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
                <h2 class="text-2xl font-bold mb-1" style="color: var(--color-text);">Crear cuenta</h2>
                <p class="text-sm" style="color: var(--color-text-muted);">Empieza a organizar tus notas hoy</p>
            </div>

            <form id="registerForm" method="POST" action="{{ route('register.store') }}" class="space-y-5" novalidate>
                @csrf

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
                            required maxlength="32" autocomplete="username" placeholder="mi_usuario"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm transition-fast focus:outline-none"
                            style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                            onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px var(--color-primary-ring)';"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none';">
                    </div>
                    @error('username')
                        <p id="usernameError" class="text-sm" style="color: var(--color-error);" role="alert">{{ $message }}</p>
                    @enderror
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
                            autocomplete="new-password" placeholder="Mínimo 8 caracteres"
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
                    <div id="passMsg" class="text-sm" style="color: var(--color-error);" aria-live="polite"></div>
                </div>

                {{-- Confirmar contraseña --}}
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-sm font-medium" style="color: var(--color-text);">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4.5 h-4.5 pointer-events-none"
                            style="color: var(--color-text-subtle);"
                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        </svg>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            required autocomplete="new-password" placeholder="Repite tu contraseña"
                            class="w-full pl-10 pr-20 py-2.5 rounded-xl text-sm transition-fast focus:outline-none"
                            style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
                            onfocus="this.style.borderColor='var(--color-primary)'; this.style.boxShadow='0 0 0 3px var(--color-primary-ring)';"
                            onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none';">
                        <button type="button" id="toggle2" aria-controls="password_confirmation"
                            aria-pressed="false"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold transition-fast"
                            style="color: var(--color-primary);">
                            Mostrar
                        </button>
                    </div>
                    <div id="confirmMsg" class="text-sm" style="color: var(--color-error);" aria-live="polite"></div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 rounded-xl text-sm font-semibold text-white transition-fast focus-visible:outline-none shadow-sm"
                    style="background: var(--color-primary);"
                    onmouseover="this.style.background='var(--color-primary-hover)';"
                    onmouseout="this.style.background='var(--color-primary)';">
                    Crear cuenta
                </button>

                <p class="text-center text-sm" style="color: var(--color-text-muted);">
                    ¿Ya tienes cuenta?
                    <a href="{{ url('/auth/login') }}"
                        class="font-medium transition-fast hover:underline"
                        style="color: var(--color-primary);">
                        Iniciar sesión
                    </a>
                </p>
            </form>
        </div>
    </div>
</main>
@endsection
