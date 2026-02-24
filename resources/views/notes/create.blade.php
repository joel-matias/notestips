@extends('layout.notes.app')

@section('title', 'Crear Nota | NotesTips')

@section('main-content')
    <div class="h-full flex overflow-hidden">
        <aside class="hidden lg:block lg:w-80 shrink-0 border-r border-slate-200 bg-white overflow-auto">
            @include('partials.notes.list')
        </aside>

        <section class="flex-1 overflow-auto bg-slate-50">
            <div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
                <div class="mb-4 lg:hidden">
                    <a href="{{ route('notes.index', request()->query()) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        <span aria-hidden="true">←</span>
                        Ver notas
                    </a>
                </div>
                @include('partials.notes.create-form')
            </div>
        </section>
    </div>
@endsection
