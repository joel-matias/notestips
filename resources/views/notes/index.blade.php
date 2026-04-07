@extends('layout.notes.app')

@section('title', 'Mis notas | NotesTips')
@section('page', 'list')

@section('header-title')
    <span class="text-sm font-medium" style="color: var(--color-text-muted);" id="header-note-title">
        @if($note) {{ $note->title }} @else Mis notas @endif
    </span>
@endsection

@section('header-actions')
    @if($note)
        <div class="flex items-center gap-1">
            <a href="{{ route('notes.edit', $note->id) }}"
                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-fast"
                style="color: var(--color-text-muted); border: 1px solid var(--color-border);"
                onmouseover="this.style.background='var(--color-bg)';"
                onmouseout="this.style.background='transparent';">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                </svg>
                <span class="hidden sm:inline">Editar</span>
            </a>
        </div>
    @endif
@endsection

@section('main-content')
<div class="h-full overflow-auto" style="background: var(--color-bg);">
    <div id="note-panel" class="h-full">
        @if ($noteNotFound)
            <div class="flex flex-col items-center justify-center gap-4 h-full py-16 text-center px-6">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: #FEF2F2;">
                    <svg class="w-7 h-7" style="color: #EF4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold" style="color: var(--color-text);">Nota no encontrada</p>
                <a href="{{ route('notes.index') }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                    Ver todas las notas
                </a>
            </div>

        @elseif ($note)
            @include('partials.notes.show')

        @else
            <div class="flex flex-col items-center justify-center gap-4 h-full text-center px-6">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                    style="background: var(--color-primary-soft);">
                    <svg class="w-8 h-8" style="color: var(--color-primary); opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-base font-semibold" style="color: var(--color-text);">Selecciona una nota</p>
                    <p class="text-sm mt-0.5" style="color: var(--color-text-muted);">
                        Elige una nota de la lista para leerla aquí
                    </p>
                </div>
                <a href="{{ route('notes.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-white transition-fast"
                    style="background: var(--color-primary);"
                    onmouseover="this.style.background='var(--color-primary-hover)';"
                    onmouseout="this.style.background='var(--color-primary)';">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Nueva nota
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
