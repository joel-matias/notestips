@extends('layout.notes.app')

@section('title', 'Editar nota | NotesTips')

@section('header-title')
    @if($note)
        <span class="text-sm font-medium truncate" style="color: var(--color-text-muted);">
            Editando: {{ $note->title }}
        </span>
    @else
        <span class="text-sm font-medium" style="color: var(--color-text-muted);">Editar nota</span>
    @endif
@endsection

@section('main-content')
    <div class="h-full overflow-auto" style="background: var(--color-bg);">
        @if ($note)
            @include('partials.notes.edit-form')
        @else
            <div class="flex flex-col items-center justify-center gap-4 h-full text-center px-6">
                <p class="text-sm" style="color: var(--color-text-muted);">Nota no encontrada</p>
                <a href="{{ route('notes.index') }}" class="text-sm font-medium hover:underline" style="color: var(--color-primary);">
                    Ver todas las notas
                </a>
            </div>
        @endif
    </div>
@endsection
