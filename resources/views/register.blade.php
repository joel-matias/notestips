@extends('layout.app')

@section('main-content')
    <main class="min-h-screen flex items-center justify-center p-4 bg-(--color-bg)">
        <section class="w-full max-w-md" aria-labelledby="page-title">
            <div class="rounded-2xl shadow-lg p-8 bg-(--color-surface) border border-(--color-border)">

                <header class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4 bg-(--color-primary)"
                        aria-hidden="true">
                        <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" aria-hidden="true"
                            focusable="false">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" />
                            <path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 0 4 19.5v-15Z" stroke="currentColor"
                                stroke-width="2" stroke-linejoin="round" />
                            <path d="M8 6h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M8 10h8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <h1 id="page-title" class="text-3xl mb-2 text-(--color-text)">Mis Notas</h1>
                    <p class="text-(--color-text-muted)">Crea tu cuenta para empezar a organizar tus notas</p>
                </header>

                <form id="registerForm" method="POST" action="{{ route('register.store') }}" class="space-y-6" novalidate>
                    @csrf

                    <fieldset class="space-y-6">
                        <legend class="sr-only">Formulario de registro</legend>
                        <div>
                            <label for="username" class="block text-sm mb-2 text-(--color-text-muted)">
                                Nombre de usuario
                            </label>

                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-(--color-text-muted)"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                    <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                                <input id="username" type="text" name="username" value="{{ old('username') }}" required
                                    maxlength="32" autocomplete="username" placeholder="Ingresa tu usuario"
                                    @error('username') aria-invalid="true" aria-describedby="usernameError" @enderror
                                    class="w-full pl-11 pr-4 py-3 rounded-lg bg-(--color-bg) border border-(--color-border)
                                       text-(--color-text) outline-none transition
                                       focus:border-(--color-primary) focus:ring-4 focus:ring-blue-500/10" />
                            </div>

                            @error('username')
                                <p id="usernameError" class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm mb-2 text-(--color-text-muted)">
                                Contraseña
                            </label>

                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-(--color-text-muted)"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                    <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                    <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2"
                                        stroke-linejoin="round" />
                                </svg>

                                <input id="password" type="password" name="password" required autocomplete="new-password"
                                    placeholder="Ingresa tu contraseña" aria-describedby="passMsg"
                                    class="w-full pl-11 pr-24 py-3 rounded-lg bg-(--color-bg) border border-(--color-border)
                                       text-(--color-text) outline-none transition
                                       focus:border-(--color-primary) focus:ring-4 focus:ring-blue-500/10" />

                                <button type="button" id="toggle1" aria-controls="password" aria-pressed="false"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium
                                       text-(--color-primary) hover:underline">
                                    Mostrar
                                </button>
                            </div>

                            <div id="passMsg" class="mt-2 text-sm text-red-600" aria-live="polite"></div>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm mb-2 text-(--color-text-muted)">
                                Confirmar contraseña
                            </label>

                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-(--color-text-muted)"
                                    viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                    <path d="M7 11V8a5 5 0 0 1 10 0v3" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                    <path d="M6 11h12v10H6V11Z" stroke="currentColor" stroke-width="2"
                                        stroke-linejoin="round" />
                                </svg>

                                <input id="password_confirmation" type="password" name="password_confirmation" required
                                    autocomplete="new-password" placeholder="Confirma tu contraseña"
                                    aria-describedby="confirmMsg"
                                    class="w-full pl-11 pr-24 py-3 rounded-lg bg-(--color-bg) border border-(--color-border)
                                       text-(--color-text) outline-none transition
                                       focus:border-(--color-primary) focus:ring-4 focus:ring-blue-500/10" />

                                <button type="button" id="toggle2" aria-controls="password_confirmation"
                                    aria-pressed="false"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium
                                       text-(--color-primary) hover:underline">
                                    Mostrar
                                </button>
                            </div>

                            <div id="confirmMsg" class="mt-2 text-sm text-red-600" aria-live="polite"></div>
                        </div>

                        <button type="submit"
                            class="w-full text-white py-3 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg
                               bg-(--color-primary) hover:brightness-95 active:brightness-90">
                            Crear cuenta
                        </button>

                        <p class="text-center text-sm text-(--color-text-muted)">
                            ¿Ya tienes cuenta?
                            <a href="{{ url('/') }}" class="text-(--color-primary) hover:underline">Volver al
                                inicio</a>
                        </p>
                    </fieldset>
                </form>

            </div>
        </section>
    </main>
@endsection

@push('scripts')
    @vite('resources/js/pages/register.js')
@endpush
