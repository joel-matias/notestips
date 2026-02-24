@extends('layout.notes.app')

@section('title', 'Mis notas | NotesTips')

@section('main-content')
    @php
        $showDetailPanel = $noteNotFound || (bool) $note;
    @endphp
    <div class="h-full flex overflow-hidden">
        <aside
            class="{{ $showDetailPanel ? 'hidden lg:block' : 'block' }} w-full lg:w-80 shrink-0 border-r border-slate-200 bg-white overflow-auto">
            @include('partials.notes.list')
        </aside>

        <section class="{{ $showDetailPanel ? 'block' : 'hidden lg:block' }} flex-1 overflow-auto bg-slate-50">
            <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
                @if ($showDetailPanel)
                    <div class="mb-4 lg:hidden">
                        <a href="{{ route('notes.index', request()->query()) }}"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                            <span aria-hidden="true">←</span>
                            Volver a notas
                        </a>
                    </div>
                @endif

                @if ($noteNotFound)
                    <div class="h-full flex items-center justify-center text-slate-500">
                        Nota no encontrada
                    </div>
                @elseif ($note)
                    @include('partials.notes.show')
                @else
                    <div class="hidden lg:flex h-full items-center justify-center text-slate-500">
                        Seleccione una nota para ver su contenido
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
